<?php
return [    
    'class' => 'yii\authclient\Collection',
    // !!! update this fileds in the following (if it is empty) - this is required for correct oauth work
    'clients' => [
        'google' => [
            'class' => 'yii\authclient\clients\Google',
            'clientId' => env('GOOGLE_CLIENT_ID',''),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET',''),
        ],
        'facebook' => [
            'class' => 'yii\authclient\clients\Facebook',
            'clientId' => env('FACEBOOK_CLIENT_ID',''),
            'clientSecret' => env('FACEBOOK_CLIENT_SECRET',''),
        ],
        'twitter' => [
            'class' => 'yii\authclient\clients\Twitter',
            'consumerKey' => env('TWITTER_CLIENT_ID',''),
            'consumerSecret' => env('TWITTER_CLIENT_SECRET',''),
        ],
        'github' => [
            'class' => 'yii\authclient\clients\GitHub',
            'clientId' => env('GITHUB_CLIENT_ID',''),
            'clientSecret' => env('GITHUB_CLIENT_SECRET',''),
        ],
        'linkedin' => [
            'class' => 'yii\authclient\clients\LinkedIn',
            'clientId' => env('LINKEDIN_CLIENT_ID',''),
            'clientSecret' => env('LINKEDIN_CLIENT_SECRET',''),
        ],
        'vkontakte' => [
            'class' => 'yii\authclient\clients\VKontakte',
            'clientId' => env('VKONTAKTE_CLIENT_ID',''),
            'clientSecret' => env('VKONTAKTE_CLIENT_SECRET',''),
        ],
    ],
];