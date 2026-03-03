<?php

return [
    'pages' => [
        'auth' => [
            'login' => [
                'actions' => [
                    'authenticate' => ['label' => 'Entrar'],
                ],
                'form' => [
                    'email'    => ['label' => 'E-mail'],
                    'password' => ['label' => 'Senha'],
                    'remember' => ['label' => 'Lembrar de mim'],
                ],
                'heading'  => 'Entrar',
                'messages' => ['failed' => 'Credenciais inválidas.'],
                'title'    => 'Entrar',
            ],
        ],
    ],
];
