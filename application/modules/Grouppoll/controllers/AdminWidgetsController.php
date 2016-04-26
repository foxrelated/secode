<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetsController.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_AdminWidgetsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  { 
		//TAB CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('grouppoll_admin_main', array(), 'grouppoll_admin_widget_settings');
		$this->view->form = $form = new Grouppoll_Form_Admin_Widget();


    if ( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      
			//CHECK THAT NO. OF WIDGETS ARE CANT BE NULL OR ZERO
      $is_error = 0;
     
			if ($values['grouppoll_comment_widgets'] == 0) {
				$is_error = 1;
			}
      elseif($values['grouppoll_view_widgets'] == 0) {
				$is_error = 1;
			}
			elseif($values['grouppoll_recent_widgets'] == 0) {
				$is_error = 1;  
			}
			elseif($values['grouppoll_vote_widgets'] == 0) {
				$is_error = 1;  
			}
			elseif($values['grouppoll_like_widgets'] == 0) {
				$is_error = 1; 
			}
                   
			if($is_error == 1) {
				$error = $this->view->translate('Filled value can not be zero !');
				$this->view->status = false;
				$error = Zend_Registry::get('Zend_Translate')->_($error);				
				$form->getDecorator('errors')->setOption('escape', false);
				$form->addError($error);
				return;
			}
			else {
			  foreach ($values as $key => $value) { 
			    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value); 
			  }
			}  
    }   
  }
}
?>