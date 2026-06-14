<?php

namespace App\Libraries;

class BrevoEmail
{
    private string $apiKey = '';
    private string $senderEmail = 'noreply@santulan.com';
    private string $senderName = 'Santulan Support';

    public function __construct()
    {
        $this->apiKey = env('BREVO_API_KEY') ?: getenv('BREVO_API_KEY') ?: '';
    }

    /**
     * Send email via Brevo REST API
     */
    public function sendEmail(string $toEmail, string $subject, string $htmlContent): bool
    {
        $url = 'https://api.brevo.com/v3/smtp/email';

        $payload = [
            'sender' => [
                'name'  => $this->senderName,
                'email' => $this->senderEmail
            ],
            'to' => [
                [
                    'email' => $toEmail
                ]
            ],
            'subject'     => $subject,
            'htmlContent' => $htmlContent
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'api-key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        log_message('error', 'Brevo email sending failed to ' . $toEmail . '. HTTP Code: ' . $httpCode . ', Response: ' . $response);
        return false;
    }

    /**
     * Send Verification Email
     */
    public function sendVerificationEmail(string $toEmail, string $token): bool
    {
        $verifyUrl = base_url('api/auth/verify?token=' . $token);
        $subject = 'Verify Your Santulan Account';
        
        $htmlContent = '
        <html>
        <head>
            <title>Verify Your Santulan Account</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f4f6fa; padding: 20px; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <h2 style="color: #1E3A8A; margin-top: 0;">Welcome to Santulan!</h2>
                <p>Thank you for signing up. Please click the button below to verify your email address and complete your registration requests setup.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $verifyUrl . '" style="background-color: #1E3A8A; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Verify Email Address</a>
                </div>
                <p style="font-size: 12px; color: #666;">If the button above does not work, copy and paste this link into your browser:<br>' . $verifyUrl . '</p>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="font-size: 11px; color: #999; text-align: center;">This is an automated message. Please do not reply directly to this email.</p>
            </div>
        </body>
        </html>';

        return $this->sendEmail($toEmail, $subject, $htmlContent);
    }

    /**
     * Send Password Reset Email
     */
    public function sendPasswordResetEmail(string $toEmail, string $token): bool
    {
        // For password reset, since this is a mobile client API, we can either:
        // 1. Send a link to a web page on the server that resets it, OR
        // 2. Send a 6-digit verification code that the user enters in the mobile app.
        // Let's send the token directly as a 6-digit numeric/string code if it's short, or a web reset link.
        // Let's support a web reset link for simplicity.
        // Web reset endpoint: /admin/reset-password?token=xxx (or mobile app deep link / web form)
        // Since we are building an email-based flow, let's direct them to the reset link.
        // Wait, a web reset link is very standard! We'll host a simple admin reset page or a user reset page.
        // Let's call it base_url('admin/reset-password?token=' . $token)
        $resetUrl = base_url('admin/reset-password?token=' . $token);
        $subject = 'Reset Your Santulan Password';

        $htmlContent = '
        <html>
        <head>
            <title>Reset Your Santulan Password</title>
        </head>
        <body style="font-family: Arial, sans-serif; background-color: #f4f6fa; padding: 20px; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <h2 style="color: #1E3A8A; margin-top: 0;">Password Reset Request</h2>
                <p>We received a request to reset your password. Click the button below to choose a new password.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="' . $resetUrl . '" style="background-color: #1E3A8A; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Reset Password</a>
                </div>
                <p style="font-size: 12px; color: #666;">This link is valid for 1 hour. If you did not request this, you can safely ignore this email.</p>
                <p style="font-size: 12px; color: #666;">If the button above does not work, copy and paste this link into your browser:<br>' . $resetUrl . '</p>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                <p style="font-size: 11px; color: #999; text-align: center;">This is an automated message. Please do not reply directly to this email.</p>
            </div>
        </body>
        </html>';

        return $this->sendEmail($toEmail, $subject, $htmlContent);
    }
}
