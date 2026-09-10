<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Logger;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\LoginLog;
use App\Models\PasswordReset;
use App\Models\User;

final class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 300; // 5 minutes

    public function showLogin(): void
    {
        $this->view('admin.auth.login', [
            'pageTitle' => 'Admin Login',
        ], 'admin.layouts.guest');
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = (string) $this->input('email', '');
        $password = (string) $this->input('password', '');
        $limiterKey = 'login:' . Request::ip();

        if (RateLimiter::tooManyAttempts($limiterKey, self::MAX_ATTEMPTS, self::DECAY_SECONDS)) {
            $wait = (int) ceil(RateLimiter::availableIn($limiterKey, self::DECAY_SECONDS) / 60);
            Session::flash('error', "Too many login attempts. Please try again in {$wait} minute(s).");
            $this->redirect('admin/login');

            return;
        }

        $validator = $this->validate(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if ($validator->fails()) {
            Session::flash('error', 'Please enter a valid email and password.');
            $this->redirect('admin/login');

            return;
        }

        if (Auth::attempt($email, $password)) {
            RateLimiter::clear($limiterKey);
            $user = User::findByEmail($email);
            LoginLog::record($user['id'] ?? null, $email, 'success');
            $this->redirect('admin/dashboard');

            return;
        }

        RateLimiter::hit($limiterKey, self::DECAY_SECONDS);
        LoginLog::record(null, $email, 'failed');

        Session::flash('error', 'Invalid email or password.');
        $this->redirect('admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('admin/login');
    }

    public function showForgotPassword(): void
    {
        $this->view('admin.auth.forgot-password', [
            'pageTitle' => 'Forgot Password',
        ], 'admin.layouts.guest');
    }

    public function sendResetLink(): void
    {
        $this->requireCsrf();

        $email = (string) $this->input('email', '');
        $limiterKey = 'forgot-password:' . Request::ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 3, 600)) {
            Session::flash('error', 'Too many requests. Please try again later.');
            $this->redirect('admin/forgot-password');

            return;
        }

        RateLimiter::hit($limiterKey, 600);

        $user = User::findByEmail($email);

        // Always show a generic success message — never reveal whether the email exists.
        if ($user !== null) {
            $token = PasswordReset::createToken($email);
            // TODO: dispatch via the SMTP-configured mailer instead of logging.
            // The raw token/URL must never reach production logs (it's a live
            // credential for the next hour) — only surface it when APP_DEBUG
            // is on, for local testing.
            $appConfig = require dirname(__DIR__, 3) . '/config/app.php';

            if ($appConfig['debug']) {
                Logger::info('Password reset requested (debug only)', [
                    'email' => $email,
                    'reset_url' => View::url('admin/reset-password/' . $token),
                ]);
            } else {
                Logger::info('Password reset requested', ['email' => $email]);
            }
        }

        Session::flash('success', 'If an account exists for that email, a reset link has been sent.');
        $this->redirect('admin/login');
    }

    public function showResetPassword(string $token): void
    {
        if (PasswordReset::findValid($token) === null) {
            Session::flash('error', 'This password reset link is invalid or has expired.');
            $this->redirect('admin/forgot-password');

            return;
        }

        $this->view('admin.auth.reset-password', [
            'pageTitle' => 'Reset Password',
            'token' => $token,
        ], 'admin.layouts.guest');
    }

    public function resetPassword(string $token): void
    {
        $this->requireCsrf();

        $reset = PasswordReset::findValid($token);

        if ($reset === null) {
            Session::flash('error', 'This password reset link is invalid or has expired.');
            $this->redirect('admin/forgot-password');

            return;
        }

        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('password_confirmation', '');

        $validator = $this->validate(['password' => $password], ['password' => 'required|min:8']);

        if ($validator->fails() || $password !== $confirm) {
            Session::flash('error', 'Password must be at least 8 characters and match the confirmation.');
            $this->redirect('admin/reset-password/' . $token);

            return;
        }

        $user = User::findByEmail($reset['email']);

        if ($user !== null) {
            User::update((int) $user['id'], ['password' => password_hash($password, PASSWORD_BCRYPT)]);
        }

        PasswordReset::markUsed((int) $reset['id']);

        Session::flash('success', 'Your password has been reset. You can now log in.');
        $this->redirect('admin/login');
    }
}
