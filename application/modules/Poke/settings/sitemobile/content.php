<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: content.php 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
	array (
	'title' => $view->translate('Poke'),
	'description' => $view->translate("Shows the users who have poked recently and add the friend's to whom you want to poke."),
	'category' => 'Pokes',
	'type' => 'widget',
	'name' => 'poke.pokeusers',
	'defaultParams' => array(
		'title' => ''),
	)    
)  
?>