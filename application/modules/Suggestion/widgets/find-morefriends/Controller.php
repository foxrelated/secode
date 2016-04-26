<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_Widget_FindMorefriendsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    // $this->view->getLayout = $getLayout = $this->_getParam('getLayout', 0);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $this->view->viewer_displayname =  $viewer->displayname; 
    $front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$action = $front->getRequest()->getActionName();
		
    if (empty($viewer_id) || ($module != 'user' && $action != 'home')) {
      return $this->setNoRender();
    }

    
  }

}
?>