<?php

return array(

	/*
	| Which version of the schema should be considered "current"
	|
	|	Default: 0
	|
	*/
	'version' => array(
		'app' => array(
			'default' => 0,
		),
		'module' => array(),
		'package' => array()
	),

	/*
	| Folder name where migrations are stored relative to App, Module and Package Paths?
	|
	|	Default: 'migrations/'
	|
	*/
	'folder' => 'migrations/',

	/*
	| Table name
	|
	|	Default: 'migration'
	|
	*/
	'table' => 'migration',

);

