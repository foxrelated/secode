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
class Sesmusic_Widget_MusicHomeErrorController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'create');

    $this->view->paginator = Engine_Api::_()->getDbTable('albums', 'sesmusic')->getAlbums(array());

    if (count($this->view->paginator) > 0)
      return $this->setNoRender();
  }

}