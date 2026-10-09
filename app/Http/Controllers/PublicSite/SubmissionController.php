<?php

namespace App\Http\Controllers\PublicSite;

use App\Enums\SpaceType;
use App\Enums\SubmissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSite\StoreSubmissionRequest;
use App\Mail\SubmissionReceived;
use App\Models\Career;
use App\Models\Event;
use App\Models\SiteSetting;
use App\Models\Space;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SubmissionController extends Controller
{
    public function contact(string $locale): View
    {
        return view('public.contact', [
            'siteSettings' => SiteSetting::query()->with('translations')->first(),
            'languageUrls' => collect(config('cms.locales'))
                ->mapWithKeys(fn (string $name, string $targetLocale) => [
                    $targetLocale => route('public.contact', $targetLocale),
                ])
                ->all(),
        ]);
    }

    public function storeContact(StoreSubmissionRequest $request): RedirectResponse
    {
        return $this->store($request, SubmissionType::Contact);
    }

    public function storeEvent(StoreSubmissionRequest $request, string $locale, string $slug): RedirectResponse
    {
        $event = $this->publishedBySlug(Event::class, $locale, $slug);
        abort_unless($event->booking_mode->allowsInternal(), 404);

        return $this->store($request, SubmissionType::EventRegistration, $event);
    }

    public function storeEventSpace(StoreSubmissionRequest $request, string $locale, string $slug): RedirectResponse
    {
        $space = $this->publishedBySlug(Space::class, $locale, $slug);
        abort_unless($space->type === SpaceType::EventSpace && $space->booking_mode->allowsInternal(), 404);

        return $this->store($request, SubmissionType::SpaceBooking, $space, [
            'event_type',
            'preferred_date',
            'preferred_time',
            'attendees',
        ]);
    }

    public function storeLeasing(StoreSubmissionRequest $request, string $locale, string $slug): RedirectResponse
    {
        $space = $this->publishedBySlug(Space::class, $locale, $slug);
        abort_unless($space->type === SpaceType::Leasing && $space->is_available && $space->leasingUnit()->exists() && $space->booking_mode->allowsInternal(), 404);

        return $this->storeLeasingApplication($request, $space);
    }

    public function storeCareer(StoreSubmissionRequest $request, string $locale, string $slug): RedirectResponse
    {
        $career = $this->publishedBySlug(Career::class, $locale, $slug, true);
        abort_unless($career->booking_mode->allowsInternal(), 404);

        return $this->store($request, SubmissionType::CareerApplication, $career);
    }

    /**
     * @param  array<int, string>  $detailKeys
     */
    private function store(
        StoreSubmissionRequest $request,
        SubmissionType $type,
        ?Model $related = null,
        array $detailKeys = [],
    ): RedirectResponse {
        $validated = $request->validated();
        $attachmentPath = null;

        try {
            if ($file = $validated['attachment'] ?? null) {
                $attachmentPath = $file->store(
                    'submissions/'.now()->format('Y/m'),
                    'local'
                );
                if ($attachmentPath === false) {
                    throw ValidationException::withMessages(['attachment' => __('cms.upload_failed')]);
                }
            }

            $submission = DB::transaction(function () use (
                $validated,
                $type,
                $related,
                $detailKeys,
                $attachmentPath,
            ): Submission {
                $file = $validated['attachment'] ?? null;
                $submission = new Submission([
                    'type' => $type,
                    'name' => $validated['name'] ?? trim(($validated['first_name'] ?? '').' '.($validated['last_name'] ?? '')),
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'subject' => $validated['subject'] ?? $this->relatedTitle($related),
                    'message' => $validated['message'] ?? null,
                    'details' => collect($validated)->only($detailKeys)->filter(fn ($value) => filled($value))->all(),
                    'attachment_disk' => $file ? 'local' : null,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $file?->getClientOriginalName(),
                    'attachment_mime' => $file?->getMimeType(),
                    'attachment_size' => $file?->getSize(),
                ]);
                $submission->related()->associate($related);
                $submission->save();

                return $submission;
            });
        } catch (Throwable $exception) {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }

            throw $exception;
        }

        $this->notifyStaff($submission);

        return back()->with('success', __('cms.request_received'))
            ->with('submitted_space_id', $type === SubmissionType::SpaceBooking ? $related->id : null);
    }

    private function storeLeasingApplication(StoreSubmissionRequest $request, Space $space): RedirectResponse
    {
        $validated = $request->validated();
        $storedFiles = [];

        try {
            foreach (array_keys(StoreSubmissionRequest::leasingDocumentLabels()) as $field) {
                $file = $request->file($field);

                if (! $file) {
                    continue;
                }

                $path = $file->store('submissions/'.now()->format('Y/m'), 'local');
                if ($path === false) {
                    throw ValidationException::withMessages([$field => __('cms.upload_failed')]);
                }
                $storedFiles[] = [
                    'document_type' => $field,
                    'disk' => 'local',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }

            $submission = DB::transaction(function () use ($validated, $space, $storedFiles): Submission {
                $detailKeys = [
                    'company_name',
                    'nipt',
                    'entity_type',
                    'established_year',
                    'company_address',
                    'city',
                    'employee_count',
                    'annual_turnover',
                    'contact_position',
                    'contact_phone',
                    'contact_mobile',
                    'offer_per_sqm',
                ];

                $details = collect($validated)->only($detailKeys)->all();
                $details['unit_code'] = $space->leasingUnit->code;
                $details['floor'] = $space->leasingUnit->floor;
                $details['area_sqm'] = $space->area_sqm;
                $details['monthly_rent'] = round((float) $validated['offer_per_sqm'] * (float) $space->area_sqm, 2);

                $submission = new Submission([
                    'type' => SubmissionType::Leasing,
                    'name' => trim($validated['contact_first_name'].' '.$validated['contact_last_name']),
                    'email' => $validated['contact_email'],
                    'phone' => $validated['contact_mobile'],
                    'subject' => $this->relatedTitle($space),
                    'details' => $details,
                ]);
                $submission->related()->associate($space);
                $submission->save();
                $submission->attachments()->createMany($storedFiles);

                return $submission;
            });
        } catch (Throwable $exception) {
            foreach ($storedFiles as $file) {
                Storage::disk($file['disk'])->delete($file['path']);
            }

            throw $exception;
        }

        $this->notifyStaff($submission);

        return back()->with('success', __('cms.request_received'));
    }

    private function notifyStaff(Submission $submission): void
    {
        $settings = SiteSetting::query()->first();
        $notificationEmail = $settings?->notification_email;

        if (! $notificationEmail) {
            return;
        }

        try {
            $message = new SubmissionReceived($submission);
            if ($settings->postmark_enabled) {
                $mailer = Mail::build($settings->postmarkTransport());
                $mailer->to($notificationEmail)->send($message->from($settings->mail_from_address, $settings->mail_from_name));
            } else {
                Mail::to($notificationEmail)->send($message);
            }
        } catch (Throwable $exception) {
            // SMTP diagnostics can include authentication exchanges; never log them.
            if ($settings->postmark_enabled) {
                logger()->error('Postmark notification failed; request remains saved.', ['submission_id' => $submission->id]);
            } else {
                report($exception);
            }
        }
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function publishedBySlug(
        string $modelClass,
        string $locale,
        string $slug,
        bool $open = false,
    ): Model {
        return $modelClass::query()
            ->published()
            ->when($open, fn (Builder $query) => $query->open())
            ->whereHas('translations', fn (Builder $query) => $query
                ->where('locale', $locale)
                ->where('slug', $slug))
            ->with('translations')
            ->firstOrFail();
    }

    private function relatedTitle(?Model $related): ?string
    {
        $translation = $related?->translation(app()->getLocale());

        return $translation?->title ?? $translation?->name;
    }
}
