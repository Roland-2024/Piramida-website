<?php

namespace Tests\Feature;

use App\Enums\BookingMode;
use App\Enums\SpaceType;
use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
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
        $this->assertSame(2, $submission->details['attendees']);
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

    public function test_leasing_form_creates_an_enquiry_without_booking_dates(): void
    {
        $space = Space::factory()->published()->create([
            'type' => SpaceType::Leasing,
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.spaces.leasing-request', ['en', "space-{$space->id}"]), [
            'name' => 'Business Owner',
            'email' => 'owner@example.test',
            'phone' => '+355 69 222 2222',
            'organization' => 'Example Studio',
            'message' => 'We would like to discuss this commercial space.',
            'privacy' => '1',
            'website' => '',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $submission = Submission::query()->firstOrFail();
        $this->assertSame(SubmissionType::Leasing, $submission->type);
        $this->assertSame(['organization' => 'Example Studio'], $submission->details);

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

    public function test_career_attachment_is_private_and_admin_can_review_submission(): void
    {
        Storage::fake('local');
        $career = Career::factory()->published()->create([
            'booking_mode' => BookingMode::Internal,
        ]);

        $this->post(route('public.careers.apply', ['en', "position-{$career->id}"]), [
            'name' => 'Candidate Name',
            'email' => 'candidate@example.test',
            'message' => 'I would like to apply.',
            'attachment' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
            'privacy' => '1',
        ])->assertRedirect();

        $submission = Submission::query()->firstOrFail();
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
