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

class Sesvideo_Widget_BreadcrumbController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreApi = Engine_Api::_()->core();

    $this->view->viewPageType = $viewPageType = $this->_getParam('viewPageType', 'video');
		$setting = Engine_Api::_()->getApi('settings', 'core');
		if ($this->view->viewPageType == 'chanel' && !$setting->getSetting('video_enable_chanel', 1)) {
      return $this->setNoRender();
    }

    if ($viewPageType == 'video') {

      if (!$coreApi->hasSubject('video'))
        return $this->setNoRender();

      $this->view->video = $coreApi->getSubject('video');
    } elseif ($viewPageType == 'chanel') {

      if (!$coreApi->hasSubject('sesvideo_chanel'))
        return $this->setNoRender();

      $this->view->chanel = $coreApi->getSubject('sesvideo_chanel');
    } elseif ($viewPageType == 'artist') {

      $artist_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('artist_id');
      if (empty($artist_id))
        return $this->setNoRender();

      $this->view->artist = Engine_Api::_()->getItem('sesvideo_artist', $artist_id);
    } elseif ($viewPageType == 'playlist') {

      if (!$coreApi->hasSubject('sesvideo_playlist'))
        return $this->setNoRender();

      $this->view->playlist = $coreApi->getSubject('sesvideo_playlist');
    }
  }

}
