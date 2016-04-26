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
class Sitelike_Widget_ProfileUserLikesController extends Engine_Content_Widget_Abstract
{
  public function indexAction() {
  
	  $likeBrowseShow = Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'like.browse.auth' ) ;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
    if (empty($likeBrowseShow) && empty($viewer_id)) {
      return $this->setNoRender();
    }
    
    //FETCHING TOTAL NO. OF LIKES OF THE PROFILE USER.
    $profile_owner_id = Engine_Api::_()->core()->getSubject()->getIdentity() ;
		$resource_type_array = array() ;
		$resource_type_array = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitelike' )->getMixLikeSetting();
		
    $resource_type_string = "'" ;  
    $resource_type_string .= implode( $resource_type_array , "','" ) ;
    $resource_type_string.="'" ; 
    $total_user_profile_likes = Engine_Api::_()->sitelike()->user_likes( $profile_owner_id , $resource_type_string ) ;

    if (!empty($total_user_profile_likes)) {
    
      $this->view->user_likes = $total_user_profile_likes ;
      //CHECKING IF THE PROFILE VIEWR IS OWNER OR NOT. IF HE IS NOT OWNER OF THAT PROFILE THEN WE WILL FETCH THE TOTAL MUTUAL LIKES.
      $this->view->profileuser_id = $profile_owner_id ;
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity() ;
      if ( $viewer_id != $profile_owner_id )
			{
        $total_mutual_likes = Engine_Api::_()->sitelike()->mutual_likes($profile_owner_id , $resource_type_string);
        if ( empty( $total_mutual_likes ) )
				{
          $total_mutual_likes = 0 ;
        }
        $this->view->total_mutual_likes = $total_mutual_likes ;
        $this->view->ownerview = 0 ;
      }
			else
			{
        $this->view->ownerview = 1 ;
      }
      $profile_owner_entries = $this->_getParam('itemCountPerPage', 3);
      //FETCHING THE REANDOM 3 LIKES OF THE PROFILE OWNER.
      $random_likes_obj = Engine_Api::_()->sitelike()->userLikesInfo( 1 , $profile_owner_entries , $profile_owner_id , 0 , $resource_type_string ) ;
      $this->view->random_like_obj = $random_likes_obj ;
    }
		else
		{
			$this->setNoRender() ;
    }
  }
}