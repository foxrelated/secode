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
class Sesmusic_Widget_YouMayAlsoLikeAlbumSongsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->contentType = $contentType = $this->_getParam('contentType', 'albums');
    $this->view->viewType = $this->_getParam('viewType', 'gridview');
    $this->view->information = $this->_getParam('information', array('featuredLabel', 'sponsoredLabel', 'newLabel', 'likeCount', 'commentCount', 'viewCount', 'songsCount', 'title', 'postedby'));
    $this->view->height = $this->_getParam('height', 200);
    $this->view->width = $this->_getParam('width', 100);

    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Album Settings
    $this->view->albumlink = unserialize($settings->getSetting('sesmusic.albumlink'));

    $params = array();
    $params['showPhoto'] = $this->_getParam('showPhoto', 1);
    $params['limit'] = $this->_getParam('itemCount', 3);
    $params['popularity'] = 'You May Also Like';

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();

    if ($contentType == 'albums') {

      $params['column'] = array('album_id', 'title', 'description', 'photo_id', 'owner_id', 'view_count', 'like_count', 'comment_count', 'song_count', 'featured', 'hot', 'sponsored', 'rating', 'special');

      $this->view->results = $results = Engine_Api::_()->getDbtable('albums', 'sesmusic')->widgetResults($params);
    } else {

      $params['column'] = array('album_id', "albumsong_id", 'title', 'description', 'photo_id', 'view_count', 'like_count', 'comment_count', 'download_count', 'featured', 'hot', 'sponsored', 'rating', 'track_id', 'song_url');

      $this->view->results = $results = Engine_Api::_()->getDbtable('albumsongs', 'sesmusic')->widgetResults($params);
    }

    if (count($results) <= 0)
      return $this->setNoRender();
  }

}