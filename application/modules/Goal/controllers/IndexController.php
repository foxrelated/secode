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

class Goal_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('goal', null, 'view')->isValid() )
    return;
  }

  public function browseAction()
  {
      
    $viewer = Engine_Api::_()->user()->getViewer();

    // Form
    $this->view->formFilter = $formFilter = new Goal_Form_Filter_Browse();
    $defaultValues = $formFilter->getValues();

    if( !$viewer || !$viewer->getIdentity() ) {
      $formFilter->removeElement('view');
    }

    // Populate options
    $categories = Engine_Api::_()->getDbtable('categories', 'goal')->getCategoriesAssoc();
    $formFilter->category_id->addMultiOptions($categories);

    // Populate form data
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $this->view->formValues = $values = $formFilter->getValues();
    } else {
      $formFilter->populate($defaultValues);
      $this->view->formValues = $values = array();
    }

    // Prepare data
    $this->view->formValues = $values = $formFilter->getValues();

    if( $viewer->getIdentity() && @$values['view'] == 1 ) {
      $values['users'] = array();
      if(count($viewer->membership()->getMembersInfo(true)) > 0){
        foreach( $viewer->membership()->getMembersInfo(true) as $memberinfo ) {
            $values['users'][] = $memberinfo->user_id;
        }
      }else {
          $this->view->usersArray = $values['users'] = array(0);
      }
      
    }

    $values['search'] = 1;

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if( $user_id ) {
      $values['user_id'] = $user_id;
    }
    // Make paginator
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('goal')
            ->getGoalPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Render
    $this->_helper->content
        ->setEnabled()
        ;
  }

  public function createAction()
  {
     
    if( !$this->_helper->requireUser->isValid() )
        return;
    if( !$this->_helper->requireAuth()->setAuthParams('goal', null, 'create')->isValid() )
        return;

    // Create form
    $this->view->form = $form = new Goal_Form_Create();

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
    // Check method/data validitiy
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    // Process
    $values = $form->getValues();
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();

    $db = Engine_Api::_()->getDbtable('goals', 'goal')->getAdapter();
    $db->beginTransaction();
   
    try {
      // Create goal
      $table = Engine_Api::_()->getDbtable('goals', 'goal');
      $goal = $table->createRow();
      $goal->setFromArray($values);
      $goal->save();

      // Set photo
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
     
      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $goal, 'goal_create');
      if( $action ) {
        $activityApi->attachActivity($action, $goal);
      }

      // Commit
      $db->commit();

      // Redirect
      return $this->_helper->redirector->gotoRoute(array('id' => $goal->getIdentity()), 'goal_profile', true);
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
   }
  }
 
  public function manageAction()
  {
    // Render
    $this->_helper->content        
        ->setEnabled();
  }
  
                            // Stars developer custom code
                            
  //create goal using template
  public function createtempAction()
  {
         
    if( !$this->_helper->requireUser->isValid() )
        return;
    if( !$this->_helper->requireAuth()->setAuthParams('goal', null, 'create')->isValid() )
        return;
     // Populate with templates (custom code)
    $templates = Engine_Api::_()->getDbtable('templates', 'goal')->getTemplatesAssoc();
    asort($templates, SORT_LOCALE_STRING);
    $templateOptions = array('0' => '');
    foreach( $templates as $k => $v ) {
      $templateOptions[$k] = $v;
    }
    
    if(count($templateOptions)==1) {
    
       $this->view->notemplates="notemplate";
 
    }
    
    
    // Create form
    $this->view->form = $form = new Goal_Form_Createtemp();

    $form->task->setRegisterInArrayValidator(false);
    
   
    
    $form->template_id->setMultiOptions($templateOptions);

    if( count($form->template_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('template_id');
   
  
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
    
    
    // Check method/data validitiy
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    
    // Process
    $values = $form->getValues();
 
    $template_id =  $values['template_id'];
    
    if(empty($template_id)){
        return $form->addError('Please select valid template.');
    }
    //Get goal templte data 
         $templatesTable = Engine_Api::_()->getDbtable('templates','goal');
         $template_sel = $templatesTable->select()
                 ->where('template_id = ?', $template_id);
         $temlate = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll($template_sel);
         
    //set title and description for the choosed template     
       foreach ($temlate as $temp):  
       $values['title'] = $temp->title;
        $values['photo_id'] = $temp->photo_id;
         if(empty($values['description'])){
            $values['description'] = $temp->description;
       }
      
      
       endforeach;     
      
    
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();

    $db = Engine_Api::_()->getDbtable('goals', 'goal')->getAdapter();
    $db->beginTransaction();
     
    //making search  
     $values['search'] = 0;
     
     
     //goal is being created using template so saving that check to database
     $values['templateused'] = 1;
     
    try {
      // Create goal
      $table = Engine_Api::_()->getDbtable('goals', 'goal');
      $goal = $table->createRow();
      $goal->setFromArray($values);
      $goal->save();
     
        
      // Set photo
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
     
      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $goal, 'goal_create');
      if( $action ) {
        $activityApi->attachActivity($action, $goal);
      }

      // Commit
      $db->commit();
     
      //saving goal tasks 
      $savetasks = Engine_Api::_()->getDbtable('goals', 'goal')->saveGoalTasks($values,$goal);
     
   
      
 

      // Redirect
      return $this->_helper->redirector->gotoRoute(array('id' => $goal->getIdentity()), 'goal_profile', true);
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
   }
  }
  
  
   public function createtAction()
  {
           
    if( !$this->_helper->requireUser->isValid() )
        return;
    if( !$this->_helper->requireAuth()->setAuthParams('goal', null, 'create')->isValid() )
        return;
     // Populate with templates (custom code)
    $templates = Engine_Api::_()->getDbtable('templates', 'goal')->getTemplatesAssoc();
    asort($templates, SORT_LOCALE_STRING);
    $templateOptions = array('0' => '');
    foreach( $templates as $k => $v ) {
      $templateOptions[$k] = $v;
    }
    
    if(count($templateOptions)==1) {
     
       $this->view->notemplates="notemplate";
 
    }
    
    
    // Create form
    $this->view->form = $form = new Goal_Form_Createt();

    $form->task->setRegisterInArrayValidator(false);
    
   
    
    $form->template_id->setMultiOptions($templateOptions);

    if( count($form->template_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('template_id');
   
  
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
    
    
    // Check method/data validitiy
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    
    // Process
    $values = $form->getValues();
 
    $template_id =  $values['template_id'];
    
    if(empty($template_id)){
        return $form->addError('Please select valid template.');
    }
    //Get goal templte data 
         $templatesTable = Engine_Api::_()->getDbtable('templates','goal');
         $template_sel = $templatesTable->select()
                 ->where('template_id = ?', $template_id);
         $temlate = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll($template_sel);
         
    //set title and description for the choosed template     
       foreach ($temlate as $temp):  
       $values['title'] = $temp->title;
        $values['photo_id'] = $temp->file_id;
         if(empty($values['description'])){
            $values['description'] = $temp->description;
       }
      
      
       endforeach;     
      
    
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $values['user_id'] = $viewer->getIdentity();

    $db = Engine_Api::_()->getDbtable('goals', 'goal')->getAdapter();
    $db->beginTransaction();
     
    //making search  
     $values['search'] = 0;
     
     
     //goal is being created using template so saving that check to database
     $values['templateused'] = 1;
     
    try {
      // Create goal
      $table = Engine_Api::_()->getDbtable('goals', 'goal');
      $goal = $table->createRow();
      $goal->setFromArray($values);
      $goal->save();
     
        
      // Set photo
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
     
      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $goal, 'goal_create');
      if( $action ) {
        $activityApi->attachActivity($action, $goal);
      }

      // Commit
      $db->commit();
     
      //saving goal tasks 
      $savetasks = Engine_Api::_()->getDbtable('goals', 'goal')->saveGoalTasks($values,$goal);
     
   
      
 

      // Redirect
      return $this->_helper->redirector->gotoRoute(array('id' => $goal->getIdentity()), 'goal_profile', true);
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
   }
  }
    
  //temptasksAction is used for AJAX call to show tasks after choosing Goal from drop down
  //Also description of the template is injected
   public function temptasksAction()
   {
       //Receiving template id
       $template_id = $this->_getParam('template_id',0);
       
       
      
       //making object of form
       $form = new Goal_Form_Createt();
 
       // get all tasks of this goal template
        $taskTable = Engine_Api::_()->getDbtable('temptasks','goal');
        $task_sel = $taskTable->select()
              ->where('template_id = ?', $template_id);
       
       $tasks = Engine_Api::_()->getDbtable('temptasks', 'goal')->fetchAll($task_sel);
       
       //Wrapper used to get Elements
       echo '<div class="custom-wrapper">';
       
       if(count($tasks) > 0){
          $taskOptions = array();
       
          foreach ($tasks as $task):
          $taskOptions[$task->temptask_id]=$task->title;
          endforeach;

        
         $form->task->setMultiOptions($taskOptions);

      
        // $myobject = new Engine_Form_Element_MultiCheckbox('task');
        echo $form->task;
  
       }

     
      //Get goal templte name 
         $templatesTable = Engine_Api::_()->getDbtable('templates','goal');
         $template_sel = $templatesTable->select()
              ->where('template_id = ?', $template_id);
         $templateData = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll($template_sel);
         
          foreach ($templateData as $mytemp): 
              $mytemp->description;
          endforeach;
          
        echo "<div id='temp_description'>$mytemp->description</div>";
     
       
        //Process for photo_id
       //get template by id
        $templateTable = Engine_Api::_()->getDbtable('templates','goal');
        $temp_sel = $templateTable->select()
              ->where('template_id = ?', $template_id);
        $templates = Engine_Api::_()->getDbtable('templates', 'goal')->fetchAll($temp_sel);
        
        $photo_id = 0;
        foreach ($templates as $template):
           // echo $template->photo_id;
            
            //$photo = Engine_Api::_()->getItem('goal_photo',$template->photo_id);
            if( $template->photo_id ) {
                $photo = Engine_Api::_()->getItem('goal_photo',$template->photo_id);
          
               echo '<div class="temp_photo"><img src="'.$photo->getPhotoUrl().'" class="thumb_profile"></div>';
             
            //echo $this->htmlLink($template->getHref(), $this->itemPhoto($template, 'thumb.icon'));
          }
        endforeach;
        
        echo '</div>';
        
        
   }
 
}