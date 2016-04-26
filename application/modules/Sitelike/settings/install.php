<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

class Sitelike_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

    $getErrorMsg = $this->getVersion(); 
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

		$db = $this->getDb();

    //CHECK SOCIALENGINEADDONS PLUGIN IS INSTALL OR NOT.
    $pluginName = 'Likes Plugin and Widgets';
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
          ->where('name = ?', 'seaocore');
    $check_socialengineaddons = $select->query()->fetchAll();
    $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
    $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    if ( strstr($url_string, "manage/install") ) {
      $calling_from = 'install';
    } else if ( strstr($url_string, "manage/query") ) {
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
			return $this->_error('<div class="global_form"><div><div>The SocialEngineAddOns Core Plugin is not installed on your site. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and install on your site before installing this plugin.</div></div></div>');
    } else if( !empty($check_socialengineaddons) && empty($check_socialengineaddons[0]['enabled']) ) {
      // Plugin not Enable at your site
      return $this->_error("<span style='color:red'>Note: You have installed the SocialEngineAddOns Core Plugin but not enabled it on your site yet. Please enabled it first before installing the $pluginName .</span><br/> <a href='" . 'http://' . $core_final_url . "install/manage/'>Click here</a> to enabled the SocialEngineAddOns Core Plugin.");
    } else if( $check_socialengineaddons[0]['version'] < '4.2.0' ) {
      // Please activate page plugin
      return $this->_error('<div class="global_form"><div><div> You do not have the latest version of the SocialEngineAddOns Core Plugin. Please download the latest version of this FREE plugin from your Client Area on <a href="http://www.socialengineaddons.com" target="_blank">SocialEngineAddOns</a> and upgrade this on your site.</div></div></div>');
    }
  
		$select = new Zend_Db_Select( $db ) ;
		$select
			->from( 'engine4_core_modules' )
			->where( 'name = ?' , 'sitelike' ) ;
		$check_sitelike = $select->query()->fetchObject() ;
		if (empty( $check_sitelike )) {
  
  		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_pages')
					->where('name = ?', 'sitelike_index_browse')
					->limit(1);
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitelike_index_browse',
					'displayname' => 'Browse Liked Items',
					'title' => 'Browse Liked Items',
					'description' => 'This is the most like browse page.',
					'custom' => 1,
			));
    $page_id = $db->lastInsertId('engine4_core_pages');

    //CONTAINERS
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

            //CONTAINERS
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitelike.list-browse-mixlikes',
            'parent_content_id' => $middle_id,
            'order' => 14,
            'params' => '{"title":"Liked Items","itemCountPerPage":"10","name":"sitelike.list-browse-mixlikes"}',
        ));

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $topcontainer_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 4,
        'params' => '',
    ));
    $left_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $topcontainer_id,
        'order' => 6,
        'params' => '',
    ));
    $topmiddle_id = $db->lastInsertId('engine4_core_content');



    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitelike.navigation-like',
        'parent_content_id' => $topmiddle_id,
        'order' => 3,
        'params' => '',
    ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'right',
          'parent_content_id' => $container_id,
          'order' => 5,
          'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');
  }


$select = new Zend_Db_Select( $db ) ;

// Browse page
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'sitelike_index_browse' )
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
    // left_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'left' )
        ->limit( 1 ) ;
    $left_id = $select->query()->fetchObject()->content_id ;

    // right_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'right' )
        ->limit( 1 ) ;
    $right_id = $select->query()->fetchObject()->content_id ;

    // Check left_id is empty or not
    if ( !empty( $left_id ) ) {

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepage' ) ;
      $check_sitepage = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepage ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Pages","resource_type":"sitepage_page","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 23 ,
            'params' => '{"title":"Most Liked Pages","resource_type":"sitepage_page","nomobile":"0","name":"sitelike.list-like-items"}' ,
              ) ) ;
        }
      }

            $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagealbum' ) ;
      $check_sitepagealbum = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagealbum ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items')
            ->where( 'params = ?' , '{"title":"Most Liked Page Album Photos","resource_type":"sitepage_photo","nomobile":"0","name":"sitelike.list-like-items"}');

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 51 ,
            'params' => '{"title":"Most Liked Page Album Photos","resource_type":"sitepage_photo","nomobile":"0","name":"sitelike.list-like-items"}' ,
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagealbum' ) ;
      $check_sitepagealbum = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagealbum ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Page Albums","resource_type":"sitepage_album","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 24 ,
            'params' => '{"title":"Most Liked Page Albums","resource_type":"sitepage_album","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagevideo' ) ;
      $check_sitepagevideo = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagevideo ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Videos","resource_type":"sitepagevideo_video","nomobile":"0","name":"sitelike.list-like-items"}' );

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 25 ,
            'params' => '{"title":"Most Liked Page Videos","resource_type":"sitepagevideo_video","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagenote' ) ;
      $check_sitepagenote = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagenote ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Notes","resource_type":"sitepagenote_note","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 26 ,
            'params' => '{"title":"Most Liked Page Notes","resource_type":"sitepagenote_note","nomobile":"0","name":"sitelike.list-like-items"}',
            ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagedocument' ) ;
      $check_sitepagedocument = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagedocument ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Documents","resource_type":"sitepagedocument_document","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 27 ,
            'params' => '{"title":"Most Liked Page Documents","resource_type":"sitepagedocument_document","nomobile":"0","name":"sitelike.list-like-items"}',
                        ) ) ;
        }
      }





