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

class Sesvideo_Widget_ChanelOverviewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if (!$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $subject = $this->view->subject = Engine_Api::_()->core()->getSubject();
    $this->view->editOverview = $editOverview = Engine_Api::_()->authorization()->isAllowed('sesvideo_chanel', $viewer, 'edit');
    if (!$editOverview && (!$subject->overview || is_null($subject->overview))) {
      return $this->setNoRender();
    }
  }

}
