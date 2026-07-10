<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\MailtrapService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test-email', function () {
    $mailtrap = new MailtrapService();
    $testCode = '123456';
    $success = $mailtrap->sendVerificationEmail('nate14rd@gmail.com', 'Test User', $testCode);
    
    if ($success) {
        $this->info('Email envoyé avec succès à nate14rd@gmail.com');
        $this->info('Code de test: ' . $testCode);
    } else {
        $this->error('Échec de l\'envoi de l\'email');
    }
})->purpose('Test email sending with Mailtrap');
