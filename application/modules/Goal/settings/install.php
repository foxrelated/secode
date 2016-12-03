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

class Goal_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_checklicense();
    $this->_addUserProfileContent();  
    $this->_addGoalProfilePage();
    $this->_addGoalBrowsePage();
    $this->_addGoalManagePage();
    
    parent::onInstall();
  }
  
  protected function _addGoalManagePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'goal_index_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'goal_index_manage',
        'displayname' => 'Goal Manage Page',
        'title' => 'My Goals',
        'description' => 'This page lists a user\'s goals.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert active goals
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.goals-active',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
     // Insert tasks due soon
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.tasks-due-soon',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 2,
      ));
     // Insert goals completed
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.goals-completed',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 3,
      ));
      
     // Insert right side category
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.goal-categories',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));

    }
  }
  
  protected function _addGoalProfilePage()
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // goal main page
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'goal_profile_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {
      $db->insert('engine4_core_pages', array(
        'name' => 'goal_profile_index',
        'displayname' => 'Goal Profile',
        'title' => 'Goal Profile',
        'description' => 'This is the profile for an goal.',
        'custom' => 0,
        'provides' => 'subject=goal',
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
      ));
      $left_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.goal-status',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.tasks-completed',
        'parent_content_id' => $middle_id,
        'order' => 2,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.tasks-due',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.comments',
        'parent_content_id' => $middle_id,
        'order' => 4,
        'params' => '',
      ));
      
      
      // left column
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.profile-photo',
        'parent_content_id' => $left_id,
        'order' => 1,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.profile-options',
        'parent_content_id' => $left_id,
        'order' => 2,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'goal.profile-info',
        'parent_content_id' => $left_id,
        'order' => 3,
        'params' => '',
      ));


    }
  }

  
  protected function _addGoalBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'goal_index_browse')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'goal_index_browse',
        'displayname' => 'Goal Browse Page',
        'title' => 'Goal Browse',
        'description' => 'This page lists goals.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
      
      // Insert search
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.browse-search',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));
      
      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'goal.goal-categories',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 2,
      ));
    }
  }
  
  protected function _addUserProfileContent()
  {
    // install content areas
    
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    // group.profile-groups

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'goal.profile-goals')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = $middle_id;
      }

      // tab on profile
      if( $tab_id ) {
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'goal.profile-goals',
          'parent_content_id' => $tab_id,
          'order'   => 9,
          'params'  => '{"title":"Goals","titleCount":true}',
        ));
      }
    }
  }
  
  protected function _checklicense()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $db = $this->getDb();
    $select = $db->select()->from('engine4_core_modules')->where('name = ?', 'goal');

    $sdgoals = $db->fetchRow($select);
    
    if(!$sdgoals){
        $task = 'install';
    } else {
     if($sdgoals->version != '4.8.6'){
        $task = 'upgrade';
     } else {
        $task = 'refresh';
     }
    }
      
    $parameters = array(
                   'task' => $task,
                   'product' => 'goal',
                   'domain' => $_SERVER['HTTP_HOST'],
                   'ip'=> $_SERVER['SERVER_ADDR']
                  );
    
    $curl = curl_init();
    $url = "http://clients.starsdeveloper.com/license/";

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    if(curl_errno($curl)){
      $error_message = $translate->_('Unable to connect Stars Developer server. Please check your Internet connection.');
      return $this->_error($error_message);
    }
    curl_close($curl);

    if(strlen(trim($result)) === 1){
        $error_message = $translate->_('Error! Unregistered module.');
        return $this->_error($error_message);
        
    } else{
        
        foreach(json_decode($result) as $qry){
            $db->query($qry);
        }
        
        return $this->_message(sprintf('%1$d queries succeeded.', count(json_decode($result))));
        
    }
  }
  
}
