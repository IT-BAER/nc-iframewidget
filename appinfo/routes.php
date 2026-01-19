<?php
return [
    'routes' => [
        [
            'name' => 'config#getConfig',
            'url' => '/config',
            'verb' => 'GET'
        ],
        [
            'name' => 'config#setAdminConfig',
            'url' => '/config',
            'verb' => 'POST'
        ],
        [
            'name' => 'config#proxyIcon',
            'url' => '/proxy-icon/{icon}',
            'verb' => 'GET'
        ],
        [
            'name' => 'config#getGroups',
            'url' => '/groups',
            'verb' => 'GET'
        ],
        // Public widget routes (multiple widgets support)
        [
            'name' => 'config#getPublicWidgets',
            'url' => '/public-widgets',
            'verb' => 'GET'
        ],
        [
            'name' => 'config#setPublicWidget',
            'url' => '/public-widgets',
            'verb' => 'POST'
        ],
        [
            'name' => 'config#deletePublicWidget',
            'url' => '/public-widgets/{widgetId}',
            'verb' => 'DELETE'
        ],
        // Group widget routes
        [
            'name' => 'config#getGroupWidgets',
            'url' => '/group-widgets',
            'verb' => 'GET'
        ],
        [
            'name' => 'config#setGroupWidget',
            'url' => '/group-widgets',
            'verb' => 'POST'
        ],
        [
            'name' => 'config#deleteGroupWidget',
            'url' => '/group-widgets/{widgetId}',
            'verb' => 'DELETE'
        ],
        // Personal settings routes
        [
            'name' => 'personal_settings#getSettings',
            'url' => '/personal-settings',
            'verb' => 'GET'
        ],
        [
            'name' => 'personal_settings#setSettings',
            'url' => '/personal-settings',
            'verb' => 'POST'
        ]
    ]
];
