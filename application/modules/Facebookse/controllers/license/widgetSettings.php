<?php
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

//PLACE FACEBOOK LIKE BUTTON WIDGET ON RECIPE PROFILE PAGE.
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'recipe_index_view')
      ->limit(1);
    $page = $select->query()->fetchObject();
if( !empty($page) ) {
  $page_id = $page->page_id;
  // group.profile-groups

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'Facebookse.facebookse-recipeprofilelike')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
       ->where('name = ?', 'main')
        ->limit(1);
      $container = $select->query()->fetchObject();
	if(!empty($container)){
		$container_id = $container->content_id;
  
	  // middle_id (will always be there)
	  $select = new Zend_Db_Select($db);
	  $select
		->from('engine4_core_content')
		->where('parent_content_id = ?', $container_id)
		->where('type = ?', 'container')
		->where('name = ?', 'middle')
		->limit(1);
	  $middle = $select->query()->fetchObject();
		if(!empty($middle)) {
			  $middle_id = $middle->content_id;
		  
			  $db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type'    => 'widget',
				'name'    => 'Facebookse.facebookse-recipeprofilelike',
				'parent_content_id' => $middle_id,
				'order'   => 1,
				'params'  => '{"title":"","titleCount":true}',
			  ));
			}

		}
    }
}

//PLACE FACEBOOK LIKE BUTTON ON SITEESTORE PROFILE PAGE.
	$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteestore');
    $isFacebooksePlugin = $select->query()->fetchObject();
    if (!empty($isFacebooksePlugin)) {
		$select = new Zend_Db_Select($db);
		$select
				->from('engine4_core_pages')
				->where('name = ?', 'siteestore_index_view')
				->limit(1);
		$page_id = $select->query()->fetchObject()->page_id;

		// @Make an condition
		if (!empty($page_id)) {

		  // container_id (will always be there)
		  $select = new Zend_Db_Select($db);
		  $select
				  ->from('engine4_core_content')
				  ->where('page_id = ?', $page_id)
				  ->where('type = ?', 'container')
				  ->where('name = ?', 'main')
				  ->limit(1);
		  $container_id = $select->query()->fetchObject()->content_id;
		  if (!empty($container_id)) {
			// $right_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_content')
				->where('parent_content_id = ?', $container_id)
				->where('type = ?', 'container')
				->where('name = ?', 'right')
				->limit(1);
			$right_id = $select->query()->fetchObject()->content_id;

			if (!empty($right_id)) {
			  // Check if it's already been placed
			  $select = new Zend_Db_Select($db);
			  $select
					  ->from('engine4_core_content')
					  ->where('parent_content_id = ?', $right_id)
					  ->where('type = ?', 'widget')
					  ->where('name = ?', 'Facebookse.facebookse-commonlike');
			  $info = $select->query()->fetch();
			  if (empty($info)) {
				// tab on profile
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'Facebookse.facebookse-commonlike',
					'parent_content_id' => $right_id,
					'order' => 11,
					'params'  => '{"fbbutton_commentbox":"0"}',
				));
			  }
			}
		}
	}
 }
 
 //PLACE FACEBOOK LIKE BUTTON WIDGET ON Sitestore PROFILE PAGE.
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'sitestore_index_index')
      ->limit(1);
    $page = $select->query()->fetchObject();
if( !empty($page) ) {
  $page_id = $page->page_id;
  // group.profile-groups

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'Facebookse.facebookse-commonlike')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
       ->where('name = ?', 'main')
        ->limit(1);
      $container = $select->query()->fetchObject();
	if(!empty($container)){
		$container_id = $container->content_id;
  
	  // middle_id (will always be there)
	  $select = new Zend_Db_Select($db);
	  $select
		->from('engine4_core_content')
		->where('parent_content_id = ?', $container_id)
		->where('type = ?', 'container')
		->where('name = ?', 'middle')
		->limit(1);
	  $middle = $select->query()->fetchObject();
		if(!empty($middle)) {
			  $middle_id = $middle->content_id;
		  
			  $db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type'    => 'widget',
				'name'    => 'Facebookse.facebookse-commonlike',
				'parent_content_id' => $middle_id,
				'order'   => 4,
				'params'  => '{"title":"","titleCount":true, "action_type":"og.likes", "object_type":"object"}',
			  ));
			}

		}
    }
}
 
?>
