<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Membership Card'),
        'description' => $view->translate('This widget has the Membership Card of the users. It should be placed in Tabbed Blocks area of Member Profile page.'),
        'category' => $view->translate('Membership Card'),
        'type' => 'widget',
        'name' => 'mcard.print-card',
        'defaultParams' => array(
       	'title' => $view->translate('Membership Card')
    ))
)
?>
