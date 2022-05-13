<?php
return [
    'class' => 'nterms\mailqueue\MailQueue',
    'useFileTransport' => false,
    'table' => 'mail_queue',
    'mailsPerRound' => 20,
    'maxAttempts' => 3,
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => env('SMTP_HOST'),
        'username' => env('SMTP_USERNAME'),
        'password' => env('SMTP_PASSWORD'),
        'port' => env('SMTP_PORT'),
        'encryption' => env('ENCRYPTION'),
        'streamOptions' => ['ssl' => ['allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false],]
    ],
    'htmlLayout' => '@artsoft/auth/views/mail/layouts/html',
    'textLayout' => '@artsoft/auth/views/mail/layouts/text',
];
