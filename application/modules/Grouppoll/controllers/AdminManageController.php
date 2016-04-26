<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	//CREATE NAVIGATION TABS
   	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('grouppoll_admin_main', array(), 'grouppoll_admin_main_manage');
      
    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Grouppoll_Form_Admin_Manage_Filter();

    $currentPageNumber = $this->_getParam('page',1);

    $values = array();
    if ( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    $paginator = Engine_Api::_()->getItemTable('grouppoll_poll')->getGrouppollResult($values);
		$paginator_value = $paginator['value'];
		$paginator = $paginator['paginator'];
		$this->view->assign($paginator_value);
    $this->view->paginator = $paginator;
    $paginator->setItemCountPerPage(10)->setCurrentPageNumber($currentPageNumber);

  }

  //ACTION FOR MULTI DELETE POLLS
  public function multiDeleteAction()
  {	
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'delete_' . $value) {		
        	//DELETE POLLS FROM DATABASE AND SCRIBD
          $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', (int)$value);

					//FINALLY DELETE POLL MODEL
					if (!empty($grouppoll)) {
						$grouppoll->delete();
					}
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}
?>