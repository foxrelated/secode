<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


class Facebookse_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  { 
    $this->view->viewer_id  = $viewer =  Engine_Api::_()->user()->getViewer()->getIdentity();
    
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('delete', 'json');
  }

  //FUNCTION WHICH IS CALLED WHEN WE HAVE TO RESET SESSION OF FACEBOOK_FEED INFO.
  function resetfbfeedAction () {
  	$session = new Zend_Session_Namespace();
  	unset($session->feed_info);
  	exit;
  }

  //FINDING WETHERE THE USER HAS LIKED THE VIWING CONTENT OR NOT.
 function checklikeAction () {
		$item =  $this->_getParam('type');
		$item_id =  $this->_getParam('type_id');
    $enable_likemodule =  $this->_getParam('enable_likemodule');
    $enable_likeintsetting =  $this->_getParam('enable_likeintsetting');
		$curr_url =  $this->_getParam('curr_url');
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!empty($viewer_id) && $enable_likemodule == 1 && $enable_likeintsetting == 1) {	
			//CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
			 $like_unlike = Engine_Api::_()->sitelike()->checkAvailability( $item, $item_id );
		}
    else {
			$like_unlike = true;
    }
		$this->view->item = $item;
		$this->view->item_id = $item_id;  
		$this->view->like = $like_unlike;
    //We are not using Facebook Like Statistics feature so we are going to comment this. 03/02/2014.
    //GETTING THE LIKED CURRENT URL AND SAVING IT IN FACEBOOKSE_STATISTICS TABLE TO USE LATER IN SHOWING FACEBOOK STATISTICS OF SITE URL IN ADMIN PANEL.
		    
    //BEFORE INSTERING WE WILL CHECK IF CURRENTLY LIKED URL IS ALREADY LIKED BY SOMEONE ELSE IF SO THEN WE WILL ONLY UPDATE IT'S UPADTED FIELD OTHERWISE WILL INSERT A NEW ROW.
