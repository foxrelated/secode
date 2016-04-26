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
class Sesvideo_Widget_OwnerPhotoController extends Engine_Content_Widget_Abstract {
  public function indexAction() {
    $this->view->title = $this->_getParam('showTitle', 1);
    if (Engine_Api::_()->core()->hasSubject('video'))
      $item = Engine_Api::_()->core()->getSubject('video');
    elseif (Engine_Api::_()->core()->hasSubject('sesvideo_chanel')){
			$setting = Engine_Api::_()->getApi('settings', 'core');
			if (!$setting->getSetting('video_enable_chanel', 1)) {
				return $this->setNoRender();
			}
      $item = Engine_Api::_()->core()->getSubject('sesvideo_chanel');
		}
    elseif (Engine_Api::_()->core()->hasSubject('sesvideo_playlist'))
      $item = Engine_Api::_()->core()->getSubject('sesvideo_playlist');
    $user = Engine_Api::_()->getItem('user', $item->owner_id);
    $this->view->item = $user;
    if (!$item)
      return $this->setNoRender();
  }
}