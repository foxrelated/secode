<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_AdminController extends Core_Controller_Action_Admin
{
  protected $_navigation;

 //ACTION FOR APPROVED AND DIS-APPROVED GROUP-POLL
 public function approvedAction() 
 {
    
    // GET THE POLL ID 
		$poll_id = $this->_getParam('poll_id');
   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
    	$grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $poll_id);
     	if($grouppoll->approved == 0){
   		  $grouppoll->approved = 1;	
   		}
   		else {
   			$grouppoll->approved = 0;
   		}
     	$grouppoll->save();
   		$db->commit();
  	}
    catch( Exception $e ) {
    	$db->rollBack();
      throw $e;
    }  
    $this->_redirect('admin/grouppoll/manage');   
 }
   
  //ACTION FOR DELETE THE GROUPPOLL
  public function deleteAction()
  {
		$this->_helper->layout->setLayout('admin-simple');

    // GET THE POLL ID AND PASS TO THE TPL
		$this->view->poll_id = $poll_id = $this->_getParam('poll_id');
		
		if ( $this->getRequest()->isPost()){
			$db = Engine_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			try {
			  $grouppoll = Engine_Api::_()->getItem('grouppoll_poll', $poll_id);
			
			 //FINALLY DELETE POLL MODEL
			  $grouppoll->delete();
			  $db->commit();
			}
			catch( Exception $e ){
			  $db->rollBack();
			  throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh'=> 10,
				'messages' => array('')
			));
   	}
		$this->renderScript('admin/delete.tpl');
	}

	//CREATE TABS 
  public function getNavigation($active = false)
  {
    if ( is_null($this->_navigation) ) {
      $navigation = $this->_navigation = new Zend_Navigation();

      if ( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
        $navigation->addPage(array(
          'label' => 'Manage Group Polls',
          'route' => 'grouppoll_admin',
          'module' => 'grouppoll',
          'controller' => 'admin',
          'action' => 'view'
        ));

        $navigation->addPage(array(
          'label' => 'Global Settings',
          'route' => 'grouppoll_admin',
          'module' => 'grouppoll',
          'controller' => 'admin',
          'action' => 'settings',
          'active' => $active
          
        ));

        $navigation->addPage(array(
          'label' => 'Member Level Settings',
          'route' => 'grouppoll_admin_manage_level',
          'module' => 'grouppoll',
          'controller' => 'admin-level',
        ));
      }
    }
    return $this->_navigation;
  }
}
?>