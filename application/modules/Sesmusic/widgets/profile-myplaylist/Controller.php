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
class Sesmusic_Widget_ProfileMyplaylistController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject())
      return $this->setNoRender();

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();

    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $values['user'] = $viewer_id;
    $values['action'] = 'manage';
    $this->view->paginator = Engine_Api::_()->getDbTable('playlists', 'sesmusic')->getPlaylistPaginator($values);
    $this->view->information = $this->_getParam('information', array('featured', 'viewCount', 'title', 'postedby'));

    if ($viewer_id != $subject->getIdentity() && empty($_POST['playlist_id']) && empty($subject->infomusic_playlist))
      return $this->setNoRender();

    if (!empty($_POST) && isset($_POST['playlist_id'])) {

      Engine_Api::_()->getDbtable('users', 'user')->update(array('infomusic_playlist' => $_POST['playlist_id']), array('user_id =?' => $subject->getIdentity()));

      $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
      $redirector->gotoRoute(array('module' => 'user', 'controller' => 'profile', 'action' => 'index', 'id' => $subject->getIdentity()), 'user_profile', false);
    }
  }

}