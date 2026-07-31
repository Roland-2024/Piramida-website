<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubmissionRequest;
use App\Models\Submission;
use App\Models\SubmissionAttachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-submissions');
        $filters = $this->validatedFilters($request);

        $submissions = $this->filteredQuery($filters)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.submissions.index', [
            'submissions' => $submissions,
            'filters' => $filters,
            'types' => SubmissionType::cases(),
            'statuses' => SubmissionStatus::cases(),
        ]);
    }

    public function show(Submission $submission): View
    {
        Gate::authorize('manage-submissions');
        $submission->load(['related', 'handledBy', 'attachments']);

        return view('admin.submissions.show', [
            'submission' => $submission,
            'statuses' => SubmissionStatus::cases(),
        ]);
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        $data = $request->validated();
        $status = SubmissionStatus::from($data['status']);

        $submission->update([
            ...$data,
            'handled_by' => $status === SubmissionStatus::New ? null : $request->user()->id,
            'handled_at' => $status === SubmissionStatus::New ? null : now(),
        ]);

        return back()->with('success', 'Submission updated.');
    }

    public function download(Submission $submission): StreamedResponse
    {
        Gate::authorize('manage-submissions');
        abort_unless($submission->hasAttachment(), 404);

        return Storage::disk($submission->attachment_disk)
            ->download($submission->attachment_path, $submission->attachment_name);
    }

    public function downloadAttachment(Submission $submission, SubmissionAttachment $attachment): StreamedResponse
    {
        Gate::authorize('manage-submissions');
        abort_unless($attachment->submission_id === $submission->id, 404);

        return Storage::disk($attachment->disk)
            ->download($attachment->path, $attachment->original_name);
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('manage-submissions');
        $filters = $this->validatedFilters($request);

        return response()->streamDownload(function () use ($filters): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['ID', 'Type', 'Status', 'Name', 'Email', 'Phone', 'Subject', 'Created']);

            foreach ($this->filteredQuery($filters)->latest()->cursor() as $submission) {
                fputcsv($handle, [
                    $submission->id,
                    $submission->type->label(),
                    $submission->status->label(),
                    $submission->name,
                    $submission->email,
                    $submission->phone,
                    $submission->subject,
                    $submission->created_at->toIso8601String(),
                ]);
            }

            fclose($handle);
        }, 'piramida-submissions-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(SubmissionType::class)],
            'status' => ['nullable', Rule::enum(SubmissionStatus::class)],
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return Submission::query()
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%"));
            })
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status));
    }
}
