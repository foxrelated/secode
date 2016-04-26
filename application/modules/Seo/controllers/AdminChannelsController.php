<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @channel   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

class Seo_AdminChannelsController extends Core_Controller_Action_Admin
{
	
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_channels');

    $this->view->channels = $this->getChannelTable()->getChannels();

    $this->view->sitemap = Engine_Api::_()->seo()->getSitemapInfo();
    
    /*
    $tasks = Engine_Api::_()->getDbtable('tasks', 'core')->getPendingTasks();
    foreach ($tasks as $task)
    {
      echo "<br>$task->plugin";
      if ($task->module == 'seo')
      {
       // $result = Engine_Api::_()->getDbtable('tasks', 'core')->shouldTaskExecute($task, true, true);
       // Zend_Debug::dump($result, 'shouldTaskExecute');
       
        Engine_Api::_()->getDbtable('tasks', 'core')->_executeTask($task);
        
      }
    }
    
    Engine_Api::_()->getDbtable('tasks', 'core')->execute();
    
    $host = $_SERVER['HTTP_HOST'];
    $addr = '127.0.0.1'; // $_SERVER['SERVER_ADDR']
    $port = ( !empty($_SERVER['SERVER_PORT']) ? (integer) $_SERVER['SERVER_PORT'] : 80 );
    $path = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'utility', 'action' => 'tasks'), 'default', true)
      . '?notrigger=1'
      . '&key=' . Engine_Api::_()->getDbtable('tasks', 'core')->getKey()
      . '&pid=' . Engine_Api::_()->getDbtable('tasks', 'core')->getPid()
      ;
    $url = 'http://' . $host . $path;
    echo "<br>";
    echo $url;
    */
  }

  public function buildAction()
  {
    // Check post
    if( $this->getRequest()->isPost())
    {
      $this->view->file = Engine_Api::_()->seo()->buildSitemap(); 

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Sitemap file generated.'))
      ));
    }
  }


  public function generateAction()
  {
    $this->view->channel = $channel = $this->getChannelTable()->getChannel($this->_getParam('name'));
  	if (!$channel)
  	{
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('SEO sitemap channel not found.'))
      ));
      return;
  	}
  	
  	$this->view->file = Engine_Api::_()->seo()->buildSitemap($channel); 
  }
  
  
  public function notifyAction()
  {
    $this->view->form = $form = new Seo_Form_Admin_Channel_Notify();

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      foreach ($values['notifyservices'] as $service)
      {
        $services[$service] = array();
        if ($service == 'yahoo')
        {
          $services[$service] = array('appid' => $values['notifyyahooappid']);
        } 
      }
      $this->view->results = $results = Engine_Api::_()->seo()->submitSitemap($values['url'], $services);
      
      foreach ($results as $service => $result)
      {
        $service = ucfirst($service);
        if ($result instanceof Exception)
        {
          $notice = Zend_Registry::get('Zend_Translate')->_("Notify %s: Exception [%s]");
          $notice = sprintf($notice, $service, $result->getMessage());
        }
        else if ($result == false)
        {
          $notice = Zend_Registry::get('Zend_Translate')->_("Notify %s: FAILED");
          $notice = sprintf($notice, $service);
        }
        else
        {
          $notice = Zend_Registry::get('Zend_Translate')->_("Notify %s: SUBMITTED");
          $notice = sprintf($notice, $service);
        }
        
        
        Engine_Api::_()->getApi('settings', 'core')->setSetting('seo.sitemaplastsubmit', time());
        
        //echo "<br/>$service => $notice";
        $form->addNotice($notice);
      }
    }
    
  }
  
  
  public function createAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_channels');    
    
    $this->view->form = $form = new Seo_Form_Admin_Channel_Create();
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $values = $form->getValues();
    
    $table = Engine_Api::_()->getDbtable('channels', 'seo');
    
    $db = $table->getDefaultAdapter();
    $db->beginTransaction();

    try
    {

      $values['custom'] = 1;
      $values['name'] = 'custom_' . (count($table->getChannels()) + 1);
      
      $page = $table->createRow();
      $page->setFromArray($values);
      $page->save();


      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'seo', 'controller' => 'channels'));    
  }
  
  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('seo_admin_main', array(), 'seo_admin_main_channels');    
        
    
  	$this->view->channel = $channel = $this->getChannelTable()->getChannel($this->_getParam('name'));
  	if (!$channel)
  	{
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('SEO sitemap channel not found.'))
      ));
      return;
  	}
  	
    $this->view->form = $form = new Seo_Form_Admin_Channel_Edit(array(
      'item' => $channel
    ));
    
    $form->populate($channel->toArray());
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $channel->setFromArray($values);
        $channel->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'seo', 'controller' => 'channels')); 
    }
  }


  
  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
	    $channels = $this->getChannelTable()->getChannels();
	    foreach ($channels as $channel)
	    {
	      $channel->order = $this->getRequest()->getParam('admin_channel_item_'.$channel->name);
	      $channel->save();
	      //echo "\n".$channel->channel_id.'='.$channel->order;
	    }
	    
	    $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return;
  }

  public function scheduleAction()
  {
    $table = Engine_Api::_()->getDbtable('tasks', 'core');
    $select = $table->select()
                ->where('module = ?', 'seo')
                ->where('plugin = ?', 'Seo_Plugin_Task_Sitemap_Submit');
          
    $task = Engine_Api::_()->getDbtable('tasks', 'core')->fetchRow($select);
    
    if (!$task)
    {
      // SE v4.1 delete this row when do upgrade .. gotta re-insert
      $data = array(
        'title' => 'Sitemap Build Submit',
        'module' => 'seo',
        'plugin' => 'Seo_Plugin_Task_Sitemap_Submit',
        'timeout' => '604800'
      );
      $table->insert($data);
      $task = Engine_Api::_()->getDbtable('tasks', 'core')->fetchRow($select);
      //die('Could not find task plugin Seo_Plugin_Task_Sitemap_Submit');
    }
    
    $this->view->form = $form = new Seo_Form_Admin_Channel_Scheduler();

    $form->populate($task->toArray());
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      
      $db = $table->getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $values = $form->getValues();
        $task->setFromArray($values);
        $task->save();
        
  	    $db->commit();
  	    
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
        $form->addNotice($savedChangesNotice);  	    
  	    
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }


    }    
  }
  
  /**
	 * @return Seo_Model_DbTable_Channels
   */
  protected function getChannelTable()
  {
    return Engine_Api::_()->getDbtable('channels', 'seo');
  }  
  

}