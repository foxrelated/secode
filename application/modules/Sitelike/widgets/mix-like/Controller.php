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
class Sitelike_Widget_MixLikeController extends Engine_Content_Widget_Abstract {

  protected $used_like_id = 0 ;

  public function indexAction() {

    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
		$likeBrowseShow = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.browse.auth' ) ;
    $this->view->like_setting_button = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.setting.button' ) ;
    if ( empty( $likeBrowseShow ) && empty( $this->view->viewer_id ) ) {
      return $this->setNoRender() ;
    }

    $tab_show = $tab_show_values = $this->_getParam( 'tab_show' , 1 ) ;
    $isajax = $this->_getParam( 'isajax' , 0 ) ;

    $resource_type_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();

    $resource_type_string = "'" ;
    $resource_type_string .= implode( $resource_type_array , "','" ) ;
    $resource_type_string.="'" ;

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
      //DEFINE THE TAB1 OR TAB2 SHOW TO ZERO IS BY DEFAULT
      $tab1_data = 0 ;
      $tab2_data = 0 ;
      if ( empty( $isajax ) ) {
        $tab_show_values = 1 ;
        //CHECK THE TAB1_SHOW IF ONE OR NOT THEAN GO TO ELSE CASE
        if ( $likesetting_array['tab1_show'] == 1 ) {
          $duration_tab = $likesetting_array['tab1_duration'] ;
          $LIMIT = $likesetting_array['tab1_entries'] ;
          //FUNCTION CALL FROM CORE.PHP FILE
          $fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $resource_type_string , $LIMIT , $duration_tab);
          //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GET TOTAL FUNCTION
          if ( !empty( $fetch_sub ) && !count( $fetch_sub ) <= 0 ) {
            //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
            $tab1_data = 1 ;
            $tab_show_values = 1 ;
          }
        }
        //CHECK THE TAB2_SHOW TO SHOW BYDEFAULT  WHEN THE CONDITION IS TRUE
        if ( $likesetting_array['tab2_show'] == 1 && $tab1_data == 0 ) {
          $duration_tab = $likesetting_array['tab2_duration'] ;
          $LIMIT = $likesetting_array['tab2_entries'] ;
          //FUNCTION IS CALLING
          $fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $resource_type_string , $LIMIT , $duration_tab);
          //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GETTOTALCOUNT() FUNCTION
          if ( !empty( $fetch_sub ) && !count( $fetch_sub ) <= 0 ) {
            //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
            $tab2_data = 1 ;
            $tab_show_values = 2 ;
          }
        }

        //CHECK THE TAB3_SHOW TO SHOW BYDEFAULT  WHEN THE CONDITION IS TRUE
        if ( !empty( $likesetting_array['tab3_show'] ) && $tab2_data == 0 && $tab1_data == 0 ) {
          $duration_tab = $likesetting_array['tab3_duration'] ;
          $LIMIT = $likesetting_array['tab3_entries'] ;
          //FUNCTION IS CALLING
          $fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $resource_type_string , $LIMIT , $duration_tab);

          //CHECK THE TOTAL NUMBER ENTRY IN THE TABLE BY GETTOTALCOUNT() FUNCTION
          if ( !empty( $fetch_sub ) && !count( $fetch_sub ) <= 0 ) {
            //PASS THE VALUE TO .TPL FILE TO SHOW THE RESULT
            $tab_show_values = 3 ;
          }
        }
        $this->view->ajaxrequest = 0 ;
      }	else {	
        $tab_show_values = $tab_show ;
        $duration_tab = $likesetting_array['tab' . $tab_show . '_duration'] ;
        $LIMIT = $likesetting_array['tab' . $tab_show . '_entries'] ;
        //FUNCTION IS CALLING
        $fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $resource_type_string , $LIMIT , $duration_tab);
        $this->view->ajaxrequest = 1 ;
      }

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