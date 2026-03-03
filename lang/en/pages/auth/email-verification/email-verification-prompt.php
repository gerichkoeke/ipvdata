<?php

return [

    'title' => 'Verify email address',

    'heading' => 'Verify your email address',

    'actions' => [

        'resend_notification' => [
            'label' => 'Resend verification email',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Didn\'t receive the email we sent?',
        'notification_sent' => 'We\'ve sent an email to :email with instructions to verify your email address.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Email resent.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Too many resend attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
