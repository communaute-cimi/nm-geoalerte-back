<?php
return [
    'settings' => [
        'displayErrorDetails' => true,

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        // Database settings
        'database' => [
          'dsn' => 'pgsql:host=localhost;dbname=postgres',
          'usr' => 'postgres',
          'pwd' => 'necmergitur'
        ],
    ],
];
