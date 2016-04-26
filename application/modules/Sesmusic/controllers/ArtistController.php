<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: ArtistController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_ArtistController extends Core_Controller_Action_Standard {

  //Browse Action
  public function browseAction() {
    $this->_helper->content->setEnabled();
  }

  public function favouriteArtistsAction() {

    //Get viewer/subject
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //Artists settings
    $this->view->artistlink = unserialize($settings->getSetting('sesmusic.artistlink'));

    if (!$settings->getSetting('sesmusic.checkmusic'))
      return $this->_forward('notfound', 'error', 'core');

    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->getFavourites(array('resource_type' => 'sesmusic_artist'));
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    //Render
    $this->_helper->content->setEnabled();
  }

  //Artist View Action
  public function viewAction() {
    $this->_helper->content->setEnabled();
  }

}
