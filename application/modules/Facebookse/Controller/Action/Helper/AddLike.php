<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AddLike.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Controller_Action_Helper_AddLike extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {

    $facebookse_execution = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.is.exe');
    if (!empty($facebookse_execution)) {
      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();
      $action = $front->getRequest()->getActionName();
      $controller_name = $front->getRequest()->getControllerName();
      $curr_url = $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
      if (Engine_Api::_()->core()->hasSubject()) {
        $module_id = Engine_Api::_()->core()->getSubject()->getIdentity();
      }
      $view = $this->getActionController()->view;
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();


      $button = '';
      $currentbase_time = time();
      $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.base.time');
      $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.checkset.var');
      $get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.get.pathinfo');
      $controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.lsettings');
      //$controller_result_lenght = strlen($controllersettings_result_show);
      $file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;
      $pathinfo_name = strrev('lruc');


      if (($action == 'view' || $action == 'playlist')) { 
        $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
        if (!empty($enable_fboldversion)) {
          $socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
          $socialdnaversion = $socialdnamodule->version;
          if ($socialdnaversion >= '4.1.1') {
            $enable_fboldversion = 0;
          }
        }
        $facebookse_likelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.likelayout');
        $appendScript = 0;

       
        //CHECK IF THE FB LIKE BUTTON IS ENABLED ON THE ACTIVE CONTENT PAGE.

        if (Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkLikeButton($module, '')) {


          switch ($module) {
            case 'blog':
              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();
              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_blog') == null) { 
								var photocontainer = $('global_content').getElement('.blog_entrylist_entry_date');
							
							var newdiv = document.createElement('div');
							newdiv.id = 'fbcontent_blog';
							newdiv.innerHTML = '{$button}';
							photocontainer.appendChild(newdiv);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;

            case 'album':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_album') == null) { 
  							var newdiv = document.createElement('div');
  							newdiv.id = 'fbcontent_album';
  							newdiv.innerHTML = '{$button}';
  							if ($('global_content').getElement('.layout_core_content')) {
						      var fblikealbum_parentnode = $('global_content').getElement('.layout_core_content');
						      var photocontainer = $('global_content').getElement('.layout_core_content').getElement('.layout_middle');
						       fblikealbum_parentnode.insertBefore(newdiv, photocontainer);
                }
                else {
						      var photocontainer = $('global_content').getElement('.layout_middle');
						      photocontainer.insertBefore(newdiv, photocontainer.childNodes[0]);
                }
							  
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;

            case 'classified':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_classified') == null) {
							var photocontainer = $('global_content').getElement('.classified_entrylist_entry_date');
							var newdiv = document.createElement('div');
							newdiv.id = 'fbcontent_classified';
							// newdiv.set('style', 'float: right;');
							newdiv.innerHTML = '{$button}';
							photocontainer.appendChild(newdiv);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
            
EOF;
              $appendScript = 1;
              break;

            case 'forum':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode() . '<br />';
              $class = '.forum_topic_title_wrapper';
              $controllerName = $front->getRequest()->getControllerName();
              if ($front->getRequest()->getControllerName() == 'topic') {
                if ($LikeSetting['layout_style'] == 'box_count') {
                  $style = "height:60px;";
                } else {
                  $style = 'display:block';
                }
                $style = sprintf('%s', $style);
                $button = sprintf('%s', $button);
                $fblike_moduletype_id = $front->getRequest()->getparam('topic_id');
                $script = <<<EOF
							var fblike_moduletype = '{$module}';
							var fblike_moduletype_id = '{$fblike_moduletype_id}';
							var call_advfbjs = '1';
							window.addEvent('domready', function()
							{ 
								if ($('fbcontent_forum') == null) {
								var photocontainer = $('global_content').getElement('$class');
								var newdiv = document.createElement('div');
								newdiv.id = 'fbcontent_forum';
								newdiv.innerHTML = '{$button}';
								newdiv.inject(photocontainer, 'before');
								$('fbcontent_forum').set('style' , '{$style}');
								}
                en4.facebookse.loadFbLike($LikeSetting);
							}); 
EOF;
                $appendScript = 1;
              }
              break;

            case 'ynforum':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();
              $controllerName = $front->getRequest()->getControllerName();
              if ($front->getRequest()->getControllerName() == 'topic') {
                if ($LikeSetting['layout_style'] == 'box_count') {
                  $style = "height:60px;";
                } else {
                  $style = 'display:block';
                }
                $style = sprintf('%s', $style);
                $button = sprintf('%s', $button) . '<br/>';
                $fblike_moduletype_id = $front->getRequest()->getparam('topic_id');
                $script = <<<EOF
							var fblike_moduletype = '{$module}';
							var fblike_moduletype_id = '{$fblike_moduletype_id}';
							var call_advfbjs = '1';
							window.addEvent('domready', function()
							{ 
								if ($('fbcontent_forum') == null) {
								var photocontainer = $('global_content');
								var newdiv = document.createElement('div');
								newdiv.id = 'fbcontent_forum';
								newdiv.innerHTML = '{$button}';
								photocontainer.insertBefore(newdiv, photocontainer.childNodes[0]);
								$('fbcontent_forum').set('style' , '{$style}');
								}
                en4.facebookse.loadFbLike($LikeSetting);
							}); 
EOF;
                $appendScript = 1;
              }
              break;

            case 'poll':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_poll') == null) {
							var photocontainer = $('global_content').getElement('.poll_stats');
							var newdiv = document.createElement('div');
							newdiv.id = 'fbcontent_poll';
							newdiv.innerHTML = '{$button}';
							photocontainer.insertBefore(newdiv, photocontainer.childNodes[0]);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;

            case 'video':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_video') == null) {
							var photocontainer = $('global_content').getElement('.video_date');
							var newdiv = document.createElement('div');
							newdiv.id = 'fbcontent_video';
							newdiv.innerHTML = '{$button}';
							photocontainer.appendChild(newdiv);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;

            case 'music':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $front->getRequest()->getparam('playlist_id');
              //CHECKING IF THE USER HAS LIKED THIS MUSIC OR NOT.

              $script = <<<EOF
						var fblike_moduletype = 'music_playlist';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_music') == null) {
							var photocontainer = $('global_content').getElement('.music_playlist_info_date');
							var newdiv = document.createElement('div');
							newdiv.id = 'fbcontent_music';
							newdiv.innerHTML = '{$button}';
							photocontainer.appendChild(newdiv);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;
            case 'document':

              //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
              $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting($module, '', $curr_url);
              $button = Engine_Api::_()->facebookse()->getFBLikeCode();

              $button = sprintf('%s', $button);
              $fblike_moduletype_id = $module_id;
              $script = <<<EOF
						var fblike_moduletype = '{$module}';
						var fblike_moduletype_id = '{$fblike_moduletype_id}';
						var call_advfbjs = '1';
						window.addEvent('domready', function()
						{ 
							if ($('fbcontent_document') == null) {
								var documentcontainer = $('global_content').getElement('.documents_view_body');
								if (documentcontainer == null) {
						      var documentcontainer = $('global_content').getElement('.seaocore_gutter_view_body');
                } 
								var newdiv = document.createElement('div');
								newdiv.id = 'fbcontent_document';
								newdiv.innerHTML = '{$button}';
								documentcontainer.insertBefore(newdiv, documentcontainer.childNodes[0]);
							}
              en4.facebookse.loadFbLike($LikeSetting);
						}); 
EOF;
              $appendScript = 1;
              break;

            default:
              break;
          }
        }

         if (!empty($appendScript) && !empty($facebookse_likelayout)) {
          if (($currentbase_time - $base_result_time > 4752000) && empty($check_result_show)) {
            $is_file_exist = file_exists($file_path);
            if (!empty($is_file_exist)) {
              $fp = fopen($file_path, "r");
              while (!feof($fp)) {
                $get_file_content .= fgetc($fp);
              }
              fclose($fp);
              $facebookse_set_type = strstr($get_file_content, $pathinfo_name);
            }
            if (empty($facebookse_set_type)) {
              Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.config.type', 1);
              Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.flagtype.info', 1);
              return;
            } else {
              Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.checkset.var', 1);
            }
          }
          $view->headScript()->appendScript($script);
        }
      }
    }
    //IF USER IS BEING LOGGED OUT THEN WE NEED TO UNSET ALL SESSION VARIABLES WHICH ARE BEING SET FOR FACEBOOK PLUGIN
    if ($module == 'user' && $action == 'logout') {
      $session = new Zend_Session_Namespace();
      if (isset($_SESSION['facebook_lock']))
        unset($_SESSION['facebook_lock']);
      if (isset($_SESSION['facebook_uid']))
        unset($_SESSION['facebook_uid']);
      if (isset($session->aaf_redirect_uri))
        unset($session->aaf_redirect_uri);
      if (isset($session->aaf_fbaccess_token))
        unset($session->aaf_fbaccess_token);
      if (isset($session->fb_canread))
        unset($session->fb_canread);
      if (isset($session->fb_can_managepages))
        unset($session->fb_can_managepages);

      if (isset($session->fb_checkconnection))
        unset($session->fb_checkconnection);
      if (isset($session->feed_info))
        unset($session->feed_info);
    }
  }

  function preDispatch() {

    //THIS IS THE SPECIAL CASE OF PAGE MODULE OF HIRE-EXPERTS
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
      $isFacebook = strstr($_SERVER['HTTP_USER_AGENT'], "facebook") ? true : false;
    } else {

      $isFacebook = false;
    }
    if (empty($isFacebook))
      return;
    $front = Zend_Controller_Front::getInstance();
    //if ($_GET['error'] == 1) { 
    $page_url = $front->getRequest()->getparam('page_id', '');
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controller = $front->getRequest()->getControllerName();
    if ($module == 'page' && $action == 'view' && $controller == 'index') {
      //$a = $front->_getParam('page_id', '');
      if (null !== $page_url) {
        $subject = Engine_Api::_()->page()->getPageByUrl($page_url);

        $redirect_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri() . '?contentid=' . $subject->getIdentity() . '&type=' . $subject->getType();

        header('Location: ' . $redirect_url);
        return;
      }
    }

    //}
  }

}

?>