<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\BrevoEmail;

class AuthController extends BaseController
{
    private function parseDateTime($val)
    {
        if (empty($val)) {
            return null;
        }
        $ts = strtotime($val);
        return $ts !== false ? date('Y-m-d H:i:s', $ts) : null;
    }

    /**
     * POST /api/auth/register
     */
    public function register()
    {
        $request = service('request');
        $json = $request->getJSON(true) ?? $request->getPost();

        $email = $json['email'] ?? null;
        $password = $json['password'] ?? null;

        if (!$email || !$password) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Email and password are required'
            ])->setStatusCode(400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid email address'
            ])->setStatusCode(400);
        }

        if (strlen($password) < 6) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Password must be at least 6 characters long'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $exists = $builder->where('email', $email)->countAllResults() > 0;
        if ($exists) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Email address is already registered'
            ])->setStatusCode(409);
        }

        $verificationToken = bin2hex(random_bytes(32));

        $data = [
            'email'              => $email,
            'password_hash'      => password_hash($password, PASSWORD_BCRYPT),
            'google_id'          => null,
            'verification_token' => $verificationToken,
            'status'             => 'pending',
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        $db->transStart();
        $builder->insert($data);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Failed to create user account'
            ])->setStatusCode(500);
        }

        // Send email via Brevo REST API
        $brevo = new BrevoEmail();
        $emailSent = $brevo->sendVerificationEmail($email, $verificationToken);

        if (!$emailSent) {
            // Note: We don't fail the registration if email fails to send, but log it
            log_message('error', 'Could not send verification email to ' . $email);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Registration successful. Please check your email to verify your address.'
        ]);
    }

    /**
     * GET /api/auth/verify?token=xxx
     */
    public function verify()
    {
        $request = service('request');
        $token = $request->getGet('token');

        if (!$token) {
            return $this->renderVerifyResult(false, 'Missing verification token.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('verification_token', $token)->get()->getRow();

        if (!$user) {
            return $this->renderVerifyResult(false, 'Invalid or expired verification token.');
        }

        $builder->where('id', $user->id)->update([
            'verification_token' => null,
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s')
        ]);

        return $this->renderVerifyResult(true, 'Your email has been verified successfully! Your account is now pending administrator approval before you can log in.');
    }

    /**
     * POST /api/auth/login
     */
    public function login()
    {
        $request = service('request');
        $json = $request->getJSON(true) ?? $request->getPost();

        $email = $json['email'] ?? null;
        $password = $json['password'] ?? null;

        if (!$email || !$password) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Email and password are required'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('email', $email)->get()->getRow();

        if (!$user || !$user->password_hash || !password_verify($password, $user->password_hash)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid email or password'
            ])->setStatusCode(401);
        }

        // Enforce email verification
        if (empty($user->email_verified_at)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Please verify your email address first.'
            ])->setStatusCode(403);
        }

        // Enforce admin registration approval
        if ($user->status === 'pending') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Account pending admin registration approval.'
            ])->setStatusCode(403);
        }

        if ($user->status === 'rejected') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Account registration has been rejected or disabled.'
            ])->setStatusCode(403);
        }

        // Generate session token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $db->table('user_tokens')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => (int)$user->id,
                'email' => $user->email,
                'status'=> $user->status
            ]
        ]);
    }

    /**
     * POST /api/auth/google
     */
    public function google()
    {
        $request = service('request');
        $json = $request->getJSON(true) ?? $request->getPost();

        $googleId = $json['googleId'] ?? null;
        $email = $json['email'] ?? null;

        if (!$googleId || !$email) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'googleId and email are required'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Check if user exists by google_id or email
        $user = $builder->groupStart()
                            ->where('google_id', $googleId)
                            ->orWhere('email', $email)
                        ->groupEnd()
                        ->get()
                        ->getRow();

        if ($user) {
            // Update google_id if missing (e.g. registered via email first)
            if (empty($user->google_id)) {
                $builder->where('id', $user->id)->update([
                    'google_id' => $googleId,
                    'updated_at'=> date('Y-m-d H:i:s')
                ]);
            }

            // Check status
            if ($user->status === 'pending') {
                return $this->response->setJSON([
                    'status'  => 'pending',
                    'message' => 'Account pending admin registration approval.'
                ])->setStatusCode(403);
            }

            if ($user->status === 'rejected') {
                return $this->response->setJSON([
                    'status'  => 'rejected',
                    'message' => 'Account registration has been rejected or disabled.'
                ])->setStatusCode(403);
            }
        } else {
            // Create pending Google user (email verified automatically via Google OAuth)
            $data = [
                'email'             => $email,
                'google_id'         => $googleId,
                'password_hash'     => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status'            => 'pending',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s')
            ];

            $builder->insert($data);
            $userId = $db->insertID();

            return $this->response->setJSON([
                'status'  => 'pending',
                'message' => 'Registration request submitted. Account pending admin registration approval.'
            ])->setStatusCode(201);
        }

        // Generate session token for approved user
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $db->table('user_tokens')->insert([
            'user_id'    => $user->id,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => (int)$user->id,
                'email' => $user->email,
                'status'=> $user->status
            ]
        ]);
    }

    /**
     * POST /api/auth/forgot-password
     */
    public function forgotPassword()
    {
        $request = service('request');
        $json = $request->getJSON(true) ?? $request->getPost();

        $email = $json['email'] ?? null;

        if (!$email) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Email is required'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('email', $email)->get()->getRow();

        if ($user && $user->password_hash) {
            $resetToken = bin2hex(random_bytes(16));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $builder->where('id', $user->id)->update([
                'reset_token'            => $resetToken,
                'reset_token_expires_at' => $expiresAt,
                'updated_at'             => date('Y-m-d H:i:s')
            ]);

            $brevo = new BrevoEmail();
            $brevo->sendPasswordResetEmail($email, $resetToken);
        }

        // Always return success to prevent user enumeration
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'If this email is registered, a password reset link has been sent.'
        ]);
    }

    /**
     * POST /api/auth/reset-password
     */
    public function resetPassword()
    {
        $request = service('request');
        $json = $request->getJSON(true) ?? $request->getPost();

        $token = $json['token'] ?? null;
        $password = $json['password'] ?? null;

        if (!$token || !$password) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Token and password are required'
            ])->setStatusCode(400);
        }

        if (strlen($password) < 6) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Password must be at least 6 characters long'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        $user = $builder->where('reset_token', $token)
                        ->where('reset_token_expires_at >', date('Y-m-d H:i:s'))
                        ->get()
                        ->getRow();

        if (!$user) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Invalid or expired password reset token'
            ])->setStatusCode(400);
        }

        $builder->where('id', $user->id)->update([
            'password_hash'          => password_hash($password, PASSWORD_BCRYPT),
            'reset_token'            => null,
            'reset_token_expires_at' => null,
            'updated_at'             => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Password has been reset successfully.'
        ]);
    }

    /**
     * Render Email Verification HTML Result Page
     */
    private function renderVerifyResult(bool $success, string $message): string
    {
        $title = $success ? 'Verification Successful' : 'Verification Failed';
        $color = $success ? '#10B981' : '#EF4444';
        
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $title . '</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    background-color: #0F172A;
                    color: #F8FAFC;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .card {
                    background: rgba(30, 41, 59, 0.7);
                    backdrop-filter: blur(10px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 24px;
                    padding: 40px;
                    max-width: 450px;
                    width: 90%;
                    text-align: center;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                }
                .icon {
                    font-size: 64px;
                    color: ' . $color . ';
                    margin-bottom: 20px;
                }
                h1 {
                    font-size: 24px;
                    font-weight: 700;
                    margin-bottom: 15px;
                }
                p {
                    color: #94A3B8;
                    line-height: 1.6;
                    font-size: 15px;
                    margin-bottom: 30px;
                }
                .btn {
                    display: inline-block;
                    background-color: #1E3A8A;
                    color: #fff;
                    padding: 12px 30px;
                    text-decoration: none;
                    border-radius: 12px;
                    font-weight: 600;
                    font-size: 14px;
                    transition: background-color 0.2s;
                }
                .btn:hover {
                    background-color: #2563EB;
                }
            </style>
        </head>
        <body>
            <div class="card">
                <div class="icon">' . ($success ? '✓' : '✗') . '</div>
                <h1>' . $title . '</h1>
                <p>' . htmlspecialchars($message) . '</p>
                <a href="#" class="btn">Close Window</a>
            </div>
        </body>
        </html>
        ';
    }
}
