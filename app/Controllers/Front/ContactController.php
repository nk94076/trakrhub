<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\Logger;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Models\ContactLead;
use App\Models\Setting;

final class ContactController extends Controller
{
    public function submit(): void
    {
        $this->requireCsrf();

        // Honeypot: a real visitor never fills this hidden field.
        if ((string) $this->input('website', '') !== '') {
            Session::flash('success', 'Thanks! We will be in touch shortly.');
            $this->back();

            return;
        }

        $limiterKey = 'contact:' . Request::ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 5, 3600)) {
            Session::flash('error', 'Too many submissions. Please try again later.');
            $this->back();

            return;
        }

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $message = trim((string) $this->input('message', ''));
        $leadType = in_array($this->input('lead_type'), ['advertiser', 'publisher', 'general'], true)
            ? $this->input('lead_type')
            : 'general';

        $validator = $this->validate(
            ['name' => $name, 'email' => $email, 'message' => $message],
            ['name' => 'required|max:150', 'email' => 'required|email|max:150', 'message' => 'required|max:2000']
        );

        if ($validator->fails()) {
            RateLimiter::hit($limiterKey, 3600);
            Session::flash('error', 'Please fill in your name, a valid email and a message.');
            $this->back();

            return;
        }

        if (!$this->recaptchaPasses()) {
            Session::flash('error', 'We could not verify you are human. Please try again.');
            $this->back();

            return;
        }

        RateLimiter::hit($limiterKey, 3600);

        $id = ContactLead::create([
            'name' => $name,
            'email' => $email,
            'phone' => trim((string) $this->input('phone', '')) ?: null,
            'company' => trim((string) $this->input('company', '')) ?: null,
            'lead_type' => $leadType,
            'message' => $message,
            'source_page' => $_SERVER['HTTP_REFERER'] ?? null,
            'ip_address' => Request::ip(),
            'status' => 'new',
        ]);

        // TODO(Phase 5): send via the SMTP-configured mailer; logged for now, same as password-reset emails.
        Logger::info('New contact lead received', ['id' => $id, 'email' => $email, 'lead_type' => $leadType]);

        Session::flash('success', "Thanks {$name}! We've received your message and will be in touch shortly.");
        $this->back();
    }

    private function recaptchaPasses(): bool
    {
        $secret = Setting::get('recaptcha', 'secret_key', '');

        // No key configured yet — do not block real leads on an unset integration.
        if ($secret === '' || $secret === null) {
            return true;
        }

        $token = (string) $this->input('g-recaptcha-response', '');

        if ($token === '') {
            return false;
        }

        $context = stream_context_create(['http' => ['method' => 'POST', 'timeout' => 5]]);
        $response = @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secret) . '&response=' . urlencode($token),
            false,
            $context
        );

        $result = $response !== false ? json_decode($response, true) : null;

        return (bool) ($result['success'] ?? false);
    }
}
