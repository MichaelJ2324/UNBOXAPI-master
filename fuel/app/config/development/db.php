<?php
return array(
	'active' => 'default',
	'default' => 
	array(
		'type' => 'mysqli',
		'connection' => 
		array(
			'persistent' => false,
			'hostname' => 'localhost',
			'port' => '3306',
			'database' => 'UNBOXAPIV2',
			'username' => 'root',
			'password' => 'password',
		),
		'identifier' => '`',
		'table_prefix' => '',
		'charset' => 'utf8',
		'enable_cache' => false,
		'profiling' => false,
		'readonly' => false,
	),
	'dbUtil' => 
	array(
		'type' => 'mysqli',
		'connection' => 
		array(
			'persistent' => false,
			'hostname' => 'localhost',
			'port' => '3306',
			'username' => 'root',
			'password' => 'password',
		),
		'identifier' => '`',
		'table_prefix' => '',
		'charset' => 'utf8',
		'enable_cache' => false,
		'profiling' => false,
		'readonly' => false,
	),
);
