<?php

return [
    'pages' => [
        'auth' => [
            'login' => [
                'actions' => [
                    'authenticate' => ['label' => 'Iniciar sesión'],
                ],
                'form' => [
                    'email'    => ['label' => 'Correo electrónico'],
                    'password' => ['label' => 'Contraseña'],
                    'remember' => ['label' => 'Recordarme'],
                ],
                'heading'  => 'Iniciar sesión',
                'messages' => ['failed' => 'Las credenciales no coinciden con nuestros registros.'],
                'title'    => 'Iniciar sesión',
            ],
        ],
    ],
];