$select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'document' ) ;
      $check_document = $select->query()->fetchObject() ;
      if ( !empty( $check_document ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Documents","resource_type":"document","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 6 ,
            'params' => '{"title":"Most Liked Documents","resource_type":"document","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }





$select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'group' ) ;
      $check_group = $select->query()->fetchObject() ;
      if ( !empty( $check_group ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Groups","resource_type":"group","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 7 ,
            'params' => '{"title":"Most Liked Groups","resource_type":"group","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }





$select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'event' ) ;
      $check_event = $select->query()->fetchObject() ;
      if ( !empty( $check_event ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Events","resource_type":"event","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 8 ,
            'params' => '{"title":"Most Liked Events","resource_type":"event","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }


$select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'blog' ) ;
      $check_blog = $select->query()->fetchObject() ;
      if ( !empty( $check_blog ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Blogs","resource_type":"blog","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 9 ,
            'params' => '{"title":"Most Liked Blogs","resource_type":"blog","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }


$select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'group' ) ;
      $check_group = $select->query()->fetchObject() ;
      if ( !empty( $check_group ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Group Photos","resource_type":"group_photo","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 10 ,
            'params' => '{"title":"Most Liked Group Photos","resource_type":"group_photo","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'video' ) ;
      $check_video = $select->query()->fetchObject() ;
      if ( !empty( $check_video ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Videos","resource_type":"video","nomobile":"0","name":"sitelike.list-like-items"}') ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $left_id ,
            'order' => 11 ,
            'params' => '{"title":"Most Liked Videos","resource_type":"video","nomobile":"0","name":"sitelike.list-like-items"}',) ) ;
        }
      }
    }
    if ( !empty( $right_id ) ) {
      $select = new Zend_Db_Select( $db ) ;
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Members","resource_type":"user","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 16 ,
            'params' => '{"title":"Most Liked Members","resource_type":"user","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'album' ) ;
      $check_album = $select->query()->fetchObject() ;
      if ( !empty( $check_album ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Albums","resource_type":"album","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 17 ,
            'params' => '{"title":"Most Liked Albums","resource_type":"album","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'classified' ) ;
      $check_classified = $select->query()->fetchObject() ;
      if ( !empty( $check_classified ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Classifieds","resource_type":"classified","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 18 ,
            'params' => '{"title":"Most Liked Classifieds","resource_type":"classified","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'music' ) ;
      $check_music = $select->query()->fetchObject() ;
      if ( !empty( $check_music ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Music","resource_type":"music_playlist","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 19 ,
            'params' => '{"title":"Most Liked Music","resource_type":"music_playlist","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'poll' ) ;
      $check_poll = $select->query()->fetchObject() ;
      if ( !empty( $check_poll ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Polls","resource_type":"poll","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 20 ,
            'params' => '{"title":"Most Liked Polls","resource_type":"poll","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'album' ) ;
      $check_album = $select->query()->fetchObject() ;
      if ( !empty( $check_album ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Album Photos","resource_type":"album_photo","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 21 ,
            'params' => '{"title":"Most Liked Album Photos","resource_type":"album_photo","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

    $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'event' ) ;
      $check_event = $select->query()->fetchObject() ;
      if ( !empty( $check_event ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Event Photos","resource_type":"event_photo","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 22 ,
            'params' => '{"title":"Most Liked Event Photos","resource_type":"event_photo","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagepoll' ) ;
      $check_sitepagepoll = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagepoll ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
                        ->where( 'params = ?' , '{"title":"Most Liked Page Polls","resource_type":"sitepagepoll_poll","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 28 ,
            'params' => '{"title":"Most Liked Page Polls","resource_type":"sitepagepoll_poll","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

            $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagemusic' ) ;
      $check_sitepagemusic = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagemusic ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Music","resource_type":"sitepagemusic_playlist","nomobile":"0","name":"sitelike.list-like-items"}');
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 50 ,
            'params' => '{"title":"Most Liked Page Music","resource_type":"sitepagemusic_playlist","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }


      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepagereview' ) ;
      $check_sitepagereview = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepagereview ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Reviews","resource_type":"sitepagereview_review","nomobile":"0","name":"sitelike.list-like-items"}');

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 29 ,
            'params' => '{"title":"Most Liked Page Reviews","resource_type":"sitepagereview_review","nomobile":"0","name":"sitelike.list-like-items"}',

              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepageevent' ) ;
      $check_sitepageevent = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepageevent ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Page Events","resource_type":"sitepageevent_event","nomobile":"0","name":"sitelike.list-like-items"}');

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 30 ,
            'params' => '{"title":"Most Liked Page Events","resource_type":"sitepageevent_event","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'recipe' ) ;
      $check_recipe = $select->query()->fetchObject() ;
      if ( !empty( $check_recipe ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Recipes","resource_type":"recipe","nomobile":"0","name":"sitelike.list-like-items"}' ) ;
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 31 ,
            'params' => '{"title":"Most Liked Recipes","resource_type":"recipe","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'list' ) ;
      $check_list = $select->query()->fetchObject() ;
      if ( !empty( $check_list ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'page_id = ?' , $page_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.list-like-items' )
            ->where( 'params = ?' , '{"title":"Most Liked Listings","resource_type":"list_listing","nomobile":"0","name":"sitelike.list-like-items"}' ) ;

        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.list-like-items' ,
            'parent_content_id' => $right_id ,
            'order' => 32 ,
            'params' => '{"title":"Most Liked Listings","resource_type":"list_listing","nomobile":"0","name":"sitelike.list-like-items"}',
              ) ) ;
        }
      }
    }
  }
}

//members profile page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'user_profile_index' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
    // left_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'middle' )
        ->limit( 1 ) ;
    $middle_id = $select->query()->fetchObject()->content_id ;

    // @Make an condition
    if ( !empty( $middle_id ) ) {

      // left_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'left' )
          ->limit( 1 ) ;
      $left_id = $select->query()->fetchObject()->content_id ;

      // @Make an condition
      if ( !empty( $left_id ) ) {

        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $left_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.common-friend-like' ) ;
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.common-friend-like' ,
            'parent_content_id' => $left_id ,
            'order' => 35 ,
            'params' => '{"title":"","itemCountPerPage":3}' ,
              ) ) ;
        }
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $left_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.common-like' ) ;
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.common-like' ,
            'parent_content_id' => $left_id ,
            'order' => 35 ,
            'params' => '{"title":"","itemCountPerPage":3}' ,
              ) ) ;
        }


        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $left_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.profile-user-likes' ) ;
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.profile-user-likes' ,
            'parent_content_id' => $left_id ,
            'order' => 35 ,
            'params' => '{"itemCountPerPage":"3","title":"","name":"sitelike.profile-user-likes"}' ,
              ) ) ;
        }
      }

      if ( !empty( $middle_id ) ) {
        // Check if it's already been placed
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $middle_id )
            ->where( 'type = ?' , 'widget' )
            ->where( 'name = ?' , 'sitelike.common-like-button' ) ;
        $info = $select->query()->fetch() ;
        if ( empty( $info ) ) {
          // tab on profile
          $db->insert( 'engine4_core_content' , array (
            'page_id' => $page_id ,
            'type' => 'widget' ,
            'name' => 'sitelike.common-like-button' ,
            'parent_content_id' => $middle_id ,
            'order' => 2 ,
            'params' => '{"title":""}' ,
              ) ) ;
        }
      }
    }
  }
}

