<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        if($request->filled('website')) {
            return back()->withErrors(["delivery" => "Your message couldn’t be sent. Please email me directly."]);
        }
        $validated = $request->validate(
            [
                "name" => "required|string|max:100",
                "email" => "required|email|max:254",
                "message" => "required|string|min:10|max:5000",
            ],
            [
                "name.required" => "Name is required.",
                "name.string" => "Name must be text.",
                "name.max" => "Name must not exceed 100 characters.",
                "email.required" => "Email is required.",
                "email.email" => "Email must be a valid email address.",
                "email.max" => "Email must not exceed 254 characters.",
                "message.required" => "Message is required.",
                "message.string" => "Message must be text.",
                "message.max" => "Message must not exceed 5000 characters.",
                "message.min" => "Message must contain at least 10 characters."
            ]
        );
        $rateLimitKey = "contact:" . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return back()->withErrors(["delivery" => "Too many attempts. Please wait before trying again."]);
        }
        RateLimiter::hit($rateLimitKey, 60);
        try {
            $recipient = config('mail.contact_to');
            $contact = new ContactMessage($validated['name'], $validated['email'], $validated['message']);
            Mail::to($recipient)->send($contact);
        } catch (TransportExceptionInterface $e) {
            report($e);
            return back()->withErrors(
                ["delivery" => "Your message couldn’t be sent. Please try again or email me directly."]
            );
        }

        return redirect('/');
    }
}
