<?php
return array(
	'caching' => true,
	'cache_name' => 'metadata',
	'available_properties' => array(
		'Module' => array(
			'config' => array(
				'export' => true
			),
			'labels' => array(
				'export' => true
			),
			'fields' => array(
				'export' => true
			),
			'relationships' => array(
				'export' => true
			),
			'views' => array(
				'export' => false
			),
			'seeds' => array(
				'export' => false
			),
			'canisters' => array(
				'export' => false
			)
		),
		'Layout' => array(
			'config' => array(
				'export' => true
			),
			'labels' => array(
				'export' => true
			),
			'links' => array(
				'export' => true
			),
			'templates' => array(
				'export' => true
			)
		),
		'System' => array(
			'config' => array(
				'export' => true
			),
			'labels' => array(
				'export' => true
			),
			'modules' => array(
				'export' => true
			),
			'layouts' => array(
				'export' => true
			),
			'templates' => array(
				'export' => true
			)
		)
	),
	'excluded_modules' => array(
		'oauth',
		'versions',
		'users'
	),
	'box_types' => array(
		'module',
		'layout'
	)
);