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
class Sesmusic_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->params = $this->_getAllParams();    
    $this->getElement()->removeDecorator('Title');
    
    //Get navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_main', array());

    if (count($this->view->navigation) == 1)
      $this->view->navigation = null;

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.checkmusic'))
      return $this->setNoRender();
  }

}