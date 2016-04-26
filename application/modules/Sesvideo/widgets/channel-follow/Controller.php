<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Widget_ChannelFollowController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
   $user = Engine_Api::_()->user()->getViewer();
	 if($user->getIdentity() == 0)
	 	return $this->setNoRender();
		$coreApi = Engine_Api::_()->core();
		if (!$coreApi->hasSubject('sesvideo_chanel'))
        return $this->setNoRender();
		 $subject_chanel = $coreApi->getSubject('sesvideo_chanel');
		$this->view->chanel_id = $chanelId = $subject_chanel->chanel_id;
		$this->view->subject  = $chanelItem = Engine_Api::_()->getItem('sesvideo_chanel', $chanelId);
		if($chanelItem->follow == 0 || !Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.subscription',1))
			return $this->setNoRender(); 
    $this->view->viewer_id = $userId = $user->getIdentity();
  }
}
