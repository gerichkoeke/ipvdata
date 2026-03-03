<?php

return [

    'column_toggle' => [
        'heading' => 'Colunas',
    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Selecionar/desmarcar todos',
        ],
        'bulk_select_record' => [
            'label' => 'Selecionar/desmarcar :label',
        ],
        'bulk_select_group' => [
            'label' => 'Selecionar/desmarcar :label',
        ],
        'search' => [
            'label'       => 'Pesquisar',
            'placeholder' => 'Pesquisar',
            'indicator'   => 'Pesquisar',
        ],
    ],

    'summary' => [
        'heading'     => 'Resumo',
        'subheadings' => [
            'all'  => 'Todos :label',
            'page' => ':label nesta página',
            'currently_visible' => ':label visíveis atualmente',
        ],
        'summarizers' => [
            'average' => ['label' => 'Média'],
            'count'   => ['label' => 'Contagem'],
            'sum'     => ['label' => 'Soma'],
        ],
    ],

    'actions' => [
        'disable_reordering'  => ['label' => 'Concluir reordenação'],
        'enable_reordering'   => ['label' => 'Reordenar registros'],
        'filter'              => ['label' => 'Filtrar'],
        'open_bulk_actions'   => ['label' => 'Ações em massa'],
        'toggle_columns'      => ['label' => 'Alternar colunas'],
    ],

    'empty' => [
        'heading'     => 'Nenhum :model encontrado',
        'description' => '',
    ],

    'filters' => [
        'actions' => [
            'apply'      => ['label' => 'Aplicar filtros'],
            'remove'     => ['label' => 'Remover filtro'],
            'remove_all' => ['label' => 'Limpar todos os filtros'],
            'reset'      => ['label' => 'Redefinir filtros'],
        ],
        'heading'          => 'Filtros',
        'indicator'        => 'Filtros ativos',
        'multi_select'     => ['placeholder' => 'Todos'],
        'select'           => ['placeholder' => 'Todos'],
        'trashed'          => [
            'label'           => 'Registros excluídos',
            'only_trashed'    => 'Apenas excluídos',
            'with_trashed'    => 'Com excluídos',
            'without_trashed' => 'Sem excluídos',
        ],
    ],

    'grouping' => [
        'fields' => [
            'group'     => ['label' => 'Agrupar por', 'placeholder' => 'Agrupar por'],
            'direction' => [
                'label'   => 'Direção do grupo',
                'options' => ['asc' => 'Crescente', 'desc' => 'Decrescente'],
            ],
        ],
    ],

    'reorder_indicator' => 'Arraste os registros para reordenar.',

    'selection_indicator' => [
        'selected_count' => '1 registro selecionado|:count registros selecionados',
        'actions'        => [
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

    'pagination' => [
        'label'    => 'Paginação',
        'overview' => 'Exibindo :first–:last de :total resultados',
        'fields'   => [
            'records_per_page' => ['label' => 'por página'],
        ],
        'next'     => ['label' => 'Próxima'],
        'previous' => ['label' => 'Anterior'],
    ],

];
