<?php
class Ynfundraising_Installer extends Engine_Package_Installer_Module {
	function onInstall() {
		//
		// install content areas
		//
		$db = $this->getDb ();
		$select = new Zend_Db_Select ( $db );

		// profile page
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'user_profile_index' )->limit ( 1 );
		$page_id = $select->query ()->fetchObject ()->page_id;

		// Check if it's already been placed
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'widget' )->where ( 'name = ?', 'ynfundraising.profile-campaigns' );

		$info = $select->query ()->fetch ();

		// ynfundraising.profile-campaigns

		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'container' )->limit ( 1 );
		$container_id = $select->query ()->fetchObject ()->content_id;

		// middle_id (will always be there)
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_content' )->where ( 'parent_content_id = ?', $container_id )->where ( 'type = ?', 'container' )->where ( 'name = ?', 'middle' )->limit ( 1 );
		$middle_id = $select->query ()->fetchObject ()->content_id;

		// tab_id (tab container) may not always be there
		$select->reset ( 'where' )->where ( 'type = ?', 'widget' )->where ( 'name = ?', 'core.container-tabs' )->where ( 'page_id = ?', $page_id )->limit ( 1 );
		$tab_id = $select->query ()->fetchObject ();
		if ($tab_id && @$tab_id->content_id) {
			$tab_id = $tab_id->content_id;
		} else {
			$tab_id = null;
		}

		// tab on profile
		if (empty ( $info )) {
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.profile-campaigns',
					'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
					'order' => 4,
					'params' => '{"title":"Campaigns","titleCount":true}'
			) );
		}

		// Browse campaigns
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_index_browse' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_index_browse',
					'displayname' => 'Fundraising - Browse Campaigns',
					'title' => 'Fundraising - Browse Campaigns',
					'description' => 'This is Fundraising Home Page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 3,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu-quick',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":""}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-topdonors',
					'parent_content_id' => $right_id,
					'order' => 3,
					'params' => '{"title":"Top Donors"}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-tagscloud',
					'parent_content_id' => $right_id,
					'order' => 4,
					'params' => '{"title":"Tags"}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-statistics',
					'parent_content_id' => $right_id,
					'order' => 5,
					'params' => '{"title":"Statistics"}'
			) );
		}
		else{
			$page_id = $info['page_id'];
		}
		
		
		// middle column
		$db->insert ( 'engine4_core_content', array (
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynfundraising.campaigns-featured',
				'parent_content_id' => $middle_id,
				'order' => 1,
				'params' => '{"title":"Featured Campaigns"}'
		) );
		$db->insert ( 'engine4_core_content', array (
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynfundraising.campaigns-recent',
				'parent_content_id' => $middle_id,
				'order' => 2,
				'params' => '{"title":"Recent Campaigns"}'
		) );
		$db->insert ( 'engine4_core_content', array (
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynfundraising.campaigns-ideabox',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '{"title":"Idea Box\'s Campaigns"}'
		) );

		// View listing campaigns
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_index_list' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_index_list',
					'displayname' => 'Fundraising - Listing Page',
					'title' => 'Fundraising - Listing Page',
					'description' => 'This is Campaigns Listing page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-tagscloud',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":"Tags"}'
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// View past campaigns
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_index_past-campaigns' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_index_past-campaigns',
					'displayname' => 'Fundraising - Past Campaigns',
					'title' => 'Fundraising - Past Campaigns',
					'description' => 'This is past Campaigns page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-tagscloud',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":"Tags"}'
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// My Camaigns
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_campaign_index' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_campaign_index',
					'displayname' => 'Fundraising - My Campaigns',
					'title' => 'Fundraising - My Campaigns',
					'description' => 'This is My Campaigns page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// My requests
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_request_index' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_request_index',
					'displayname' => 'Fundraising - My Requests',
					'title' => 'Fundraising - My Requests',
					'description' => 'This is My Requests page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.requests-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// Manage requests
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_index_manage-requests' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_index_manage-requests',
					'displayname' => 'Fundraising - Manage Requests',
					'title' => 'Fundraising - Manage Requests',
					'description' => 'This is Manage Requests page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.requests-search',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// View statistics camapaign
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_campaign_view-statistics-list' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_campaign_view-statistics-list',
					'displayname' => 'Fundraising - Statistics Listing Page',
					'title' => 'Fundraising - Statistics Listing Page',
					'description' => 'This is Statistics Listing page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// right column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.menu-statistics-chartlist',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.statistics-search',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => ''
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );
		}

		// Detail Campaign
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynfundraising_index_view' )->limit ( 1 );
		;
		$info = $select->query ()->fetch ();

		if (empty ( $info )) {
			$db->insert ( 'engine4_core_pages', array (
					'name' => 'ynfundraising_index_view',
					'displayname' => 'Fundraising - Detail Campaign',
					'title' => 'Fundraising - Detail Campaign',
					'description' => 'This is Detail Campaign page.'
			) );
			$page_id = $db->lastInsertId ( 'engine4_core_pages' );

			// containers
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => ''
			) );
			$top_id = $db->lastInsertId ( 'engine4_core_content' );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.browse-menu',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => ''
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => ''
			) );
			$container_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'left',
					'parent_content_id' => $container_id,
					'order' => 4,
					'params' => ''
			) );
			$left_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 5,
					'params' => ''
			) );
			$right_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 6,
					'params' => ''
			) );
			$middle_id = $db->lastInsertId ( 'engine4_core_content' );

			// left column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-photo',
					'parent_content_id' => $left_id,
					'order' => 1,
					'params' => ''
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-rating',
					'parent_content_id' => $left_id,
					'order' => 2,
					'params' => ''
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-options',
					'parent_content_id' => $left_id,
					'order' => 3,
					'params' => ''
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-addthis',
					'parent_content_id' => $left_id,
					'order' => 4,
					'params' => ''
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-owner',
					'parent_content_id' => $left_id,
					'order' => 5,
					'params' => '{"title":"Campaign Owner"}'
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-donors',
					'parent_content_id' => $left_id,
					'order' => 6,
					'params' => '{"title":"Thank You, Donors"}'
			) );

			// right column

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-fundraising-goal',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => ''
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-supporters',
					'parent_content_id' => $right_id,
					'order' => 2,
					'params' => '{"title":"Supporters"}'
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-parent-detail',
					'parent_content_id' => $right_id,
					'order' => 3,
					'params' => ''
			) );

			// middle column
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-info',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '{"title":""}'
			) );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.container-tabs',
					'parent_content_id' => $middle_id,
					'order' => 2,
					'params' => '{"max":"6","title":"","name":"core.container-tabs"}'
			) );
			$tab0_id = $db->lastInsertId ( 'engine4_core_content' );

			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-description',
					'parent_content_id' => $tab0_id,
					'order' => 1,
					'params' => '{"title":"Description"}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'activity.feed',
					'parent_content_id' => $tab0_id,
					'order' => 2,
					'params' => '{"title":"Updates"}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-news',
					'parent_content_id' => $tab0_id,
					'order' => 3,
					'params' => '{"title":"News"}'
			) );
			$db->insert ( 'engine4_core_content', array (
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynfundraising.campaigns-profile-aboutme',
					'parent_content_id' => $tab0_id,
					'order' => 4,
					'params' => '{"title":"About Me"}'
			) );
		}

		// add widget to detail idea page
		$select = new Zend_Db_Select ( $db );
		$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynidea_index_detail' )->limit ( 1 );
		$item = $select->query ()->fetch ();

		if (!empty ( $item )) {
			$page_id = $select->query ()->fetchObject ()->page_id;

			// Check if it's already been placed
			$select = new Zend_Db_Select ( $db );
			$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'widget' )->where ( 'name = ?', 'ynfundraising.campaigns-for-parent' );

			$info = $select->query ()->fetch ();

			$select = new Zend_Db_Select ( $db );
			$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'container' )->where ( 'name = ?', 'left' )->limit ( 1 );
			$left_id = $select->query ()->fetchObject ()->content_id;

			// tab on profile
			if (empty ( $info ) && $page_id) {
				$db->insert ( 'engine4_core_content', array (
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynfundraising.campaigns-for-parent',
						'parent_content_id' => $left_id,
						'order' => 9,
						'params' => '{"title":""}'
				) );
			}

			// add widget to detail trophy page
			$select = new Zend_Db_Select ( $db );
			$select->from ( 'engine4_core_pages' )->where ( 'name = ?', 'ynidea_trophies_detail' )->limit ( 1 );
			$page_id = $select->query ()->fetchObject ()->page_id;

			// Check if it's already been placed
			$select = new Zend_Db_Select ( $db );
			$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'widget' )->where ( 'name = ?', 'ynfundraising.campaigns-for-parent' );

			$info = $select->query ()->fetch ();

			$select = new Zend_Db_Select ( $db );
			$select->from ( 'engine4_core_content' )->where ( 'page_id = ?', $page_id )->where ( 'type = ?', 'container' )->where ( 'name = ?', 'left' )->limit ( 1 );
			$left_id = $select->query ()->fetchObject ()->content_id;
			if (empty ( $info ) && $page_id) {
				$db->insert ( 'engine4_core_content', array (
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'ynfundraising.campaigns-for-parent',
						'parent_content_id' => $left_id,
						'order' => 9,
						'params' => '{"title":""}'
				) );
			}
		}

		parent::onInstall ();
	}
}
?>