<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Api_Core extends Core_Api_Abstract {

  /**
   * Returns: This is show duration of the tab.
   *
   * @param Int $duration_tab
   * @param String $table_name
   * @param String $modules_name
   * @param Int $entries_tab
   * @return like result
   */
  public function tabLikeDuration( $duration_tab  , $modules_name , $entries_tab, $resource_id ) {

    //MODULE NAME IS RESOURCE TYPE.
    $resource_type = $modules_name ;

    //CHECK THE VALIDATION FOR DEFAULT DURATION
    if ( !empty( $duration_tab ) ) {

      //CHECK THE ALL DURATION IS ONE OR NOT
      if ( $duration_tab != 1 ) {
        //CONDITION IS CHECK WHEN DURATION TAB VALUES IS  NOT OVERALL
        if ( $duration_tab != 'overall' ) {
          $duration_tab1 = $duration_tab - 1 ;
          //DATE IS CONVERT IN TO SECONDS
          $duration_tab1_seconds = $duration_tab1 * 24 * 60 * 60 ;
          $seconds_enddate = time() ;
          $seconds_startdate = $seconds_enddate - $duration_tab1_seconds ;
          //START DATE CAN GET FROM THE DATE FUNCTION AND MINUS RESULT
          $start_date = date( 'Y-m-d' , $seconds_startdate ) ;
        }
      }
      else {
        $start_date = date( 'Y-m-d' ) ;
      }
      $modNameForCondition = true ;
      //CHECK FOR ALL MODULES TYPE.
      switch ( $modules_name ) {

        case 'album_photo' :
          $modNameForCondition = false ;
          $albumTableName = Engine_Api::_()->getItemTable( 'album' )->info( 'name' );
          $album_table_name = Engine_Api::_()->getItemTable( $modules_name )->info( 'name' );
          break ;

        case 'sitepage_photo' :
          $modNameForCondition = false ;
          $sitepagealbumTableName = Engine_Api::_()->getItemTable( 'sitepage_album' )->info( 'name' );
          $sitepagealbum_table_name = Engine_Api::_()->getItemTable( $modules_name )->info( 'name' );
          break ;

        case 'group_photo' :
          $modNameForCondition = false ;
          $groupTableName = Engine_Api::_()->getItemTable( 'group' )->info( 'name' );
          $group_table_name = Engine_Api::_()->getItemTable( $modules_name )->info( 'name' );
          break ;

        case 'event_photo' :
          $modNameForCondition = false ;
          $eventTableName = Engine_Api::_()->getItemTable( 'event' )->info( 'name' );
          $event_table_name = Engine_Api::_()->getItemTable( $modules_name )->info( 'name' );
          break ;
      }

      //FETCHING THE REFERENCE PERTICUALR TYPE MODULE TABLE.
      $tableModules = Engine_Api::_()->getItemTable( $modules_name ) ;
      $tableModulesName = $tableModules->info( 'name' ) ;
      $likeTable = Engine_Api::_()->getDbtable( 'likes' , 'core' ) ;
      $likeTableName = $likeTable->info( 'name' ) ;

      //CHECK FOR THE CONTENT IS ACCORDING TO CONDITION.
      $getModCondition = $this->getModCondition( $resource_type , $modNameForCondition ) ;

      //SELECT THE QUERY WITH RIGHT JOIN FOR MOST LIKE ACCORDING DAYS.
      $select = $tableModules->select() ;
      $select
          ->setIntegrityCheck( false )
          ->from( $tableModulesName )
          ->join( $likeTableName , "($tableModulesName.$resource_id = $likeTableName.resource_id AND $likeTableName.resource_type = '$resource_type')" , array ( 'COUNT( ' . $likeTableName . '.resource_id ) as COUNT_Likes' ) ) ;

      if ( $resource_type == 'album_photo' ) {
        $select->join( $albumTableName , "($tableModulesName.album_id = $albumTableName.album_id)" ,
          array ($albumTableName . '.title AS album_title' ) ) ;
      }
      elseif ( $resource_type == 'sitepage_photo' ) {
        $select->join( $sitepagealbumTableName , "($tableModulesName.album_id =
        $sitepagealbumTableName.album_id)" , array ( $sitepagealbumTableName . '.title AS album_title' ) ) ;
      }
      else if ( $resource_type == 'group_photo' ) {
        $select->join( $groupTableName , "($tableModulesName.group_id = $groupTableName.group_id)" , array
         ($groupTableName . '.title AS group_title' ) ) ;
      }
      else if ( $resource_type == 'event_photo' ) {
        $select->join( $eventTableName , "($tableModulesName.event_id = $eventTableName.event_id)" , array
        ($eventTableName . '.title AS event_title' ) ) ;
      }

      //HERE CHECK THIS CONTENT ARE ACCORDING TO PRIVACY.
      if ( !empty( $getModCondition ) ) {
        foreach ( $getModCondition as $condition => $value ) {
          $select->where( $tableModulesName . '.' . $condition . ' =?' , $value ) ;
        }
      }

      $select->group( $likeTableName . '.' . 'resource_id' )->order( 'COUNT_Likes DESC' )->limit( $entries_tab ) ;

      //CONDITION IS CHECK WHEN DURATION TAB VALUES IS NOT OVERALL.
      if ( $duration_tab != 'overall' ) {
        $select->where( "$likeTableName.creation_date >= ?" , $start_date ) ;
      }
      return $tableModules->fetchAll( $select ) ;
    }
  }

  /**
   * This function is used for title turncation.
   *
   * @param Int $string
   * @return string result
   */
  public function turncation( $string ) {

    $titleLength = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.title.turncation' , 16 ) ;
    $string = strip_tags( $string ) ;
    return Engine_String::strlen( $string ) > $titleLength ? Engine_String::substr( $string , 0 , ($titleLength - 3 ) ) . '...' : $string ;
  }

  /**
   * check the item is like or not.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @return results
   */
  public function checkAvailability( $RESOURCE_TYPE , $RESOURCE_ID ) {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer() ;
    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $sub_status_select = $likeTable->select()
            ->from( $likeTableName , array ( 'like_id' ) )
            ->where( 'resource_type = ?' , $RESOURCE_TYPE )
            ->where( 'resource_id = ?' , $RESOURCE_ID )
            ->where( 'poster_type =?' , $viewer->getType() )
            ->where( 'poster_id =?' , $viewer->getIdentity() )
            ->limit( 1 ) ;
    return $sub_status_select->query()->fetchAll() ;
  }

  /**
   * Function for showing 'Number of Likes'.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @return number of likes
   */
  public function numberOfLike( $RESOURCE_TYPE , $RESOURCE_ID ) {

    //GET THE VIEWER (POSTER) AND RESOURCE.
    $poster = Engine_Api::_()->user()->getViewer() ;
    $resource = Engine_Api::_()->getItem( $RESOURCE_TYPE , $RESOURCE_ID ) ;
    return Engine_Api::_()->getDbtable( 'likes' , 'core' )->getLikeCount( $resource , $poster ) ;
  }

  /**
   * Function for showing 'Friend number of Likes'.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @return friend number of likes
   */
  public function friendNumberOfLike( $RESOURCE_TYPE , $RESOURCE_ID ) {

    $functioName = 'friendNumberOfLike' ;
    //THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF LIKES.
    $fetch_count = $this->userFriendNumberOflike( $RESOURCE_TYPE , $RESOURCE_ID , $functioName ) ;
    if ( !empty( $fetch_count ) ) {
      return $fetch_count[0]['like_count'] ;
    }
    else {
      return 0 ;
    }
  }

  /**
   * Function for showing 'User Friend Likes'.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @param Int limit
   * @return user friend like Object
   */
  public function userFriendLikes( $RESOURCE_TYPE , $RESOURCE_ID , $limit ) {

    $functioName = 'userFriendLikes' ;
    //THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF LIKES.
    $like_fetch_record = $this->userFriendNumberOflike( $RESOURCE_TYPE , $RESOURCE_ID , $functioName , $limit ) ;
    $friend_likes_obj = NULL ;
    if ( !empty( $like_fetch_record ) ) {
      foreach ( $like_fetch_record as $fetch_friend_id ) {
        $friend_likes_obj[] = Engine_Api::_()->getItem( 'user' , $fetch_friend_id['poster_id'] ) ;
      }
    }
    return $friend_likes_obj ;
  }

  /**
   * fetching the total number of user "Likes"
   *
   * @param Stirng $resourcetype_string
   * @param Int $profile_owner_id
   * @return Total result
   */
  public function user_likes( $profile_owner_id , $resourcetype_string = null ) {

    //FETCHING PROFILE USER ID.
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $select = $coreLikeTable->select()
            ->from( $coreLikeTableName , array ( 'COUNT(' . $coreLikeTableName . '.like_id) AS like_count' ) )
            ->where( $coreLikeTableName . '.poster_id = ?' , $profile_owner_id ) ;

    //CHECK FOR RESOURCE TYPE STRING.
    if ( !empty( $resourcetype_string ) ) {
      $select->where( "`{$coreLikeTableName}`.resource_type IN ($resourcetype_string)" ) ;
    }
    $result_totalcount = $select->query()->fetchColumn( 0 ) ;
    return $result_totalcount ;
  }

  /**
   * FETCHING THE TOTAL NO. OF MUTUAL LIKES OF A PROFILE USER WITH CURRENTLY VIWEING USER.
   *
   * @param Stirng $resourcetype_string
   * @param Int $profile_owner_id
   * @return Total Mutual Likes
   */
  public function mutual_likes( $profile_owner_id , $resourcetype_string = null ) {

    //GET TEH USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $like_select = $coreLikeTable->select()
            ->from( $coreLikeTableName , array ( 'COUNT(' . $coreLikeTableName . '.resource_id) AS like_count' ) )
            ->join( $coreLikeTableName , "`{$coreLikeTableName}`.resource_id=`{$coreLikeTableName}_2`.resource_id  " , null ) ;

    //CHECK FOR RESOURCE TYPE STRING.
    if ( !empty( $resourcetype_string ) ) {
      $like_select->where( "`{$coreLikeTableName}`.resource_type IN ($resourcetype_string)" ) ;
    }

    $like_select->where( $coreLikeTableName . '.poster_id = ?' , $profile_owner_id )
        ->where( "`{$coreLikeTableName}_2`.poster_id = ?" , $user_id )
        ->where( "`{$coreLikeTableName}_2`.resource_type = `{$coreLikeTableName}`.resource_type" ) ;
    $result_totalmutualcount = $like_select->query()->fetchColumn( 0 ) ;
    return $result_totalmutualcount ;
  }

  /**
   * FETCHING THE 3 RANDOM LIKES FOR USER.
   *
   * @param Int $page
   * @param Int $limit
   * @param Int $profile_owner_id
   * @param int $mutual
   * @param String $resourcetype_string
   * @return like array
   */
  public function userLikesInfo( $page , $limit , $profile_owner_id , $mutual , $resourcetype_string = null ) {

    //GET THE USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $like_select = $coreLikeTable->select()->from( $coreLikeTableName , array ( 'resource_type' , 'resource_id' ) ) ;

    if ( !empty( $mutual ) ) {
      $like_select->join( $coreLikeTableName , "`{$coreLikeTableName}`.resource_id=`{$coreLikeTableName}_2`.resource_id " , null ) ;
      if ( !empty( $resourcetype_string ) ) {
        $like_select->where( "`{$coreLikeTableName}`.resource_type IN ($resourcetype_string)" ) ;
      }
      $like_select->where( $coreLikeTableName . '.poster_id = ?' , $profile_owner_id )
          ->where( "`{$coreLikeTableName}_2`.poster_id = ?" , $user_id )
          ->where( "`{$coreLikeTableName}_2`.resource_type = `{$coreLikeTableName}`.resource_type" ) ;
    }
    else {
      $like_select->where( $coreLikeTableName . '.poster_id = ?' , $profile_owner_id ) ;
      if ( !empty( $resourcetype_string ) ) {
        $like_select->where( "`{$coreLikeTableName}`.resource_type IN ($resourcetype_string)" ) ;
      }
    }

    $paginator = Zend_Paginator::factory( $like_select ) ;
    $paginator->setCurrentPageNumber( $page ) ;
    $paginator->setItemCountPerPage( $limit ) ;
    $like_array = array ( ) ;

    foreach ( $paginator as $value ) {
      $like_array[] = $value->toarray() ;
    }
    $likes_array = $this->likeMixObject( $like_array ) ;
    return $likes_array ;
  }

  /**
   * Return the object for "mix widget".
   *
   * @param Array $like_array
   * @param Int $page
   * @param Int $activetab
   * @param Int $LIMIT
   * @return Object Array
   */
  public function likeMixObject( $like_array , $page = 1 , $activetab = null , $LIMIT = 10 ) {

    $return_object_array = array ( ) ;
    foreach ( $like_array as $fetch_record ) {

      //CHECK FOR THE CONTENT IS ACCORDING TO CONDITION.
      $getModCondition = $this->getModCondition(  $fetch_record['resource_type'] ) ;

      $moduleTable = Engine_Api::_()->getItemTable(  $fetch_record['resource_type'] ) ;
			$primary = current($moduleTable->info("primary"));
      $moduleTableName = $moduleTable->info( 'name' ) ;
      $selectModuleMixContent = $moduleTable->select()->where( $primary . " =? " , $fetch_record['resource_id']);

      //HERE CHECK THIS CONTENT ARE ACCORDING TO PRIVACY.
      if ( !empty( $getModCondition ) ) {
        foreach ( $getModCondition as $condition => $value ) {
          $selectModuleMixContent->where( $moduleTableName . '.' . $condition . ' =?' , $value ) ;
        }
      }

      $resultObject = $moduleTable->fetchAll( $selectModuleMixContent ) ;
      $count = @COUNT( $resultObject ) ;

      if ( !empty( $count ) ) {
        $mix_object['type'] = $fetch_record['resource_type'] ;
		//$mix_object['primary']= current($moduleTable->info("primary"));
        if ( !empty( $fetch_record['poster_id'] ) ) {
          $mix_object['poster_id'] = $fetch_record['poster_id'] ;
        }
        $mix_object['object'] = $resultObject ;
        $mix_object['limit'] = Engine_Api::_()->getApi('like', 'seaocore')->likeCount( $fetch_record['resource_type'] , $fetch_record['resource_id']);
        $return_object_array[] = $mix_object ;
      }
    }
    return $return_object_array ;
  }

  /**
   * FETCHING TOTAL MIX-INFO COUNT FROM. FETCHING MODULES MIX-INFO FROM CORE LIKE TABLE.
   *
   * @param String $RESOURCE_TYPE_STRING
   * @param Int $duration_tab
   * @param Int $orderby
   * @param Int $page
   * @param Int $startindex
   * @param Int $activetab
   * @param Int $LIMIT
   * @return all duration result
   */
  public function likeMixInfo( $RESOURCE_TYPE_STRING , $LIMIT , $duration_tab , $orderby = 'like_count DESC' , $page = 1 , $startindex = 0 , $activetab = null ) {

    //CHECK THE VALIDATION FOR DEFAULT DURATION
    if ( !empty( $duration_tab ) ) {
      //CHECK THE ALL DURATION IS ONE OR NOT
      if ( $duration_tab != 1 ) {
        //CONDITION IS CHECK WHEN DURATION TAB VALUES IS  NOT OVERALL
        if ( $duration_tab != 'overall' ) {
          $duration_tab1 = $duration_tab - 1 ;
          //DATE IS CONVERT IN TO SECONDS
          $duration_tab1_scconds = $duration_tab1 * 24 * 60 * 60 ;
          $seconds_enddate = time( ) ;
          $seconds_startdate = $seconds_enddate - $duration_tab1_scconds ;
          //START DATE CAN GET FROM THE DATE FUNCTION AND MINUS RESULT
          $start_date = date( 'Y-m-d' , $seconds_startdate ) ;
        }
      }
      else {
        $start_date = date( 'Y-m-d' ) ;
      }
    }

    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;

    $like_select = $coreLikeTable->select() ;
    if ( $orderby != 'like_id DESC' ) {
      $like_select->from( $coreLikeTableName , array ( 'resource_type' , 'resource_id' , 'COUNT(' . $coreLikeTableName . '.like_id) AS like_count' , 'like_id' ) ) ;
    }
    else {
      $like_select->from( $coreLikeTableName , array ( 'resource_type' , 'resource_id' , 'like_id' , 'poster_id' ) ) ;
    }
    if ( !empty( $duration_tab ) && $duration_tab != 'overall' ) {
      $like_select->where( "$coreLikeTableName.creation_date >= ?" , $start_date ) ;
    }
    $like_select->where( "`{$coreLikeTableName}`.resource_type IN ({$RESOURCE_TYPE_STRING})" ) ;
    //if ( $orderby == 'like_id DESC' ) {
    $like_select->group( 'resource_type' )->group( 'resource_id' ) ;
    //}
    $like_select->limit( $LIMIT )->order( $orderby ) ;
    return $like_select->query()->fetchAll() ;
    //return $like_array;
  }

  /**
   * This is show My Likes.
   *
   * @param Int $userid
   * @param String $resource_type
   * @param Int $page
   * @param Int $limit
   * @return Paginator
   */
  public function likeMylikes( $userid , $resource_type , $page , $limit ) {

    $getModCondition = $this->getModCondition( $resource_type , true ) ;

		$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
		$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $resource_type));
		$resourceTypeId = $sub_status_select->resource_id;

		$moduleName = $resource_type;

    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $membershipTable = Engine_Api::_()->getDbtable( 'membership' , 'user' ) ;
    $membershipTableName = $membershipTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( $moduleName ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
            ->from( $coreLikeTableName , array ( 'resource_type' , 'resource_id' ) )
            ->join( $moduleTableName , "`{$coreLikeTableName}`.resource_id=`{$moduleTableName}`.`{$resourceTypeId}`" ) ;

    //CHECK FOR USER ID.
    if ( empty( $userid ) ) {
      $userid = Engine_Api::_()->user()->getViewer()->getIdentity() ;
      $like_select->joinInner( $membershipTableName , "$membershipTableName . resource_id = $coreLikeTableName . poster_id" , NULL )
          ->where( $membershipTableName . '.user_id = ?' , $userid )
          ->where( $membershipTableName . '.active = ?' , 1 )
          ->where( $coreLikeTableName . '.poster_id <> ?' , $userid )
          ->group( $coreLikeTableName . '.resource_id' ) ;
    }
    else {
      $like_select->where( $coreLikeTableName . '.poster_id = ?' , $userid ) ;
    }
    $like_select->where( $coreLikeTableName . '.resource_type = ?' , $moduleName ) ;

    //HERE CHECK THIS CONTENT ARE ACCORDING TO PRIVACY.
    if ( !empty( $getModCondition ) ) {
      foreach ( $getModCondition as $condition => $value ) {
        $like_select->where( $moduleTableName . '.' . $condition . ' =?' , $value ) ;
      }
    }

    $paginator = Zend_Paginator::factory( $like_select ) ;
    $paginator->setCurrentPageNumber( $page ) ;
    $paginator->setItemCountPerPage( $limit ) ;
    return $paginator ;
  }

  /**
   * This is show My Content Likes.
   *
   * @param String $resource_type
   * @param Int $page
   * @param Int $limit
   * @return Paginator
   */
  public function likeMycontent( $resource_type , $page , $limit ) {


    //GET THE OWNER ID.
    $owner_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
		$mixsettingstable = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' );
		$sub_status_select = $mixsettingstable->fetchRow(array('resource_type = ?'=> $resource_type));
		$resourceTypeId = $sub_status_select->resource_id;
		$metaDatainfo = Engine_Api::_()->getItemTable( $resource_type )->info('metadata');
		if (isset($metaDatainfo['owner_id'])) {
			$owner_id_field = 'owner_id' ;
		} else {
			$owner_id_field = 'user_id' ;
		}

    $coreLikeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $coreLikeTableName = $coreLikeTable->info( 'name' ) ;
    $moduleTable = Engine_Api::_()->getItemTable( $resource_type ) ;
    $moduleTableName = $moduleTable->info( 'name' ) ;

    $like_select = $moduleTable->select()
            ->setIntegrityCheck( false )
            ->from( $coreLikeTableName , array ( 'resource_type' , 'resource_id' , 'COUNT(' . $coreLikeTableName . '.resource_id) AS like_count' ) )
            ->join( $moduleTableName , "`{$coreLikeTableName}`.resource_id=`{$moduleTableName}`.`{$resourceTypeId}` AND `{$moduleTableName}`.$owner_id_field=$owner_id" )
            ->where( $coreLikeTableName . '.resource_type = ?' , $resource_type )
            ->group( 'resource_id' )
            ->order( 'like_count DESC' ) ;
    $paginator = Zend_Paginator::factory( $like_select ) ;
    $paginator->setCurrentPageNumber( $page ) ;
    $paginator->setItemCountPerPage( $limit ) ;
    return $paginator ;
  }

  /**
   * THIS FUNCTION FOR THE ALL WIDGET
   *
   * @param String $modules_name
   * @param String $table_name
   * @return Settings Results
   */
  public function allSettingsWidget( $modules_name) {

    $likesettingTable = Engine_Api::_()->getDbtable( 'settings' , 'sitelike' ) ;

    //SELECT THE CONTENT TYPE BLOGS FROM LIKE SETTING TABLE
    $select = $likesettingTable->select()->where( 'content_type = ?' , $modules_name ) ;

    //HERE WE CAN FETCH THE PARTICULAR ROW BY THE FETCH ROW FROM THE SETING TABLE
    return $likesettingTable->fetchRow( $select ) ;
  }

  /**
   * THIS FUNCTION SHOW DURATION OF ALL TAB
   *
   * @param Array $likesetting_array
   * @param String $modules_name
   * @param String $table_name
   * @return duration array
   */
  public function allDurationWidget( $likesetting_array , $modules_name , $resource_id ) {

    $return_array = array ( ) ;

    //HERE DURATION TAB DEFINE EMPTY AND TAB1 OR TAB2 SHOW TO ZERO IS BY DEFAULT
    $duration_tab = '' ;
    $tab1_data = 0 ;
    $tab2_data = 0 ;
    $tab_show_values = '' ;

    //CHECK THE TAB1_SHOW IF ONE OR NOT THEAN GO TO ELSE CASE
    if ( $likesetting_array['tab1_show'] == 1 ) {
      $duration_tab = $likesetting_array['tab1_duration'] ;
      $entries_tab = $likesetting_array['tab1_entries'] ;
      if ( !empty( $entries_tab ) ) {
        //FUNCTION CALL FROM CORE.PHP FILE
        $likes_result = Engine_Api::_()->sitelike()->tabLikeDuration( $duration_tab  , $modules_name , $entries_tab, $resource_id ) ;

        //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GET TOTAL FUNCTION
        if ( !count( $likes_result ) <= 0 ) {
          //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
          $return_array['paginator'] = $likes_result ;
          $tab1_data = 1 ;
          $tab_show_values = 1 ;
        }
      }
    }

    //CHECK THE TAB2_SHOW TO SHOW BYDEFAULT  WHEN THE CONDITION IS TRUE
    if ( $likesetting_array['tab2_show'] == 1 && $tab1_data == 0 ) {
      $duration_tab = $likesetting_array['tab2_duration'] ;
      $entries_tab = $likesetting_array['tab2_entries'] ;
      if ( !empty( $entries_tab ) ) {
        //FUNCTION IS CALLING
        $likes_result = Engine_Api::_()->sitelike()->tabLikeDuration( $duration_tab  , $modules_name , $entries_tab, $resource_id ) ;

        //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GETTOTALCOUNT() FUNCTION
        if ( !count( $likes_result ) <= 0 ) {
          //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
          $return_array['paginator'] = $likes_result ;
          $tab2_data = 1 ;
          $tab_show_values = 2 ;
        }
      }
    }

    //CHECK THE TAB3_SHOW TO SHOW BYDEFAULT  WHEN THE CONDITION IS TRUE
    if ( !empty( $likesetting_array['tab3_show'] ) && $tab2_data == 0 && $tab1_data == 0 ) {
      $duration_tab = $likesetting_array['tab3_duration'] ;
      $entries_tab = $likesetting_array['tab3_entries'] ;
      if ( !empty( $entries_tab ) ) {
        //FUNCTION IS CALLING
        $likes_result = Engine_Api::_()->sitelike()->tabLikeDuration( $duration_tab , $modules_name , $entries_tab, $resource_id ) ;

        //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GETTOTALCOUNT() FUNCTION
        if ( !count( $likes_result ) <= 0 ) {
          //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
          $return_array['paginator'] = $likes_result ;
          $tab_show_values = 3 ;
        }
      }
    }
    $return_array['tab_show_values'] = $tab_show_values ;
    return $return_array ;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE LIKES.
   *
   * @param String $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @param int $LIMIT
   * @return array of result
   */
  public function likePeopleWidget( $RESOURCE_TYPE , $RESOURCE_ID , $LIMIT ) {

    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $sub_status_select = $likeTable->select()
            ->from( $likeTableName , array ( 'poster_id' ) )
            ->where( 'resource_type = ?' , $RESOURCE_TYPE )
            ->where( 'resource_id = ?' , $RESOURCE_ID )
            ->order( 'like_id DESC' )
            ->limit( $LIMIT ) ;
    return $sub_status_select->query()->fetchAll() ;
  }

  /**
   * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
   *
   * @param String $call_status
   * @param String $resource_type
   * @param int $resource_id
   * @param Int $user_id
   * @param Int $search
   * @return results
   */
  public function friendPublicLike($params = array()) {

    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $membershipTable = Engine_Api::_()->getDbtable( 'membership' , 'user' ) ;
    $memberName = $membershipTable->info( 'name' ) ;
    $userTable = Engine_Api::_()->getItemTable( 'user' ) ;
    $userTableName = $userTable->info( 'name' ) ;

    $sub_status_select = $userTable->select()
            ->setIntegrityCheck( false )
            ->from( $likeTableName , array ( 'poster_id' ) )
            ->where( $likeTableName . '.resource_type = ?' , $params['resource_type'] )
            ->where( $likeTableName . '.resource_id = ?' , $params['resource_id'] )
            ->where( $likeTableName . '.poster_id != ?' , 0 )
            ->where( $userTableName . '.displayname LIKE ?' , '%' . $params['search'] . '%' )
            ->order( 'like_id DESC' ) ;

    if ( $params['action_name'] == 'friend' || $params['action_name'] == 'myfriendlikes' ) {
      $sub_status_select->joinInner( $memberName , "$memberName . resource_id = $likeTableName . poster_id" , NULL )
          ->joinInner( $userTableName , "$userTableName . user_id = $memberName . resource_id" )
          ->where( $memberName . '.user_id = ?' , $params['user_id'] )
          ->where( $memberName . '.active = ?' , 1 )
          ->where( $likeTableName . '.poster_id != ?' , $params['user_id'] ) ;
    }
    else if ( $params['action_name'] == 'public' ) {
      $sub_status_select->joinInner( $userTableName , "$userTableName . user_id = $likeTableName . poster_id" ) ;
    }
    return Zend_Paginator::factory( $sub_status_select ) ;
  }

  /**
   * This function show my liked items .
   *
   * @param Array $params
   * @return results
   */
  public function getMyLikedItems( $params=array ( ) ) {

    $likeTable = Engine_Api::_()->getDbtable( 'likes' , 'core' ) ;
    $select = $likeTable->select()
            ->where( 'poster_id = ?' , $params['poster_id'] )
            ->where( 'poster_type = ?' , $params['poster_type'] )
            ->order( !empty( $params['order'] ) ? 'creation_date ' . $params['order'] : 'creation_date DESC' ) ;

    if ( isset( $params['resource_type'] ) && !empty( $params['resource_type'] ) )
      $select->where( 'resource_type = ?' , $params['resource_type'] ) ;
    if ( isset( $params['resource_id'] ) && !empty( $params['resource_id'] ) )
      $select->where( 'resource_id = ?' , $params['resource_id'] ) ;
    if ( isset( $params['creation_date_less'] ) && !empty( $params['creation_date_less'] ) )
      $select->where( 'creation_date < ?' , $params['creation_date_less'] ) ;
    return $likeTable->fetchAll( $select ) ;
  }

  /**
   * This function set activity feed.
   *
   * @param Object $subject
   * @param Object $subject
   */
  public function setLikeFeed( $subject , $object ) {

    $api = Engine_Api::_()->getDbtable( 'actions' , 'activity' ) ;
    if ( false ) {
//     $likeFeedEnableOnly = array("list_listing", "sitepage_page");
//    if (in_array($object->getType(), $likeFeedEnableOnly)) {

      $content = null ;
      $getMyLikedItems = Engine_Api::_()->sitelike()->getMyLikedItems( array ( "poster_type" => $subject->getType() , "poster_id" => $subject->getIdentity() , "resource_type" => $object->getType() ) ) ;
      $count_other_likedItems = count( $getMyLikedItems ) - 1 ;
      if ( $count_other_likedItems > 1 ) {
        $item_name = $object->getType() ;
        switch ( $object->getType() ) {
          case "list_listing":
            $item_name = "listings" ;
            break ;
          case "sitepage_page":
            $item_name = "pages" ;
            break ;
          case "sitebusiness_business":
            $item_name = "businesses" ;
            break ;
        }
        $view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;
        $url = $view->url( array ( 'module' => 'sitelike' , 'controller' => 'index' , 'action' => 'get-tool-tip' , 'poster_id' => $subject->getIdentity() , 'poster_type' => $subject->getType() , 'resource_id' => $object->getIdentity() , 'resource_type' => $object->getType() ) , 'default' , true ) ;
        $content = " and <a class='feed_item_username' href='javascript:void(0);' onclick='javascript:Smoothbox.open (\"{$url}\");' > $count_other_likedItems other $item_name</a>." ;
      }
      else if ( $count_other_likedItems == 1 ) {
        $resource = Engine_Api::_()->getItem( $getMyLikedItems[1]->resource_type , $getMyLikedItems[1]->resource_id ) ;
        $content = " and <a class='feed_item_username' href=" . $resource->getHref() . "> " . $resource->getTitle() . " </a>." ;
      }
      $action = $api->fetchRow( array ( 'type =?' => "sitelike_count_" . $object->getType() , "subject_type =?" => $subject->getType() , "subject_id =?" => $subject->getIdentity() , "object_type =? " => $object->getType() , "object_id = ?" => $object->getIdentity() ) ) ;
      if ( !empty( $action ) ) {
        $action->deleteItem() ;
        $action->delete() ;
      }
    }

    $action = $api->fetchRow( array ( 'type =?' => "like_" . $object->getType() , "subject_type =?" => $subject->getType() , "subject_id =?" => $subject->getIdentity() , "object_type =? " => $object->getType() , "object_id = ?" => $object->getIdentity() ) ) ;

    if ( !empty( $action ) ) {
      $action->deleteItem() ;
      $action->delete() ;
    }
    if ($object->getType() == 'sitereview_listing') {
			$sitereviewTable = Engine_Api::_()->getDbtable( 'listings' , 'sitereview' );
			$listingtype_id = $sitereviewTable->select()
							->from($sitereviewTable->info('name'), "listingtype_id")
							->where('listing_id  = ?', $object->getIdentity())
							->query()->fetchColumn();
			$title_singular = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypeColumn($listingtype_id, 'title_singular');
			
			$listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingtype_id)->toarray(); 
			
			$action = $api->addActivity( $subject , $object , "like_" . $object->getType(), "", array('listing_name' => strtolower($title_singular) . " " . strtolower($listingType['language_phrases']['text_listing']))) ;
    } else {
			$action = $api->addActivity( $subject , $object , "like_" . $object->getType() ) ;
    }
    if ( $action ) {
      $api->attachActivity( $action , $object ) ;
    }
    if ( false ) {
      if ( !empty( $count_other_likedItems ) )
        $action = $api->addActivity( $subject , $object , "sitelike_count_" . $object->getType() , null , array ( 'content' => $content ) ) ;
    }
//    }
  }

  /**
   * This function remove activity feed.
   *
   * @param Object $subject
   * @param Object $subject
   */
  public function removeLikeFeed( $subject , $object ) {

    $api = Engine_Api::_()->getDbtable( 'actions' , 'activity' ) ;
    if ( false ) {
      $action = $api->fetchRow( array ( 'type =?' => "sitelike_count_" . $object->getType() , "subject_type =?" => $subject->getType() , "subject_id =?" => $subject->getIdentity() , "object_type =? " => $object->getType() , "object_id = ?" => $object->getIdentity() ) ) ;
      if ( !empty( $action ) ) {
        $action->deleteItem() ;
        $action->delete() ;
      }
    }
    $action = $api->fetchRow( array ( 'type =?' => "like_" . $object->getType() , "subject_type =?" => $subject->getType() , "subject_id =?" => $subject->getIdentity() , "object_type =? " => $object->getType() , "object_id = ?" => $object->getIdentity() ) ) ;
    if ( !empty( $action ) ) {
      $action->deleteItem() ;
      $action->delete() ;
    }
  }


  /**
   * THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF LIKES.
   *
   * @param String $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @param String $functioName
   * @param Int $limit
   * @return count results
   */
  public function userFriendNumberOflike( $RESOURCE_TYPE , $RESOURCE_ID , $functioName , $limit = null ) {

    //GET THE USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
    $likeTableName = $likeTable->info( 'name' ) ;
    $membership_table = Engine_Api::_()->getDbtable( 'membership' , 'user' ) ;
    $member_name = $membership_table->info( 'name' ) ;

    $count_select = $likeTable->select() ;
    if ( $functioName == 'friendNumberOfLike' ) {
      $count_select->from( $likeTableName , array ( 'COUNT(' . $likeTableName . '.like_id) AS like_count' ) ) ;
    }
    elseif ( $functioName == 'userFriendLikes' ) {
      $count_select->from( $likeTableName , array ( 'poster_id' ) ) ;
    }
    $count_select->joinInner( $member_name , "$member_name . resource_id = $likeTableName . poster_id" , NULL )
        ->where( $member_name . '.user_id = ?' , $user_id )
        ->where( $member_name . '.active = ?' , 1 )
        ->where( $likeTableName . '.resource_type = ?' , $RESOURCE_TYPE )
        ->where( $likeTableName . '.resource_id = ?' , $RESOURCE_ID )
        ->where( $likeTableName . '.poster_id != ?' , $user_id )
        ->where( $likeTableName . '.poster_id != ?' , 0 ) ;

    if ( $functioName == 'friendNumberOfLike' ) {
      $count_select->group( $likeTableName . '.resource_id' ) ;
    }
    elseif ( $functioName == 'userFriendLikes' ) {
      $count_select->order( $likeTableName . '.like_id DESC' )
          ->limit( $limit ) ;
    }
    $fetch_count = $count_select->query()->fetchAll() ;
    return $fetch_count ;
  }

  /**
   * check for CDN concept
   *
   * @return path of cdn
   */
  public function getCdnPath() {

    //GET THE STROGE MODULE NAD VERSION OF STROGE MODULE.
    $storagemodule = Engine_Api::_()->getDbtable( 'modules' , 'core' )->getModule( 'storage' ) ;
    $storageversion = $storagemodule->version ;

    $db = Engine_Db_Table::getDefaultAdapter() ;
    $type_array = $db->query( "SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'" )->fetch() ;
    $cdn_path = "" ;

    if ( $storageversion >= '4.1.6' && !empty( $type_array ) ) {

      $storageServiceTypeTable = Engine_Api::_()->getDbtable( 'serviceTypes' , 'storage' ) ;
      $storageServiceTypeTableName = $storageServiceTypeTable->info( 'name' ) ;

      $storageServiceTable = Engine_Api::_()->getDbtable( 'services' , 'storage' ) ;
      $storageServiceTableName = $storageServiceTable->info( 'name' ) ;

      $select = $storageServiceTypeTable->select()
              ->setIntegrityCheck( false )
              ->from( $storageServiceTypeTableName , array ( '' ) )
              ->join( $storageServiceTableName , "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id" , array ( 'enabled' , 'config' , 'default' ) )
              ->where( "$storageServiceTypeTableName.plugin != ?" , "Storage_Service_Local" )
              ->where( "$storageServiceTypeTableName.enabled = ?" , 1 )
              ->limit( 1 ) ;

      $storageCheck = $storageServiceTypeTable->fetchRow( $select ) ;
      if ( !empty( $storageCheck ) ) {
        if ( $storageCheck->enabled == 1 && $storageCheck->default == 1 ) {
          $config = Zend_Json::decode( $storageCheck->config ) ;
          $config_baseUrl = $config['baseUrl'] ;
          if ( !preg_match( '/http:\/\//' , $config_baseUrl ) && !preg_match( '/https:\/\//' , $config_baseUrl ) ) {
            $cdn_path.= "http://" . $config_baseUrl ;
          }
          else {
            $cdn_path.= $config_baseUrl ;
          }
        }
      }
    }
    return $cdn_path ;
  }

  public function getModCondition( $modName , $actionFlag = true ) {
    if ( empty( $actionFlag ) ) {
      return ;
    }
    $getModCondition = array ( ) ;
    switch ( $modName ) {
      case 'album':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'blog':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'classified':
        $getModCondition = array ( 'search' => 1 , 'closed' => 0 ) ;
        break ;
			case 'poll':
				$getPollVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('poll')->version;
				if( $getPollVersion < '4.1.5' ) {
					$getModCondition = array('search' => 1, 'is_closed' => 0);
				} else {
					$getModCondition = array('search' => 1, 'closed' => 0);
				}
			break;
      case 'recipe':
        $getModCondition = array ( 'search' => 1 , 'closed' => 0 , 'draft' => 1 ) ;
        break ;
      case 'document':
        $getModCondition = array ( 'draft' => 0 , 'approved' => 1 , 'status' => 1 ) ;
        break ;
      case 'forum':
      case 'forum_topic' :
        $getModCondition = array ( 'closed' => 0 ) ;
        break ;
      case 'group':
        $getModCondition = array ( 'search' => 1 , 'invite' => 1 ) ;
        break ;
      case 'event':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'music':
      case 'music_playlist':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'list':
      case 'list_listing':
        $getModCondition = array ( 'approved' => 1 , 'closed' => 0 , 'draft' => 1 ) ;
        break ;
      case 'video':
        $getModCondition = array ( 'search' => 1 , 'status' => 1 ) ;
        break ;
      case 'sitepage':
      case 'sitepage_page' :
        $getModCondition = array ( 'closed' => 0 , 'approved' => 1 , 'declined' => 0 , 'draft' => 1 ) ;
        break ;
      case 'sitepagedocument':
      case 'sitepagedocument_document' :
        $getModCondition = array ( 'draft' => 0 , 'approved' => 1 , 'status' => 1 , 'search' => 1 , ) ;
        break ;
      case 'sitepagepoll':
      case 'sitepagepoll_poll':
        $getModCondition = array ( 'search' => 1 , 'approved' => 1 ) ;
        break ;
      case 'sitepagevideo':
      case 'sitepagevideo_video':
        $getModCondition = array ( 'search' => 1 , 'status' => 1 ) ;
        break ;
      case 'sitepageevent':
      case 'sitepageevent_event':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'sitepagenote':
      case 'sitepagenote_note':
        $getModCondition = array ( 'search' => 1 , 'draft' => 0 ) ;
        break ;
      case 'sitepagealbum':
      case 'sitepage_album':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
      case 'sitepagemusic':
      case 'sitepagemusic_playlist':
        $getModCondition = array ( 'search' => 1 ) ;
        break ;
			case 'siteestore_product':
				$getModCondition = array ('status' => 1) ;
			break ;
//       default:
//         $getModCondition = array('search' => 1);
//       break;
    }
    return $getModCondition ;
  }

  /**
   * Plugin which return the error, if Siteadmin not using correct version for the plugin.
   *
   */
  public function isModulesSupport() {
    $modArray = array(
        'suggestion' => '4.2.3',
    );
    $finalModules = array();
    foreach ($modArray as $key => $value) {
      $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($key);
      if (!empty($isModEnabled)) {
        $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($key);
        $isModSupport = strcasecmp($getModVersion->version, $value);
        if ($isModSupport < 0) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }
    return $finalModules;
  }

  public function getTotalCount($resourcetype, $poster_id = false, $urlAction = false) {

    if (empty($resourcetype)) {
      return;
    }
    $photo_table_name = Engine_Api::_()->getItemTable($resourcetype)->info('name');
    $changeResourceType = '';
    $likeTable = Engine_Api::_()->getDbtable('likes', 'core');
    $likeTableName = $likeTable->info('name');
    $count = 0;
    $getModCondition = array();
    $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'sitelike');
    switch ($resourcetype) {
      case 'list_photo':
        $changeResourceType = 'list_listing';
        $getModCondition = array('approved' => 1, 'closed' => 0, 'draft' => 1, 'search' => 1);
        break;
      case 'sitepagenote_photo':
        $changeResourceType = 'sitepagenote_note';
        $getModCondition = array('search' => 1, 'draft' => 0);
        break;
      case 'sitebusinessnote_photo':
        $changeResourceType = 'sitebusinessnote_note';
        $getModCondition = array('search' => 1, 'draft' => 0);
        break;
      case 'recipe_photo':
        $changeResourceType = 'recipe';
        $getModCondition = array('approved' => 1, 'closed' => 0, 'draft' => 1, 'search' => 1);
        break;
      case 'sitepage_photo':
        $changeResourceType = 'sitepage_page';
        $getModCondition = array('closed' => 0, 'approved' => 1, 'declined' => 0, 'draft' => 1);
        break;
      case 'sitebusiness_photo':
        $changeResourceType = 'sitebusiness_business';
        $getModCondition = array('closed' => 0, 'approved' => 1, 'declined' => 0, 'draft' => 1);
        break;
      case 'group_photo':
        $changeResourceType = 'group';
        $getModCondition = array('search' => 1, 'invite' => 1);
        break;
      case 'event_photo':
        $changeResourceType = 'event';
        $getModCondition = array('search' => 1);
        break;
      case 'album_photo':
        $changeResourceType = 'album';
        $getModCondition = array('search' => 1);
        break;
    }
    $getRow = $mixsettingstable->fetchRow(array('resource_type = ?' => $changeResourceType));
    $resourceId = $getRow->resource_id;
    $resourceType = $getRow->resource_type;
    $main_table_name = Engine_Api::_()->getItemTable($resourceType)->info('name');

    $select = $likeTable->select()
            ->from($likeTableName, array('count( * ) AS count'));

    if ($urlAction != 'myfriendslike') {
      $select = $select->join($photo_table_name, "($photo_table_name.photo_id = $likeTableName.resource_id AND $likeTableName.resource_type = '$resourcetype')", NULL);
    } else {
      $select = $select->join($photo_table_name, "( $likeTableName.resource_type = '$resourcetype')", NULL);
    }
    $select = $select->join("$main_table_name", "$main_table_name.$resourceId = $photo_table_name.$resourceId", NULL)->group($likeTableName . '.resource_id');


    if (!empty($poster_id) && ($urlAction != 'myfriendslike' && $urlAction != 'mycontent')) {
      $select = $select->where($likeTableName . ".poster_id = ?", $poster_id);
    }

    if (!empty($poster_id) && $urlAction == 'mycontent') {
      if ($resourcetype == 'album_photo') {
        $select = $select->where($photo_table_name . ".owner_id = ?", $poster_id);
      } else {
        $select = $select->where($photo_table_name . ".user_id = ?", $poster_id);
      }
    }

    if ($urlAction == 'myfriendslike') {
      //GET THE RESOURCE TYPE AND RESOURCE ID AND VIEWER.
      $user = Engine_Api::_()->user()->getViewer();
      //GET THE FRIEND OF LOGIN USER.
      $user_id = $user->membership()->getMembershipsOfIds();
      $select = $select->where($likeTableName . '.poster_id IN (?)', (array) $user_id);
      //$select = $select->where($likeTableName . '.poster_id IN (?)', (array) $user_id); 
    }

    //HERE CHECK THIS CONTENT ARE ACCORDING TO PRIVACY.
    if (!empty($getModCondition)) {
      foreach ($getModCondition as $condition => $value) {
        $select = $select->where($main_table_name . '.' . $condition . ' =?', $value);
      }
    }

    $results = $likeTable->fetchALL($select);
    $count = count($results);

    return $count;
  }
}
?>