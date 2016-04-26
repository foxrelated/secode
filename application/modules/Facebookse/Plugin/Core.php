<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Plugin_Core extends Zend_Controller_Plugin_Abstract
{  

public function routeShutdown(Zend_Controller_Request_Abstract $request) {
     
       if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			   $isFacebook = strstr($_SERVER['HTTP_USER_AGENT'], "facebook") ? true : false;
      }
      else {
        
          $isFacebook = false;  
      }
      if (empty($isFacebook)) return;
       $front = Zend_Controller_Front::getInstance();
       $module = $front->getRequest()->getModuleName();
			 $action = $front->getRequest()->getActionName();
			 $controller = $front->getRequest()->getControllerName();	
       $redirect_url = urlencode($_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri());
       $module_info =  $front->getRequest()->getParams();
       $module_id =  @$module_info['contentid'];
       $resourcetype = @$module_info['type'];
       if (!empty($resourcetype)) {
         $module_temp = explode("_", $resourcetype);
         $module = $module_temp[0];
       }
       //SET THE SUBJECT HERE
//       if (Engine_Api::_()->core()->hasSubject()) {
//         Engine_Api::_()->core()->clearSubject();         
//       }
       
       if(!empty($resourcetype) && !empty($module_id)) {
         $subject = Engine_Api::_()->getItem($resourcetype, $module_id);
         if($subject)
          Engine_Api::_()->core()->setSubject($subject);
       }
      //if (empty($resourcetype) && empty($module_id) && $module != 'core') return; 
       if ($resourcetype == 'user')
		 $module_info['user_id'] = $module_id;	
       //CHECK IF ADMIN HAS ENABLED THE OPEN GRAPH FOR THIS MODULE TYPE.

     if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup') && ($resourcetype ==  $module . '_album' || $resourcetype == $module . '_photo')) {
      $resourcetype = $module . '_photo';
      $module = $module . 'album';

    }
    if ($module == 'core')
      $module = 'home';
	  $module_fb = $module;
      if ($module == 'sitealbum' || $module == 'album') { 

				$module_fb = 'album';
        $resourcetype = 'album';
      }
      
			$enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module_fb, $resourcetype);
	  
			if (empty($enable_managemodule))
	      return false; 
    
			 $plugins_temp = $module;
			   	if (($module == 'user' && $controller == 'profile') && $action == 'index') {
          
          	$module_info['user_id'] = $module_info['id'];
				}
				
				else {
					if ($module == 'music' || $module == 'sitepagemusic' || $module == 'sitebusinessmusic' || $module == 'sitegroupmusic') {
					
						  $plugins_temp = 'playlist';
					}
					else if ($module == 'forum') {					
						$plugins_temp = 'topic';
					}
					else if ($module == 'list') {						
						$plugins_temp = 'listing';
					}   
					else {
						if ($module != 'home') {
							if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup') && $action == 'view' && $controller == 'album' ) {	 
						  
						    if ($module == 'sitepage') {
						      $module = 'sitepagealbum';
						      $plugins_temp = str_replace('sitepage', "", $module);
						    }
						    elseif ($module == 'sitebusiness')  {
						      $module = 'sitebusinessalbum';
						      $plugins_temp = str_replace('sitebusiness', "", $module);
						    }
                else {
                  $module = 'sitegroupalbum';
						      $plugins_temp = str_replace('sitegroup', "", $module);
                }
							}
							else if (($module == 'sitepage' || $module == 'sitebusiness'  || $module == 'sitegroup') && $action == 'view' && $controller == 'photo' ) {
							  return;
							}
							else if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup') && $action == 'view' && $controller == 'topic' ) {							 
						    $plugins_temp = 'topic';
							}
							else { 
							  
							  $plugins_temp = str_replace('sitepage', "", $module);
							  if ($plugins_temp == $module) {
							    $plugins_temp = str_replace('sitebusiness', "", $module);
							  }
                if ($plugins_temp == $module) {
							    $plugins_temp = str_replace('sitegroup', "", $module);
							  }
                if ($plugins_temp == $module) {
							    $plugins_temp = str_replace('siteevent', "", $module);
							  }
							  if ($module == 'sitepage') {							    
							    
							    $plugins_temp = 'page';
							  }
							  else if ($module == 'sitebusiness'){							   
							    $plugins_temp = 'business';
							  }
                else if ($module == 'sitegroup'){							   
							    $plugins_temp = 'group';
							  }
                else if ($module == 'siteevent'){							   
							    $plugins_temp = 'event';
							  }
							}
						  
							}
						}
					}
   	   
					$moduleinfo_array = array('module' => $module, 
					                          'controller' => $controller,
					                          'module_id' => @$module_id,
					                          'user_id' =>  @$module_info['user_id'],
					                          'action' => $action,
					                          'redirect_url' => $redirect_url,
                                    'resource_type' => $resourcetype
					                          
					                          );

					$_SESSION['fbmetainfo'] =  $moduleinfo_array;  
					$_SESSION['fbmetainfo_enter'] =  false;
  
  
 
  
  $request->setModuleName('facebookse');
				$request->setControllerName('index');
				$request->setActionName('opengraphredirect');
				
				
  
  
  }
	public function onRenderLayoutDefault($event)
  {  
		$facebookse_execution = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.is.exe');
		if( !empty($facebookse_execution) ) {
			// Arg should be an instance of Zend_View 
	    $isFacebook = Engine_Api::_()->facebookse()->isRenderFacebook();
			$view = $event->getPayload();
			$viewer = Engine_Api::_()->user()->getViewer();
			if( $view instanceof Zend_View) { 
        $view->headTranslate(array('Be the first to like this content.', 'You like this.', 'You and %s other people like this.', '%s people like this.', 'You', 'You and', 'and', 'like this.'));
				$front = Zend_Controller_Front::getInstance();
				$module = $front->getRequest()->getModuleName();
				$controllerName = $front->getRequest()->getControllerName();
				$action = $front->getRequest()->getActionName();
				$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
				$curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
				//CHECK IF LIKE MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
				$enable_likemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike');

				//CHECK IF ADMIN HAS ENABLE FACEBOOK LIKE INTEGRATION WITH SITE LIKE.
				$enable_likeintsetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('fb.site.likeint', 1); 
				 
			
				if (($module == 'user' && $action == 'home') || ($module == 'core' && ($action == 'index' || $action == 'requireuser')) )         {
					$module = 'home';
          $action = 'index';
				}
				
				$module_id = 0;
				if (Engine_Api::_()->core()->hasSubject())  
          $module_id = Engine_Api::_()->core()->getSubject()->getIdentity();
          $isFacebook = Engine_Api::_()->facebookse()->isRenderFacebook();
				 if (((Engine_Api::_()->core()->hasSubject() || $module == 'home') && !isset($_SESSION['fbmetainfo_enter']))) { 
				    Engine_Api::_()->facebookse()->showOpenGraph ($module,  $controllerName, '', $action, '', $front, $view, $viewer,$module_id);
				 }
			 
			}
			if($isFacebook) return;
			$enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
			if (!empty($enable_fboldversion)) {
				$socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
				$socialdnaversion = $socialdnamodule->version;
				if ($socialdnaversion >= '4.1.1') {
					$enable_fboldversion = 0;
				}
			}
      $enable_fbpagemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksepage');
			$currentbase_time = time();
			$base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.base.time');
			$check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.checkset.var');
			$controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.lsettings');
			$controller_result_lenght = strlen($controllersettings_result_show);
      $local_language = Engine_Api::_()->getApi('settings', 'core')->getSetting('fblanguage.id', 'en_US'); 
			$fbappid =  Engine_Api::_()->getApi("settings", "core")->core_facebook_appid;
			
			
			//GETTING THE SETTINGS OF ADMIN ABOUT EITHER THE SOCIALENGINE'S COMMNET BOX HAS TO BE REPLACED WITH THE FACEBOOK COMMENT BOX SOCIAL PLUGIN OR NOT FOR THE CURRENT MODULE.
			$comment_setting = 0;
			$getFBContent = 0;
			if ($module == 'sitealbum'  && $controllerName != 'photo') {
          $module = 'album';
      }
      $Subject_Set = false;
      //CHECK IF THE SUBJECT IS SET OR NOT.
      if (Engine_Api::_()->core()->hasSubject()) {
          $Subject_Set = true;
          $resourceType = Engine_Api::_()->core()->getSubject()->getType();
         
      }
     
      $checkDefaultModule =  Engine_Api::_()->facebookse()->checkDefaultModule ($module); 
      $success_showFBCommentBox =  Engine_Api::_()->facebookse()->showFBCommentBox ($module);      
			$module_temp = $module;
			if (strripos($module, 'sitepage') !== false) {
          $module_temp = 'sitepage';
      }
      if (strripos($module, 'sitebusiness') !== false) {
          $module_temp = 'sitebusiness';
      }
      if (strripos($module, 'sitegroup') !== false) {
          $module_temp = 'sitegroup';
      }
      if (strripos($module, 'siteevent') !== false) {
          $module_temp = 'siteevent';
      }
      
      
     
      
      //SPECIAL CASE OF SITEREVIEW PLUGIN.
     if ($module_temp == 'sitereview' || $module_temp == 'sitestoreproduct'){
        $checkDefaultModule = 0;
     }
      //GETTING THE SETTING FOR COMMENT BOX FOR THIS MODULE.
        
     
			if( $checkDefaultModule && ($action == 'view' || $action == 'playlist' || $action == 'shopping' || ($action == 'home' && $module_temp != 'list' && $module_temp != 'sitepage' && $module_temp != 'sitebusiness' && $module_temp != 'sitegroup' && $module_temp != 'recipe' && $module_temp != 'siteevent') || ($action == 'index' )) )  { 
			
			if (empty($success_showFBCommentBox)) {
			  $success_showFBCommentBox = 0;
			}
			else {
			  $_SESSION['comment_box'] = 1;
			}
			$enable_fbcommentbox = $success_showFBCommentBox;
			
			if (($controllerName == 'photo' && ($module == 'sitealbum' || $module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup')) || ($module == 'user') ) {
          $success_showFBCommentBox = 0;
          
      }
      else if ($module == 'sitealbum') {
        $enable_fbcommentbox = Engine_Api::_()->facebookse()->showFBCommentBox ('album');
        $module = 'album';
      }
		}
		else if ($success_showFBCommentBox && ! $checkDefaultModule){
		  $success_showFBCommentBox = 0; 
		  $enable_fbcommentbox = 1;
   }
   else {
      $success_showFBCommentBox = 0; 
		  $enable_fbcommentbox = 0;
   }
   
   //IF THE PAGE IS USER PROFILE PAGE AND FACEBOOK LIKE BUTTON IS ENABLED FOR HTIS PAGE THEN ALSO INCLUSE THE CORE.JS FILE.
   $loadJS = 0;
   if($module == 'user' && $action == 'index')
     $loadJS = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkLikeButton('user');
   //IF SHOWFBCOMMENTBOX IS TRUE THEN LOAD THE FACEBOOK CORE JS
   if($success_showFBCommentBox || $loadJS)
     $view->headScript()
				  ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');
  
		$event = 'domready';

      //SCRAPING THE URL WITH FACEBOOK URL LINTER TO CLEAR THE CACHE FIRST.
      
$script = <<<EOF
        			 
        		 local_language = '{$local_language}';
    					window.addEvent('domready', function () {  
                if($loadJS) 
                  call_advfbjs = 1;
               if(typeof call_advfbjs != 'undefined') {	
    					  enable_likemodule = '{$enable_likemodule}';
          			enable_likeintsetting = '{$enable_likeintsetting}';
                enable_fbpagemodule = '{$enable_fbpagemodule}';
          			enable_fboldversion = '{$enable_fboldversion}';
          			
          			if (typeof call_advfbjs != 'undefined' &&  call_advfbjs == 1)
          			   fbappid = '{$fbappid}';
          			//local_language = '{$local_language}';
          			enable_fbcommentbox = '{$enable_fbcommentbox}';
          			curr_fbscrapeUrl = '{$curr_url}';
          			if ($success_showFBCommentBox) { 
          			  call_advfbjs = 1;
          			  fbappid = '{$fbappid}';
    			     if (typeof defalutCommentClass != 'undefined' && $('global_content').getElement(defalutCommentClass)) {
          			  if ($success_showFBCommentBox == 1) { 
          			    SeaoCommentbox_obj = $('global_content').getElement(defalutCommentClass).getParent();
          			    SeaoCommentbox_obj.innerHTML = '';
                  } 
             
            }
                  
            showFbCommentBox ('{$curr_url}', '{$module}', '{$success_showFBCommentBox}');          
    			  			  
          }
          
           }
    	   });
           
      			
EOF;
		
		
    if( ($currentbase_time - $base_result_time > 4320000) && empty($check_result_show) ) {
				if( $controller_result_lenght != 20 ) {
					Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.config.type', 1);
					Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.flagtype.info', 1);
					return;
				} else {
					Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.checkset.var', 1);
				}
			} 
			
			$view->headScript()
				  ->appendScript($script); 
	}
  }

  public function onRenderLayoutDefaultSimple($event)
  { 
    $isFacebook = Engine_Api::_()->facebookse()->isRenderFacebook();
    if ($isFacebook)    // Forward
      return $this->onRenderLayoutDefault($event, 'simple');
    return;
  }
  
  
  
  
  //DELETE THE ITEM URL IF IT'S EXIST IN THE FACEBOOK STATISTIC TABLE.
  public function onItemDeleteBefore($event) {
    
    $item = $event->getPayload();
    //check if the item is supported item.
    $supportedModules = Engine_Api::_()->facebookse()->isSupportedModule ($item->getType());
    if ($supportedModules && is_object($item) && method_exists($item, 'getHref')){ 
      try {
          $curr_url = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $item->getHref();
       }
       catch (Exception $e) {
         
       } 
       $db = Engine_Db_Table::getDefaultAdapter();
       $db->query("DELETE FROM `engine4_facebookse_statistics` WHERE `url` =  '$curr_url'");
    }  
  }
  
}
?>