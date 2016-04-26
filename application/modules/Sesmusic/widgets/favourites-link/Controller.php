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
class Sesmusic_Widget_FavouritesLinkController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $HTTP_REFERER = $_SERVER['REQUEST_URI'];
    if (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/album/')) {
      $refere_array = explode('/album/', $HTTP_REFERER);
      $action = $refere_array['1'];
    } elseif (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/song/')) {
      $refere_array = explode('/song/', $HTTP_REFERER);
      $action = $refere_array['1'];
    } elseif (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/playlist/')) {
      $refere_array = explode('/music/', $HTTP_REFERER);
      $action = $refere_array['1'];
    } elseif (!empty($HTTP_REFERER) && strstr($HTTP_REFERER, '/artists/')) {
      $refere_array = explode('/artists/', $HTTP_REFERER);
      $action = $refere_array['1'];
    }
    $this->view->action = $action;
    $this->view->information = $this->_getParam('information', array('favAlbums', 'ratedAlbums', 'likedAlbums', 'favSongs', 'ratedSongs', 'likedSongs', 'favArtists', 'playlists'));
    if (!$this->view->information)
      return $this->setNoRender();
    
    //Get viewer info
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (empty($viewer_id))
      return $this->setNoRender();
  }

}