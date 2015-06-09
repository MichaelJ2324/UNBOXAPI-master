<?php

namespace Manager;

class Manager extends \UNBOXAPI\Layout{
    protected static $_name = "Manager";
    protected static $_label = "Manager";
    protected static $_label_plural = "Manager";
    protected static $_link = "#manage";
    protected static $_icon = "<i class='fa fa-database'></i>";
	protected static $_links = array(
		'packages' => array(
			'name' => 'Packages',
			'link' => "#manager/packages",
			'icon' => "",
			'type' => "",
			'layout' => "",
			'enabled' => true
		)
	);


} 