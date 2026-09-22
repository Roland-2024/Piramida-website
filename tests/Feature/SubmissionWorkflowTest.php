<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Http\Requests\PublicSite\StoreSubmissionRequest;
use App\Mail\SubmissionReceived;
use App\Models\Career;
use App\Models\Event;
use App\Models\SiteSetting;
use App\Models\Space;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_event_form_creates_request_and_notifies_staff(): void
    {
        Mail::fake();
        SiteSetting::query()->create(['notification_email' => 'team@piramida.test']);
        $event = Event::factory()->published()->create([
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.events.request', ['en', "event-{$event->id}"]), [
            'name' => 'Visitor Name',
            'email' => 'visitor@example.test',
            'phone' => '+355 69 000 0000',
            'attendees' => 2,
            'message' => 'Please reserve two places.',
            'privacy' => '1',
            'website' => '',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission = Submission::query()->firstOrFail();
        $this->assertSame(SubmissionType::EventRegistration, $submission->type);
        $this->assertSame($event->id, $submission->related_id);
        $this->assertSame([], $submission->details);
        Mail::assertSent(SubmissionReceived::class);
    }

    public function test_external_only_event_rejects_internal_submission(): void
    {
        $event = Event::factory()->published()->create([
            'booking_mode' => BookingMode::External,
            'external_url' => 'https://example.test/register',
        ]);

        $this->post(route('public.events.request', ['en', "event-{$event->id}"]), [
            'name' => 'Visitor Name',
            'email' => 'visitor@example.test',
            'phone' => '+355 69 000 0000',
            'attendees' => 1,
            'privacy' => '1',
        ])->assertNotFound();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_event_space_form_creates_a_staff_confirmed_request(): void
    {
        $space = Space::factory()->published()->create([
            'type' => SpaceType::EventSpace,
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.spaces.event-request', ['en', "space-{$space->id}"]), [
            'name' => 'Event Organizer',
            'email' => 'events@example.test',
            'phone' => '+355 69 111 1111',
            'event_type' => 'Conference',
            'preferred_date' => now()->addWeek()->toDateString(),
            'preferred_time' => '18:30',
            'attendees' => 120,
            'message' => 'We need a presentation setup.',
            'privacy' => '1',
            'website' => '',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission = Submission::query()->firstOrFail();
        $this->assertSame(SubmissionType::SpaceBooking, $submission->type);
        $this->assertSame($space->id, $submission->related_id);
        $this->assertSame('Conference', $submission->details['event_type']);
        $this->assertSame(120, $submission->details['attendees']);
    }

    public function test_leasing_form_creates_the_designed_application_with_private_documents(): void
    {
        Storage::fake('local');
        $space = Space::factory()->published()->create([
            'type' => SpaceType::Leasing,
            'booking_mode' => BookingMode::Internal,
            'area_sqm' => 110.2,
        ]);

        $documents = collect(StoreSubmissionRequest::leasingDocumentLabels())
            ->except('other_documents')
            ->mapWithKeys(fn (string $label, string $field) => [
                $field => UploadedFile::fake()->create("{$field}.pdf", 100, 'application/pdf'),
            ])
            ->all();

        $this->post(route('public.spaces.leasing-request', ['en', "space-{$space->id}"]), [
            'company_name' => 'Example Studio',
            'nipt' => 'L12345678A',
            'entity_type' => 'llc',
            'established_year' => 2018,
            'company_address' => 'Main Street 10',
            'city' => 'Tirana',
            'employee_count' => 12,
            'annual_turnover' => 250000,
            'contact_first_name' => 'Business',
            'contact_last_name' => 'Owner',
            'contact_position' => 'Director',
            'contact_phone' => '+355 4 222 2222',
            'contact_mobile' => '+355 69 222 2222',
            'contact_email' => 'owner@example.test',
            'offer_per_sqm' => 22,
            ...$documents,
            'privacy' => '1',
            'website' => '',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission = Submission::query()->firstOrFail();
        $this->assertSame(SubmissionType::Leasing, $submission->type);
        $this->assertSame('Business Owner', $submission->name);
        $this->assertSame('Example Studio', $submission->details['company_name']);
        $this->assertSame(2424.4, $submission->details['monthly_rent']);
        $this->assertCount(10, $submission->attachments);
        $submission->attachments->each(fn ($attachment) => Storage::disk('local')->assertExists($attachment->path));

        $attachment = $submission->attachments->first();
        $this->actingAs(User::factory()->create())
            ->get(route('admin.submissions.attachments.download', [$submission, $attachment]))
            ->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.submissions.attachments.download', [$submission, $attachment]))
            ->assertOk();

        $this->post(route('public.spaces.event-request', ['en', "space-{$space->id}"]), [
            'name' => 'Business Owner',
            'email' => 'owner@example.test',
            'phone' => '+355 69 222 2222',
            'event_type' => 'Conference',
            'preferred_date' => now()->addWeek()->toDateString(),
            'preferred_time' => '18:30',
            'attendees' => 20,
            'privacy' => '1',
        ])->assertNotFound();
    }

    public function test_leasing_rejects_offers_below_the_minimum(): void
    {
        $space = Space::factory()->published()->create([
            'type' => SpaceType::Leasing,
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.spaces.leasing-request', ['en', "space-{$space->id}"]), [
            'offer_per_sqm' => 21.99,
        ])->assertRedirect()->assertSessionHasErrors('offer_per_sqm');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_career_attachment_is_private_and_admin_can_review_submission(): void
    {
        Storage::fake('local');
        $career = Career::factory()->published()->create([
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.careers.apply', ['en', "position-{$career->id}"]), [
            'first_name' => 'Candidate',
            'last_name' => 'Name',
            'email' => 'candidate@example.test',
            'phone' => '+355 69 333 3333',
            'message' => 'I would like to apply.',
            'attachment' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
            'privacy' => '1',
        ])->assertRedirect();

        $submission = Submission::query()->firstOrFail();
        $this->assertSame('Candidate Name', $submission->name);
        Storage::disk('local')->assertExists($submission->attachment_path);

        $editor = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($editor)
            ->get(route('admin.submissions.show', $submission))
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('admin.submissions.update', $submission), [
                'status' => SubmissionStatus::InReview->value,
                'internal_notes' => 'Reviewing the application.',
            ])
            ->assertRedirect();

        $submission->refresh();
        $this->assertSame(SubmissionStatus::InReview, $submission->status);
        $this->assertSame($admin->id, $submission->handled_by);
    }
}
