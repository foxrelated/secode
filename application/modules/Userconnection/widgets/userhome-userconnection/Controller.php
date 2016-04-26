<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Userconnection_Widget_UserhomeUserconnectionController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $tempAjaxResponce = $loadFlage = false;
    $showContent = true;    
    $this->view->tempTitle = $tempTitle = $this->_getParam('tempTitle', null);
    if( !empty($tempTitle) ) {
      $tempAjaxResponce = true;
      $this->view->isAjaxEnabled = $isAjaxEnabled = $this->_getParam('getWidAjaxEnabled', 1);
    }
    
    if( empty($tempAjaxResponce) ) {
      $this->view->isAjaxEnabled = $isAjaxEnabled = $this->_getParam('getWidAjaxEnabled', 1);
    }
    
    if( !empty($_GET['loadFlage']) ) {
      $this->view->isAjaxEnabled = $isAjaxEnabled = false;
    }
    
    if( !empty($isAjaxEnabled) ) {
      $showContent = false;
      if (!empty($_GET['loadFlage'])) {
        $loadFlage = 1;
        $showContent = true;
      }
    }
    $this->view->loadFlage = $loadFlage;
    $this->view->showContent = $showContent;
    
    if( empty($_GET['user_id']) ) {
    $this->view->loadFlage = $loadFlage;
    $this->view->showContent = $showContent;
    $isProfilePage = Engine_Api::_()->userconnection()->isMemberProfilePage($this->view->identity);
    if( !empty($isProfilePage) ) {
      $this->view->user_id = $user_id = Engine_Api::_()->core()->getSubject()->getIdentity(); 
    }else {
      $this->view->user_id = $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    }else {
      $this->view->user_id = $user_id = $_GET['user_id'];
    }
    
    if( !empty($showContent) ) {
      $count_first_degree_contacts = 0;
      $count_second_degree_contacts = 0;
      $count_third_degree_contacts = 0;
      //GET TOTAL NUMBER OF EVERY LEVEL 
      $userconnections_userhome_path = Zend_Registry::get( 'userconnection_userhome_path' );
      //fetch record from core.php
      $userconnection_combind_path_contacts_array = Engine_Api::_()->userconnection()->user_connection_path($user_id, 0, 4, "user_home");
      $user_contacts_degree = $userconnection_combind_path_contacts_array[1];
      $bhfu_uwo_podsh = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.yueuc');
      if($bhfu_uwo_podsh != 20/4)	{
        return $this->setNoRender();
      }
      if( empty($userconnections_userhome_path) ) {
        return $this->setNoRender();
      }
      for($distance = 1; $distance < 4; $distance++) 
      {
        $id = array_keys ($user_contacts_degree,$distance);
        switch($distance) 
        {
          case 1:
            $this->view->count_first_degree_contacts = $count_first_degree_contacts = count($id);					
          break;

          case 2:
            $this->view->count_second_degree_contacts = $count_second_degree_contacts = count($id);
          break;

          case 3:
            $this->view->count_third_degree_contacts = $count_third_degree_contacts = count($id);
          break;				
        }
      }
    }
  }
}