//group profile page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'group_profile_index' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
    // left_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'middle' )
        ->limit( 1 ) ;
    $middle_id = $select->query()->fetchObject()->content_id ;

    // @Make an condition
    if ( !empty( $middle_id ) ) {

      // left_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'left' )
          ->limit( 1 ) ;
      $left_id = $select->query()->fetchObject()->content_id ;

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'group' ) ;
      $check_group = $select->query()->fetchObject() ;
      if ( !empty( $check_group ) ) {
        // @Make an condition
        if ( !empty( $left_id ) ) {

          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-friend-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-friend-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
        }

        if ( !empty( $middle_id ) ) {
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $middle_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like-button' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like-button' ,
              'parent_content_id' => $middle_id ,
              'order' => 2 ,
              'params' => '{"title":""}' ,
                ) ) ;
          }
        }
      }
    }
  }
}

//event profile page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'event_profile_index' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
    // left_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'middle' )
        ->limit( 1 ) ;
    $middle_id = $select->query()->fetchObject()->content_id ;

    // @Make an condition
    if ( !empty( $middle_id ) ) {

      // left_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'left' )
          ->limit( 1 ) ;
      $left_id = $select->query()->fetchObject()->content_id ;

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'event' ) ;
      $check_event = $select->query()->fetchObject() ;
      if ( !empty( $check_event ) ) {
        // @Make an condition
        if ( !empty( $left_id ) ) {

          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-friend-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-friend-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
        }

        if ( !empty( $middle_id ) ) {
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $middle_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like-button' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like-button' ,
              'parent_content_id' => $middle_id ,
              'order' => 2 ,
              'params' => '{"title":""}' ,
                ) ) ;
          }
        }
      }
    }
  }
}


	//Member home page
	$select = new Zend_Db_Select( $db ) ;
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
			// $right_id (will always be there)
			$select = new Zend_Db_Select( $db ) ;
			$select
				->from( 'engine4_core_content' )
				->where( 'parent_content_id = ?' , $container_id )
				->where( 'type = ?' , 'container' )
				->where( 'name = ?' , 'left' )
				->limit( 1 ) ;
			$left_id = $select->query()->fetchObject()->content_id ;
			
			if ( !empty( $left_id ) ) {
				// Check if it's already been placed
				$select = new Zend_Db_Select( $db ) ;
				$select
					->from( 'engine4_core_content' )
					->where( 'parent_content_id = ?' , $left_id )
					->where( 'type = ?' , 'widget' )
					->where( 'name = ?' , 'sitelike.list-like-items' )
					->where( 'params = ?' , '{"title":"Most Liked Products","resource_type":"siteestore_product","nomobile":"0","name":"sitelike.list-like-items"}' );
				$info = $select->query()->fetch() ;
				
				if ( empty( $info ) ) {
					$db->insert( 'engine4_core_content' , array (
					'page_id' => $page_id ,
					'type' => 'widget' ,
					'name' => 'sitelike.list-like-items' ,
					'parent_content_id' => $left_id ,
					'order' => 50,
					'params' => '{"title":"Most Liked Products Photos","resource_type":"siteestore_product","nomobile":"0","name":"sitelike.list-like-items"}',
					)) ;
				}
			}
		}
	}


