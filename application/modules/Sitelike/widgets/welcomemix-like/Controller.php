<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitelike_Widget_WelcomemixLikeController extends Engine_Content_Widget_Abstract {

  protected $used_like_id = 0 ;

  public function indexAction() {

    $this->view->viewer_id = $viewerId = Engine_Api::_()->user()->getViewer()->getIdentity() ;
		$likeBrowseShow = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.browse.auth' ) ;
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;
    if ( empty( $likeBrowseShow ) && empty( $viewerId ) ) {
      return $this->setNoRender() ;
    }

		// Conditions for Welcome Tab.
		$getLikeSettings = Engine_Api::_()->advancedactivity()->getCustomBlockSettings(array('sitelike.welcomemix-like'));
		if( empty($getLikeSettings) ) {
			return $this->setNoRender();
		} else {
			$welcomeTabLimit = Engine_Api::_()->getApi( 'settings' , 'core'
			)->getSetting('welcome.like.limit', 20);
			$this->view->is_welcomeTab_enabled = true;
		}

    $tab_show = $tab_show_values = $this->_getParam( 'tab_show' , 1 ) ;
    $isajax = $this->_getParam( 'isajax' , 0 ) ;

    $RESOURCE_TYPE_ARRAY = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();

    $RESOURCE_TYPE_STRING = "'" ;
    $RESOURCE_TYPE_STRING .= implode( $RESOURCE_TYPE_ARRAY , "','" ) ;
    $RESOURCE_TYPE_STRING.="'" ;

    $likesettingTable = Engine_Api::_()->getDbtable( 'settings' , 'sitelike' ) ;
    $select = $likesettingTable->select()->where( 'content_type = ?' , 'mixed' ) ;
    $likesetting_result = $likesettingTable->fetchRow( $select ) ;

    //HERE WE CAN CHECK WHEN NO ROW FETCH FROM THE TABLE
    if ( $likesetting_result != null ) {
    
      //HERE WE CAN CONVERT THE RESULT IN TO THE ARRAY FORM
      $this->view->likesetting = $likesetting_array = $likesetting_result->toarray() ;

      //CURRENT DATE CAN GET FROM THE DATE FUNCTION
      $end_date = date( 'Y-m-d' ) ;
      
      //HERE DURATION TAB DEFINE EMPTY
      $duration_tab = '' ;
			$tab_show_values = $tab_show ;
			$duration_tab = $likesetting_array['tab' . $tab_show . '_duration'] ;
			$LIMIT = $likesetting_array['tab' . $tab_show . '_entries'] ;
			
			//FUNCTION IS CALLING
			$fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $RESOURCE_TYPE_STRING , $welcomeTabLimit ,$duration_tab);
			$this->view->ajaxrequest = 1 ;

      $this->view->active_tab = $tab_show_values ;
      $level = 0 ;
      $mix_like_object = array ( ) ;
      while ( $level != count( $fetch_sub ) ) {

        if ( !empty( $fetch_sub ) ) {
          $key = $fetch_sub[$level]['resource_type'] . '_' . $fetch_sub[$level]['resource_id'] ;
          $key_status = array_key_exists( $key , $mix_like_object ) ;
          if ( empty( $key_status ) ) {
            $mix_key['resource_id'] = $fetch_sub[$level]['resource_id'] ;
            $mix_key['resource_type'] = $fetch_sub[$level]['resource_type'] ;
            $mix_like_object[$fetch_sub[$level]['resource_type'] . '_' . $fetch_sub[$level]['resource_id']] = $mix_key ;
          }	else {
            // If same like id come then insert in protected variobale that not repete.
            $this->used_like_id .= ',' . $fetch_sub[$level]['like_id'] ;
          }
        }	else {
        }
        $level++ ;
      }

      if ( !empty( $mix_like_object ) ) {
        $object = Engine_Api::_()->sitelike()->likeMixObject( $mix_like_object , 1 , 'browsemixinfo' , $LIMIT ) ;
				if (!empty($object))
					$this->view->mix_object = $object;			
      }	else {
        if ( empty( $isajax ) ) {
          $this->setNoRender() ;
        }
      }
    }	else {
      $this->setNoRender() ;
    }
  }
}