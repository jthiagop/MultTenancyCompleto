<?php

declare(strict_types=1);

namespace Flasher\Laravel\Resources;

return [
    // Default notification library - usando toastr (já instalado)
    'default' => 'toastr',

    // Themes configuration
    'themes' => [
        'toastr' => [
            'scripts' => [
                '/vendor/flasher/flasher.min.js',
                '/vendor/flasher/flasher-toastr.min.js',
            ],
            'styles' => [
                '/vendor/flasher/flasher.min.css',
                '/vendor/flasher/toastr.min.css',
            ],
            'options' => [
                // Posição do toast
                'positionClass' => 'toast-top-right',
                
                // Animações
                'showMethod' => 'fadeIn',
                'hideMethod' => 'fadeOut',
                'showDuration' => 300,
                'hideDuration' => 1000,
                
                // Tempo de exibição
                'timeOut' => 5000,
                'extendedTimeOut' => 1000,
                
                // Comportamento
                'closeButton' => true,
                'progressBar' => true,
                'preventDuplicates' => false,
                'newestOnTop' => true,
                
                // Aparência
                'opacity' => 1,
            ],
        ],
    ],

    // Whether to translate PHPFlasher messages using Laravel's translation service
    'translate' => true,

    // Automatically inject PHPFlasher assets into HTML response
    'inject_assets' => true,

    // Configuration for the flash bag (converting Laravel flash messages)
    // Map Laravel session keys to PHPFlasher types
    'flash_bag' => [
        'success' => ['success'],
        'error' => ['error'],
        'warning' => ['warning'],
        'info' => ['info'],
    ],

    // Filter criteria for notifications (e.g., limit number, types)
    'filter' => [
        'limit' => 5, // Limit the number of displayed notifications
    ],
];
