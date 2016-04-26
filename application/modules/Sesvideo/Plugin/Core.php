<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Core.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Plugin_Core {
  public function onStatistics($event) {
    $table = Engine_Api::_()->getDbTable('videos', 'sesvideo');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'video');
  }
	public function onRenderLayoutDefaultSimple($event) {
    return $this->onRenderLayoutDefault($event,'simple');
  }
  public function onRenderLayoutDefault($event,$mode=null) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		$view->headTranslate(array(
		'Quick share successfully', 'Video removed successfully from watch later', 'Video successfully added to watch later', 'Video added as Favourite successfully', 'Video Unfavourited successfully', 'Video Liked successfully', 'Video Unliked successfully', 'Playlist Liked successfully', 'Playlist Unliked successfully', 'Playlist added as Favourite successfully', 'Playlist Unfavourited successfully', 'Channel added as Favourite successfully','Channel Unfavourited successfully','Channel Liked successfully','Channel Unliked successfully','Artist added as Favourite successfully','Artist Unfavourited successfully','Channel un-follow successfully','Channel follow successfully','Artist Rated successfully','Video Rated successfully','Channel Rated successfully'
		));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $moduleName = $request->getModuleName();
		$actionName = $request->getActionName();
		$controllerName = $request->getControllerName();
		$viewer = Engine_Api::_()->user()->getViewer();		
		$checkWelcomePage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.check.welcome',2);
		$checkWelcomeEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.enable.welcome',1);
		$checkWelcomePage = (($checkWelcomePage == 1 && $viewer->getIdentity() == 0) ? true : ($checkWelcomePage == 0 && $viewer->getIdentity() != 0) ? true : ($checkWelcomePage == 2) ? true : false);
		if(!$checkWelcomeEnable)
			$checkWelcomePage = false;
		if(!$checkWelcomePage && $actionName == 'welcome' && $controllerName == 'index' && $moduleName == 'sesvideo'){
		  $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		  $redirector->gotoRoute(array('module' => 'sesvideo', 'controller' => 'index', 'action' => 'home'), 'sesvideo_general', false);
		}
		if($moduleName == 'sesvideo' && $actionName == 'index' && $controllerName == 'chanel')
			$view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
              . 'application/modules/Sesvideo/externals/styles/styles.css');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() == 0)
      $level = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    else
      $level = $viewer;
		$headScript = new Zend_View_Helper_HeadScript();
    $type = Engine_Api::_()->authorization()->getPermission($level, 'video', 'imageviewer');    
    if ($type == 1) {
      $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
                      . 'application/modules/Sesvideo/externals/scripts/sesvideovieweradvance/photoswipe.min.js')
              ->appendFile(Zend_Registry::get('StaticBaseUrl')
                      . 'application/modules/Sesvideo/externals/scripts/sesvideovieweradvance/photoswipe-ui-default.min.js')
              ->appendFile(Zend_Registry::get('StaticBaseUrl')
                      . 'application/modules/Sesvideo/externals/scripts/sesvideoimagevieweradvance.js');
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
              . 'application/modules/Sesbasic/externals/styles/photoswipe.css');
    } else {
      $loadImageViewerFile = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Sesvideo/externals/scripts/sesvideoimageviewerbasic.js';
      $headScript->appendFile($loadImageViewerFile);
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl
              . 'application/modules/Sesbasic/externals/styles/medialightbox.css');
    }
    $script = '';
    if ($moduleName == 'sesvideo') {
      $script .=
              "sesJqueryObject(document).ready(function(){
     sesJqueryObject('.core_main_sesvideo').parent().addClass('active');
    });
";
    }
        $script .= <<<EOF
  //Cookie get and set function
  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/"; 
  } 

  function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
  }
EOF;
    $script .=
            "var openVideoInLightBoxsesVideo = " . Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.enable.lightbox', 1) . ";
var videoURLsesvideo = '" . Engine_Api::_()->getApi('settings', 'core')->getSetting('video.video.manifest', 'video') . "';
var showAddnewVideoIconShortCut = ".Engine_Api::_()->getApi('settings', 'core')->getSetting('sesalbum.enable.addphotoshortcut',1).";
";
if($viewer->getIdentity() != 0){
	//$script .= 'sesJqueryObject(document).ready(function() {
	//if(sesJqueryObject("body").attr("id").search("sesvideo") > -1 && typeof showAddnewVideoIconShortCut != "undefined" && showAddnewVideoIconShortCut ){
		//sesJqueryObject("<a class=\'sesvideo_create_btn sesvideo_animation\' href=\'albums/create\' title=\'Add New\'><i class=\'fa fa-plus\'></i></a>").appendTo("body");
	//}
//});';		
}
    $view->headScript()->appendScript($script);
  }

  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {
      // Delete videos
      $videoTable = Engine_Api::_()->getDbtable('videos', 'sesvideo');
      $videoSelect = $videoTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach ($videoTable->fetchAll($videoSelect) as $video) {
        Engine_Api::_()->getApi('core', 'sesvideo')->deleteVideo($video);
      }
    }
  }

}
