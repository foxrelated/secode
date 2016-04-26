<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Menus.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Plugin_Menus
{  
  public function onMenuInitialize_UserHomeBirthday($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $birthday_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('birthday.link', 1);
		$result = $row->toarray();
		$final_array = array_merge($result, array(
			'icon' => 'application/modules/Birthday/externals/images/wish.png',
			'route' => 'birthday_extended',
			'params' => array(
				'controller' => 'index',
				'action' => 'view'
			)
    ));
    if($birthday_link) {
      return $final_array;
   }
    else {
      return false;
    }
  }
}