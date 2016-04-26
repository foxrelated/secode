<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Widget_FacebookseBookmarksController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()
  {
    //fetching the settings of bookmarks button for this site for this loggedin user.Either user has already bookmarks this site or not.
	  
	$fb_uid = Engine_Api::_()->getDbtable('facebook', 'user')->fetchRow(array('user_id = ?'=>Engine_Api::_()->user()->getViewer()->getIdentity()));
	  
	if (!empty($fb_uid->facebook_uid)) {
	  $facebook = Engine_Api::_()->seaocore()->getFBInstance();
		if ($facebook && $facebook->getUser()) {
			$session['uid'] = $facebook->getUser();	
   }
   else {
		$session = '';
   }
	  
	  if ($session) {
			$query = "SELECT * FROM permissions WHERE uid='{$session['uid']}'";
			$param = array (
				'method' => 'fql.query',
				'query' => $query,
				'callback' => ''
			);
			try {
				$result = $facebook->api($param);
			}
			catch (Exception $e) {
				
			}
	  }
	}
	
	if (! $session['uid'])  {
		return $this->setNoRender();
	}
  }
}