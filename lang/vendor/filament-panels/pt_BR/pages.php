<?php

return [

    'auth' => [
        'email_verification' => [
            'title'   => 'Verificar e-mail',
            'heading' => 'Verificar e-mail',
            'actions' => [
                'resend_notification' => ['label' => 'Reenviar e-mail'],
            ],
            'messages'      => ['notification_not_received' => 'Não recebeu o e-mail?'],
            'notifications' => ['notification_resent' => ['title' => 'E-mail reenviado!']],
        ],
        'login'  => [
            'title'   => 'Entrar',
            'heading' => 'Entrar',
            'actions' => [
                'authenticate'          => ['label' => 'Entrar'],
                'request_password_reset'=> ['label' => 'Esqueci minha senha'],
            ],
            'form' => [
                'email'    => ['label' => 'E-mail'],
                'password' => ['label' => 'Senha'],
                'remember'  => ['label' => 'Lembrar de mim'],
            ],
            'messages'     => ['failed' => 'Credenciais inválidas.'],
            'notifications'=> ['throttled' => ['title' => 'Muitas tentativas.', 'body' => 'Tente novamente em :seconds segundos.']],
        ],
        'logout' => [
            'title' => 'Sair',
        ],
        'password_reset' => [
            'request' => [
                'title'   => 'Redefinir senha',
                'heading' => 'Redefinir senha',
                'actions' => ['request' => ['label' => 'Enviar link de redefinição']],
                'form'    => ['email' => ['label' => 'E-mail']],
                'notifications' => ['throttled' => ['title' => 'Muitas tentativas.']],
            ],
            'reset' => [
                'title'   => 'Redefinir senha',
                'heading' => 'Redefinir senha',
                'actions' => ['reset' => ['label' => 'Redefinir senha']],
                'form'    => [
                    'email'                 => ['label' => 'E-mail'],
                    'password'              => ['label' => 'Nova senha'],
                    'password_confirmation' => ['label' => 'Confirmar nova senha'],
                ],
                'notifications' => ['invalid_token' => ['title' => 'Token inválido.']],
            ],
        ],
        'edit_profile' => [
            'title'   => 'Editar perfil',
            'heading' => 'Editar perfil',
            'actions' => [
                'cancel' => ['label' => 'Cancelar'],
                'save'   => ['label' => 'Salvar alterações'],
            ],
            'form' => [
                'email'                 => ['label' => 'E-mail'],
                'name'                  => ['label' => 'Nome'],
                'password'              => ['label' => 'Nova senha'],
                'password_confirmation' => ['label' => 'Confirmar nova senha'],
            ],
            'notifications' => ['saved' => ['title' => 'Perfil atualizado!']],
        ],
        'register' => [
            'title'   => 'Registrar',
            'heading' => 'Criar conta',
            'actions' => ['register' => ['label' => 'Registrar']],
            'form'    => [
                'email'                 => ['label' => 'E-mail'],
                'name'                  => ['label' => 'Nome'],
                'password'              => ['label' => 'Senha'],
                'password_confirmation' => ['label' => 'Confirmar senha'],
            ],
            'notifications' => ['throttled' => ['title' => 'Muitas tentativas.']],
        ],
    ],

    'dashboard' => [
        'title' => 'Dashboard',
    ],

    'error' => [
        'title'   => 'Erro :status',
        'heading' => ':status',
    ],

    'tenancy' => [
        'billing'          => ['title' => 'Faturamento'],
        'register_tenant'  => ['title' => 'Criar nova organização', 'heading' => 'Criar nova organização', 'form' => ['name' => ['label' => 'Nome']], 'actions' => ['register' => ['label' => 'Criar']]],
        'select_tenant'    => ['title' => 'Selecionar organização', 'heading' => 'Selecionar organização', 'actions' => ['create_tenant' => ['label' => 'Criar nova'], 'billing' => ['label' => 'Gerenciar faturamento']]],
    ],

];
