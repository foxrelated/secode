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
class Sesmusic_Widget_AlbumsSongsLikeController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $coreApi = Engine_Api::_()->core();

    $contentType = $this->_getParam('contentType', 'albums');
    if ($contentType == 'albums')
      $subject = $coreApi->getSubject('sesmusic_album');
    elseif ($contentType == 'songs')
      $subject = $coreApi->getSubject('sesmusic_albumsong');

    $this->view->type = $type = $subject->getType();
    $this->view->id = $id = $subject->getIdentity();
    $limit = $this->_getParam('itemCount', 3);
    $this->view->showViewType = $this->_getParam('showViewType', 1);
    $this->view->showUsers = $this->_getParam('showUsers', 'all');

    if ($this->view->showUsers != 'all') {
      $friendIds = Engine_Api::_()->user()->getViewer()->membership()->getMembershipsOfIds();
      if (empty($friendIds))
        return $this->setNoRender();
    }

    $this->view->results = $results = Engine_Api::_()->sesmusic()->albumsSongsLikeResults(array('type' => $type, 'id' => $id, 'limit' => $limit, 'showUsers' => $this->view->showUsers));

    $item = Engine_Api::_()->getItem($type, $id);
    $this->view->like_count = Engine_Api::_()->getDbtable('likes', 'core')->getLikeCount($item);
    if (!empty($this->view->like_count) && $this->view->like_count > $limit)
      $this->view->viewAllLink = 1;

    if (count($results) <= 0)
      return $this->setNoRender();
  }

}