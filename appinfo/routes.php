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
		]

    ]
];
