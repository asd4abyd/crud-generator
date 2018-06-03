<?php
/**
 *
 * User: Abdelqader Osama
 *
 */

return [


    'namespace' => '',

    'repository' => false,

    'perPage' => 10,

    'files' => [
        'controller' => 'controller',
        'model'      => 'model',
        'add_edit'   => 'add_edit_blade',
        'list'       => 'list_blade',
        'route'      => 'route_service_provider',
        'main'       => 'main_blade',

        'javascript' => 'javascript_blade',

        'components.decimal',
        'components.int',
        'components.text',
        'components.textarea',
    ],

	'timestamp' => [
		'created_at',
		'updated_at',
		'deleted_at'
	]


];