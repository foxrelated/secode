<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Plugin_Menus
{
  // SHOWING LINK ON "MEMBER PROFILE PAGE". 
  public function onMenuInitialize_FacebooksepageFriendHome($row)
  { 
  	$show_myfacebook_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('fb.my.facebook.link', 1);
    $enable_fbfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
    $active_fbfeedmodule = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebooksefeed.isActivate', 1);
    $active_fbmodule = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.isActivate', 1);
   
   	if(!empty($show_myfacebook_link) && !empty($enable_fbfeedmodule) && $active_fbmodule && $active_fbfeedmodule)
  	{
       $view = Zend_Registry::get('Zend_View');
      return array(
        'label' => $row->params['label'],
        'icon' => $view->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/images/facebookse.png',
        'route' => $row->params['route']
      );
  	}
  }
  
  public function onMenuInitialize_FacebookseMainMyfacebooksettings($row)
  {  
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    //CHECK IF FACEBOOKSEFEED MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR ENABLING OR DISABLING FEED SETTINGS FOR USER.
		$enable_facebooksefeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
		$enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
		//FETCHING THE SETTINGS OF ALL MODULE POST EITHER ADMIN HAS ENABLED THE MODULE POST OR NOT.IF NOT THEN WE WILL NOT SHOWO USER THAT MODULE POST OPTION IF SETTING TAB.IF ALL MODULE FEEDS ARE DISABLED THEN WE WILL NOT SHOW THE MY SETTING TAB.
		$item_array = array ();
		if (!empty($enable_facebooksefeedmodule)) {
		  $permissionTable_feed = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
			$select = $permissionTable_feed->select();
			$permissionTable_feed = $permissionTable_feed->fetchAll($select)->toarray();
			$redirect_home = false;
		  foreach ($permissionTable_feed as $item) {
				$item_array[$item['activityfeed_type']] = $item['streampublishenable'];
				if (!empty($item['streampublishenable'])) {
				  $redirect_home = true;
				}
		  }
    }
     
   
  	return $redirect_home;
  }
}