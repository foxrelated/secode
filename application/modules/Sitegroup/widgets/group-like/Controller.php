<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_GroupLikeController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

//     //GET THE VIEWER AND CHECK
//     $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
//     if (empty($viewer)) {
//       return $this->setNoRender();
//     }

    $this->view->resource_type = $RESOURCE_TYPE = 'sitegroup_group';
		$this->view->resource_id = $RESOURCE_ID = Engine_Api::_()->core()->getSubject()->getIdentity();
    $LIMIT = $this->_getParam('itemCount', 3);

		//FUNCTION CALLING AND PASS RESOURCE ID AND RESOURCE TYPE AND LIMIT
    $fetch_sub = Engine_Api::_()->sitegroup()->groupLike($RESOURCE_TYPE, $RESOURCE_ID, $LIMIT);

    if (!empty($fetch_sub)) {
      foreach ($fetch_sub as $fetch_id) {
        $like_user_object[] = Engine_Api::_()->getItem('user', $fetch_id['poster_id']);
      }
      $this->view->user_obj = $like_user_object;
      $this->view->num_of_like = $num_of_like = Engine_Api::_()->sitegroup()->numberOfLike($RESOURCE_TYPE, $RESOURCE_ID);
      if (!empty($num_of_like) && $num_of_like > $LIMIT) {
        $this->view->detail = 1;
      }
    } else {
      return $this->setNoRender();
    }
  }
}
?>