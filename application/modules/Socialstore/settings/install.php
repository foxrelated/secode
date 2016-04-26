<?php

class Socialstore_Installer extends Engine_Package_Installer_Module 
{
	function onInstall() 
	{
		//
		// install content areas
		//
		$db = $this -> getDb();
		
		// Update Store baseUrl
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$baseUrl = sprintf('%s://%s', $request -> getScheme(), $request -> getHttpHost());
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_settings') -> where('name = ?', 'store.baseUrl') -> limit(1);
		$item_info = $select -> query() -> fetchObject();
		if(!$item_info)
		{
			$db -> insert('engine4_core_settings', array('name' => 'store.baseUrl', 'value' => $baseUrl));
		}
		
		$select = new Zend_Db_Select($db);

		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// profile like,follow,favourite stores/products

		// Check if followed stores already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.profile-followed-stores');
		$info = $select -> query() -> fetch();

		if (empty($info)) {

			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id) {
				$tab_id = $tab_id -> content_id;
			} else {
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.profile-followed-stores', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 6, 'params' => '{"title":"Followed Stores"}', ));

		}
		// Check if liked stores already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.profile-like-stores');
		$info = $select -> query() -> fetch();

		if (empty($info)) {

			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id) {
				$tab_id = $tab_id -> content_id;
			} else {
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.profile-like-stores', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 6, 'params' => '{"title":"Liked Stores"}', ));

		}

		// Check if favourite products already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.profile-favourite-products');
		$info = $select -> query() -> fetch();

		if (empty($info)) {

			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id) {
				$tab_id = $tab_id -> content_id;
			} else {
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.profile-favourite-products', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 6, 'params' => '{"title":"Favourited Products"}', ));

		}

		// Check if favourite products already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.profile-like-products');
		$info = $select -> query() -> fetch();

		if (empty($info)) {

			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id) {
				$tab_id = $tab_id -> content_id;
			} else {
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.profile-like-products', 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => 6, 'params' => '{"title":"Liked Products"}', ));

		}

		// BROWSE STORE
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_index_index') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_index_index', 'displayname' => 'Store Home Page', 'title' => 'Store Home Page', 'description' => 'This is Store Home Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-slideshow', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-recent-stores', 'parent_content_id' => $middle_id, 'order' => 7, 'params' => '{"title":"Recent Stores"}', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));

		}

		// STORE DETAIL PAGE
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_index_detail') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_index_detail', 'displayname' => 'Store Detail Page', 'title' => 'Store Detail Page', 'description' => 'This is Store Detail Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-detail', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product-in-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.top-sold-products', 'parent_content_id' => $right_id, 'order' => 18, 'params' => '', ));
		}
		// STORE LISTING PAGE store_index_listing
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_index_listing') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_index_listing', 'displayname' => 'Store Listing Page', 'title' => 'Store Listing Page', 'description' => 'This is Store Listing Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.listing-stores', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
		}
		// STORE FOLLOWING PAGE store_index_listing
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_my-follow-store_index') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_my-follow-store_index', 'displayname' => 'Store My Follow Store Page', 'title' => 'Store My Follow Store Page', 'description' => 'This is My Follow Store Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.my-following-stores', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.most-followed-stores', 'parent_content_id' => $right_id, 'order' => 18, 'params' => '', ));
		}

		// STORE PRODUCT FAVOURITE PAGE store_index_listing
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_my-favourite-product_index') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_my-favourite-product_index', 'displayname' => 'Store My Favourite Product Page', 'title' => 'Store My Favourite Product Page', 'description' => 'This is My Favourite Product Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.my-favourite-products', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.most-favourite-products', 'parent_content_id' => $right_id, 'order' => 18, 'params' => '', ));
		}

		// STORE PRODUCT LISTING PAGE store_index_listing
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_product_listing') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_product_listing', 'displayname' => 'Store Products Listing Page', 'title' => 'Store Products Listing Page', 'description' => 'This is Products Listing Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.listing-products', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '{"title":"Listing Products"}', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
		}

		// PRODUCT DETAIL PAGE
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_product_detail') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_product_detail', 'displayname' => 'Store Product Detail Page', 'title' => 'Store Product Detail Page', 'description' => 'This is Store Product Detail Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.product-detail', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => 7, 'params' => '{"max":"6"}', ));

			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.product-description', 'parent_content_id' => $tab_id, 'order' => 8, 'params' => '{"title":"Product Description"}', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-info', 'parent_content_id' => $tab_id, 'order' => 9, 'params' => '{"title":"Store Information"}', ));
			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
		} else {
			$page_id = $info['page_id'];
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.product-detail');
			$info = $select -> query() -> fetch();
			$middle_id = $info['parent_content_id'];
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.product-description');
			$info = $select -> query() -> fetch();
			if (empty($info)) {
				$select = new Zend_Db_Select($db);
				$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'socialstore.product-detail');
				$info = $select -> query() -> fetch();
				if ($info) {
					$order = $info['order'];
				} else {
					$order = null;
				}
				$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => $order ? $order + 1 : 7, 'params' => '{"max":"6"}', ));

				$tab_id = $db -> lastInsertId('engine4_core_content');

				$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.product-description', 'parent_content_id' => $tab_id, 'order' => $order ? $order + 2 : 8, 'params' => '{"title":"Product Description"}', ));
				$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-info', 'parent_content_id' => $tab_id, 'order' => $order ? $order + 3 : 9, 'params' => '{"title":"Store Information"}', ));
			}
		}
		// BROWSE PRODUCTS
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_product_index') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_product_index', 'displayname' => 'Store Products Home Page', 'title' => 'Store Products Home Page', 'description' => 'This is Store Products Home Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.product-slideshow', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-recent-products', 'parent_content_id' => $middle_id, 'order' => 7, 'params' => '{"title":"Recent Products"}', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));

		}

		// STORE LISTING PRODUCT IN STORE

		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_product_store-list-product') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_product_store-list-product', 'displayname' => 'Store Listing Products in a Store Page', 'title' => 'Store Listing Products in a Store Page', 'description' => 'This is Store\'s Products Listing Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-listing-products', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product-in-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));
		}

		//STORE FRONT PAGE

		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'socialstore_store_front') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'socialstore_store_front', 'displayname' => 'Store Front Page', 'title' => 'Store Front Page', 'description' => 'This is Store Front Page.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.main-menu', 'parent_content_id' => $middle_id, 'order' => 3, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 6, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 5, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-front', 'parent_content_id' => $middle_id, 'order' => 6, 'params' => '', ));
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.store-listing-products', 'parent_content_id' => $middle_id, 'order' => 7, 'params' => '{"title":"Listing Products"}', ));

			// right column

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'socialstore.search-product-in-store', 'parent_content_id' => $right_id, 'order' => 17, 'params' => '', ));

		}

		parent::onInstall();
	}

}
