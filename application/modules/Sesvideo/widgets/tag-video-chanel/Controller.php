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

class Sesvideo_Widget_tagVideoChanelController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->type = isset($_GET['type']) ? $_GET['type'] : 'video';
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($this->view->type != 'video' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    $this->view->tagCloudData = Engine_Api::_()->sesvideo()->tagCloudItemCore('fetchAll', array('type' => $this->view->type));
    // Do not render if nothing to show
    if (count($this->view->tagCloudData) <= 0)
      return $this->setNoRender();
  }

}
