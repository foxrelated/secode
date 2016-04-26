<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewPageType = $this->_getParam('viewPageType', 'album');
    $this->view->viewType = $this->_getParam('viewType', 'vertical');
    $coreMenuApi = Engine_Api::_()->getApi('menus', 'core');

    if ($viewPageType == 'album') {

      if (!Engine_Api::_()->core()->hasSubject('sesmusic_album'))
        return $this->setNoRender();

      $this->view->navigation = $coreMenuApi->getNavigation('sesmusic_profile');
    } elseif ($viewPageType == 'song') {

      if (!Engine_Api::_()->core()->hasSubject('sesmusic_albumsong'))
        return $this->setNoRender();

      $this->view->navigation = $coreMenuApi->getNavigation('sesmusic_song_profile');
    }
  }

}