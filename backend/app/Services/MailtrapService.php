<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MailtrapService
{
    private string $apiKey;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->apiKey = env('MAILTRAP_API_KEY', 'aa7ad2140fd5057e7ae5f2182554b5fb');
        $this->fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@golden-shop.mg');
        $this->fromName = env('MAIL_FROM_NAME', 'Golden Shop MDG');
    }

    public function sendVerificationEmail(string $toEmail, string $name, string $verificationCode): bool
    {
        \Log::info('Sending verification email', [
            'to' => $toEmail,
            'name' => $name,
            'code' => $verificationCode,
            'api_key' => substr($this->apiKey, 0, 10) . '...',
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://send.api.mailtrap.io/api/send', [
            'from' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName,
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $name,
                ],
            ],
            'subject' => 'Code de vérification - Golden Shop MDG',
            'html' => $this->getVerificationEmailTemplate($name, $verificationCode),
            'text' => "Bonjour {$name},\n\nVotre code de vérification est: {$verificationCode}\n\nCe code expire dans 15 minutes.\n\nSi vous n'avez pas créé de compte, vous pouvez ignorer cet email.",
            'category' => 'Email Verification',
        ]);

        \Log::info('Email response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'successful' => $response->successful(),
        ]);

        return $response->successful();
    }

    private function getVerificationEmailTemplate(string $name, string $verificationCode): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <title>Vérification Email</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background: linear-gradient(to right, #ca8a04, #f59e0b); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                    <h1 style='color: white; margin: 0; font-size: 28px;'>Golden Shop MDG</h1>
                </div>
                <div style='background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;'>
                    <h2 style='color: #1f2937; margin-top: 0;'>Bonjour {$name},</h2>
                    <p style='color: #4b5563;'>Merci de vous être inscrit sur Golden Shop MDG. Voici votre code de vérification:</p>
                    
                    <div style='background: #e5e7eb; padding: 20px; text-align: center; border-radius: 8px; margin: 30px 0;'>
                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #1f2937;'>{$verificationCode}</span>
                    </div>
                    
                    <p style='color: #4b5563; font-size: 14px;'>Ce code expire dans 15 minutes.</p>
                    
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                    
                    <p style='color: #9ca3af; font-size: 12px; margin: 0;'>Si vous n'avez pas créé de compte sur Golden Shop MDG, vous pouvez ignorer cet email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
