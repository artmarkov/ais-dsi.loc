<?php
return [
        'class' => 'yii\swiftmailer\Mailer',
        //'viewPath' => '@common/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
//        'useFileTransport' => YII_ENV_DEV,
        'useFileTransport' => false,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => env('SMTP_HOST'),
            'username' => env('SMTP_USERNAME'),
            'password' => env('SMTP_PASSWORD'),
            'port' => env('SMTP_PORT'),
            'encryption' => env('ENCRYPTION'),
            'streamOptions' => [ 'ssl' => [ 'allow_self_signed' => true, 'verify_peer' => false, 'verify_peer_name' => false, ], ]
        ],
        'htmlLayout' => '@artsoft/auth/views/mail/layouts/html',
        'textLayout' => '@artsoft/auth/views/mail/layouts/text',
];
