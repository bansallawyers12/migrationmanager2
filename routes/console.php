<?php

use App\Support\Chatbot\ChatbotFaqInstaller;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('chatbot:seed-faq-library', function () {
    ChatbotFaqInstaller::installFromPhpArray();
    $this->info('Chatbot training FAQs imported from database/chatbot_training/faq_entries.php');
})->describe('Load scripted Bansal Immigration chatbot FAQs (exact training-document answers)');
