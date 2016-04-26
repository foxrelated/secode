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

class Sesvideo_Widget_ChanelInfoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('sesvideo_chanel');
    if (!$subject) {
      return $this->setNoRender();
    }
    $this->view->subject = $subject;
    $this->view->chanelTags = $subject->tags()->getTagMaps();
  }

}
