<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Socialengineaddon
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Widget_SuggestionInvitesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  
    $webmail_enabledisable = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggestion.friend.invite.enable', 1);
	  if (!$webmail_enabledisable)
	     return $this->setNoRender();
	     
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    if (Engine_Api::_()->core()->hasSubject()) {
      // Get subject
      $subject = Engine_Api::_()->core()->getSubject();
      if (!$subject->authorization()->isAllowed($viewer, 'view')) {
        return $this->setNoRender();
      }
    }
    $this->view->user_id = $viewer_id = $viewer->getIdentity();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->baseURL = Zend_Controller_Front::getInstance()->getBaseUrl();
    //CHECK EITHTE THIS WIDGET HAS TO RENDER OR NOT.
    $is_WelcomePage = false;
    $is_pluginEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
    if( !empty($is_pluginEnabled) ) {
      $is_WelcomePage = Engine_Api::_()->advancedactivity()->getPageObj($this->view->identity, 'welcometab');
    }

    if( !empty($is_WelcomePage) ) {
        //WE WILL RENDER THIS WIDGET ONLY IF THE LOGGEDIN USER HAS LESS THEN OR EQUAL TO THE LIMIT OF FRIENDS WHICH ADMIN HAS SET.
        $getInviteSettings = Engine_Api::_()->advancedactivity()->getCustomBlockSettings(array('suggestion-invites'));
        if (empty($getInviteSettings)) {
          return $this->setNoRender();
        }
     }    
  }
}