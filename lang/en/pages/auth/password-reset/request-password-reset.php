<?php

return [

    'title' => 'Reset password',

    'heading' => 'Forgot your password?',

    'actions' => [

        'login' => [
            'label' => 'Back to login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'actions' => [

            'request' => [
                'label' => 'Send email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'If your account exists, you will receive an email with instructions on how to reset your password.',
        ],

        'throttled' => [
            'title' => 'Too many requests',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
