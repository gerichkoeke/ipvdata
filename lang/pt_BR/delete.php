<?php

return [
    'label' => 'Excluir',
    'modal' => [
        'heading'     => 'Excluir :label',
        'description' => 'Tem certeza que deseja excluir este registro? Esta ação não pode ser desfeita.',
        'actions'     => [
            'delete' => ['label' => 'Excluir'],
            'cancel' => ['label' => 'Cancelar'],
        ],
    ],
    'notifications' => [
        'deleted' => ['title' => 'Excluído com sucesso'],
    ],
];