//Member home page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'user_index_home' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
      // $right_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'right' )
          ->limit( 1 ) ;
      $right_id = $select->query()->fetchObject()->content_id ;

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'event' ) ;
      $check_event = $select->query()->fetchObject() ;
      if ( !empty( $check_event ) ) {
        // @Make an condition
        if ( !empty( $right_id ) ) {

          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $right_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.mix-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.mix-like' ,
              'parent_content_id' => $right_id ,
              'order' => 1 ,
              'params' => '{"title":"Most Liked Items"}' ,
                ) ) ;
          }
        }
      }
  }
}

//home page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'core_index_index' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
		// $right_id (will always be there)
		$select = new Zend_Db_Select( $db ) ;
		$select
				->from( 'engine4_core_content' )
				->where( 'parent_content_id = ?' , $container_id )
				->where( 'type = ?' , 'container' )
				->where( 'name = ?' , 'left' )
				->limit( 1 ) ;
		$left_id = $select->query()->fetchObject()->content_id ;
		// @Make an condition
		if ( !empty( $left_id ) ) {

			// Check if it's already been placed
			$select = new Zend_Db_Select( $db ) ;
			$select
					->from( 'engine4_core_content' )
					->where( 'parent_content_id = ?' , $left_id )
					->where( 'type = ?' , 'widget' )
					->where( 'name = ?' , 'sitelike.list-like-items' )
					->where( 'params = ?' , '{"title":"Most Liked Products","resource_type":"siteestore_product","nomobile":"0","name":"sitelike.list-like-items"}' );
			$info = $select->query()->fetch() ;
			if ( empty( $info ) ) {
				// tab on profile
				$db->insert( 'engine4_core_content' , array (
					'page_id' => $page_id ,
					'type' => 'widget' ,
					'name' => 'sitelike.list-like-items' ,
					'parent_content_id' => $left_id ,
					'order' => 2,
					'params' => '{"title":"Most Liked Products Photos","resource_type":"siteestore_product","nomobile":"0","name":"sitelike.list-like-items"}',
						) ) ;
			}
		}
  }
}


//home page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'core_index_index' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
      // $right_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'right' )
          ->limit( 1 ) ;
      $right_id = $select->query()->fetchObject()->content_id ;

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'event' ) ;
      $check_event = $select->query()->fetchObject() ;
      if ( !empty( $check_event ) ) {
        // @Make an condition
        if ( !empty( $right_id ) ) {

          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $right_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.mix-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.mix-like' ,
              'parent_content_id' => $right_id ,
              'order' => 2,
              'params' => '{"title":"Most Liked Items"}' ,
                ) ) ;
          }
        }
      }
  }
}

//Page event profile page
$select = new Zend_Db_Select( $db ) ;
$select
    ->from( 'engine4_core_pages' )
    ->where( 'name = ?' , 'sitepageevent_index_view' )
    ->limit( 1 ) ;
