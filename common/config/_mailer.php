<?php
return [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@common/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => YII_ENV_DEV,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => env('SMTP_HOST'),
            'username' => env('SMTP_USERNAME'),
            'password' => env('SMTP_PASSWORD'),
            'port' => env('SMTP_PORT'),
            'encryption' => env('ENCRYPTION'),
        ],
        'htmlLayout' => '@vendor/artsoft/yii2-art-auth/views/mail/layouts/html',
        'textLayout' => '@vendor/artsoft/yii2-art-auth/views/mail/layouts/text',
];
