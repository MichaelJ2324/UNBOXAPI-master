<?php
return array(
    //OAuth routes
	'oauth' => 'oauth/v1/oauth',
	'api/oauth' => 'oauth/v1/oauth',
	'rest/v(:num)/oauth' => 'oauth/v$1/oauth',
    //Users custom routes
	'api/users/me' => 'users/v1/users/me',
    'api/users/me/(:segment)' => 'users/v1/users/me',
    'api/users/login' => 'users/v1/users/login',
    'api/users/logout' => 'users/v1/users/logout',
    'api/users/register' => 'users/v1/users/register',
	'rest/v(:num)/users/me' => 'users/v$1/users/me',
    'rest/v(:num)/users/me/(:segment)' => 'users/v$1/users/me',
    'rest/v(:num)/users/login' => 'users/v$1/users/login',
    'rest/v(:num)/users/logout' => 'users/v$1/users/logout',
    'rest/v(:num)/users/register' => 'users/v$1/users/register',
    //Metadata routes
    'api/metadata' => 'v1/metadata',
    'rest/v(:num)/metadata' => 'v$1/metadata',
    //Standard Rest Routes
    'api/(:segment)/' => '$1/v1/$1/',
    'api/(:segment)/(:any)' => '$1/v1/$1/index/$2',
    'rest/v(:num)/(:segment)' => '$2/v$1/$2',
    'rest/v(:num)/(:segment)/(:any)' => '$2/v$1/$2/$3',
	'_root_'  => 'unbox/index',
);