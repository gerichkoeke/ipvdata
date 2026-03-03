<?php

return [

    'title' => 'Sign in',

    'heading' => 'Sign in to your account',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'Create an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgot your password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Sign in',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'These credentials do not match our records.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many login attempts.',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
