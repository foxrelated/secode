<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: indexController.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_IndexController extends Core_Controller_Action_Standard {

  protected $_navigation ;

  public function browseAction() {

    //GET THE SETTINGS.
    $likeBrowseShow = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.browse.auth' ) ;

    //CHECK THE VALID USERS.
    if ( empty( $likeBrowseShow ) ) {
      if ( !$this->_helper->requireUser()->isValid() )
        return ;
    }

    $coreVersion = Engine_Api::_()->getDbtable( 'modules' , 'core' )->getModule('core')->version ;
    if ( $coreVersion < '4.1.0' ) {
      $this->_helper->content->render() ;
    } else {
      $this->_helper->content->setNoRender()->setEnabled() ;
    }
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitelike_main');
  }

  /**
   * This is for tab show values
   *
   * @return Tab show values
   */
  public function indexAction() {

    //HERE WE CAN GET THE USRE ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;

    //GET THE MODULE NAME AND PASS.
    $this->view->content_type = $resource_type = $this->_getParam( 'modules' , null ) ;

    //FETCHING THE REFERENCE PERTICUALR TYPE SEETING TABLE
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    $getResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getResults(array('resource_type' => $resource_type, 'column_name' => array('resource_id', 'module', 'resource_type')));
		$this->view->module_name = $getResults[0]['module'];
		$this->view->resource_type = $getResults[0]['resource_type'];
		$this->view->id = $resource_id = $getResults[0]['resource_id'];
		
    //SELECT THE CONTENT TYPE BLOGS FROM LIKE SETTING TABLE
    $likesettingTable = Engine_Api::_()->getDbtable( 'settings' , 'sitelike' ) ;
    $likesetting_result = '' ;
    $select = $likesettingTable->select() ;
    if ( !empty( $resource_type ) ) {
      $select->where( 'content_type = ?' , $resource_type ) ;
    }
    $likesetting_result = $likesettingTable->fetchRow( $select ) ;
    
    //HERE WE CAN CONVERT THE RESULT IN TO THE ARRAY FORM
    if ( !empty( $likesetting_result ) ) {
      $this->view->likesetting = $likesetting_array = $likesetting_result->toarray() ;
    }
    
    //HERE WE CAN GET THE VALUES OF TAB SHOW WHEN WE PASS FROM AJAX REQUEST
    $tab_show_values = $this->_getParam( 'tab_show' , null ) ;

    //HERE DURATION TAB DEFINE EMPTY
    $duration_tab = '' ;

    //CONDITION IS CHECK FOR TAB SHOW VALUES IS ONE THROUGH FROM THE AJAX REQUEST
    if ( $tab_show_values == 1 ) {
      //HERE WE CAN GET THE VALUE OF TAB DURATION AND TAB ENTRIES FROM ARRAY
      $duration_tab = $likesetting_array['tab1_duration'] ;
      $entries_tab = $likesetting_array['tab1_entries'] ;
    }
    //CONDITION IS CHECK FOR TAB SHOW VALUES IS TWO THROUGH FROM THE AJAX REQUEST
    else if ( $tab_show_values == 2 ) {
      //HERE WE CAN GET THE VALUE OF TAB DURATION AND TAB ENTRIES FROM ARRAY
      $duration_tab = $likesetting_array['tab2_duration'] ;
      $entries_tab = $likesetting_array['tab2_entries'] ;
    }
    //CONDITION IS CHECK FOR TAB SHOW VALUES IS THREE  THROUGH FROM THE AJAX REQUEST
    else if ( $tab_show_values == 3 ) {
      //HERE WE CAN GET THE VALUE OF TAB DURATION AND TAB ENTRIES FROM ARRAY
      $duration_tab = $likesetting_array['tab3_duration'] ;
      $entries_tab = $likesetting_array['tab3_entries'] ;
    }

    $this->view->active_tab = $tab_show_values ;
    $this->view->duration = $likesetting_result['tab'.$tab_show_values.'_duration'];
    if ( !empty( $entries_tab ) ) {
      //FUNCTION IS CALLING FOR TAB LIKE DURATION.
      $likes_result = Engine_Api::_()->sitelike()->tabLikeDuration( $duration_tab , $resource_type , $entries_tab, $resource_id ) ;
    }

    if ( !empty( $likes_result ) ) {
      $this->view->paginator = $likes_result ;
    }
    $this->view->ajaxrequest = '1' ;
  }

  //Function for showing how many likes by users.
  public function globallikesAction() {

    //GET THE VIEWER.
    $viewer = Engine_Api::_()->user()->getViewer() ;
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity() ;
    if (empty($viewer_id))
      return ;
      
    //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND LIKE ID.
    $this->view->resource_id = $resource_id = $this->_getParam( 'resource_id' ) ;
    $this->view->resource_type = $resource_type = $this->_getParam( 'resource_type' ) ;
    $like_id = $this->_getParam( 'like_id' ) ;
    $status = $this->_getParam( 'smoothbox' , 1 ) ;
    $this->view->status = true ;

    //GET THE LIKE BUTTON SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //GET THE RESOURCE.
    $resource = Engine_Api::_()->getItem( $resource_type , $resource_id ) ;
    
    if($resource_type == 'sitepage_page')
    $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;

    //CHECK THE LIKE ID.
    if ( empty( $like_id ) ) {

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type , $resource_id);

      //CHECK THE THE ITEM IS LIKED OR NOT.
      if ( empty( $hasLike[0]['like_id'] ) ) {

        $likeTable = Engine_Api::_()->getItemTable( 'core_like' ) ;
        $notify_table = Engine_Api::_()->getDbtable( 'notifications' , 'activity' ) ;
        $db = $likeTable->getAdapter() ;
        $db->beginTransaction() ;
        try {

          if ( !empty( $resource ) ) {

						//START PAGE MEMBER PLUGIN WORK.
						if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.2.9p3') {
							Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Join');
							Engine_Api::_()->sitepage()->itemCommentLike($resource, 'sitepage_contentlike');
						} elseif($resource_type == 'siteevent_event') {
							Engine_Api::_()->siteevent()->itemCommentLike($resource, 'siteevent_contentlike', '', 'like');
						}
						//END PAGE MEMBER PLUGIN WORK.
					
            $like_id = $likeTable->addLike( $resource , $viewer )->like_id ;
            Engine_Api::_()->sitelike()->setLikeFeed( $viewer , $resource ) ;
          }
          
          //START NOTIFICATION WORK.
          if ( $resource_type == 'forum_topic' ) {
            $getOwnerId = Engine_Api::_()->getItem( $resource_type , $resource_id )->user_id ;
            $label = '{"label":"forum topic"}' ;
            $object_type = $resource_type ;
          }
          else if ( $resource_type == 'user' ) {
            $getOwnerId = $resource_id ;
            $label = '{"label":"profile"}' ;
            $object_type = 'user' ;
          }
          else {
            if ( $resource_type == 'album_photo' ) {
              $label = '{"label":"photo"}' ;
            }
            else if ( $resource_type == 'group_photo' ) {
              $label = '{"label":"group photo"}' ;
            }
            else if ( $resource_type == 'sitepageevent_event' ) {
              $label = '{"label":"page event"}' ;
            }
            else if ( $resource_type == 'sitepage_page' ) {
              $label = '{"label":"page"}' ;
            }
            else {
              $label = '{"label":"' . $resource->getShortType() . '"}' ;
            }
            if ( !strstr($resource_type, 'siteestore_product') ) {
							$getOwnerId = Engine_Api::_()->getItem( $resource_type , $resource_id )->getOwner()->user_id ;
            }
            $object_type = $resource_type ;
          }
          
          if ( !empty( $getOwnerId ) && $getOwnerId != $viewer->getIdentity() ) {
            $notifyData = $notify_table->createRow() ;
            $notifyData->user_id = $getOwnerId ;
            $notifyData->subject_type = $viewer->getType() ;
            $notifyData->subject_id = $viewer->getIdentity() ;
            $notifyData->object_type = $object_type ;
            $notifyData->object_id = $resource_id ;
            $notifyData->type = 'liked' ;
            $notifyData->params = $label;
            $notifyData->date = date( 'Y-m-d h:i:s' , time() ) ;
            $notifyData->save() ;
          }
          //END NOTIFICATION WORK.
          
          //PASS THE LIKE ID VALUE.
          $this->view->like_id = $like_id ;
          $db->commit() ;
        }
        catch ( Exception $e ) {
          $db->rollBack() ;
          throw $e ;
        }
        $like_msg = Zend_Registry::get( 'Zend_Translate' )->_( 'Successfully Liked.' ) ;
      }
      else {
        $this->view->like_id = $hasLike[0]['like_id'] ;
      }
    }
    else {
    
			//START PAGE MEMBER PLUGIN WORK
			if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.2.9p3') {
				Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Leave');
			}
			//END PAGE MEMBER PLUGIN WORK
				
      //START UNLIKE WORK.
      //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNLIKE
      if ( !empty( $resource ) && isset( $resource->like_count ) ) {
        $resource->like_count-- ;
        $resource->save() ;
      }
      $contentTable = Engine_Api::_()->getDbTable( 'likes' , 'core' )->delete( array ( 'like_id =?' => $like_id ) ) ;
      //END UNLIKE WORK.

			//START DELETE NOTIFICATION
      Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?'  => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
			//END DELETE NOTIFICATION
			
      //REMOVE LIKE ACTIVITY FEED.
      Engine_Api::_()->sitelike()->removeLikeFeed( $viewer , $resource ) ;
      $like_msg = Zend_Registry::get( 'Zend_Translate' )->_( 'Successfully Unliked.' ) ;
    }
    if ( empty( $status ) ) {
      $this->_forward( 'success' , 'utility' , 'core' , array (
        'smoothboxClose' => true ,
        'parentRefresh' => true ,
        'messages' => array ( $like_msg )
          )
      ) ;
    }
    //HERE THE CONTENT TYPE MEANS MODULE NAME
    $num_of_contenttype = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($resource_type , $resource_id);
    $this->view->num_of_like = $this->view->translate( array ( '%s like' , '%s likes' , $num_of_contenttype ) , $this->view->locale()->toNumber( $num_of_contenttype ) ) ;
  }

  //This is for like by my friend.
  public function myfriendlikesAction() {

    //GET THE VALUE OF RESOURCE TYPE AND RESOURCE ID AND USER ID.
    $this->view->resource_type = $this->_getParam( 'resource_type' ) ;
    $this->view->resource_id = $this->_getParam( 'resource_id' ) ;
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $search = $this->_getParam( 'search' , '' ) ;
    if ( empty( $search ) ) {
      $this->view->search = $this->view->translate( '' ) ;
      $this->view->ajax_res = 0 ;
    } else {
      $this->view->search = $search ;
      $this->view->ajax_res = 1 ;
    }

    //HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NUMBER OF FRIEND.
    $fetch_sub = Engine_Api::_()->sitelike()->friendPublicLike(array('action_name' => 'myfriendlikes', 'resource_type' => $this->view->resource_type, 'resource_id' => $this->view->resource_id, 'user_id' => $user_id, 'search' => $search)) ;
    $check_object_result = count( $fetch_sub ) ;

    if ( !empty( $check_object_result ) ) {
      $this->view->user_obj = $fetch_sub ;
    } else {
      $this->view->no_result_msg = $this->view->translate( 'No results were found.' ) ;
    }
  }

  //FETCHING TOTAL LIKES OF USER'S CONTENTS.
  public function mycontentAction() {

    //GET THE LIKE BUTTON SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;
    $this->view->action_type = 'like_likelist' ;

    //CHECK FOR VIEWER ID.
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if ( (!$this->_helper->requireUser()->isValid()) || (empty( $this->view->viewer_id )) ) {
      return ;
    }

    //CONDITION FOR SHOWING "SUGGEST TO FRIEND" WILL SHOW OR NOT.
    if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'suggestion' )) {
      $this->view->show_link_permition = 1 ;
    }

    $this->view->message_link_auth = 1 ;
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitelike_main');
    $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->isajaxrequest = $isajax = $this->_getParam( 'isajax' , 0 ) ;
    $this->view->activetab = $row_module_name = $this->_getParam( 'resource_type' , '' ) ;

		$finalModuleArray = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();

    if ( empty( $isajax ) ) {
      foreach ( $finalModuleArray as $row_module_name ) {
				if ( !strstr($row_module_name, 'siteestore_product') ) {
					$paginator = Engine_Api::_()->sitelike()->likeMycontent( $row_module_name , $page , 10 ) ;
					if ( $paginator->count() > 0 ) {
						$this->view->appname = $row_module_name ;
						$this->view->activetab = $row_module_name ;
						break ;
					}
				}
      }
    }
    else {
			if ( !strstr($row_module_name, 'siteestore_product') ) {
				$paginator = Engine_Api::_()->sitelike()->likeMycontent( $row_module_name , $page , 10 ) ;
				$this->view->appname = $row_module_name ;
			}
    }
    $this->view->enablemodules = $finalModuleArray ;
    $this->view->paginator = $paginator ;
  }

  //Function for showing user which 'Liked' of perticular 'resource_id' & 'resource_type' and call for popup when click on see all page.
  public function likelistAction() {

    //GET THE VALUE OF RESOURCE TYPE AND RESOURCE ID AND USER ID AND PAGE.
    $like_user_str = 0 ;
    $this->view->resource_type = $resource_type = $this->_getParam( 'resource_type' ) ;
    $this->view->resource_id = $resource_id = $this->_getParam( 'resource_id' ) ;
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $this->view->page = $this->_getParam( 'page' , 1 ) ;
    $search = $this->_getParam( 'search' , '' ) ;
    $this->view->is_ajax = $this->_getParam( 'is_ajax' , 0 ) ;
    $call_status = $this->_getParam( 'call_status' ) ;

    if ( empty( $call_status ) && $resource_type == 'forum_topic' ) {
      $call_status = 'public' ;
    }
    
    $this->view->call_status = $call_status ;
    if ( empty( $search ) ) {
      $this->view->search = $search ;
    }
    
    //HERE FUNCTION CALL FROM THE CORE.PHP FILE OR THIS IS SHOW NO OF FRIEND
    $fetch_sub = Engine_Api::_()->sitelike()->friendPublicLike(array('action_name' => $call_status, 'resource_type' => $resource_type, 'resource_id' => $resource_id, 'user_id' => $user_id, 'search' => $search)) ;
    $check_object_result = count( $fetch_sub ) ;

    if ( !empty( $check_object_result ) ) {
      $this->view->user_obj = $fetch_sub ;
    } else {
      $this->view->no_result_msg = $this->view->translate( 'No results were found.' ) ;
    }

    $fetch_sub->setCurrentPageNumber( $this->view->page ) ;
    $fetch_sub->setItemCountPerPage( 10 ) ;

    //NUMBER OF FRIEND WHICH LIKED THIS CONTENT.
    $this->view->public_count = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($resource_type , $resource_id);

    //NUMBER OF MY FRIEND WHICH LIKED THIS CONTENT.
    $this->view->friend_count = Engine_Api::_()->sitelike()->friendNumberOfLike( $resource_type , $resource_id ) ;

    //FIND OUT THE TITLE OF LIKES.
    if ( $resource_type == 'user' ) {
      $this->view->like_title = Engine_Api::_()->getItem( 'user' , $resource_id )->displayname ;
    } else if ($resource_type == 'sitepagedocument_document') {
				$this->view->like_title = Engine_Api::_()->getItem( $resource_type , $resource_id )->sitepagedocument_title ;
		}	else {
      $this->view->like_title = Engine_Api::_()->getItem( $resource_type , $resource_id )->title ;
    }
  }

  //FUNCTION FOR FETCHING PROFILE USERS LIKES.
  public function profileuserlikesAction() {

    //GET THE LIKE SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //GET THE VIEWER ID.
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if ( $this->_getParam( 'profileuser_id' ) != $viewer_id ) {
      $this->view->ownerview = 0 ;
    } else {
      $this->view->ownerview = 1 ;
    }

    $subject = null ;
    //GET THE PROFILE USER ID AND CHECK.
    $this->view->profileuser_id = $profileuserId = $this->_getParam( 'profileuser_id' ) ;
    $this->view->ownername = '' ;
    if ( null !== $profileuserId ) {
      $subject = Engine_Api::_()->getItem( 'user' , $profileuserId ) ;
      if ( !empty( $subject ) ) {
        $this->view->ownername = $subject->displayname ;
      }
    }
    $this->view->viewer_id = $viewer_id ;

    $like_user_str = 0 ;
    $this->view->current_page = $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->current_totallikes = $page * 10 ;
    $this->view->mutual = $mutual = $this->_getParam( 'mutual' , 0 ) ;
    $RESOURCE_TYPE_ARRAY = array ( ) ;
    //FETCHING ALL MODULES
		$RESOURCE_TYPE_ARRAY = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();

    $RESOURCE_TYPE_STRING = "'" ;
    $RESOURCE_TYPE_STRING .= implode( $RESOURCE_TYPE_ARRAY , "','" ) ;
    $RESOURCE_TYPE_STRING.="'" ;

    //FETCHING TOTAL NUMBER OF LIKES OF THE PROFILE USER.
    $total_user_likes = Engine_Api::_()->sitelike()->user_likes( $this->_getParam( 'profileuser_id' ) , $RESOURCE_TYPE_STRING ) ;

    //FETCHING TOTAL NUMBER OF LIKES OF THE PROFILE USER.
    $total_user_mutual_likes = Engine_Api::_()->sitelike()->mutual_likes( $this->_getParam( 'profileuser_id' ) , $RESOURCE_TYPE_STRING ) ;

    $this->view->alllikes = $total_user_likes ;
    $this->view->mutuallikes = $total_user_mutual_likes ;

    if ( $mutual == 0 ) {
      $this->view->activetab = 1 ;
      //FETCHING TOTAL NUMBER OF LIKES OF THE PROFILE USER.
      $this->view->total_count_profilelike = $total_user_likes ;
    } else {
      $this->view->activetab = 0 ;
      //FETCHING TOTAL NUMBER OF LIKES OF THE PROFILE USER.
      $this->view->total_count_profilelike = $total_user_mutual_likes ;
    }

    $profileuser_likes = Engine_Api::_()->sitelike()->userLikesInfo( $page , 10 , $this->_getParam( 'profileuser_id' ) , $mutual , $RESOURCE_TYPE_STRING ) ;
    $this->view->profileuser_likes = $profileuser_likes ;
  }

  //FUNCTION FOR MY LIKES.
  public function mylikesAction() {

    //GET THE LIKE SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //GET AND CHECK VIEWER ID.
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if ( (!$this->_helper->requireUser()->isValid()) || (empty( $viewer_id )) ) {
      return ;
    }

    //CONDITION FOR SHOWING "SUGGEST TO FRIEND" WILL SHOW OR NOT.
    if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'suggestion' )) {
      $this->view->show_link_permition = 1 ;
    }

    //GET THE NAVIGATION AND CHECK FOR PROFILE.
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitelike_main');

    //GET THE RESOURCE TYPE.
    $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->isajaxrequest = $isajax = $this->_getParam( 'isajax' , 0 ) ;
    $row_module_name = $this->_getParam( 'resource_type' , 'member' ) ;
    $this->view->activetab = $row_module_name ;

    //FETCHING ALL MODULES
		$final_module_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();

    if ( empty( $isajax ) ) {
      foreach ( $final_module_array as $row_module_name ) {
        $paginator = Engine_Api::_()->sitelike()->likeMylikes( $viewer_id , $row_module_name , $page , 10 ) ;
        if ( $paginator->count() > 0 ) {
          $this->view->appname = $row_module_name ;
          $this->view->activetab = $row_module_name ;
          break ;
        }
      }
    } else {
      $paginator = Engine_Api::_()->sitelike()->likeMylikes( $viewer_id , $row_module_name , $page , 10 ) ;
      $this->view->appname = $row_module_name ;
    }

    $this->view->enablemodules = $final_module_array ;
    $this->view->paginator = $paginator ;
  }

  //FUNCTION USER FOR MY FRIEND LIKES.
  public function myfriendslikeAction() {

    //CHECK FOR VIEWER ID.
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if ( (!$this->_helper->requireUser()->isValid()) || (empty( $viewer_id )) ) {
      return ;
    }

    //GET THE LIKES SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //GET THE RESOURCE TYPE AND PAGE.
    $this->view->action_type = 'like_myfriend' ;
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitelike_main');
    $page = $this->_getParam( 'page' , 1 ) ;
    $this->view->isajaxrequest = $isajax = $this->_getParam( 'isajax' , 0 ) ;
    $this->view->activetab = $row_module_name = $this->_getParam( 'resource_type' , '' ) ;

    //FETCHING ALL MODULES
		$finalModuleArray = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();
    if ( empty( $isajax ) ) {
      foreach ( $finalModuleArray as $row_module_name ) {
        $paginator = Engine_Api::_()->sitelike()->likeMylikes( 0 , $row_module_name , $page , 10 ) ;
        if ( $paginator->count() > 0 ) {
          $this->view->appname = $row_module_name ;
          $this->view->activetab = $row_module_name ;
          break ;
        }
      }
    } else {
      $paginator = Engine_Api::_()->sitelike()->likeMylikes( 0 , $row_module_name , $page , 10 ) ;
      $this->view->appname = $row_module_name ;
    }
    $this->view->enablemodules = $finalModuleArray ;
    $this->view->paginator = $paginator ;
  }

  //USE FOR MEMBER LIKES.
  public function memberlikeAction() {

    if ( !$this->_helper->requireUser()->isValid() )
      return ;

    //MEMBER LEVEL SETTING CHECK.
    $level_id = Engine_Api::_()->user()->getViewer()->level_id ;
    $this->view->can_view = Engine_Api::_()->authorization()->getPermission( $level_id , 'messages' , 'auth' ) ;

    //GET THE SETTINGS OF PROFILE SHOW OR NOT.
    $like_profile_show = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.profile.show' ) ;
    if ( empty( $like_profile_show ) ) {
      return $this->_helper->redirector->gotoRoute( array ( 'action' => 'browse' ) , 'like_general' , true ) ;
    }

    //GET THE LIKE BUTTON SETTINGS AND USER ID AND RESOURCE TYPE AND RESOURCE ID AND STATUS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitelike_main');
    $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl() ;
    $this->view->user_id = $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $like_user_str = 0 ;
    $this->view->resource_type = $resource_type = 'user' ;
    $this->view->resource_id = $resource_id = $user_id ;
    $this->view->page = $page = $this->_getParam( 'page' , 1 ) ;
    $search = $this->_getParam( 'search' , '' ) ;
    $this->view->is_ajax = $is_ajax = $this->_getParam( 'is_ajax' , 0 ) ;
    $this->view->call_status = $call_status = $this->_getParam( 'call_status' , 'public' ) ;

    //CHECK FOR SEARCH.
    if ( empty( $search ) ) {
      $this->view->search = $this->view->translate( '' ) ;
    }
    else {
      $this->view->search = $search ;
    }

    //HERE FUNCTION CALLING FROM THE CORE.PHP FILE OR THIS IS SHOW NO OF FRIEND
    $fetch_sub = Engine_Api::_()->sitelike()->friendPublicLike(array('action_name' => $call_status, 'resource_type' => $resource_type, 'resource_id' => $resource_id, 'user_id' => $user_id, 'search' => $search)) ;
    $check_object_result = count( $fetch_sub ) ;

    if ( !empty( $check_object_result ) ) {
      $this->view->user_obj = $fetch_sub ;
    }
    else {
      $this->view->no_result_msg = $this->view->translate( 'No members liked you till now.' ) ;
    }

    $fetch_sub->setCurrentPageNumber( $page ) ;
    $fetch_sub->setItemCountPerPage( 10 ) ;

    //NUMBER OF FRIEND WHICH LIKE THIS CONTENT.
    $this->view->public_count = Engine_Api::_()->getApi('like', 'seaocore')->likeCount($resource_type , $resource_id);

    //NUMBER OF MY FRIEND WHICH LIKE THIS CONTENT.
    $this->view->friend_count = Engine_Api::_()->sitelike()->friendNumberOfLike( $resource_type , $resource_id ) ;

    //CHECKK FOR RESOURCE TYPE AND FIND OUT THE TITLE OF LIKE.
    if ( $resource_type == 'user' ) {
      $this->view->like_title = Engine_Api::_()->getItem( 'user' , $resource_id )->displayname ;
    }
    else {
      $this->view->like_title = Engine_Api::_()->getItem( $resource_type , $resource_id )->title ;
    }
  }

  //NAVIGATION FUNCTION TO SHOW TABS
  public function likesettingsAction() {

    //GET LIKE BUTTON SETTINGS.
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;

    //CURRENT USER ID.
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;

    //GET THE NAVIGATION.
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitelike_main');

    if ( !$this->_helper->requireUser()->isValid() )
      return ;

    //GET THE SETTINGS FOR PROFILE.
    $like_profile_show = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.profile.show' ) ;
    if ( empty( $like_profile_show ) ) {
      return $this->_helper->redirector->gotoRoute( array ( 'action' => 'browse' ) , 'like_general' , true ) ;
    }

    //MAKE THE OBJECT FOR CONNECTION SETINGS FORM.
    $this->view->form = $form = new Sitelike_Form_sitelikesettingsform() ;

    if ( $this->getRequest()->isPost() && $form->isValid( $this->getRequest()->getPost() ) ) {

      $table = Engine_Api::_()->getItemTable( 'sitelike_mysettings' ) ;
      $select = $table->select()->where( 'user_id = ?' , $user_id ) ;
      $result = $table->fetchRow( $select ) ;
      if ( !empty( $result ) ) {
        $like_array = $result->toarray() ;
      }
      $values = $this->getRequest()->getPost() ;
      if ( empty( $like_array['user_id'] ) ) {
        $row = $table->createRow() ;
        $row->user_id = $user_id ;
        $row->like = $values["like"] ;
        $row->save() ;
      }
      else {
        $result->delete() ;
      }
    }
  }

  //NAVIGATION FUNCTION TO SHOW TABS
  public function getNavigation( $active = false ) {

    if ( is_null( $this->_navigation ) ) {
			$this->_navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitelike_main');
    }
    return $this->_navigation ;
  }

  //USE FOR COMPOSE THE MESSAGE.
  public function composeAction() {

    //GET THE RESOURCE TYPE AND RESOURCE ID AND VIEWER.
    $multi = 'member' ;
    $multi_ids = '' ;
    $resource_id = $this->_getParam( "resource_id" ) ;
    $resource_type = $this->_getParam( "resource_type" ) ;
    $viewer = Engine_Api::_()->user()->getViewer() ;
    $this->view->form = $form = new Messages_Form_Compose() ;
    $form->removeElement( 'to' ) ;
    $form->setDescription( 'Create your new message with the form below.' ) ;
    $friends = Engine_Api::_()->user()->getViewer()->membership()->getMembers() ;
    $data = array ( ) ;

    foreach ( $friends as $friend ) {
      $friend_photo = $this->view->itemPhoto( $friend , 'thumb.icon' ) ;
      $data[] = array ( 'label' => $friend->getTitle() , 'id' => $friend->getIdentity() , 'photo' => $friend_photo ) ;
    }

    $data = Zend_Json::encode( $data ) ;
    $this->view->friends = $data ;

    //LOGIC FOR HANDLING MULTIPLE RECIPIENTS.
    if ( !empty( $multi ) ) {
      $user_id = $viewer->getIdentity() ;
      $sub_status_table = Engine_Api::_()->getItemTable( 'core_like' ) ;
      $sub_status_name = $sub_status_table->info( 'name' ) ;
      $user_table = Engine_Api::_()->getItemTable( 'user' ) ;
      $user_Name = $user_table->info( 'name' ) ;

      $sub_status_select = $user_table->select()
              ->setIntegrityCheck( false )
              ->from( $sub_status_name , array ( 'poster_id' ) )
              ->joinInner( $user_Name , "$user_Name . user_id = $sub_status_name . poster_id" , null )
              ->where( $sub_status_name . '.resource_type = ?' , $resource_type )
             // ->where( $sub_status_name . '.resource_id = ?' , $resource_id )
              ->where( $sub_status_name . '.poster_id != ?' , $user_id )
              ->group( 'poster_id' ) ;
      $members_ids = $sub_status_select->query()->fetchAll() ;

      foreach ( $members_ids as $member_id ) {
        $multi_ids .= ',' . $member_id['poster_id'] ;
      }
      $multi_ids = ltrim( $multi_ids , "," ) ;

      if ( $multi_ids ) {
        $this->view->multi = $multi ;
        $this->view->multi_name = $viewer->getTitle() ;
        $this->view->multi_ids = $multi_ids ;
        $form->toValues->setValue( $multi_ids ) ;
      }
    }

    //ASSIGN THE COMPOSING STUFF.
    $composePartials = array ( ) ;
    foreach ( Zend_Registry::get( 'Engine_Manifest' ) as $data ) {
      if ( empty( $data['composer'] ) )
        continue ;
      foreach ( $data['composer'] as $type => $config ) {
        $composePartials[] = $config['script'] ;
      }
    }
    $this->view->composePartials = $composePartials ;

    //CHECK METHOD / DATA.
    if ( !$this->getRequest()->isPost() ) {
      return ;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    //PROCESS.
    $db = Engine_Api::_()->getDbtable( 'messages' , 'messages' )->getAdapter() ;
    $db->beginTransaction() ;

    try {

      $attachment = null ;
      $attachmentData = $this->getRequest()->getParam( 'attachment' ) ;
      if ( !empty( $attachmentData ) && !empty( $attachmentData['type'] ) ) {
        $type = $attachmentData['type'] ;
        $config = null ;
        foreach ( Zend_Registry::get( 'Engine_Manifest' ) as $data ) {
          if ( !empty( $data['composer'][$type] ) ) {
            $config = $data['composer'][$type] ;
          }
        }
        if ( $config ) {
          $plugin = Engine_Api::_()->loadClass( $config['plugin'] ) ;
          $method = 'onAttach' . ucfirst( $type ) ;
          $attachment = $plugin->$method( $attachmentData ) ;
          $parent = $attachment->getParent() ;
          if ( $parent->getType() === 'user' ) {
            $attachment->search = 0 ;
            $attachment->save() ;
          }
          else {
            $parent->search = 0 ;
            $parent->save() ;
          }
        }
      }

      $viewer = Engine_Api::_()->user()->getViewer() ;
      $values = $form->getValues() ;

      // Prepopulated
      if( $toObject instanceof User_Model_User ) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
        // Validate friends
        if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
          if( !$viewer->membership()->isMember($recipients) ) {
            return $form->addError('One of the members specified is not in your friends list.');
          }
        }
        
      } else if( $toObject instanceof Core_Model_Item_Abstract &&
          method_exists($toObject, 'membership') ) {
        $recipientsUsers = $toObject->membership()->getMembers();
//        $recipients = array();
//        foreach( $recipientsUsers as $recipientsUser ) {
//          $recipients[] = $recipientsUser->getIdentity();
//        }
        $recipients = $toObject;
      }
      // Normal
      else {
        $recipients = preg_split('/[,. ]+/', $values['toValues']);
        // clean the recipients for repeating ids
        // this can happen if recipient is selected and then a friend list is selected
        $recipients = array_unique($recipients);
        // Slice down to 10
        $recipients = array_slice($recipients, 0, $maxRecipients);
        // Get user objects
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
        // Validate friends
        if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
          foreach( $recipientsUsers as &$recipientUser ) {
            if( !$viewer->membership()->isMember($recipientUser) ) {
              return $form->addError('One of the members specified is not in your friends list.');
            }
          }
        }
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment
      );

      // Send notifications
      foreach( $recipientsUsers as $user ) {
        if( $user->getIdentity() == $viewer->getIdentity() ) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      $db->commit() ;

      return $this->_forward( 'success' , 'utility' , 'core' , array (
        'smoothboxClose' => true ,
        'parentRefresh' => true ,
        'messages' => array ( Zend_Registry::get( 'Zend_Translate' )->_( 'Your message has been sent successfully.' ) )
      ) ) ;
    }
    catch ( Exception $e ) {
      $db->rollBack() ;
      throw $e ;
    }
  }

  //USE FOR CSS SETTINGS.
  public function likesettingcssAction() {

    header( "Content-type: text/css" ) ;
    include_once APPLICATION_PATH . '/application/modules/Sitelike/Api/likesettings.php' ;
    exit() ;
  }

  //USSE FOR TOOL TIP.
  public function getToolTipAction() {

    //GET THE RESOURCE TYPE AND RESOURCE ID AND POSTER ID AND POSTER TYPE.
    $this->view->poster_type = $poster_type = $this->_getParam( 'poster_type' , null ) ;
    $this->view->poster_id = $poster_id = $this->_getParam( 'poster_id' , null ) ;
    $this->view->resource_type = $resource_type = $this->_getParam( 'resource_type' , null ) ;
    $this->view->resource_id = $resource_id = $this->_getParam( 'resource_id' , null ) ;
    $this->view->type = $type = $this->_getParam( 'type' , 'smoothbox' ) ;

    $getMyLikedCurrentItem = Engine_Api::_()->sitelike()->getMyLikedItems( array ( "poster_type" => $poster_type , "poster_id" => $poster_id , "resource_type" => $resource_type , "resource_id" => $resource_id ) ) ;
    $count = count( $getMyLikedCurrentItem ) ;

    if ( $count ) {
      $getMyLikedItem = Engine_Api::_()->sitelike()->getMyLikedItems( array ( "poster_type" => $poster_type , "poster_id" => $poster_id , "resource_type" => $resource_type , "creation_date_less" => $getMyLikedCurrentItem[0]->creation_date ) ) ;
      $likeResource = array ( ) ;
      foreach ( $getMyLikedItem as $likeItems ) {
        $likeResource[] = $resource = Engine_Api::_()->getItem( $likeItems->resource_type , $likeItems->resource_id ) ;
      }
      $this->view->getMyLikedItems = $likeResource ;
    }

    $user = Engine_Api::_()->getItem( $poster_type , $poster_id ) ;
    $item_name = $resource_type ;

    switch ( $resource_type ) {
      case "list_listing":
        $item_name = "Listings" ;
        break ;
      case "sitepage_page":
        $item_name = "Pages" ;
        break ;
    }
    $this->view->heading = $item_name . " " . $user->getTitle() ;
  }

}