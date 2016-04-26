<?php

/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Birthday
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: install.php 6590 2010-17-11 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class Birthday_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  {
    $db = $this->getDb() ;
    //CHECK SOCIALENGINEADDONS PLUGIN IS INSTALL OR NOT.
    $pluginName = 'Birthdays Plugin - Listing, Wishes, Reminder Emails and Widgets';

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'seaocore');
    $check_socialengineaddons = $select->query()->fetchAll();

    $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    if ( strstr($url_string, "manage/install") ) {
      $calling_from = 'install';
    }
    else if ( strstr($url_string, "manage/query") ) {
      $calling_from = 'queary';
    }
    $explode_base_url = explode("/", $baseUrl);
    foreach ( $explode_base_url as $url_key ) {
      if ( $url_key != 'install' ) {
        $core_final_url .= $url_key . '/';
      }
    }


    if( empty($check_socialengineaddons) ) {
      // Page plugin is not install at your site.
return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area
on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and install on your site before installing this plugin.</div></div></div>');
    } else if( !empty($check_socialengineaddons) && empty($check_socialengineaddons[0]['enabled']) ) {
      // Plugin not Enable at your site
      return $this->_error("<span style='color:red'>Note: You have installed the SocialEngineAddOns Core Plugin but not enabled it on your site yet. Please enabled it first before installing the
      $pluginName .</span><br/> <a href='" . 'http://' . $core_final_url . "install/manage/'>Click here</a> to enabled the SocialEngineAddOns Core Plugin.");

    } else if( $check_socialengineaddons[0]['version'] < '4.2.0' ) {
      // Please activate page plugin
      return $this->_error('<div class="global_form"><div><div> You do not have the latest version of the SocialEngineAddOns Core Plugin. Please download the latest
version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and upgrade this on your site.</div></div></div>');
    }



    
    $select = new Zend_Db_Select( $db ) ;

    //user index page
    $select
	->from( 'engine4_core_pages' )
	->where( 'name = ?' , 'user_index_home' )
	->limit( 1 ) ;
    $page_id = $select->query()->fetchObject()->page_id ;
    if ( !empty( $page_id ) ) {
      // container_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
	  ->from( 'engine4_core_content' )
	  ->where( 'page_id = ?' , $page_id )
	  ->where( 'type = ?' , 'container' )
	  ->where( 'name = ?' , 'main' )
	  ->limit( 1 ) ;
      $container_id = $select->query()->fetchObject()->content_id ;
      if ( !empty( $container_id ) ) {
	// right_id (will always be there)
	$select = new Zend_Db_Select( $db ) ;
	$select
	    ->from( 'engine4_core_content' )
	    ->where( 'parent_content_id = ?' , $container_id )
	    ->where( 'type = ?' , 'container' )
	    ->where( 'name = ?' , 'right' )
	    ->limit( 1 ) ;
	$right_id = $select->query()->fetchObject()->content_id ;

	// Check right_id is empty or not
	if ( !empty( $right_id ) ) {

	  // Check if it's already been placed
	  $select = new Zend_Db_Select( $db ) ;
	  $select
	      ->from( 'engine4_core_content' )
	      ->where( 'page_id = ?' , $page_id )
	      ->where( 'type = ?' , 'widget' )
	      ->where( 'name = ?' , 'birthday.show-birthdays' ) ;
	  $info = $select->query()->fetch() ;
	  if ( empty( $info ) ) {
	    // tab on profile
	    $db->insert( 'engine4_core_content' , array (
	      'page_id' => $page_id ,
	      'type' => 'widget' ,
	      'name' => 'birthday.show-birthdays' ,
	      'parent_content_id' => $right_id ,
	      'order' => 23 ,
	      'params' => '{"title":"Birthdays","titleCount":"true"}',
		) ) ;
	  }
	}
      }
    }
    
    $select = new Zend_Db_Select($db);  
    $select
            ->from('engine4_activity_actiontypes')
            ->where('type = ?', 'birthday_post')
            ->where('module = ?', 'birthday')
            ->limit(1);
    $row = $select->query()->fetchObject();
    if (empty($row)) {
      $enable = 0;
      $select = new Zend_Db_Select($db);     
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'advancedactivity')
              ->limit(1);
      $moduleAdvancedactivity = $select->query()->fetchObject();
      if ($moduleAdvancedactivity)
        $enable = 1;
      
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
(\'birthday_post\', \'birthday\', \'{actors:$subject:$object}:\r\n{body:$body}\', ' . $enable . ', 3, 1, 1, 1, 0);');
    }
    
    
    
    $select = new Zend_Db_Select($db);
    $select
        ->from('engine4_core_modules')
        ->where('name = ?', 'core')
        ->limit(1);
    $core_version =  $select->query()->fetchAll();
    if ( !empty($core_version) ) {
			$version = $core_version[0]['version'];
			if( $version < '4.1.0' ) {
				$select = new Zend_Db_Select($db);
				$select
						->from('engine4_core_tasks')
						->where('plugin = ?', 'Birthdayemail_Plugin_Task_ReminderMail')
						->where('title = ?', 'Birthday Reminder')
						->limit(1);
				$task = $select->query()->fetchAll();
				if (empty($task)) {
					$db->insert('engine4_core_tasks', array(
					'title' => "Birthday Reminder",
					'category' => 'system',
					'module' => 'birthdayemail',
					'system' => '1',
					'plugin' => "Birthdayemail_Plugin_Task_ReminderMail",
					'timeout' => "86400",
					'type' => 'automatic',
					'state' => 'dormant',
					'data' => NULL,
					'enabled' => "1",
					'executing' => "0",
					'executing_id' => "0",
					'started_last' => "0",
					'started_count' => "0",
					'completed_last' => "0",
					'completed_count' => "0",
					'failure_last' => "0",
					'failure_count' => "0",
					'success_last' => "0",
					'success_count' => "0",
				));
				}
				// Insert the 
				$select = new Zend_Db_Select($db);
				$select
						->from('engine4_core_tasks')
						->where('plugin = ?', 'Birthdayemail_Plugin_Task_WishMail')
			->where('title = ?', 'Birthday Wish')
						->limit(1);
				$task = $select->query()->fetchAll();
				if (empty($task)) {
					$db->insert('engine4_core_tasks', array(
					'title' => "Birthday Wish",
					'category' => 'system',
					'module' => 'birthdayemail',
					'system' => '1',
					'plugin' => "Birthdayemail_Plugin_Task_WishMail",
					'timeout' => "86400",
					'type' => 'automatic',
					'state' => 'dormant',
					'data' => NULL,
					'enabled' => "1",
					'executing' => "0",
					'executing_id' => "0",
					'started_last' => "0",
					'started_count' => "0",
					'completed_last' => "0",
					'completed_count' => "0",
					'failure_last' => "0",
					'failure_count' => "0",
					'success_last' => "0",
					'success_count' => "0",
				));
				}
			}else {
				$select = new Zend_Db_Select($db);
				$select
						->from('engine4_core_tasks')
						->where('plugin = ?', 'Birthdayemail_Plugin_Task_ReminderMail')
						->where('title = ?', 'Birthday Reminder')
						->limit(1);
				$task = $select->query()->fetchAll();
				if (empty($task)) {
					$db->insert('engine4_core_tasks', array(
					'title' => "Birthday Reminder",
					'module' => 'birthdayemail',
					'plugin' => "Birthdayemail_Plugin_Task_ReminderMail",
					'timeout' => "86400",
					'processes' => "1",
					'semaphore' => "0",
					'started_last' => "0",
					'started_count' => "0",
					'completed_last' => "0",
					'completed_count' => "0",
					'failure_last' => "0",
					'failure_count' => "0",
					'success_last' => "0",
					'success_count' => "0",
				));
				}

				// Insert the 
				$select = new Zend_Db_Select($db);
				$select
						->from('engine4_core_tasks')
						->where('plugin = ?', 'Birthdayemail_Plugin_Task_WishMail')
			->where('title = ?', 'Birthday Wish')
						->limit(1);
				$task = $select->query()->fetchAll();
				if (empty($task)) {
					$db->insert('engine4_core_tasks', array(
					'title' => "Birthday Wish",
					'module' => 'birthdayemail',
					'plugin' => "Birthdayemail_Plugin_Task_WishMail",
					'timeout' => "86400",
					'processes' => "1",
					'semaphore' => "0",
					'started_last' => "0",
					'started_count' => "0",
					'completed_last' => "0",
					'completed_count' => "0",
					'failure_last' => "0",
					'failure_count' => "0",
					'success_last' => "0",
					'success_count' => "0",
				));
				}
			}
		}
    parent::onInstall();
  }
}
