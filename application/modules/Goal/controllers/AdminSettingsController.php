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

class Goal_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_settings');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Goal_Form_Admin_Global();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      
      $values = $form->getValues();
      $form->addNotice('Your changes have been saved.');
    }
  }
  
  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'goal')->fetchAll();
  }
  
  public function levelAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Goal_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($level_id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('goal', $level_id, array_keys($form->getValues())));

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

   // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    $module = $this->getRequest()->getModuleName();
    $Api = Engine_Api::_()->getApi('core', 'sdcore');
    $check = $Api->checklicense('check' , $module);

    if($check == 0){
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
      ));
    }

    // Process
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      if( isset($values['auth_comment']) ) {
        $values['auth_view'] = (array) @$values['auth_view'];
        $values['auth_comment'] = (array) @$values['auth_comment'];
      }
      
      $permissionsTable->setAllowed('goal', $level_id, $values);
      
      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Goal_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }      

    // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        // add category to the database
        $table = Engine_Api::_()->getDbtable('categories', 'goal');
        // insert the category into the database
        $row = $table->createRow();
        $row->title = $values["label"];
        $row->save();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Category Added')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->goal_id=$id;
    
    $goalTable = Engine_Api::_()->getDbtable('goals', 'goal');
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'goal');
    $category = $categoryTable->find($id)->current();
        
    // Check post
    if( $this->getRequest()->isPost() ) {
        
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      
      $db = $categoryTable->getAdapter();
      $db->beginTransaction();

      try {
        // go through logs and see which groups used this category id and set it to ZERO
        $goalTable->update(array(
          'category_id' => 0,
        ), array(
          'category_id = ?' => $category->getIdentity(),
        ));
        
        $category->delete();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-settings/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Goal_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }

    $categoryTable = Engine_Api::_()->getDbtable('categories', 'goal');
    $category = $categoryTable->find($id)->current();
    $form->setField($category);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
    
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $category->title = $values["label"];
        $category->save();
        
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    
    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
  
  /*
   * Custom code by Tausif 12-31-15
   * 
   */
  // Goal Templates
  public function goaltemplatesAction()
  {
      //Getting navigation
       $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_goaltemplates');
       $this->view->templates = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll();
     
  }
  
  //Add new goal template
  public function addTemplateAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Goal_Form_Admin_Template();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }      

    // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      
      try {
        // add template to the database
        $table = Engine_Api::_()->getDbtable('templates', 'goal');
        // insert the template into the database
        $row = $table->createRow();
        $row->title = $values["label"];
        $row->description = $values["description"];
        $row->save();
        
         // Set photo
      if( !empty($values['photo']) ) {
        $row->setPhoto($form->photo);
      }
        
        
        
        

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Goal Template Added')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
  
   public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new Classified_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = basename($file);
    }

    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->user_id,
      'name' => $fileName,
    );

    // Save
    $filesTable = Engine_Api::_()->getItemTable('storage_file');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    
    $iMain->bridge($iIconNormal, 'thumb.normal');
    
    // Remove temp files
    @unlink($mainPath);
    @unlink($normalPath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();
    return $this;
  }
  
  //Delete goal template
    public function deleteTemplateAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->template_id=$id;
    
 
    
    $templateTable = Engine_Api::_()->getDbtable('templates', 'goal');
    $template =  $templateTable->find($id)->current();
        
    // Check post
    if( $this->getRequest()->isPost() ) {
        
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      
      $db = $templateTable->getAdapter();
      $db->beginTransaction();

      try {
        // go through logs and see which groups used this category id and set it to ZERO
//        $goalTable->update(array(
//          'category_id' => 0,
//        ), array(
//          'category_id = ?' => $category->getIdentity(),
//        ));
        
        $template->delete();
        
          //get all temptasks of this goal template
                $taskTable = Engine_Api::_()->getDbtable('temptasks','goal');
                $task_sel = $taskTable->select()
                        ->where('template_id = ?', $id)
                        ;
                $tasks = $taskTable->fetchAll($task_sel);

                 //delete tasks of this goal
                  if(count($tasks) > 0){
                      foreach ($tasks as $task){
                          $task->delete();
                      }
                  }

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-settings/deletetemplate.tpl');
  }
  
  //Edit goal template
  public function editTemplateAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Goal_Form_Admin_Template();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }

    $templateTable = Engine_Api::_()->getDbtable('templates', 'goal');
    $template =  $templateTable->find($id)->current();
    
    $form->setField($template);
   
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
    
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
       $template->title = $values["label"];
       $template->description = $values["description"];
       $template->save();
       
        if( !empty($values['photo']) ) {
        $template->setPhoto($form->photo);
        }
        
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    
    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
  
  
  // Goal Templates Tasks
  public function tasksAction()
          
  {
       $id = $this->_request->getParam("id");
  
      //Getting navigation
       $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('goal_admin_main', array(), 'goal_admin_main_goaltemplates');
       
       //Get goal templte name 
         $templatesTable = Engine_Api::_()->getDbtable('templates','goal');
         $template_sel = $templatesTable->select()
              ->where('template_id = ?', $id);
         $this->view->templates = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll($template_sel);
         
       
       // get all tasks of this goal template
        $taskTable = Engine_Api::_()->getDbtable('temptasks','goal');
        $task_sel = $taskTable->select()
              ->where('template_id = ?', $id);
       
       $this->view->tasks = Engine_Api::_()->getDbtable('temptasks', 'goal')->fetchAll($task_sel);
       $this->view->templateId=$id;
     
  }
  
  //Add new task for goal template
  public function addTemplateTaskAction()
  {
        
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Goal_Form_Admin_Templatetask();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }      

    // we will add the template task
      $values = $form->getValues();
    
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        
       //get template id from url
      
        // add template task to the database
        $table = Engine_Api::_()->getDbtable('temptasks', 'goal');
        // insert the template task into the database
        $row = $table->createRow();
        $row->title = $values["label"];
        $row->duration = $values["duration"];
        $row->description = $values["description"];
        $row->template_id = $this->_request->getParam("id");   
        $row->save();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('Template Task Added')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
  
  
  //Delete goal template task
    public function deleteTemplateTaskAction()
  {
        
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->temptask_id=$id;
    
 
    
    $temptasksTable = Engine_Api::_()->getDbtable('temptasks', 'goal');
    $tasks =  $temptasksTable->find($id)->current();
        
    // Check post
    if( $this->getRequest()->isPost() ) {
        
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      
      $db = $temptasksTable->getAdapter();
      $db->beginTransaction();

      try {
        // go through logs and see which groups used this category id and set it to ZERO
//        $goalTable->update(array(
//          'category_id' => 0,
//        ), array(
//          'category_id = ?' => $category->getIdentity(),
//        ));
        
        $tasks->delete();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-settings/deletetemplatetask.tpl');
  }
  
   //Edit goal template
  public function editTemplateTaskAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Goal_Form_Admin_Templatetask();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }

    $temptasksTable = Engine_Api::_()->getDbtable('temptasks', 'goal');
    $task =  $temptasksTable->find($id)->current();
    $form->setField($task);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
    
      $module = $this->getRequest()->getModuleName();
      $Api = Engine_Api::_()->getApi('core', 'sdcore');
      $check = $Api->checklicense('check' , $module);
      
      if($check == 0){
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Plugin license validation failed. For help visit <a target="_blank" href="http://starsdeveloper.com/">StarsDeveloper.com</a>'))
        ));
      }
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
       $task->title = $values["label"];
       $task->duration = $values["duration"];
       $task->description = $values["description"];
       $task->save();
        
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    
    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
 
  
  /*
   * Custom code end Tausif
   */
}