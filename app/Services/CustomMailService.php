<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Illuminate\Mail\MailManager;

class CustomMailService
{
    /**
     * Send email with custom SMTP configuration
     */
    public static function sendWithCustomSmtp($to, $mailable, $senderConfig = null)
    {
        if ($senderConfig && $senderConfig->smtp_username) {
            // Create custom transport for this sender
            $transport = new EsmtpTransport(
                $senderConfig->smtp_host,
                $senderConfig->smtp_port,
                $senderConfig->smtp_enc
            );
            
            $transport->setUsername($senderConfig->smtp_username);
            $transport->setPassword($senderConfig->smtp_password);
            
            // Create custom mailer
            $symfonyMailer = new SymfonyMailer($transport);
            
            // Get Laravel's mail manager and create a custom mailer
            $mailManager = app(MailManager::class);
            $customMailer = $mailManager->mailer('custom_smtp');
            
            // Set the custom transport
            $customMailer->setSymfonyMailer($symfonyMailer);
            
            // Send the email
            return $customMailer->to($to)->send($mailable);
        } else {
            // Use default mailer
            return Mail::to($to)->send($mailable);
        }
    }
    
    /**
     * Send email template with custom SMTP
     */
    public static function sendEmailTemplate($replace, $replace_with, $alias, $to, $subject, $sender, $sendername)
    {
        // email_templates table has been deleted - using fallback content
        $email_template = DB::table('email_templates')->where('alias', $alias)->first();
        if(!$email_template) {
            \Log::warning('Email template not found for alias: ' . $alias . ' - email_templates table has been deleted');
            // Use a simple fallback email content
            $emailContent = 'Email template content is no longer available. Please contact support.';
        } else {
            $emailContent = $email_template->description;
            $emailContent = str_replace($replace, $replace_with, $emailContent);
        }
        
        if ($subject == NULL) {
            $subject = $subject;
        }
        
        $explodeTo = explode(';', $to);
        
        $mailable = new \App\Mail\CommonMail($emailContent, $subject, $sender, $sendername->company_name);
        
        return self::sendWithCustomSmtp($explodeTo, $mailable, $sendername);
    }
} 