$page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
    // left_id (will always be there)
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_content' )
        ->where( 'parent_content_id = ?' , $container_id )
        ->where( 'type = ?' , 'container' )
        ->where( 'name = ?' , 'middle' )
        ->limit( 1 ) ;
    $middle_id = $select->query()->fetchObject()->content_id ;

    // @Make an condition
    if ( !empty( $middle_id ) ) {

      // left_id (will always be there)
      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_content' )
          ->where( 'parent_content_id = ?' , $container_id )
          ->where( 'type = ?' , 'container' )
          ->where( 'name = ?' , 'left' )
          ->limit( 1 ) ;
      $left_id = $select->query()->fetchObject()->content_id ;

      $select = new Zend_Db_Select( $db ) ;
      $select
          ->from( 'engine4_core_modules' )
          ->where( 'name = ?' , 'sitepageevent' ) ;
      $check_sitepageevent = $select->query()->fetchObject() ;
      if ( !empty( $check_sitepageevent ) ) {
        // @Make an condition
        if ( !empty( $left_id ) ) {

          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-friend-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-friend-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $left_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like' ,
              'parent_content_id' => $left_id ,
              'order' => 35 ,
              'params' => '{"title":"","itemCountPerPage":3}' ,
                ) ) ;
          }
        }

        if ( !empty( $middle_id ) ) {
          // Check if it's already been placed
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $middle_id )
              ->where( 'type = ?' , 'widget' )
              ->where( 'name = ?' , 'sitelike.common-like-button' ) ;
          $info = $select->query()->fetch() ;
          if ( empty( $info ) ) {
            // tab on profile
            $db->insert( 'engine4_core_content' , array (
              'page_id' => $page_id ,
              'type' => 'widget' ,
              'name' => 'sitelike.common-like-button' ,
              'parent_content_id' => $middle_id ,
              'order' => 1 ,
              'params' => '{"title":""}' ,
                ) ) ;
          }
        }
      }
    }
  }
}

    $db = $this->getDb() ;
    $select = new Zend_Db_Select( $db ) ;

        // Browse page
    $select
        ->from( 'engine4_core_pages' )
        ->where( 'name = ?' , 'sitelike_index_browse' )
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
                // left_id (will always be there)
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $container_id )
            ->where( 'type = ?' , 'container' )
            ->where( 'name = ?' , 'left' )
            ->limit( 1 ) ;
        $left_id = $select->query()->fetchObject()->content_id ;

                // right_id (will always be there)
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $container_id )
            ->where( 'type = ?' , 'container' )
            ->where( 'name = ?' , 'right' )
            ->limit( 1 ) ;
        $right_id = $select->query()->fetchObject()->content_id ;

                // Check left_id is empty or not
        if ( !empty( $left_id ) ) {

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepage' ) ;
          $check_sitepage = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepage ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pages' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pages' ,
                'parent_content_id' => $left_id ,
                'order' => 23 ,
                'params' => '{"title":"Most Liked Pages"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagealbum' ) ;
          $check_sitepagealbum = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagealbum ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagealbums' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagealbums' ,
                'parent_content_id' => $left_id ,
                'order' => 24 ,
                'params' => '{"title":"Most Liked Page Albums"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagevideo' ) ;
          $check_sitepagevideo = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagevideo ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagevideos' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagevideos' ,
                'parent_content_id' => $left_id ,
                'order' => 25 ,
                'params' => '{"title":"Most Liked Page Videos"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagenote' ) ;
          $check_sitepagenote = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagenote ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagenotes' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagenotes' ,
                'parent_content_id' => $left_id ,
                'order' => 26 ,
                'params' => '{"title":"Most Liked Page Notes"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagedocument' ) ;
          $check_sitepagedocument = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagedocument ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagedocuments' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagedocuments' ,
                'parent_content_id' => $left_id ,
                'order' => 27 ,
                'params' => '{"title":"Most Liked Page Documents"}' ,
                  ) ) ;
            }
          }
        }
        if ( !empty( $right_id ) ) {

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagepoll' ) ;
          $check_sitepagepoll = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagepoll ) ) {

                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagepolls' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagepolls' ,
                'parent_content_id' => $right_id ,
                'order' => 28 ,
                'params' => '{"title":"Most Liked Page Polls"}' ,
                  ) ) ;
            }
          }
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepagereview' ) ;
          $check_sitepagereview = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepagereview ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pagereviews' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pagereviews' ,
                'parent_content_id' => $right_id ,
                'order' => 29 ,
                'params' => '{"title":"Most Liked Page Reviews"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepageevent' ) ;
          $check_sitepageevent = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepageevent ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-pageevent' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-pageevent' ,
                'parent_content_id' => $right_id ,
                'order' => 30 ,
                'params' => '{"title":"Most Liked Page Events"}' ,
                  ) ) ;
            }
          }

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'recipe' ) ;
          $check_recipe = $select->query()->fetchObject() ;
          if ( !empty( $check_recipe ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-recipe' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-recipe' ,
                'parent_content_id' => $right_id ,
                'order' => 31 ,
                'params' => '{"title":"Most Liked Recipes"}' ,
                  ) ) ;
            }
          }
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'list' ) ;
          $check_list = $select->query()->fetchObject() ;
          if ( !empty( $check_list ) ) {
                        // Check if it's already been placed
            $select = new Zend_Db_Select( $db ) ;
            $select
                ->from( 'engine4_core_content' )
                ->where( 'page_id = ?' , $page_id )
                ->where( 'type = ?' , 'widget' )
                ->where( 'name = ?' , 'sitelike.list-like-listings' ) ;
            $info = $select->query()->fetch() ;
            if ( empty( $info ) ) {
                            // tab on profile
              $db->insert( 'engine4_core_content' , array (
                'page_id' => $page_id ,
                'type' => 'widget' ,
                'name' => 'sitelike.list-like-listings' ,
                'parent_content_id' => $right_id ,
                'order' => 32 ,
                'params' => '{"title":"Most Liked Page Listings"}' ,
                  ) ) ;
            }
          }
        }
      }
    }
