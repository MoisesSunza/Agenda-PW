<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'API Agenda Electrónica - Moises',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),
                'annotations' => [
                    base_path('app'),
                ],
                'docs' => storage_path('api-docs'),
                'base' => env('L5_SWAGGER_BASE_PATH', null),
                'excludes' => [],
            ],
            'proxy' => false, // Soluciona el error de la imagen 40bd81.png
            'ui' => [
                'display' => [
                    'doc_expansion' => 'none',
                    'filter' => true,
                    'operations_sort' => 'alpha', // Soluciona el error de la imagen 405fa8.png
                ],
                'authorization' => [
                    'persist_authorization' => true,
                ],
            ],
            'constants' => [],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
        ],
    ],
];