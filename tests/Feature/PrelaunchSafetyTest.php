<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\SiteSetting;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PrelaunchSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_career_editor_opens_without_a_gallery_relation(): void
    {
        $career = Career::factory()->create();
        $this->actingAs(User::factory()->create())->get(route('admin.careers.edit', $career))->assertOk();
    }

    public function test_csv_keeps_visitor_formulas_as_text(): void
    {
        Submission::create(['type' => 'contact', 'name' => '=1+1', 'email' => 'visitor@example.test', 'phone' => '+355123', 'subject' => '@SUM(1)']);
        $response = $this->actingAs(User::factory()->admin()->create())->get(route('admin.submissions.export'))->assertOk();
        $rows = explode("\n", trim($response->streamedContent()));
        $row = str_getcsv($rows[1], ',', '"', '');
        $this->assertSame("\t=1+1", $row[3]);
        $this->assertSame('visitor@example.test', $row[4]);
        $this->assertSame("\t+355123", $row[5]);
        $this->assertSame("\t@SUM(1)", $row[6]);
    }

    public function test_password_change_revokes_database_sessions_and_remember_token(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['remember_token' => 'old-token']);
        DB::table('sessions')->insert(['id' => 'old-session', 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);
        $user->update(['password' => 'new-long-password']);
        $this->assertDatabaseMissing('sessions', ['id' => 'old-session']);
        $this->assertNotSame('old-token', $user->fresh()->remember_token);
    }

    public function test_old_password_session_is_rejected(): void
    {
        $user = User::factory()->admin()->create();
        $oldHash = $user->password;
        $user->update(['password' => 'new-long-password']);
        $this->actingAs($user)->withSession(['password_hash_web' => $oldHash])
            ->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_password_reset_uses_dashboard_postmark_without_external_delivery(): void
    {
        SiteSetting::create(['postmark_enabled' => true, 'postmark_username' => 'test-key', 'postmark_password' => 'test-secret', 'mail_from_address' => 'sender@example.test', 'mail_from_name' => 'Piramida']);
        $transport = new ArrayTransport;
        Mail::extend('smtp', fn (array $config) => $transport);
        $user = User::factory()->create();
        $this->post('/admin/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertCount(1, $transport->messages());
        $mail = $transport->messages()->first()->getOriginalMessage();
        $this->assertSame('sender@example.test', $mail->getFrom()[0]->getAddress());
        $this->assertSame($user->email, $mail->getTo()[0]->getAddress());
        $this->assertStringContainsString('reset-password', $mail->getHtmlBody());
    }

    public function test_reset_with_environment_mail_and_token_changes_password(): void
    {
        config(['mail.default' => 'array']);
        $user = User::factory()->create(['remember_token' => 'old-token']);
        $this->post('/admin/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $status = session('status');
        $this->post('/admin/forgot-password', ['email' => 'missing@example.test'])->assertSessionHas('status', $status);
        $token = Password::createToken($user);
        $this->post('/admin/reset-password', ['email' => $user->email, 'token' => $token, 'password' => 'new-long-password', 'password_confirmation' => 'new-long-password'])
            ->assertSessionHasNoErrors()->assertRedirect('/admin/login');
        $this->assertTrue(Hash::check('new-long-password', $user->fresh()->password));
        $this->assertNotSame('old-token', $user->fresh()->remember_token);
    }
}
