<?php

declare(strict_types=1);

namespace App\Controllers\Front;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Models\NewsletterSubscriber;

final class NewsletterController extends Controller
{
    public function subscribe(): void
    {
        $limiterKey = 'newsletter:' . Request::ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 5, 3600)) {
            Session::flash('error', 'Too many attempts. Please try again later.');
            $this->back();

            return;
        }

        $email = (string) $this->input('email', '');

        $validator = $this->validate(['email' => $email], ['email' => 'required|email|max:150']);

        if ($validator->fails()) {
            RateLimiter::hit($limiterKey, 3600);
            Session::flash('error', 'Please enter a valid email address.');
            $this->back();

            return;
        }

        RateLimiter::hit($limiterKey, 3600);

        $existing = NewsletterSubscriber::firstWhere(['email' => $email]);

        if ($existing === null) {
            NewsletterSubscriber::create(['email' => $email]);
        } elseif ($existing['status'] !== 'subscribed') {
            NewsletterSubscriber::update((int) $existing['id'], ['status' => 'subscribed']);
        }

        Session::flash('success', 'You are subscribed! Thanks for joining.');
        $this->back();
    }
}
