<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_GoalController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( 0 !== ($goal_id = (int) $this->_getParam('goal_id')) &&
        null !== ($goal = Engine_Api::_()->getItem('goal', $goal_id)) ) {
      Engine_Api::_()->core()->setSubject($goal);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('goal');
  }

  public function editAction()
  {

    $goal = Engine_Api::_()->core()->getSubject();
    $this->view->form = $form = new Goal_Form_Edit();
    
    //Get templateused check
    // templateused value will be 1 if goal was created using template
    $templateused = $goal->templateused;
     $user = Engine_Api::_()->user()->getViewer();
    
    if($templateused==1){
        $form->removeElement('search');
        //privacy options adjustment
         
     // Privacy
        $availableLabels = array(
           'owner_member' => 'Friends Only',
           'owner' => 'Just Me'
         );

    $form->auth_view->setMultiOptions($availableLabels);
    $form->auth_comment->setMultiOptions($availableLabels);

   
    }
    
     
    
    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'goal')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach( $categories as $k => $v ) {
      $categoryOptions[$k] = $v;
    }
    $form->category_id->setMultiOptions($categoryOptions);

    if( count($form->category_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('category_id');
    }

    // if goal completed/achieved then some fields are disabled for editing
    if($goal->achieved == 1){  
        $form->title->setAttrib('disabled', 'disabled');
        $form->starttime->setAttrib('disabled', 'disabled');
        $form->endtime->setAttrib('disabled', 'disabled');
    }
    
    if( !$this->getRequest()->isPost() ) {
      // Populate auth
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $actions = array('view', 'comment');
      $perms = array();
      foreach( $roles as $roleString ) {
        $role = $roleString;

        foreach( $actions as $action ) {
          if( $auth->isAllowed($goal, $role, $action) ) {
            $perms['auth_' . $action] = $roleString;
          }
        }
      }

      $form->populate($goal->toArray());
      $form->populate($perms);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getItemTable('goal')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      
      // Set group info
      $goal->setFromArray($values);
      $goal->save();

      if( !empty($values['photo']) ) {
        $goal->setPhoto($form->photo);
      }

      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

     $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      foreach( $roles as $i => $role ) { 
        $auth->setAllowed($goal, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($goal, $role, 'comment', ($i <= $commentMax));
      }
      
      // Commit
      $db->commit();
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    if( $this->_getParam('ref') === 'profile' ) {
      $this->_redirectCustom($goal);
    } else {
      $this->_redirectCustom(array('route' => 'goal_general', 'action' => 'manage'));
    }
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $goal = Engine_Api::_()->getItem('goal', $this->getRequest()->getParam('goal_id'));    

    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');
    
    // Make form
    $this->view->form = $form = new Goal_Form_Delete();
    
    if( !$goal )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Goal doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $goal->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $goal->delete();
      
   $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected goal has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'goal_general', true),
      'messages' => Array($this->view->message)
    ));
  }
  

}