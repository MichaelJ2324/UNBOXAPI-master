<?php
return array(
    'enabled' => false,
    'grant_types' => array(
        'password',
        'refresh_token',
		'client',
		'auth_code'
    ),
	'scopes' => array(
		'profile',
		'client',
		'admin',
		'api',
	)
);