<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Menus.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Plugin_Menus {

  public function canCreateVideos() {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
      return false;
    }

    return true;
  }
	public function enableLocation(){
		if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){
			return false;	
		}
		return true;	
	}
  public function canCreateChanel() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $setting = Engine_Api::_()->getApi('settings', 'core');
    if (!$setting->getSetting('video_enable_chanel', 1)) {
      return false;
    }
    if (!Engine_Api::_()->authorization()->isAllowed('sesvideo_chanel', $viewer, 'create')) {
      return false;
    }
    return true;
  }

  public function canChanelEnable() {
    $setting = Engine_Api::_()->getApi('settings', 'core');
    if (!$setting->getSetting('video_enable_chanel', 1)) {
      return false;
    }
    return true;
  }

  public function onMenuInitialize_SesvideoMainManage($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_SesvideoMainCreate($row) {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
      return false;
    }

    return true;
  }

}
