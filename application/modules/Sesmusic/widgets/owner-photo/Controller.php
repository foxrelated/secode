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
class Sesmusic_Widget_OwnerPhotoController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->title = $this->_getParam('showTitle', 1);
    if (Engine_Api::_()->core()->hasSubject('sesmusic_album')) {
      $item = Engine_Api::_()->core()->getSubject('sesmusic_album');
      $user = Engine_Api::_()->getItem('user', $item->owner_id);
    } elseif (Engine_Api::_()->core()->hasSubject('sesmusic_albumsong')) {
      $item = Engine_Api::_()->core()->getSubject('sesmusic_albumsong');
      $musicalbum = Engine_Api::_()->getItem('sesmusic_album', $item->album_id);
      $user = Engine_Api::_()->getItem('user', $musicalbum->owner_id);
    } elseif (Engine_Api::_()->core()->hasSubject('sesmusic_playlist')) {
      $item = Engine_Api::_()->core()->getSubject('sesmusic_playlist');
      $user = Engine_Api::_()->getItem('user', $item->owner_id);
    }

    $this->view->item = $user;
    if (!$item)
      return $this->setNoRender();
  }

}