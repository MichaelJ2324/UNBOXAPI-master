<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
	'default' => array(
		'type'        => 'mysqli',
		'connection'  => array(
			'hostname'       => 'localhost',
			'port'           => '3306',
			'database'       => 'UNBOXAPI',
			'username'       => 'root',
			'password'       => 'password',
			'persistent' => false,
		),
		'identifier'   => '`',
		'table_prefix' => '',
		'charset'      => 'utf8',
		'enable_cache' => false,
		'profiling'    => false,
		'readonly'     => false,
	)
);
