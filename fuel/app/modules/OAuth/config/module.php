<?php
return array(
    'enabled' => false,
    'seed_models' => array(
        'Clients',
        'Scopes'
    ),
    'grant_types' => array(
        'password',
        'refresh_token'
    ),
    'scopes' => array(
        array(
            'scope' => 'email',
            'description' => 'Access to email address on account.',
        ),
        array(
            'scope' => 'profile',
            'description' => 'Access to profile information on account.',
        ),
        array(
            'scope' => 'client',
            'description' => 'Able to access all resources. For VPS servers',
        )
    )
);