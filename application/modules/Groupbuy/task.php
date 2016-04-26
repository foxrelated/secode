<?php
/**
 * @package     Engine_Core
 * @version     $Id: lite.php 8395 2011-02-04 01:08:41Z john $
 * @copyright   Copyright (c) 2008 Webligo Developments
 * @license     http://www.socialengine.net/license/
 */
//wget -O- "http://www.jollytiger.com/?m=lite&name=task&module=groupbuy" > /dev/null

foreach(array('running','subscription','sendMail') as $name) {
	$class_name = sprintf("Groupbuy_Plugin_Task_%s", ucfirst($name));
	if(class_exists($class_name)) {
		$plugin = new $class_name();
		$plugin -> execute();
	}
}
