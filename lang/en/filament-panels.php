<?php

return [
    'pages' => [
        'auth' => [
            'login' => [
                'actions' => [
                    'authenticate' => ['label' => 'Sign in'],
                ],
                'form' => [
                    'email'    => ['label' => 'Email address'],
                    'password' => ['label' => 'Password'],
                    'remember' => ['label' => 'Remember me'],
                ],
                'heading'  => 'Sign in',
                'messages' => ['failed' => 'These credentials do not match our records.'],
                'title'    => 'Sign in',
            ],
        ],
    ],
];