//Page event profile page
    $select = new Zend_Db_Select( $db ) ;
    $select
        ->from( 'engine4_core_pages' )
        ->where( 'name = ?' , 'sitepageevent_index_view' )
        ->limit( 1 ) ;
    $page_id = $select->query()->fetchObject()->page_id ;

// @Make an condition
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
// left_id (will always be there)
        $select = new Zend_Db_Select( $db ) ;
        $select
            ->from( 'engine4_core_content' )
            ->where( 'parent_content_id = ?' , $container_id )
            ->where( 'type = ?' , 'container' )
            ->where( 'name = ?' , 'middle' )
            ->limit( 1 ) ;
        $middle_id = $select->query()->fetchObject()->content_id ;

// @Make an condition
        if ( !empty( $middle_id ) ) {

// left_id (will always be there)
          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_content' )
              ->where( 'parent_content_id = ?' , $container_id )
              ->where( 'type = ?' , 'container' )
              ->where( 'name = ?' , 'left' )
              ->limit( 1 ) ;
          $left_id = $select->query()->fetchObject()->content_id ;

          $select = new Zend_Db_Select( $db ) ;
          $select
              ->from( 'engine4_core_modules' )
              ->where( 'name = ?' , 'sitepageevent' ) ;
          $check_sitepageevent = $select->query()->fetchObject() ;
          if ( !empty( $check_sitepageevent ) ) {
// @Make an condition
            if ( !empty( $left_id ) ) {

// Check if it's already been placed
              $select = new Zend_Db_Select( $db ) ;
              $select
                  ->from( 'engine4_core_content' )
                  ->where( 'parent_content_id = ?' , $left_id )
                  ->where( 'type = ?' , 'widget' )
                  ->where( 'name = ?' , 'sitelike.pageevent-friend-like' ) ;
              $info = $select->query()->fetch() ;
              if ( empty( $info ) ) {
// tab on profile
                $db->insert( 'engine4_core_content' , array (
                  'page_id' => $page_id ,
                  'type' => 'widget' ,
                  'name' => 'sitelike.pageevent-friend-like' ,
                  'parent_content_id' => $left_id ,
                  'order' => 35 ,
                  'params' => '{"title":"Page Event Likes (Friends)"}' ,
                    ) ) ;
              }
// Check if it's already been placed
              $select = new Zend_Db_Select( $db ) ;
              $select
                  ->from( 'engine4_core_content' )
                  ->where( 'parent_content_id = ?' , $left_id )
                  ->where( 'type = ?' , 'widget' )
                  ->where( 'name = ?' , 'sitelike.pageevent-like' ) ;
              $info = $select->query()->fetch() ;
              if ( empty( $info ) ) {
// tab on profile
                $db->insert( 'engine4_core_content' , array (
                  'page_id' => $page_id ,
                  'type' => 'widget' ,
                  'name' => 'sitelike.pageevent-like' ,
                  'parent_content_id' => $left_id ,
                  'order' => 35 ,
                  'params' => '{"title":"Page Event Likes (Everyone)"}' ,
                    ) ) ;
              }
            }

            if ( !empty( $middle_id ) ) {
// Check if it's already been placed
              $select = new Zend_Db_Select( $db ) ;
              $select
                  ->from( 'engine4_core_content' )
                  ->where( 'parent_content_id = ?' , $middle_id )
                  ->where( 'type = ?' , 'widget' )
                  ->where( 'name = ?' , 'sitelike.sitepageevent-like-button' ) ;
              $info = $select->query()->fetch() ;
              if ( empty( $info ) ) {
// tab on profile
                $db->insert( 'engine4_core_content' , array (
                  'page_id' => $page_id ,
                  'type' => 'widget' ,
                  'name' => 'sitelike.sitepageevent-like-button' ,
                  'parent_content_id' => $middle_id ,
                  'order' => 2 ,
                  'params' => '{"title":""}' ,
                    ) ) ;
              }
            }
          }
        }
      }
    }

    }
		parent::onPreInstall();
  }

  function onInstall() {
    $db = $this->getDb() ;

    //Start Group feed work
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
    if (!empty($table_exist)) {
      $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_grouped'")->fetch();
      if (empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_grouped` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
    }
    //End Group feed work
    
		//CODE FOR INCREASE THE SIZE OF engine4_activity_actiontypes's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_actiontypes LIKE 'type'")->fetch();
    if (!empty($type_array)) {
			$varchar = $type_array['Type'];
			$length_varchar = explode("(", $varchar);
			$length = explode(")", $length_varchar[1]);
			$length_type = $length[0];
			if ($length_type < 64) {
				$run_query = $db->query("ALTER TABLE `engine4_activity_actiontypes` CHANGE `type` `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
			}
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_core_likes\'')->fetch();
    if(!empty($table_exist)) {
	    $column_exist = $db->query('SHOW COLUMNS FROM `engine4_core_likes` LIKE \'creation_date\'')->fetch();
	    if(empty($column_exist)) {
		    $db->query('ALTER TABLE `engine4_core_likes` ADD `creation_date` DATETIME NOT NULL');
	    }
    }

    //$db = Zend_Db_Table_Abstract::getDefaultAdapter() ;



    //$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='like';");

    $db->query("UPDATE  `engine4_core_settings` SET  `engine4_core_settings`.`value` =  '0' WHERE  `engine4_core_settings`.`name` ='sitelike.button.likeupdatefile';");

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitelike')
            ->where('version <= ?', '4.1.8p1');
    $is_enabled = $select->query()->fetchObject();

    if ( !empty($is_enabled) ) {

      //Script for widget persion and friend.
      $this->updateCommonFriendWidgteName("sitelike.event-like", "sitelike.common-like", "event.people.like");
      $this->updateCommonFriendWidgteName("sitelike.group-like", "sitelike.common-like","group.people.like");
      $this->updateCommonFriendWidgteName("sitelike.list-like", "sitelike.common-like", "list.people.like");
      $this->updateCommonFriendWidgteName("sitelike.member-like", "sitelike.common-like", "member.people.like");
      $this->updateCommonFriendWidgteName("sitelike.page-like", "sitelike.common-like", "sitepage.people.like");
      $this->updateCommonFriendWidgteName("sitelike.recipe-like", "sitelike.common-like", "recipe.people.like");
      $this->updateCommonFriendWidgteName("sitelike.pageevent-like", "sitelike.common-like", "sitepageevents.people.like");

      $this->updateCommonFriendWidgteName("sitelike.event-friend-like", "sitelike.common-friend-like", "event.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.group-friend-like", "sitelike.common-friend-like", "group.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.list-friend-like", "sitelike.common-friend-like", "list.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.member-friend-like", "sitelike.common-friend-like", "member.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.page-friend-like", "sitelike.common-friend-like", "sitepage.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.recipe-friend-like", "sitelike.common-friend-like", "recipe.friend.like");
      $this->updateCommonFriendWidgteName("sitelike.pageevent-friend-like", "sitelike.common-friend-like", "sitepageevents.friend.like");

      //Replace the Like Button widget to new widget.
      $this->updateButtonWidgteName("sitelike.event-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.group-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.list-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.profile-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.page-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.recipe-like-button", "sitelike.common-like-button");
      $this->updateButtonWidgteName("sitelike.pageevent-like-button", "sitelike.common-like-button");

      //UPDATE THE WIDGET lIKE "MOST LIKE ITEM TYPE"
      $this->updateWidgteName("sitelike.list-like-blogs", "sitelike.list-like-items", "blog", "Most Liked Blogs");
      $this->updateWidgteName("sitelike.list-like-album", "sitelike.list-like-items", "album", "Most Liked Albums");
      $this->updateWidgteName("sitelike.list-like-albumphoto", "sitelike.list-like-items", "album_photo", "Most Liked Album Photos");
      $this->updateWidgteName("sitelike.list-like-classifieds", "sitelike.list-like-items", "classified", "Most Liked Classifieds");
      $this->updateWidgteName("sitelike.list-like-document", "sitelike.list-like-items","document", "Most Liked Documents");
      $this->updateWidgteName("sitelike.list-like-events", "sitelike.list-like-items", "event","Most Liked Events");
      $this->updateWidgteName("sitelike.list-like-eventphotos", "sitelike.list-like-items", "event_photo", "Most Liked Event Photos");
      $this->updateWidgteName("sitelike.list-like-forum", "sitelike.list-like-items", "forum_topic", "Most Liked Froums");
      $this->updateWidgteName("sitelike.list-like-groups", "sitelike.list-like-items", "group", "Most Liked Groups");
      $this->updateWidgteName("sitelike.list-like-groupphotos", "sitelike.list-like-items", "group_photo", "Most Liked Group Photos");
      $this->updateWidgteName("sitelike.list-like-listings", "sitelike.list-like-items", "list_listing", "Most Liked Listings");
      $this->updateWidgteName("sitelike.list-like-members", "sitelike.list-like-items", "user", "Most Liked Members");
      $this->updateWidgteName("sitelike.list-like-musics", "sitelike.list-like-items", "music_playlist", "Most Liked Music");
      $this->updateWidgteName("sitelike.list-like-pagealbumphotos", "sitelike.list-like-items", "sitepage_photo", "Most Liked Page Album Photos");
      $this->updateWidgteName("sitelike.list-like-pagedocuments", "sitelike.list-like-items", "sitepagedocument_document", "Most Liked Page Documents");
      $this->updateWidgteName("sitelike.list-like-pagealbums", "sitelike.list-like-items", "sitepage_album", "Most Liked Page Albums");
      $this->updateWidgteName("sitelike.list-like-pageevent", "sitelike.list-like-items", "sitepageevent_event", "Most Liked Page Events");
      $this->updateWidgteName("sitelike.list-like-pagemusics", "sitelike.list-like-items", "sitepagemusic_playlist", "Most Liked Page Music");
      $this->updateWidgteName("sitelike.list-like-pagenotes", "sitelike.list-like-items", "sitepagenote_note", "Most Liked Page Notes");
      $this->updateWidgteName("sitelike.list-like-pagepolls", "sitelike.list-like-items", "sitepagepoll_poll", "Most Liked Page Polls");
      $this->updateWidgteName("sitelike.list-like-pagereviews", "sitelike.list-like-items", "sitepagereview_review", "Most Liked Page Reviews");
      $this->updateWidgteName("sitelike.list-like-pages", "sitelike.list-like-items", "sitepage_page", "Most Liked Pages");
      $this->updateWidgteName("sitelike.list-like-recipe", "sitelike.list-like-items", "recipe", "Most Liked Recipes");
      $this->updateWidgteName("sitelike.list-like-videos", "sitelike.list-like-items", "video", "Most Liked Videos");
      $this->updateWidgteName("sitelike.list-like-polls", "sitelike.list-like-items", "poll", "Most Liked Polls");
      $this->updateWidgteName("sitelike.list-like-pagevideos", "sitelike.list-like-items", "sitepagevideo_video", "Most Liked Page Videos");
    }

    parent::onInstall() ;
  }
  function onEnable() {
    $this->updateActivityFedType(1);
    parent::onEnable();
  }

  function onDisable() {
    $this->updateActivityFedType(0);
    parent::onDisable();
  }

  public function updateActivityFedType($enable) {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_activity_actiontypes')
            ->where("`type` LIKE 'like_%'");
    $results = $select->query()->fetchAll();
    foreach ($results as $row) {
       $db->update('engine4_activity_actiontypes', array(
            'enabled' => $enable,
        ), array(
            'type = ?' => $row['type']
       ));
    }
  }

	//FUNCTION FOR THE lIKE BUTTON WIDGET.
	public function updateButtonWidgteName($oldwidgteName, $newWidgetName) {

			$db = $this->getDb() ;
			$select = new Zend_Db_Select( $db ) ;
			$select->from( 'engine4_core_content' )->where( 'name = ?' , $oldwidgteName )->limit(1) ;
			$results = $select->query()->fetchAll();

			if (!empty($results)) {
					$db->query('UPDATE  `engine4_core_content` SET  `name` =  \''.$newWidgetName.'\' WHERE  `engine4_core_content`.`name` =\''.$oldwidgteName.'\';');
			}
	}

	//FUNCTION FOR THE PEOPLE AND FRIEND WIDGET.
	public function updateCommonFriendWidgteName($oldwidgteName, $newWidgetName, $setting_name) {

		$db = $this->getDb() ;

		$total_items = $db->select()
			->from('engine4_core_settings', array('value'))->where('name = ?', $setting_name)->limit(1)->query()->fetchColumn();
		if ( empty($total_items) ) {
			$total_items = 3;
		}

		$select = new Zend_Db_Select( $db ) ;
		$select->from( 'engine4_core_content' )->where( 'name = ?' , $oldwidgteName )->limit(1) ;
		$results = $select->query()->fetchAll();

		if (!empty($results)) {
				$db->query('UPDATE  `engine4_core_content` SET  `name` =  \''.$newWidgetName.'\',
				`params` =  \'{"title":"","itemCountPerPage": ' . $total_items . '}\' WHERE  `engine4_core_content`.`name` =\''.$oldwidgteName.'\';');
		}
	}

	public function updateWidgteName($oldwidgteName, $newWidgetName, $resourceType, $title) {

		$db = $this->getDb() ;
		$select = new Zend_Db_Select( $db ) ;
		$select->from( 'engine4_core_content' )->where( 'name = ?' , $oldwidgteName )->limit(1) ;
		$results = $select->query()->fetchAll();

		if (!empty($results)) {
				$db->query('UPDATE  `engine4_core_content` SET  `name` =  \''.$newWidgetName.'\',
				`params` =  \'{"title":"'.$title.'","resource_type":"'.$resourceType.'","nomobile":"0","name":"'.$newWidgetName.'"}\' WHERE  `engine4_core_content`.`name` =\''.$oldwidgteName.'\';');
		}
	}

  public function onPostInstall() {

    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('sitelike','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitelike')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitelike/integrated/0/redirect/install');
				} 
      } 
    }
  }

  private function getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'sitemobile' => '4.6.0p4',
      'seaocore' => '4.8.5'
    );
    
    $finalModules = array();
    foreach ($modArray as $key => $value) {
    		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
		$isModEnabled = $select->query()->fetchObject();
			if (!empty($isModEnabled)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_modules',array('title', 'version'))
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
				$getModVersion = $select->query()->fetchObject();

				$isModSupport = strcasecmp($getModVersion->version, $value);
				if ($isModSupport < 0) {
					$finalModules[] = $getModVersion->title;
				}
			}
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Mobile / Tablet Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }
}
?>