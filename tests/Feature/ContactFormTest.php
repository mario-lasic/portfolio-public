<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Mail\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Support\Facades\RateLimiter;

class ContactFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        config(['mail.contact_to' => 'recipient@example.com']);

        RateLimiter::clear('contact:127.0.0.1');
    }

    /**
     * A basic feature test example.
     */
    public function test_contact_required_name_email_and_message()
    {
        $response = $this->post('/contact', []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_rejects_invalid_email()
    {
        $response = $this->post('/contact', ["name" => "Test", "email" => "test", "message" => "this is test message"]);
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionDoesntHaveErrors(['name', 'message']);
    }

    public function test_contact_rejects_short_message()
    {
        $response = $this->post('/contact', ["name" => "Test", "email" => "test@mail.com", "message" => "123456789"]);
        $response->assertSessionHasErrors(['message']);
        $response->assertSessionDoesntHaveErrors(['name', 'email']);
    }

    public function test_contact_accept_ten_character_message()
    {
        $response = $this->post('/contact', ["name" => "Test", "email" => "test@mail.com", "message" => "1234567890"]);
        $response->assertSessionDoesntHaveErrors(['name', 'email', 'message']);
        $response->assertRedirect('/');
    }

    public function test_contact_accept_five_thousand_character_message()
    {
        $testString = str_repeat("1", 5000);
        $response = $this->post('/contact', ["name" => "Test", "email" => "test@mail.com", "message" => $testString]);
        $response->assertSessionDoesntHaveErrors(['name', 'email', 'message']);
        $response->assertRedirect('/');
    }

    public function test_contact_rejects_five_thousand_and_one_character_message()
    {
        $testString = str_repeat("1", 5001);
        $response = $this->post('/contact', ["name" => "Test", "email" => "test@mail.com", "message" => $testString]);
        $response->assertSessionHasErrors(['message']);
        $response->assertSessionDoesntHaveErrors(['name', 'email']);
    }

    public function test_contact_accept_one_hundred_character_name()
    {
        $testString = str_repeat("A", 100);
        $response = $this->post(
            '/contact',
            ["name" => $testString, "email" => "test@mail.com", "message" => "1234567890"]
        );
        $response->assertSessionDoesntHaveErrors(['name', 'email', 'message']);
        $response->assertRedirect('/');
    }

    public function test_contact_rejects_one_hundred_and_one_character_name()
    {
        $testString = str_repeat("A", 101);
        $response = $this->post(
            '/contact',
            ["name" => $testString, "email" => "test@mail.com", "message" => "1234567890"]
        );
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionDoesntHaveErrors(['message', 'email']);
    }

    public function test_contact_rejects_two_hundred_fifty_five_character_email()
    {
        $testString = str_repeat("a", 250) . "@m.cm";
        $response = $this->post('/contact', ["name" => "Test", "email" => $testString, "message" => "1234567890"]);
        $response->assertSessionHasErrors([
            'email' => 'Email must not exceed 254 characters.'
        ]);
        $response->assertSessionDoesntHaveErrors(['message', 'name']);
    }

    public function test_contact_accept_two_hundred_fifty_four_character_email()
    {
        $testString = str_repeat("a", 250) . "@m.m";
        $response = $this->post('/contact', ["name" => "Test", "email" => $testString, "message" => "1234567890"]);
        $response->assertSessionDoesntHaveErrors(['message', 'name', 'email']);
        $response->assertRedirect('/');
    }

    public function test_valid_contact_sends_message_to_configured_recipient(): void
    {
        $data = [
            'name' => 'Mario',
            'email' => 'sender@example.com',
            'message' => 'This is a test contact message.',
        ];

        $response = $this->post('/contact', $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        Mail::assertSent(ContactMessage::class, function (ContactMessage $mail) use ($data) {
            return $mail->hasTo('recipient@example.com')
                && $mail->hasReplyTo($data['email'], $data['name'])
                && $mail->senderName === $data['name']
                && $mail->senderEmail === $data['email']
                && $mail->messageBody === $data['message'];
        });

        Mail::assertSentCount(1);
    }

    public function test_invalid_contact_does_not_send_mail(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Mario',
            'email' => 'invalid-email',
            'message' => 'This is a test contact message.',
        ]);

        $response->assertSessionHasErrors(['email']);

        Mail::assertNothingSent();
    }

    public function test_transport_failure_returns_delivery_error(): void
    {
        Mail::shouldReceive('to')
            ->once()
            ->with('recipient@example.com')
            ->andReturnSelf();

        Mail::shouldReceive('send')
            ->once()
            ->with(\Mockery::type(ContactMessage::class))
            ->andThrow(new TransportException('Simulated transport failure.'));

        $response = $this->from('/')->post('/contact', [
            'name' => 'Mario',
            'email' => 'sender@example.com',
            'message' => 'This is a test contact message.',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'delivery' => 'Your message couldn’t be sent. Please try again or email me directly.',
        ]);
        $response->assertSessionDoesntHaveErrors([
            'name',
            'email',
            'message',
        ]);
    }

    public function test_contact_blocks_fourth_attempt(): void
    {
        $data = [
            'name' => 'Mario',
            'email' => 'sender@example.com',
            'message' => 'This is a test contact message.',
            'website' => '',
        ];

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $response = $this->from('/')->post('/contact', $data);

            $response->assertSessionHasNoErrors();
            $response->assertRedirect('/');
        }

        $response = $this->from('/')->post('/contact', $data);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'delivery' => 'Too many attempts. Please wait before trying again.',
        ]);

        Mail::assertSent(ContactMessage::class, 3);
        Mail::assertSentCount(3);
    }

    public function test_contact_blocks_filled_honeypot(): void
    {
        $response = $this->from('/')->post('/contact', [
            'name' => 'Mario',
            'email' => 'sender@example.com',
            'message' => 'This is a test contact message.',
            'website' => 'https://example.com',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'delivery' => 'Your message couldn’t be sent. Please email me directly.',
        ]);

        Mail::assertNothingSent();

        $this->assertSame(
            0,
            RateLimiter::attempts('contact:127.0.0.1')
        );
    }
}
