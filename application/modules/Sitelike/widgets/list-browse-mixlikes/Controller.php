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
class Sitelike_Widget_ListBrowseMixlikesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    $coreSettings = Engine_Api::_()->getApi( 'settings' , 'core' );
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    $this->view->like_setting_button = $coreSettings->getSetting( 'like.setting.button' ) ;
    $likeBrowseShow = $coreSettings->getSetting( 'like.browse.auth' ) ;
    if ( empty( $likeBrowseShow ) && empty( $viewer_id ) ) {
      return $this->setNoRender() ;
    }

    $limit = $this->_getParam('itemCountPerPage', 10);
    
    $tab_show = $this->_getParam( 'tab_show' , 1 ) ;
    $is_ajax = $this->_getParam( 'isajax' , 0 ) ;
    $page = $this->_getParam( 'page' , 1 ) ;

    if ( $tab_show == 1 ) {
      $orderby = "like_id DESC" ;
    }	else if ( $tab_show == 2 ) {
      $orderby = "like_count DESC" ;
    }	else {
      $orderby = "RAND()" ;
    }

    $startindex = 0 ;
    if ( empty( $is_ajax ) ) {
      $startindex = 0 ;
    }	else {
      $startindex = ($page - 1) * $limit ;
    }

    $resource_type_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();
    
    //THIS FUNCTION SHOW MIX CONTENT KEY
    $resource_type_string = "'" ;
    $resource_type_string .= implode( $resource_type_array , "','" ) ;
    $resource_type_string.="'" ;
    
    $fetch_sub = Engine_Api::_()->sitelike()->likeMixInfo( $resource_type_string , $limit , 0 , $orderby , $page , $startindex , 'browsemixinfo' ) ;

    $level = 0 ;
    $mix_like_object = array ( ) ;
    while ( $level != count( $fetch_sub ) ) {
      if ( !empty( $fetch_sub ) ) {
        $key = $fetch_sub[$level]['resource_type'] . '_' . $fetch_sub[$level]['resource_id'] ;
        $key_status = array_key_exists( $key , $mix_like_object ) ;
        if ( empty( $key_status ) ) {
          $mix_key['resource_id'] = $fetch_sub[$level]['resource_id'] ;
          $mix_key['resource_type'] = $fetch_sub[$level]['resource_type'] ;
          if ( !empty( $fetch_sub[$level]['poster_id'] ) ) {
            $mix_key['poster_id'] = $fetch_sub[$level]['poster_id'] ;
          }
          $mix_like_object[$fetch_sub[$level]['resource_type'] . '_' . $fetch_sub[$level]['resource_id']] = $mix_key ;
        }	else {
          // If same like id come then insert in protected variobale that not repete.
          $this->used_like_id .= ',' . $fetch_sub[$level]['like_id'] ;
        }
      }
      $level++ ;
    }
    $this->view->total_count_profilelike = 0 ;
    if ( !empty( $mix_like_object ) ) {
      $this->view->mix_object = $object = Engine_Api::_()->sitelike()->likeMixObject( $mix_like_object , $page , 'browsemixinfo' ) ;
			$this->view->total_count_profilelike = count( $this->view->mix_object ) ;
      if ( !empty( $is_ajax ) ) {
        $this->view->ajaxrequest = 1 ;
      }	else {
        $this->view->ajaxrequest = 0 ;
      }
      $this->view->pagelimit = $limit + 1 ;
      $this->view->current_page = $page ;
      $this->view->active_tab = $tab_show ;
      $this->view->viewer_id = $viewer_id ;
    }
        
    	if(!$is_ajax) {
			$this->view->params = $this->_getAllParams();
			if ($this->_getParam('loaded_by_ajax', true)) {
				$this->view->loaded_by_ajax = true;
				if ($this->_getParam('is_ajax_load', false)) {
					$this->view->is_ajax_load = true;
					$this->view->loaded_by_ajax = false;
					if (!$this->_getParam('onloadAdd', false))
						$this->getElement()->removeDecorator('Title');
					$this->getElement()->removeDecorator('Container');
				} else { 
					return;
				}
			}
			$this->view->showContent = true;    
    }
    else {
      $this->view->showContent = true;
    }
  }
}