<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Widget_MenuFooterController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->navigation = $navigation = Engine_Api::_()->sitemenu()->getCachedMenus('core_footer');
//    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('core_footer');
    $this->view->showOption = $showOption = $this->_getParam('sitemenu_show_in_footer', 2);
    $this->view->footerSearchWidth = $this->_getParam('sitemenu_footer_search_width', 150);
    $this->view->is_language = $this->_getParam('sitemenu_is_language', 0);
    $this->view->sitestoreproductEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
    $sitemenu_check_footer_menu = Zend_Registry::isRegistered('sitemenu_check_footer_menu') ? Zend_Registry::get('sitemenu_check_footer_menu') : null;
    
    // DON'T SHOW WIDGET, IF PLUGIN NOT ACTIVATED.
    $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.isActivate', false);
    if(empty($isPluginActivate))
      return $this->setNoRender();
    
    // SHOW SOCIAL LINKS
    if( $showOption == 2 ) {
      $this->view->social_links_array = $social_link_array =  $this->_getParam('sitemenu_social_links', array("facebooklink", "twitterlink", "pininterestlink", "youtubelink", "linkedinlink"));
        if( !empty($social_link_array) ) {
          if (in_array('facebooklink', $social_link_array)){
            $this->view->facebook_url = $this->_getParam('facebook_url', 'http://www.facebook.com/');            
            
            $this->view->facebook_default_icon = $temp_facebook_default_icon = $this->_getParam('facebook_default_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/facebook.png');
            if($temp_facebook_default_icon == 'application/modules/Sitemenu/externals/images/facebook.png'){
              $this->view->facebook_default_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/facebook.png';
            }
            
            $this->view->facebook_hover_icon = $temp_facebook_hover_icon = $this->_getParam('facebook_hover_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overfacebook.png');
            if($temp_facebook_hover_icon == 'application/modules/Sitemenu/externals/images/overfacebook.png'){
              $this->view->facebook_hover_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overfacebook.png';
            }
            
            $this->view->facebook_title = $this->_getParam('facebook_title', 'Like us on Facebook');
          }
          if (in_array('pininterestlink', $social_link_array)){
            $this->view->pinterest_url = $this->_getParam('pinterest_url', 'https://www.pinterest.com/');
            
            $this->view->pinterest_default_icon = $temp_pinterest_default_icon = $this->_getParam('pinterest_default_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/pinterest.png');
            if($temp_pinterest_default_icon == 'application/modules/Sitemenu/externals/images/pinterest.png'){
              $this->view->pinterest_default_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/pinterest.png';
            }
            
            $this->view->pinterest_hover_icon = $temp_pinterest_hover_icon = $this->_getParam('pinterest_hover_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overpinterest.png');
            if($temp_pinterest_hover_icon == 'application/modules/Sitemenu/externals/images/overpinterest.png'){
              $this->view->pinterest_hover_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overpinterest.png';
            }
            
            $this->view->pinterest_title = $this->_getParam('pinterest_title', 'Pinterest');
          }
          if (in_array('twitterlink', $social_link_array)){
            $this->view->twitter_url = $this->_getParam('twitter_url', 'https://www.twitter.com/');
            
            $this->view->twitter_default_icon = $temp_twitter_default_icon = $this->_getParam('twitter_default_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/twitter.png');
            if($temp_twitter_default_icon == 'application/modules/Sitemenu/externals/images/twitter.png'){
              $this->view->twitter_default_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/twitter.png';
            }
            
            $this->view->twitter_hover_icon = $temp_twitter_hover_icon = $this->_getParam('twitter_hover_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overtwitter.png');
            if($temp_twitter_hover_icon == 'application/modules/Sitemenu/externals/images/overtwitter.png'){
              $this->view->twitter_hover_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overtwitter.png';
            }
            
            $this->view->twitter_title = $this->_getParam('twitter_title', 'Follow us on Twitter');
          }
          if (in_array('youtubelink', $social_link_array)){
            $this->view->youtube_url = $this->_getParam('youtube_url', 'http://www.youtube.com/');
            
            $this->view->youtube_default_icon = $temp_youtube_default_icon = $this->_getParam('youtube_default_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/youtube.png');
            if($temp_youtube_default_icon == 'application/modules/Sitemenu/externals/images/youtube.png'){
              $this->view->youtube_default_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/youtube.png';
            }
            
            $this->view->youtube_hover_icon = $temp_youtube_hover_icon = $this->_getParam('youtube_hover_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overyoutube.png');
            if($temp_youtube_hover_icon == 'application/modules/Sitemenu/externals/images/overyoutube.png'){
              $this->view->youtube_hover_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overyoutube.png';
            }
            
            $this->view->youtube_title = $this->_getParam('youtube_title', 'Youtube');
          }
          if (in_array('linkedinlink', $social_link_array)){
            $this->view->linkedin_url = $this->_getParam('linkedin_url', 'https://www.linkedin.com/');
            
            $this->view->linkedin_default_icon = $temp_linkedin_default_icon = $this->_getParam('linkedin_default_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/linkedin.png');
            if($temp_linkedin_default_icon == 'application/modules/Sitemenu/externals/images/linkedin.png'){
              $this->view->linkedin_default_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/linkedin.png';
            }
            
            $this->view->linkedin_hover_icon = $temp_linkedin_hover_icon = $this->_getParam('linkedin_hover_icon', $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overlinkedin.png');
            if($temp_linkedin_hover_icon == 'application/modules/Sitemenu/externals/images/overlinkedin.png'){
              $this->view->linkedin_hover_icon = $this->view->layout()->staticBaseUrl.'application/modules/Sitemenu/externals/images/overlinkedin.png';
            }
            
            $this->view->linkedin_title = $this->_getParam('linkedin_title', 'LinkedIn');
          }
       }
    }
    
    if(empty($sitemenu_check_footer_menu))
      return $this->setNoRender();
  }
}