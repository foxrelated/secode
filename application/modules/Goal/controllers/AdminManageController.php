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

class Goal_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_manage');

    if ($this->getRequest()->isPost())
    {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'delete_' . $value)
        {
          $group = Engine_Api::_()->getItem('goal', $value);
          $group->delete();
        }
      }
    }
    
    $page = $this->_getParam('page',1);
    $this->view->paginator = Engine_Api::_()->getItemTable('goal')->getGoalPaginator(array(
      'orderby' => 'admin_id',
    ));
    $this->view->paginator->setItemCountPerPage(25);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->goal_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
        
      //get all tasks of this goal
      $taskTable = Engine_Api::_()->getDbtable('tasks','goal');
      $task_sel = $taskTable->select()
              ->where('goal_id = ?', $id);
      
      $tasks = $taskTable->fetchAll($task_sel);  
        
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $goal = Engine_Api::_()->getItem('goal', $id);
        $goal->delete();
      //also delete tasks of this goal
      if(count($tasks) > 0){
        foreach ($tasks as $task){
          $task->delete();
        }
      }
        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
}