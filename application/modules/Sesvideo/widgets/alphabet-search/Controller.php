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

class Sesvideo_Widget_AlphabetSearchController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->contentType = $this->_getParam('contentType', 'videos');
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($this->view->contentType == 'chanels' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
  }

}
