<?php
class Ynidea_Installer extends Engine_Package_Installer_Module
{
	/**
	 * 
	 * enable campaign plugin
	 */
	public function onEnable() {
		parent::onEnable();
		$db = $this->getDb();
		@$db->query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE  `name`='ynfundraising';");

	}
	/**
	 * 
	 * disable campaign plugin
	 */
	public function onDisable() {
		parent::onDisable();
		$db = $this->getDb();
		@$db->query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE  `name`='ynfundraising';");
	}
	
	  function onInstall()
	  {
	    //
	    // install content areas
	    //
	    $db     = $this->getDb();
	    $select = new Zend_Db_Select($db);
	
	    // profile page
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'user_profile_index')
	      ->limit(1);
	    $page_id = $select->query()->fetchObject()->page_id;
	
	    // Check if it's already been placed
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_content')
	      ->where('page_id = ?', $page_id)
	      ->where('type = ?', 'widget')
	      ->where('name = ?', 'ynidea.profile-ideas')
	      ;
	
	    $info = $select->query()->fetch();
	
	    // ynidea.profile-ideas
	    
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
	        $tab_id = null;
	      }
	
	      // tab on profile
	      if(empty($info))
	      {
	          $db->insert('engine4_core_content', array(
	            'page_id' => $page_id,
	            'type'    => 'widget',
	            'name'    => 'ynidea.profile-ideas',
	            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
	            'order'   => 4,
	            'params'  => '{"title":"Ideas","titleCount":true}',
	          ));
	      }
	      
	     //Browse Ideas
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_index_index')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_index_index',
	        'displayname' => 'Ideas - Home Page',
	        'title' => 'Ideas - Home Page',
	        'description' => 'This is Idea Home Page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.container-advanced',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"max":"10","title":"","name":"ynidea.container-advanced"}',
	      ));
		  $adv_content_id = $db->lastInsertId('engine4_core_content');
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.cycle-trophies',
	        'parent_content_id' => $adv_content_id,
	        'order' => 1,
	        'params' => '{"title":"Latest Trophies"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.cycle-ideas',
	        'parent_content_id' => $adv_content_id,
	        'order' => 2,
	        'params' => '{"title":"Featured Ideas"}',
	      ));
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 2,
	        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
	      ));
	      $tab0_id = $db->lastInsertId('engine4_core_content');
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-latest',
	        'parent_content_id' => $tab0_id,
	        'order' => 1,
	        'params' => '{"title":"Newest"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-updated',
	        'parent_content_id' => $tab0_id,
	        'order' => 2,
	        'params' => '{"title":"Updated"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-topscore',
	        'parent_content_id' => $tab0_id,
	        'order' => 3,
	        'params' => '{"title":"Top Score"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-topaverage',
	        'parent_content_id' => $tab0_id,
	        'order' => 4,
	        'params' => '{"title":"Top Average"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-withawards',
	        'parent_content_id' => $tab0_id,
	        'order' => 5,
	        'params' => '{"title":"With Awards"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-selected',
	        'parent_content_id' => $tab0_id,
	        'order' => 6,
	        'params' => '{"title":"Selected & Realized"}',
	      ));
		  
	      // right column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-searchbox',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '{"title":"Search Box"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-tagscloud',
	        'parent_content_id' => $right_id,
	        'order' => 2,
	        'params' => '{"title":"Tags"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-statistics',
	        'parent_content_id' => $right_id,
	        'order' => 3,
	        'params' => '{"title":"Statistics"}',
	      ));
	    }
	
	     //View all Ideas
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_ideas_view-all')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_ideas_view-all',
	        'displayname' => 'Ideas - View All',
	        'title' => 'Ideas - View All',
	        'description' => 'This is Ideas Listing page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.view-all-ideas',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      // right column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-searchbox',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-tagscloud',
	        'parent_content_id' => $right_id,
	        'order' => 2,
	        'params' => '{"title":"Tags"}',
	      ));
	     
	    }
	
		//My Ideas
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_my-ideas_index')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_my-ideas_index',
	        'displayname' => 'Ideas - My Ideas',
	        'title' => 'Ideas - My Ideas',
	        'description' => 'This is My Ideas page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.my-ideas',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      // right column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-searchbox',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      
	    }
		//Browse Trophies
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_trophies_index')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_trophies_index',
	        'displayname' => 'Ideas - Browse Trophies',
	        'title' => 'Ideas - Browse Trophies',
	        'description' => 'This is Browse Trophies page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.list-trophies',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      // right column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-searchbox',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      
	    }
		//My Trophies
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_my-trophies_index')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_my-trophies_index',
	        'displayname' => 'Ideas - My Trophies',
	        'title' => 'Ideas - My Trophies',
	        'description' => 'This is My Trophies page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'right',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $right_id = $db->lastInsertId('engine4_core_content');
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.my-Trophies',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      // right column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-searchbox',
	        'parent_content_id' => $right_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      
	    }
	
		//Detail Idea
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_index_detail')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_index_detail',
	        'displayname' => 'Ideas - Detail Idea',
	        'title' => 'Ideas - Detail Idea',
	        'description' => 'This is Detail Idea page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'left',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $left_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
		  
		  // left column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-photo',
	        'parent_content_id' => $left_id,
	        'order' => 1,
	        'params' => '',
	      ));
		  
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-options',
	        'parent_content_id' => $left_id,
	        'order' => 2,
	        'params' => '',
	      ));
		  
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-voting-box',
	        'parent_content_id' => $left_id,
	        'order' => 3,
	        'params' => '',
	      ));
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-info',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 2,
	        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
	      ));
	      $tab0_id = $db->lastInsertId('engine4_core_content');
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-description',
	        'parent_content_id' => $tab0_id,
	        'order' => 1,
	        'params' => '{"title":"Description"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-authors',
	        'parent_content_id' => $tab0_id,
	        'order' => 2,
	        'params' => '{"title":"Authors"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.ideas-profile-awards',
	        'parent_content_id' => $tab0_id,
	        'order' => 3,
	        'params' => '{"title":"Awards"}',
	      ));
		  
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
	      ));
	      $tab1_id = $db->lastInsertId('engine4_core_content');
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'activity.feed',
	        'parent_content_id' => $tab1_id,
	        'order' => 1,
	        'params' => '{"title":"Updates"}',
	      ));
	    }
		
		//Detail Trophy
	    $select = new Zend_Db_Select($db);
	    $select
	      ->from('engine4_core_pages')
	      ->where('name = ?', 'ynidea_trophies_detail')
	      ->limit(1);
	      ;
	    $info = $select->query()->fetch();
	
	    if( empty($info) ) 
	    {
	      $db->insert('engine4_core_pages', array(
	        'name' => 'ynidea_trophies_detail',
	        'displayname' => 'Ideas - Detail Trophy',
	        'title' => 'Ideas - Detail Trophy',
	        'description' => 'This is Detail Trophy page.',
	      ));
	      $page_id = $db->lastInsertId('engine4_core_pages');
	
	      // containers
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'top',
	        'parent_content_id' => null,
	        'order' => 1,
	        'params' => '',
	      ));
	      $top_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $top_id,
	        'order' => 6,
	        'params' => '',
	      ));
	       $middle_id = $db->lastInsertId('engine4_core_content');
	       $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.browse-menu',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	      ));
	      $container_id = $db->lastInsertId('engine4_core_content');
	
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'left',
	        'parent_content_id' => $container_id,
	        'order' => 5,
	        'params' => '',
	      ));
	      $left_id = $db->lastInsertId('engine4_core_content');
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	      ));
	      $middle_id = $db->lastInsertId('engine4_core_content');
	
		  
		  // left column
	
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-photo',
	        'parent_content_id' => $left_id,
	        'order' => 1,
	        'params' => '',
	      ));
		  
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-options',
	        'parent_content_id' => $left_id,
	        'order' => 2,
	        'params' => '',
	      ));
		  
	      // middle column
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-info',
	        'parent_content_id' => $middle_id,
	        'order' => 1,
	        'params' => '{"title":""}',
	      ));
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 2,
	        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
	      ));
	      $tab0_id = $db->lastInsertId('engine4_core_content');
		  
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-nominees',
	        'parent_content_id' => $tab0_id,
	        'order' => 1,
	        'params' => '{"title":"Nominees"}',
	      ));
	      $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-judges',
	        'parent_content_id' => $tab0_id,
	        'order' => 2,
	        'params' => '{"title":"Judges"}',
	      ));
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'ynidea.trophy-profile-awards',
	        'parent_content_id' => $tab0_id,
	        'order' => 3,
	        'params' => '{"title":"Awards"}',
	      ));
		  
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '{"max":"6","title":"","name":"core.container-tabs"}',
	      ));
	      $tab1_id = $db->lastInsertId('engine4_core_content');
		  $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'activity.feed',
	        'parent_content_id' => $tab1_id,
	        'order' => 1,
	        'params' => '{"title":"Updates"}',
	      ));
	    }
		
	    parent::onInstall();
		
		$this ->_addMethodColumns();
	  }
	
	protected function _addMethodColumns() {
		$sql = "ALTER TABLE  `engine4_ynidea_ideas` ADD  `category_id` INT( 11 ) NOT NULL DEFAULT '0';";
		$db = $this -> getDb();
		try {
			$info = $db -> describeTable('engine4_ynidea_ideas');
			if ($info && !isset($info['category_id'])) {
				$db -> query($sql);
			}
		} catch (Exception $e) {
		}
	}
}
?>