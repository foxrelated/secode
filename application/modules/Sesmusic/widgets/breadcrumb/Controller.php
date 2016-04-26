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
class Sesmusic_Widget_BreadcrumbController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreApi = Engine_Api::_()->core();

    $this->view->viewPageType = $viewPageType = $this->_getParam('viewPageType', 'album');

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    if ($viewPageType == 'album') {

      if (!$coreApi->hasSubject('sesmusic_album'))
        return $this->setNoRender();

      $this->view->album = $coreApi->getSubject('sesmusic_album');
    } elseif ($viewPageType == 'song') {

      if (!$coreApi->hasSubject('sesmusic_albumsong'))
        return $this->setNoRender();

      $this->view->albumSong = $albumSong = $coreApi->getSubject('sesmusic_albumsong');

      $this->view->album = Engine_Api::_()->getItem('sesmusic_album', $albumSong->album_id);
    } elseif ($viewPageType == 'artist') {

      $artist_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('artist_id');
      if (empty($artist_id))
        return $this->setNoRender();

      $this->view->artist = Engine_Api::_()->getItem('sesmusic_artist', $artist_id);
    } elseif ($viewPageType == 'playlist') {

      if (!$coreApi->hasSubject('sesmusic_playlist'))
        return $this->setNoRender();

      $this->view->playlist = $coreApi->getSubject('sesmusic_playlist');

      $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
  }

}