//    $tmTable = Engine_Api::_()->getDbtable('statistics', 'facebookse');
//    $tmName = $tmTable->info('name');
//
//    $selectSiteUrl = $tmTable->select()                    
//                    ->from($tmName, 'url')
//                    ->where("(content_id = $item_id AND resource_type = '$item')")                  
//                    ->limit(1)
//                    ->query()
//                    ->fetchColumn();
//    $current_time = new Zend_Db_Expr('NOW()');
//    $db = Engine_Db_Table::getDefaultAdapter();
//		if (empty($selectSiteUrl)) {			
//      $db->beginTransaction();
//      try
//      {
//        // Transaction
//        // insert the statistics entry into the database
//        $row = $tmTable->createRow();
//        $row->url   =  $curr_url;
//        $row->updated =  $current_time;
//        $row->url_scrape = 0;
//        $row->url_type = $item;
//        $row->content_id = $item_id;
//        $row->resource_type = $item;
//        $row->save();
//        $db->commit();
//        
//      }
//
//      catch( Exception $e )
//      {
//        $db->rollBack();
//        throw $e;
//      }
//    }
//    else {
//			//UPDATING THE ROW'S UPDATED COLUMN.     
//      $db->query("UPDATE `engine4_facebookse_statistics` SET `updated` = '$current_time' WHERE (`content_id` = '$item_id' AND `resource_type` = '$item')");			
//    } 
	}
  
  //AJAX BASED.
  public function scrapeurlAction() { 
    $curr_url_scrpe =  $this->_getParam('scrapeurl', '');
    if($curr_url_scrpe)
      $this->view->success_scrapefburl = Engine_Api::_()->facebookse()->scrapeFbAdminPage ($curr_url_scrpe);    
  }




  public function mysettingsAction () {
    
   $viewer_id =  Engine_Api::_()->user()->getViewer()->getIdentity();
   $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('facebookse_main');
    //CHECK IF FACEBOOKSEFEED MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR ENABLING OR DISABLING FEED SETTINGS FOR USER.
		$enable_facebooksefeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
		if (empty($viewer_id) || empty($enable_facebooksefeedmodule)) { 
			$this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
    }
    
    //FETCHING THE SETTINGS OF ALL MODULE POST EITHER ADMIN HAS ENABLED THE MODULE POST OR NOT.IF NOT THEN WE WILL NOT SHOWO USER THAT MODULE POST OPTION IF SETTING TAB.IF ALL MODULE FEEDS ARE DISABLED THEN WE WILL NOT SHOW THE MY SETTING TAB.
		$item_array = array ();
		if (!empty($enable_facebooksefeedmodule)) {
		  $permissionTable_feed = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
			$select = $permissionTable_feed->select();
			$permissionTable_feed = $permissionTable_feed->fetchAll($select)->toarray();
			$redirect_home = true;
		  foreach ($permissionTable_feed as $item) {
				$item_array[$item['activityfeed_type']] = $item['streampublishenable'];
				if (!empty($item['streampublishenable'])) {
				  $redirect_home = false;
				}
		  }
    }
   
     if ($redirect_home) 		{ 
    	$this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);	
   } 
    
		$this->view->form = $form = new Facebookse_Form_Mysettings () ;
    $permissionTable_feedSettings = Engine_Api::_()->getDbtable('feedsettings', 'facebookse');
    //SHOWING THE FORM FILLED IF ACTION IS NOT FOR FORM POST.
		if( !$this->getRequest()->isPost() ) {
			//GETTING THE SETTING FROM FACEBOOKSE_FEEDSETTINGS TABLE.
			$select = $permissionTable_feedSettings->select()
							->where('user_id=?', $viewer_id);
				$permissionTable_feedSettings = $permissionTable_feedSettings->fetchRow($select);
				if ($permissionTable_feedSettings) { 
				  $permissionTable_feedSettings = $permissionTable_feedSettings->toarray();
  				if (!empty($permissionTable_feedSettings['feedpublish_types'])) {
  				  $populate_array = unserialize($permissionTable_feedSettings['feedpublish_types']);
  				  $populate_array['feedsetting_id'] = $permissionTable_feedSettings['feedsetting_id'];
  				}
  				else {
  				  $populate_array = $permissionTable_feedSettings;
  				}
  				 $form->populate($populate_array);
				}
     }
     //WHEN USER SUBMIT THE FORM.
     else if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
				$values = $this->getRequest()->getPost();
         
				$form->feedsetting_id->setValue($values['feedsetting_id']);
        if (empty($values['feedsetting_id'])) {
          unset($values['feedsetting_id']);
          unset($values['submit_form']);
          $feedpublish_types['feedpublish_types'] = serialize($values);
          $feedpublish_types['user_id'] = $viewer_id;
          $table_feed = $permissionTable_feedSettings->createRow();
					$table_feed->setFromArray($feedpublish_types);
					$feedsetting_id = $table_feed->save();
					$form->feedsetting_id->setValue($feedsetting_id);
				}
        else {
					$table_feed = Engine_Api::_()->getItem('facebookse_feedsetting', $values['feedsetting_id']);
          unset($values['feedsetting_id']);
          unset($values['submit_form']);
          $feedpublish_types['feedpublish_types'] = serialize($values);
					$table_feed->setFromArray($feedpublish_types);
					$table_feed->save();
				}
     }
    
		 $this->view->fb_url = $FBloginURL =  Zend_Controller_Front::getInstance()->getRouter()
		->assemble(array('module' => 'seaocore', 'controller' => 'auth',
			'action' => 'facebook'), 'default', true). '?'
				      . http_build_query(array('redirect_urimain' => urlencode('http://' . $_SERVER['HTTP_HOST'] . $this->view->url())));
    
	}
	
	function opengraphredirectAction () {
    $this->_helper->layout->setLayout('default-simple');
    if (isset ($_SESSION['fbmetainfo_enter']) && empty($_SESSION['fbmetainfo_enter'])) {
        $controller_anonymus = @$_SESSION['fbmetainfo']['controller'];
			    $module_anonymus = @$_SESSION['fbmetainfo']['module'];
			    $user_id_anonymus = @$_SESSION['fbmetainfo']['user_id'];
			    $action_anonymus = @$_SESSION['fbmetainfo']['action'];
          $module_id_anonymus = @$_SESSION['fbmetainfo']['module_id'];
          $resourcetype = @$_SESSION['fbmetainfo']['resource_type'];
 				$redirect_url_anonymus =  @urldecode($_SESSION['fbmetainfo']['redirect_url']);
// 				if (($module_anonymus == 'sitepage' || $module_anonymus == 'sitebusiness')&& $controller_anonymus == 'topic') {
// 				  $module_id_anonymus = $_SESSION['fbmetainfo']['topic_id'];
// 				}
// 				else if ($module_anonymus == 'sitepage' ) {
// 				  $module_id_anonymus = $_SESSION['fbmetainfo']['page_id'];
// 				}
// 				else if ($module_anonymus == 'sitebusiness'){
// 				  $module_id_anonymus = $_SESSION['fbmetainfo']['business_id'];
// 				}
// 				else {
// 				 $plugins_temp = str_replace('sitepage', "", $module_anonymus);
// 				 if ($plugins_temp == $module_anonymus) {
// 				   $plugins_temp = str_replace('sitebusiness', "", $module_anonymus);
// 				 }
// 				 $module_id_anonymus = @$_SESSION['fbmetainfo'][$plugins_temp . '_id'];
// 				}
 				 $redirect_url_anonymus = ( _ENGINE_SSL ? 'https://' : 'http://' ) .$redirect_url_anonymus;
 				
			  $this->setMetaTag($module_anonymus,  $module_id_anonymus, $controller_anonymus, $user_id_anonymus, $action_anonymus, $redirect_url_anonymus, $resourcetype);	
        
    
    }
  
  }
  
  public  function setMetaTag($module_anonymus,  $module_id_anonymus, $controller_anonymus, $user_id_anonymus, $action_anonymus, $redirect_url_anonymus, $resourcetype = null) { 
		 
		  $view = $this->view; 
		 
			$viewer = Engine_Api::_()->user()->getViewer();
			if( $view instanceof Zend_View) {
				$front = Zend_Controller_Front::getInstance();
				$module = $front->getRequest()->getModuleName();
        $controllerName = $front->getRequest()->getControllerName();
				$action = $front->getRequest()->getActionName();
				$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
				
				//CHECK IF LIKE MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
				$enable_likemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');
				$module = $module_anonymus; 
			  $controllerName = $controller_anonymus;
			  $action = $action_anonymus;
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
				if ($module == 'socialdna') {
					$module = 'user';	
					
				}
				
				if ($module_anonymus == 'core') {
				  
				  $module = 'home';
				} 			

				Engine_Api::_()->facebookse()->showOpenGraph ($module,  $controllerName, $user_id_anonymus, $action, $redirect_url_anonymus, $front, $view, $viewer,$module_id_anonymus, $resourcetype);	

				
				
				}
		}
    
    //SEND NOTIFICATION ON SITE IF A COMMENT IS MADE USING FACEBOOK COMMENT BOX.
    
    public function createNotificationAction () {
     
       if( !$this->_helper->requireUser()->isValid() ) {
          return;
        }
//        if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid() ) {
//          return;
//        }
    $subject = null;
    if( Engine_Api::_()->core()->hasSubject() ) {
      $subject = Engine_Api::_()->core()->getSubject();
    } else if( ($subject = $this->_getParam('subject')) ) {
      list($type, $id) = explode('_', $subject);
      $subject = Engine_Api::_()->getItem($type, $id);
    } else if( ($type = $this->_getParam('type')) &&
        ($id = $this->_getParam('id')) ) {
      $subject = Engine_Api::_()->getItem($type, $id);
    }

        $viewer = Engine_Api::_()->user()->getViewer();
//        $subject = Engine_Api::_()->core()->getSubject();

//        $this->view->form = $form = new Core_Form_Comment_Create();

        if( !$this->getRequest()->isPost() ) {
          $this->view->status = false;
          $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");;
          return;
        }

//        if( !$form->isValid($this->_getAllParams()) ) {
//          $this->view->status = false;
//          $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
//          return;
//        }

        // Process

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $this->_getParam('body');
        $body = $filter->filter($body);


        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try { 
          //$table = $subject->comments()->getCommentTable();
          //$subject->comments()->addComment($viewer, $body);
          Engine_Api::_()->facebookse()->addComment($subject, $viewer, $body, $this->_getParam('commentId'));

          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
          $subjectOwner = $subject->getOwner('user');

          // Activity
          $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
            'owner' => $subjectOwner->getGuid(),
            'body' => $body
          ));

          //$activityApi->attachActivity($action, $subject);

          // Notifications

          // Add notification for owner (if user and not viewer)
          $this->view->subject = $subject->getGuid();
          $this->view->owner = $subjectOwner->getGuid();
          if( $subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity() )
          {
            $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
              'label' => $subject->getShortType()
            ));
          }

          // Add a notification for all users that commented or like except the viewer and poster
          // @todo we should probably limit this
          $commentedUserNotifications = array();
          foreach( $subject->comments()->getAllCommentsUsers() as $notifyUser )
          {
            if( $notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity() ) continue;

            // Don't send a notification if the user both commented and liked this
            $commentedUserNotifications[] = $notifyUser->getIdentity();

            $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
              'label' => $subject->getShortType()
            ));
          }

          // Add a notification for all users that liked
          // @todo we should probably limit this
          foreach( $subject->likes()->getAllLikesUsers() as $notifyUser )
          {
            // Skip viewer and owner
            if( $notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity() ) continue;

            // Don't send a notification if the user both commented and liked this
            if( in_array($notifyUser->getIdentity(), $commentedUserNotifications) ) continue;

            $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
              'label' => $subject->getShortType()
            ));
          }

          // Increment comment count
          Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

          $db->commit();
        }

        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }

