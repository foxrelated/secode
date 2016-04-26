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

class Sesvideo_Widget_BrowseChanelQuickController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ( !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }
    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_chanel_quick');
    if (count($quickNavigation) == 0)
      return $this->setNoRender();
  }

}
