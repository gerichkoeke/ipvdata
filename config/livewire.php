<?php
return [
    'class_namespace'   => 'App\\Livewire',
    'view_path'         => resource_path('views/livewire'),
    'layout'            => 'components.layouts.app',
    'asset_url'         => null,
    'app_url'           => null, // null = usa APP_URL dinâmico
    'middleware_group'  => 'web',
    'persist_middleware_across_redirects' => false,
    'temporary_file_upload' => [
        'disk'            => 'public',
        'rules'           => ['required', 'file', 'max:12288'],
        'directory'       => 'livewire-tmp',
        'middleware'      => 'throttle:60,1',
        'preview_mimes'   => [
            'png','gif','bmp','svg','wav','mp4','mov','avi',
            'wmv','mp3','m4a','jpg','jpeg','mpga','webp','wma',
        ],
        'max_upload_time' => 5,
    ],
    'render_on_redirect'                  => false,
    'use_morph_map_for_model_binding'     => false,
    'inject_assets'                       => true,
    'navigate'          => [
        'show_progress_bar'  => true,
        'progress_bar_color' => '#2563eb',
    ],
    'inject_morph_markers' => true,
    'pagination_theme'     => 'tailwind',
];
