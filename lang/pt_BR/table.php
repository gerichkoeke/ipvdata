<?php
return [
    'fields' => [
        'search' => [
            'label'       => 'Pesquisar',
            'placeholder' => 'Pesquisar',
        ],
    ],
    'filters' => [
        'actions' => [
            'remove'     => ['label' => 'Remover filtro'],
            'remove_all' => ['label' => 'Limpar filtros'],
            'reset'      => ['label' => 'Redefinir'],
        ],
        'heading'  => 'Filtros',
        'trashed'  => [
            'label'           => 'Registros excluídos',
            'only_trashed'    => 'Apenas excluídos',
            'with_trashed'    => 'Com excluídos',
            'without_trashed' => 'Sem excluídos',
        ],
    ],
    'actions' => [
        'filter'            => ['label' => 'Filtrar'],
        'open_bulk_actions' => ['label' => 'Ações em massa'],
        'toggle_columns'    => ['label' => 'Colunas'],
    ],
    'empty' => [
        'heading'     => 'Nenhum registro encontrado',
        'description' => 'Clique em "Novo" para começar.',
    ],
    'pagination' => [
        'label'    => 'Paginação',
        'overview' => 'Mostrando :first a :last de :total resultados',
        'fields'   => [
            'records_per_page' => ['label' => 'por página'],
        ],
        'next'     => ['label' => 'Próxima'],
        'previous' => ['label' => 'Anterior'],
    ],
    'selection_indicator' => [
        'selected_count' => '1 selecionado|:count selecionados',
        'actions' => [
            'select_all'   => ['label' => 'Selecionar todos :count'],
            'deselect_all' => ['label' => 'Desmarcar todos'],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column'    => ['label' => 'Ordenar por'],
            'direction' => [
                'label'   => 'Direção',
                'options' => ['asc' => 'Crescente', 'desc' => 'Decrescente'],
            ],
        ],
    ],
    'summary' => [
        'heading'     => 'Resumo',
        'subheadings' => [
            'all'  => 'Todos :label',
            'page' => ':label nesta página',
        ],
        'summarizers' => [
            'average' => ['label' => 'Média'],
            'count'   => ['label' => 'Contagem'],
            'sum'     => ['label' => 'Soma'],
        ],
    ],
];