//        $this->view->status = true;
//        $this->view->message = 'Comment added';
//        $this->view->body = $this->view->action('list', 'comment', 'core', array(
//          'type' => $this->_getParam('type'),
//          'id' => $this->_getParam('id'),
//          'format' => 'html',
//          'page' => 1,
//        ));
//        $this->_helper->contextSwitch->initContext();

      
    }
    
    //REMOVE THE COMMENT FROM SITE ITSELF IF THE COMMENT HAS BEEN REMOVED FROM FACEBOOK.
    
    public function removeNotificationAction () {
      
      if( !$this->_helper->requireUser()->isValid() ) return;
    
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->core()->getSubject();

      // Comment id
      $comment_id = $this->_getParam('comment_id');
      if( !$comment_id ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
        return;
      }
  
      // Comment
      $comment = Engine_Api::_()->facebookse()->getComment($subject, $comment_id);
      if( !$comment ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
        return;
      }

      // Authorization
      if( !$subject->authorization()->isAllowed($viewer, 'edit') &&
          ($comment->poster_type != $viewer->getType() ||
          $comment->poster_id != $viewer->getIdentity()) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
        return;
      }

      // Method
      if( !$this->getRequest()->isPost() ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
        return;
      }

      // Process
      $db = $subject->comments()->getCommentTable()->getAdapter();
      $db->beginTransaction();

      try
      { 
        Engine_Api::_()->facebookse()->removeComment($subject, $comment_id);

        //$subject->comments()->removeComment($comment_id);

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
    }    
    
   //authentication to the facebook app
    
   public function facebookauthAction() {

    // Clear
    if (null !== $this->_getParam('clear')) {
      unset($_SESSION['facebook_lock']);
      unset($_SESSION['facebook_uid']);
    }
    $enable_sitemobile = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemobile');
    $session = new Zend_Session_Namespace();
    $viewer = Engine_Api::_()->user()->getViewer();
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
    $permissions_array = array(
        'publish_actions',
        'user_likes'
    );
    $db = Engine_Db_Table::getDefaultAdapter();

    $URL_Home = $this->view->url(array('action' => 'home'), 'user_general', true);
    // Enabled?
//    if (!$facebook || 'none' == $settings->core_facebook_enable) {
//      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
//    }
    $redirect_uri = $this->_getParam('redirect_urimain', '');

    if (!empty($redirect_uri)) {
      $session->aaf_redirect_uri = urldecode($this->_getParam('redirect_urimain'));
    }
    // Already connected

    if ($facebook && $facebook->getUser() && empty($_GET['redirect_urimain'])) {

      try {
        if (!isset($_GET['redirect_fbback'])) {
          $permissions = $facebook->api("/me/permissions");

          if (!array_key_exists('publish_actions', $permissions['data'][0])) {
            $url = $facebook->getLoginUrl(array(
                'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url() . '?redirect_fbback=1',
                'scope' => join(',', $permissions_array),
                    ));


            return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
          }
        }
      } catch (Exception $e) {
        //continue;
      }
      $code = $facebook->getPersistentData('code');
      if (!empty($_GET['code'])) {
        $code = $_GET['code'];
      }


      //GETTING THE NEW ACCESS TOKEN FOR THIS REQUEST.
      $result = Seaocore_Api_Facebook_Facebookinvite::getAccessTokenFB($code);
      $result = explode("&expires=", $result);
      //CLEARING THE FACEBOOK OLD PERSISTENTDATA AND SETTING THEM TO NEW.

      $facebook->setPersistentData('code', $code);
      $response_temp = array();
      if (!empty($result)) {
        $response_temp = explode("access_token=", $result[0]);
        if (!empty($response_temp[1])) {
          $facebook->setAccessToken($response_temp[1]);
          $facebook->setPersistentData('access_token', $facebook->getAccessToken());
        } else {
          $response_temp[1] = $facebook->getAccessToken();
        }
      }
      if (empty($response_temp[1])) {
        $response_temp[1] = $facebook->getAccessToken();
      }

      $_SESSION['facebook_uid'] = $facebook->getUser();
      $session->aaf_fbaccess_token = $response_temp[1];
      if (!empty($session->aaf_redirect_uri)) {
        $redirect_uri = $session->aaf_redirect_uri;
        unset($session->aaf_redirect_uri);
        return $this->_helper->redirector->gotoUrl($redirect_uri, array('prependBase' => false));
      } else
      // Redirect to home
        return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
    }

    // Not connected
    else {
      // Okay
      if (!empty($_GET['code'])) {
        // This doesn't seem to be necessary anymore, it's probably
        // being handled in the api initialization

        return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
      }

      // Error
      else if (!empty($_GET['error'])) {

        return $this->_helper->redirector->gotoUrl($URL_Home . '?redirect_fb=1', array('prependBase' => false));
      } else if (isset($_GET['redirect_fbback'])) {

        if (!empty($session->aaf_redirect_uri))
          return $this->_helper->redirector->gotoUrl($session->aaf_redirect_uri, array('prependBase' => false));
      }

      // Redirect to auth page
      else {
        if (!empty($_GET['redirect_urimain'])) {
          $session = new Zend_Session_Namespace();
          $session->aaf_redirect_uri = urldecode($_GET['redirect_urimain']);
        }

        //CHECK IF THE SITE IS IN MOBILE MODE. THEN WE WILL ONLY ASK FOR PUBLISH STREAM.
        $scope = join(',', $permissions_array);

        $url = $facebook->getLoginUrl(array(
            'redirect_uri' => (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(),
            'scope' => $scope,
                ));


        return $this->_helper->redirector->gotoUrl($url, array('prependBase' => false));
      }
    }
  }
  
  
  //SET ACCESS TOKEN
  
  public function settokenAction() {
  
    //$access_token =  $this->_getParam('access_token', '');
		$facebook = $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
		$fb_checkconnection = Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebook_userfeed);
		$FBjsAccessToken = $this->_getParam('access_token', '');
		if (!empty($FBjsAccessToken)) {
			$session = new Zend_Session_Namespace();
      $_SESSION['aaf_fbaccess_token_js'] = $FBjsAccessToken;     
			$session->aaf_fbaccess_token = $FBjsAccessToken;
      $facebook->setAccessToken($FBjsAccessToken);
      $this->view->fbUserId = $facebook->getUser();;
			//$fb_checkconnection = Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebook_userfeed);
		}
  }
 
}
