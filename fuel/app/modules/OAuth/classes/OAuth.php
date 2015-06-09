<?php

namespace OAuth;


class OAuth extends \UNBOXAPI\Module {

	protected static $_name = 'OAuth';
	protected static $_models = array(
		'AccessTokens',
		'AuthCodes',
		'Clients',
		'RedirectUris',
		'RefreshTokens',
		'Scopes',
		'Sessions',
		'Users'
	);

}