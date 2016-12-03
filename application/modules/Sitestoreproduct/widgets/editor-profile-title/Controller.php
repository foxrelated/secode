<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_editorProfileTitleController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('user')) {
      return $this->setNoRender();
    }

    //GET USER SUBJECT
    $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
    
    //GET EDITOR ID
    $editor_id = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getColumnValue($user->getIdentity(), 'editor_id', 0);
    $this->view->editor = Engine_Api::_()->getItem('sitestoreproduct_editor', $editor_id);    
    
    //GET SETTINGS
    $this->view->show_designation = $this->_getParam("show_designation", 1);
  }
}