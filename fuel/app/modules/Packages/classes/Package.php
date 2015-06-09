<?php

namespace Packages;


class Package extends \UNBOXAPI\Module {

	protected static $_name = 'Packages';
	protected static $_label = 'Package';
	protected static $_label_plural = 'Packages';

	protected static $_models = array(
		'Packages',
		'Applications',
		'Apis'
	);
}