<?php

return array(

	/**
	 * Default setup group
	 */
	'default_setup' => 'default',

	/**
	 * Default setup groups
	 */
	'setups' => array(
		'default' => array(),
	),

	/**
	 * Default settings
	 */
	'defaults' => array(

		/**
		 * Mail useragent string
		 */
		'useragent' => 'UNBOX API',

		/**
		 * Mail driver (mail, smtp, sendmail, noop)
		 */
		'driver' => 'mailgun',

		/**
		 * Whether to send as html, set to null for autodetection.
		 */
		'is_html' => null,

		/**
		 * Email charset
		 */
		'charset' => 'utf-8',

		/**
		 * Wether to encode subject and recipient names.
		 * Requires the mbstring extension: http://www.php.net/manual/en/ref.mbstring.php
		 */
		'encode_headers' => true,

		/**
		 * Ecoding (8bit, base64 or quoted-printable)
		 */
		'encoding' => 'base64',

		/**
		 * Email priority
		 */
		'priority' => \Email::P_NORMAL,

		/**
		 * Default sender details
		 */
		'from' => array(
			'email'     => 'unboxapi@gmail.com',
			'name'      => 'UNBOX API',
		),

		/**
		 * Whether to validate email addresses
		 */
		'validate' => true,

		/**
		 * Auto attach inline files
		 */
		'auto_attach' => true,

		/**
		 * Auto generate alt body from html body
		 */
		'generate_alt' => true,

		/**
		 * Forces content type multipart/related to be set as multipart/mixed.
		 */
		'force_mixed' => false,

		/**
		 * Wordwrap size, set to null, 0 or false to disable wordwrapping
		 */
		'wordwrap' => null,

		/**
		 * Path to sendmail
		 */
		'sendmail_path' => '',

		/**
		 * SMTP settings
		 */
		'smtp' => array(
			'host'      => 'smtp.gmail.com',
			'port'      => 587,
			'username'  => 'unboxapi@gmail.com',
			'password'  => '',
			'timeout'   => 5,
			'starttls'  => true,
		),

		/**
		 * Newline
		 */
		'newline' => "\n",

		/**
		 * Attachment paths
		 */
		'attach_paths' => array(
			// absolute path
			'',
			// relative to docroot.
			DOCROOT,
		),

		/**
		 * Default return path
		 */
		'return_path' => false,

		/**
		 * Remove html comments
		 */
		'remove_html_comments' => true,

		/**
		 * Mandrill settings, see http://mandrill.com/
		 */
		'mandrill' => array(
			'key' => 'api_key',
			'message_options' => array(),
			'send_options' => array(
				'async'   => false,
				'ip_pool' => null,
				'send_at' => null,
			),
		),

		/**
		 * Mailgun settings, see http://www.mailgun.com/
		 */
		'mailgun' => array(
			'key' => 'key-25e1745bd9f9fa8a63c322b76321af27',
			'domain' => 'https://api.mailgun.net/v3/mailer.unboxapi.com'
		),

		/**
		 * When relative protocol uri's ("//uri") are used in the email body,
		 * you can specify here what you want them to be replaced with. Options
		 * are "http://", "https://" or \Input::protocol() if you want to use
		 * whatever was used to request the controller.
		 */
		'relative_protocol_replacement' => false,
	),
);