<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2012 Fuel Development Team
 * @link       http://fuelphp.com
 */

return array(
    'controller_prefix' => 'Controller\\',
	'base_url'  => null,
	'url_suffix'  => '',
	'index_file'  => 'index.php',
	'profiling'  => false,
	'cache_dir'       => APPPATH.'cache/',
	'caching'         => false,
	'cache_lifetime'  => null, // In Seconds
	'ob_callback'  => null,

	'errors'  => array(
		// Which errors should we show, but continue execution? You can add the following:
		// E_NOTICE, E_WARNING, E_DEPRECATED, E_STRICT to mimic PHP's default behaviour
		// (which is to continue on non-fatal errors). We consider this bad practice.
		'continue_on'  => array(
			'E_NOTICE',
			'E_WARNING'
		),
		// How many errors should we show before we stop showing them? (prevents out-of-memory errors)
		'throttle'     => 10,
		// Should notices from Error::notice() be shown?
		'notices'      => true,
	),
	'language'           => 'en', // Default language
	'language_fallback'  => 'en', // Fallback language when file isn't available for default language
	'locale'             => 'en_US', // PHP set_locale() setting, null to not set

	'encoding'  => 'UTF-8',
	'server_gmt_offset'  => 0,
	'default_timezone'   => 'UTC',
	'log_threshold'    => Fuel::L_ALL,
	'log_path'         => APPPATH.'logs/',
	'log_date_format'  => 'Y-m-d H:i:s',
	'security' => array(
		'csrf_autoload'    => false,
		'csrf_token_key'   => 'fuel_csrf_token',
		'csrf_expiration'  => 0,
		'uri_filter'       => array('htmlentities'),
		'input_filter'  => array(),
		'output_filter'  => array('Security::htmlentities'),
		'auto_filter_output'  => true,
		'whitelisted_classes' => array(
			'Fuel\\Core\\Response',
			'Fuel\\Core\\View',
			'Fuel\\Core\\ViewModel',
			'Closure',
            '',
		)
	),
	'cookie' => array(
		// Number of seconds before the cookie expires
		'expiration'  => 86400,
		// Restrict the path that the cookie is available to
		'path'        => '/',
		// Restrict the domain that the cookie is available to
		'domain'      => null,
		// Only transmit cookies over secure connections
		'secure'      => false,
		// Only transmit cookies over HTTP, disabling Javascript access
		'http_only'   => true,
	),
	'validation' => array(
		'global_input_fallback' => true,
	),
	'routing' => array(
		'case_sensitive' => true,
	),
	'module_paths' => array(
		APPPATH.'modules'.DS
	),
	'package_paths' => array(
		//PKGPATH
	),

	/**************************************************************************/
	/* Always Load                                                            */
	/**************************************************************************/
	'always_load'  => array(
		'packages'  => array(
			'orm',
		),
		'modules'  => array(
            'Users',
			'Oauth',
            'Applications',
            'Apis',
            'HttpMethods',
            'EntryPoints',
            'ParameterTypes',
            'Parameters',
            'Logins',
			'Home',
			'Versions',
            'Manager',
            'Tester',
			'Request'
        ),
		'classes'  => array(
		),
		'config'  => array(
            'unbox'
        ),
		'language'  => array(),
	),

);