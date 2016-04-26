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
class Facebookse_Api_Core extends Core_Api_Abstract {

  protected $_fbapi;
  protected $_fbObjectMetaType;

  /**
   * Gets a paginator for facebookse
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
//  public function getFacebooksePaginator($select)
//  {
//    $paginator = Zend_Paginator::factory($select);
//    if( !empty($params['page']) )
//    {
//      $paginator->setCurrentPageNumber($params['page']);
//    }
//    if( !empty($params['limit']) )
//    {
//      $paginator->setItemCountPerPage($params['limit']);
//    }
//    return $paginator;
//  }
  //FUNCTION NOT IN USE
  //THIS FUNCTION IS CALLED BY ALL LAYOUTS IN THIS FILE FOR GETTING THE SETTINGS OF LIKE BUTTON AND SHARE BUTTON.THIS FUNCTION TAKES THE INPUT MODEL NAME AND ACTION NAME EITHER IS FOR LIKE BUTTON OR FOR SHARE BUTTON.
  function admin_settings($module, $resourcetype = NULL) {
    //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
    $like_setting = '';
    $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
    $select = $mixsettingsTable->select()->where('module = ?', $module);
    if (!empty($resourcetype))
      $select = $select->where('resource_type = ?', $resourcetype);

    $mixsettingsTable = $mixsettingsTable->fetchRow($select);
    if (!empty($mixsettingsTable)) {
      $mixsettingsTable = $mixsettingsTable->toArray();

      return array('like_setting' => $mixsettingsTable);
    }
  }

  //THIS FUNCTION TAKES THE PAGE URL AND IF THERE URL IS ONLY OPEN FOR LOGGED IN USERS THEN WE WILL ADD SUBJECT PARAMS WITH THIS URL AND RETURN. IT IS BASICALLY USED FOR FACEBOOK TO FETCH THE CONTENT.
  public function getSubjectUrl($curr_url = '') {
    if (Engine_Api::_()->core()->hasSubject()) {
      $module_id = Engine_Api::_()->core()->getSubject()->getIdentity();
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
      if ($resourcetype == 'sitereview_listing')
        $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
      if ($module_id && !Engine_Api::_()->authorization()->isAllowed(Engine_Api::_()->core()->getSubject(), 'everyone', 'view')) {
        if (strstr($curr_url, '?'))
          $curr_url .= '&contentid=' . $module_id . '&type=' . $resourcetype;
        else
          $curr_url .= '?contentid=' . $module_id . '&type=' . $resourcetype;
      }
    }

    return $curr_url;
  }

  //FUNCTION NOT IN USE
  //FUNCTION FOR CHECKING VALIDITY OF EITHER TO ADD FACEBOOK LIKE BUTTON ON PAGE MODULE'S SUBMODULES OR NOT .
  function isValidFbLike($module = null) {
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $resourcetype = '';
    //CHECK IF ADMIN HAS ENABLED THE PLUGIN IN MANAGE PLUGIN SECTION.
    if (Engine_Api::_()->core()->hasSubject()) {
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();

      if ($module == 'sitealbum') {
        $module = 'album';
        //$resourcetype = $module;
      }

      if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitestore') && ($resourcetype == $module . '_album' || $resourcetype == $module . '_photo')) {
        $resourcetype = $module . '_photo';
        $module = $module . 'album';
      }

      $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);

      if (empty($enable_managemodule))
        return;
    }
    $facebookse_execution = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.is.exe');
    if (!empty($facebookse_execution)) {

      $controller_name = $front->getRequest()->getControllerName();
      $module_like = str_replace("www.", "", strtolower($_SERVER['HTTP_HOST']));
      $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();


      $curr_url = $this->getSubjectUrl($curr_url);
      //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
      //$admin_settings = $this->admin_settings($module, $resourcetype);

      $facebookse_flagtype_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.flagtype.info', 0);
      if (empty($facebookse_flagtype_info)) {
        $module_like = convert_uuencode($module_like);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('facebookse.config.type', $module_like);
      }

      $button = '';
      $currentbase_time = time();
      $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.base.time');
      $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.checkset.var');
      $get_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.get.pathinfo');
      $controllersettings_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.lsettings');
      $controller_result_lenght = strlen($controllersettings_result_show);
      $file_path = APPLICATION_PATH . '/application/modules/' . $get_result_show;
      $pathinfo_name = strrev('lruc');

      $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
      $facebookse_likelayout = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.likelayout');
      $pos = strpos($curr_url, 'http');
      if ($pos === false)
        $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $curr_url;
      $likesettings = $this->getLikeSetting($module, $resourcetype, $curr_url);
      $button = $this->getFBLikeCode();
      $button .= '<script type="text/javascript">call_advfbjs = 1;window.addEvent(\'domready\', function(){ en4.facebookse.loadFbLike(' . $likesettings . '); });</script>';

      if (!empty($facebookse_likelayout)) {
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
        return $button;
      }
    }
  }

  //GETTING THE PAGE ID OF THE CURRENT MODULE CONTENT:
  public function getTableId($front, $module, $action, $mixsettingsTable = array()) {

    $table_id = '';
    $controllerName = $front->getRequest()->getControllerName();

    if (!empty($mixsettingsTable)) {
      if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'siteevent') && Engine_Api::_()->core()->hasSubject($mixsettingsTable['resource_type'])) {
        if ($action == 'view' && $controllerName == 'topic') {
          $table_id = Engine_Api::_()->core()->getSubject($module . '_topic')->topic_id;
        } else {
          $table_id = Engine_Api::_()->core()->getSubject($mixsettingsTable['resource_type'])->$mixsettingsTable['resource_id'];
        }
      } else if ($module == 'forum' || $module == 'ynforum') {
        $table_id = $front->getRequest()->getparam('topic_id');
      } else if (empty($mixsettingsTable['default'])) {
        $table_id = $front->getRequest()->getparam('id', $front->getRequest()->getparam($mixsettingsTable['resource_id']));
      } else {
        $table_id = $front->getRequest()->getparam($mixsettingsTable['resource_id']);
      }

      if ($action == 'index' && $module != 'user') {
        $table_id = $front->getRequest()->getparam('id', $front->getRequest()->getparam($mixsettingsTable['resource_id']));
      }
    }
    return $table_id;
  }

  //GETTING THE NO OF COMMENTS FOR A PAGE URL.
  public function noOfFbComments($URL) {
    $response = @file_get_contents('https://graph.facebook.com/?ids=' . $URL);
    $totalFbComments = 0;
    if (!empty($response)) {

      $response = Zend_Json::decode($response);
    } else {
      $graph_url = 'https://graph.facebook.com/?ids=' . $URL;
      $ch = curl_init();
      $timeout = 0;
      curl_setopt($ch, CURLOPT_URL, $graph_url);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      ob_start();
      curl_exec($ch);
      curl_close($ch);
      $response = Zend_Json::decode(ob_get_contents());
      ob_end_clean();
    }

    if (!empty($response)) {
      if (!empty($response[$URL]['shares'])) {
        $totalFbComments = $response[$URL]['shares'];
      } else if (!empty($response[$URL]['comments'])) {
        $totalFbComments = $response[$URL]['comments'];
      }
    }
    return $totalFbComments;
  }

  //FINDING THE ADMIN SETTINGS FOR THIS MODULE.

  public function getLikeSetting($module, $resourcetype = '', $curr_url = '') {

    $permissionTable_Like = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
    $select = $permissionTable_Like->select()->where('module=?', $module);
    if (!empty($resourcetype)) {
      $select->where('resource_type=?', $resourcetype);
    }
    $permissionTable_Like = $permissionTable_Like->fetchRow($select);
    if (!empty($permissionTable_Like))
      $permissionTable_Like = $permissionTable_Like->toArray();

    if (!empty($permissionTable_Like) && !empty($permissionTable_Like['enable'])) {
      if ($permissionTable_Like['like_faces'] != 1) {
        $permissionTable_Like['like_faces'] = 'false';
      }

      //GETTING THE SETTING FOR USER FACE EITHER HAS TO SHOW OR NOT.
      if ($permissionTable_Like['send_button'] != 1) {
        $permissionTable_Like['send_button'] = 'false';
      }
    }

    if (!empty($curr_url)) {
      $permissionTable_Like['objectToLike'] = $this->getFBLikeCode();
      $permissionTable_Like['objectToLikeOrig'] = $curr_url;
    }

    if (empty($permissionTable_Like['action_type']))
      $permissionTable_Like['action_type'] = 'og.likes';
    if (empty($permissionTable_Like['object_type']))
      $permissionTable_Like['object_type'] = 'object';

    $front = Zend_Controller_Front::getInstance();
    $array_params = array('isajax' => 1, 'format' => 'html', 'module_current' => $module, 'action_current' => $front->getRequest()->getActionName(), 'requested_uri' => $front->getRequest()->getRequestUri());
    $getallparams = array_merge($array_params, $permissionTable_Like);
    return json_encode($getallparams);
  }

  //GETTING THE FACEBOOK LIKE BUTTON CODE:

  public function getFBLikeCode() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $view->headScript()
      ->appendFile($view->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');
    return '<ul><li><div id="contentlike-fb" class="fblikebutton clr" style="display:none;"></div></li></ul>';
  }

  public function getFBLikeCodeDefault($enable_fboldversion, $LikeSetting, $curr_url) {

    if (isset($LikeSetting['module']))
      $module = $LikeSetting['module'];
    else {
      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();
    }
    //CHECK IF ADMIN HAS ENABLED THE PLUGIN IN MANAGE PLUGIN SECTION.
    if (Engine_Api::_()->core()->hasSubject()) {
      if ($module == 'sitealbum')
        $module = 'album';
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
      if ($resourcetype == 'sitereview_listing')
        $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;


      $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);
      if (empty($enable_managemodule))
        return;
    }
    $curr_url = $this->getSubjectUrl($curr_url);
    $button = '';
    $like_setting = $LikeSetting['enable'];
    //CHECK IF url contains the http or https
    $pos = strpos($curr_url, 'http');
    if ($pos === false)
      $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $curr_url;
    if (!empty($enable_fboldversion)) {
      $button .= $like_setting == 1 ? '<iframe src="http://www.facebook.com/plugins/like.php?href=' . urlencode($curr_url) . '&amp;layout=' . $LikeSetting['layout_style'] . '&amp;show_faces=' . $LikeSetting['like_faces'] . '&amp;width=450&amp;action=' . $LikeSetting['like_type'] . '&amp;colorscheme=' . $LikeSetting['like_color'] . '&amp;font=' . $LikeSetting['like_font'] . '&amp;height=70" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:70px;" allowTransparency="true"></iframe>' : '';
    } else {
      $button .= $like_setting == 1 ? '<fb:like href="' . $curr_url . '" layout="' . $LikeSetting['layout_style'] . '" show_faces="' . $LikeSetting['like_faces'] . '" action="' . $LikeSetting['like_type'] . '" font="' . $LikeSetting['like_font'] . '" colorscheme="' . $LikeSetting['like_color'] . '" send="' . $LikeSetting['send_button'] . '" width="' . @$LikeSetting['like_width'] . '"></fb:like><br />' : '';
    }

    return $button;
  }

  public function getFBLikeURL($curr_url) {

    if (isset($LikeSetting['module']))
      $module = $LikeSetting['module'];
    else {
      $front = Zend_Controller_Front::getInstance();
      $module = $front->getRequest()->getModuleName();
    }
    //CHECK IF ADMIN HAS ENABLED THE PLUGIN IN MANAGE PLUGIN SECTION.
    if (Engine_Api::_()->core()->hasSubject()) {

      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
      if ($resourcetype == 'sitereview_listing')
        $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;

//				      $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);
//				      if (empty($enable_managemodule)) return;
    }
    if (Engine_Api::_()->core()->hasSubject()) {
      $module_id = Engine_Api::_()->core()->getSubject()->getIdentity();

      if ($module_id && !Engine_Api::_()->authorization()->isAllowed(Engine_Api::_()->core()->getSubject(), 'everyone', 'view')) {
        if (strstr($curr_url, '?'))
          $curr_url .= '&contentid=' . $module_id . '&type=' . $resourcetype;
        else
          $curr_url .= '?contentid=' . $module_id . '&type=' . $resourcetype;
      }
    }
    $button = '';
    //$like_setting = $LikeSetting['enable'];
    //CHECK IF url contains the http or https
    $pos = strpos($curr_url, 'http');
    if ($pos === false)
      $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $curr_url;
//    if (!empty($enable_fboldversion)) {
//			$button .= $like_setting == 1 ? '<iframe src="http://www.facebook.com/plugins/like.php?href='. urlencode($curr_url) .'&amp;layout='. $LikeSetting['layout_style'].'&amp;show_faces='. $LikeSetting['like_faces'] .'&amp;width=450&amp;action='. $LikeSetting['like_type'] .'&amp;colorscheme='. $LikeSetting['like_color'] .'&amp;font='. $LikeSetting['like_font'] .'&amp;height=70" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:70px;" allowTransparency="true"></iframe>' : '';
//		}	else {
//			$button .= $like_setting == 1 ? '<fb:like href="' . $curr_url . '" layout="' . $LikeSetting['layout_style'] .'" show_faces="' . $LikeSetting['like_faces'] . '" action="' . $LikeSetting['like_type'] . '" font="' . $LikeSetting['like_font'] . '" colorscheme="' . $LikeSetting['like_color'] . '" send="' . $LikeSetting['send_button'] . '" width="'. @$LikeSetting['like_width'] . '"></fb:like><br />' : '';
//		}

    return $curr_url;
  }

  public function checkMetaProperty() {

    //CHECKING IF THE "property" content type  is overwritten in the HeadMeta.php file at: "application/libraries/zend/view/helper/HeadMeta.php" file. This property is now added in socialengien 4.8.0 version.
    $coreversion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    if ($coreversion >= '4.8.0') {
      $doctypeHelper = new Zend_View_Helper_Doctype();
      $doctypes = $doctypeHelper->getDoctypes();
      if (isset($doctypes['XHTML1_RDFA']))
        return 0;
    }
    $activityTextPath_Original = APPLICATION_PATH
      . '/application/libraries/Zend/View/Helper/HeadMeta.php';
    $showTip = 1;
    if (is_file($activityTextPath_Original)) {
      @chmod($activityTextPath_Original, 0777);
      $fileData = file($activityTextPath_Original);
      foreach ($fileData as $key => $value) {
        $pos = strpos($value, "array('name', 'http-equiv', 'property')");
        if ($pos !== false) {
          $showTip = 0;
          break;
        } else {
          $pos = strpos($value, 'array("name", "http-equiv", "property")');
          if ($pos !== false) {
            $showTip = 0;
            break;
          }
        }
      }
    }

    return $showTip;
  }

  public function getCategoryPlugin($resourceType) {
    $module_temp = explode("_", $resourceType);
    $module = $module_temp[0];
    //CHECK IF THE MODULE BELONGS TO SITEGROUP
    $SiteGroup = explode("sitegroup", $module);
    if ($resourceType == 'sitepage_photo' || $resourceType == 'sitebusiness_photo' || $resourceType == 'sitegroup_photo' || $resourceType == 'sitestore_photo')
      $module = 'album';
    if ($module == 'home') {
      $plugin_type = 'homelike';
    } else if ($module == 'user') {
      $plugin_type = 'profilelike';
    } else if ($module == 'album' || $module == 'sitealbum' || $module == 'blog' || $module == 'poll' || $module == 'music' || $module == 'sitepageevent' || $module == 'sitepagedocument' || $module == 'sitepagepoll' || $module == 'sitepagevideo' || $module == 'sitepagereview' || $module == 'sitepagealbum' || $module == 'sitepagediscussion' || $module == 'sitepagenote' || $module == 'sitepagemusic' || $module == 'sitebusinessevent' || $module == 'sitebusinessdocument' || $module == 'sitebusinesspoll' || $module == 'sitebusinessvideo' || $module == 'sitebusinessreview' || $module == 'sitebusinessalbum' || $module == 'sitebusinessdiscussion' || $module == 'sitebusinessnote' || $module == 'sitebusinessmusic' || $module == 'ynblog' || (isset($SiteGroup[1]) && !empty($SiteGroup[1])) || $module == 'sitepageoffer' || $module == 'sitebusinessoffer' || $module == 'siteeventdocument') {
      $plugin_type = 'bloglike';
    } elseif ($module == 'classified' || $module == 'event' || $module == 'ynevent' || $module == 'forum' || $module == 'ynforum' || $module == 'group' || $module == 'advgroup' || $module == 'video' || $module == 'ynvideo' || $module == 'document' || $module == 'list' || $module == 'sitepage' || $module == 'recipe' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitereview' || $module == 'sitestore' || $module == 'sitestoreproduct' || $module == 'siteevent') {
      $plugin_type = 'grouplike';
    } else {
      $plugin_type = 'other';
    }
    return $plugin_type;
  }

  public function showOpenGraph($module, $controllerName, $user_id_anonymus, $action, $redirect_url_anonymus, $front, $view, $viewer, $module_id = '', $resourcetype_fb = null) {

    //GET THE MODULE CATEGORY TYPE:
    $base_url = $front->getBaseUrl();

    //THIS IS THE SPECIAL CASE OF THIRDPARTY PLUGIN WEBHIVEMODES FOR MEMBER PROFILE
    if ($module == 'webhivemods')
      $module = 'user';

    $plugin_type = $this->getCategoryPlugin($module);
    $resourcetype = '';
    $subject = '';

    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
    }

    if ($module == 'sitealbum') {
      $module = 'album';
    } elseif ($action == 'view' && ($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitestore' || $module == 'sitegroup' || $module == 'sitebusinessnote' || $module == 'sitepagenote' || $module == 'sitegroupnote')) {
      if ($controllerName == 'topic') {
        $module = $module . 'discussion';
        $plugin_type = 'bloglike';
      } elseif ($controllerName == 'album' || $controllerName == 'photo') {
        if ($module != 'sitepagenote' && $module != 'sitebusinessnote' && $module != 'sitegroupnote')
          $module = $module . 'album';
        $plugin_type = 'bloglike';
      }
    }
    else if ($module == 'sitereview' && !empty($resourcetype)) {
      if ($resourcetype != 'sitereview_listing')
        return;

      $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
    }

    if ($action == 'view' && $controllerName == 'photo' && ($module == 'list' || $module == 'recipe')) {
      $plugin_type = 'grouplike';
      $parent_module = str_replace('album', '', $module);
    }


    if (!empty($_SERVER['HTTP_HOST'])) {
      $fbmeta_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
    }

    if (!empty($redirect_url_anonymus)) {
      $fbmeta_url = $redirect_url_anonymus;
    }

    $fbmeta_url = $this->getSubjectUrl($fbmeta_url);
    $fbmeta_site_name = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;

    if ($resourcetype == 'sitepage_album' || $resourcetype == 'sitebusiness_album' || $resourcetype == 'sitegroup_album') {
      $resourcetype = str_replace("_album", "_photo", $resourcetype);
    }

    if ($module == 'sitevideo' && $action == 'view' && $controllerName == 'video') {
      $resourcetype = 'sitevideo_video';
    }
    if (empty($resourcetype))
      $resourcetype = $resourcetype_fb;

    //FINDING THE META INFO FROM FACEBOOKSE_METAINFOS TABLE.

    $metainfos = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($module, $resourcetype);

    if ($plugin_type != 'other' && Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default') !== 'default') {
      if ($metainfos->action_type == 'og.likes')
        $this->setObjectMetaType($metainfos->object_type);
      else
        $this->setObjectMetaType(Engine_Api::_()->getApi('settings', 'core')->getSetting('fbapp.namespace', '') . ':' . $metainfos->object_type);
    }

    if ($module == 'sitepagediscussion' && $action == 'view' && $controllerName == 'topic') {
      $module = 'sitepage';
    } else if ($module == 'sitebusinessdiscussion' && $action == 'view' && $controllerName == 'topic') {
      $module = 'sitebusiness';
    } else if ($module == 'sitegroupdiscussion' && $action == 'view' && $controllerName == 'topic') {
      $module = 'sitegroup';
    }

    //CHECKING IF THE "property" content type  is overwritten in the HeadMeta.php file at: "application/libraries/zend/view/helper/HeadMeta.php" file.
    $showTip = $this->checkMetaProperty();
    if ($metainfos && isset($metainfos->module_enable) && !empty($metainfos->module_enable) && empty($showTip)) {
      $admin_meta_title = $metainfos->title;
      $admin_meta_enable = $metainfos->opengraph_enable;
      $admin_meta_desc = $metainfos->description;
      $admin_meta_photo_id = $metainfos->photo_id;
      $admin_meta_type = $metainfos->types;

      if (($plugin_type == 'grouplike' || $plugin_type == 'other' ) && !empty($admin_meta_type)) {
        $admin_meta_types = unserialize($admin_meta_type);
        foreach ($admin_meta_types as $meta_types) {
          $admin_meta_types = explode("-", $meta_types);
          $metaTypes_Categories[$admin_meta_types[0]] = $admin_meta_types[1];
        }
      } else if (!empty($admin_meta_type)) {
        $admin_meta_types = unserialize($admin_meta_type);
        $admin_meta_types = explode("-", $admin_meta_types[0]);
      }

      $admin_meta_fbadmin_appid = $metainfos->fbadmin_appid;

      if ((($action == 'view' || $action == 'playlist' || $action == 'shopping' || $action == 'album' || ($action == 'home' && $module == 'user') || ($action == 'home' && $module == 'home' && $controllerName == 'index') || ($action == 'index' ) || ($action == 'detail' && $module == 'ultimatenews' && $controllerName == 'index')) && !empty($admin_meta_enable))) {

        //GETTING THE PAGE ID OF THE CURRENT MODULE CONTENT:
        if (empty($module_id)) {
          if (Engine_Api::_()->core()->hasSubject())
            $table_id = Engine_Api::_()->core()->getSubject()->getIdentity();
          else
            $table_id = Engine_Api::_()->facebookse()->getTableId($front, $module, $action, $metainfos->toArray());
        } else
          $table_id = $module_id;

        if (empty($table_id) && $plugin_type != 'homelike' && $plugin_type != 'profilelike')
          return;

        //FINDING INFORMATION FROM BLOGLIKE TABLES.
        $user_id = $front->getRequest()->getparam('user_id', null);
        //THIS IS SPECIAL CASE FOR ALBUM ONLY BECAUSE IN THIS CASE WE DON'T HAVE TO JOIN WITH USER TABLE BECAUSE WE HAVE TO PHOTO OF ALBUM AND PHOTO NOT USER AS WE WERE SHOWING FOR OTHER BOLG LIKE PLUGINS.
        if ($module == 'album' || $module == 'sitepagealbum' || $module == 'sitebusinessalbum' || $module == 'sitegroupalbum') {

//			      if ($module == 'album') {
//			        if ($controllerName == 'photo') { 
//			          $metainfos = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($module, 'album_photo');
//			        }
//			      
//			      }
          if ($controllerName == 'photo') {
//								$content_id = $front->getRequest()->getParam($metainfos->resource_id);
//								if (empty($content_id))
//										$content_id = $table_id;
            $fbmetainfoTable = $subject;
          }

          if (!empty($fbmetainfoTable)) {
            $fbmeta_title = $fbmetainfoTable->getTitle();
            $fbmeta_desc = $fbmetainfoTable->getDescription();
          } else {
            $fbmeta_title = '';
            $fbmeta_desc = '';
          }

          if (empty($fbmeta_title)) {
            $album_id = $front->getRequest()->getParam('album_id');

            if ($module == 'album')
              $fbmetainfoTable_album[0] = Engine_Api::_()->getItem('album', $album_id);
            else
              $fbmetainfoTable_album[0] = Engine_Api::_()->getItem(str_replace('album', '_album', $module), $album_id);

            if (empty($fbmetainfoTable))
              $fbmetainfoTable = $fbmetainfoTable_album[0];

            $fbmeta_title = $fbmetainfoTable_album[0]->title;
            if (empty($fbmeta_desc)) {
              $fbmeta_desc = $fbmetainfoTable_album[0]->description;
            }
          }
          $photo_id = $fbmetainfoTable->photo_id;

//  				 $user_id = $metainfos->owner_field;
//           if (!isset ($fbmetainfoTable->user_id))
//             $user_id = 'owner_id';      
//  				$owner_id = $fbmetainfoTable->$user_id;
          $owner_id = $fbmetainfoTable->getOwner()->getIdentity();
        } else {
          if ($module != 'home' && $module != 'user') {
            $fbMixResults = $metainfos->toArray();

            if (empty($fbMixResults['resource_type']) && ($fbMixResults['module'] == 'sitepagediscussion' || $fbMixResults['module'] == 'sitebusinessdiscussion' || $fbMixResults['module'] == 'sitegroupdiscussion' || $fbMixResults['module'] == 'forum')) {
              $fbMixResults['resource_type'] = str_replace("discussion", '', $fbMixResults['module']) . '_topic';
            }

            if ($fbMixResults['module'] == 'sitereview') {
              $sitereview_resourcetype = explode("_", $fbMixResults['resource_type']);
              $fbMixResults['resource_type'] = $sitereview_resourcetype[0] . '_' . $sitereview_resourcetype[1];
            }

            $plugin_table = Engine_Api::_()->getItemTable($fbMixResults['resource_type']);
            $plugin_tableName = $plugin_table->info('name');
          }
        }

        if ($plugin_type == 'bloglike' && $module != 'album' && $module != 'sitepagealbum' && $module != 'sitebusinessalbum' && $module != 'sitegroupalbum') {

          $fbMixResults = $metainfos->toArray();
          $user_table = Engine_Api::_()->getDbtable('users', 'user');
          $user_tableName = $user_table->info('name');
          $modulepostowner = $fbMixResults['owner_field'];

          //WE ARE HAVING JOIN HERE BECAUSE WE HAVE TO GET USER PROFILE PHOTO IF IT EXIST.
          if ($module == 'music' || $module == 'sitepagemusic' || $module == 'sitebusinessmusic' || $module == 'sitepageevent' || $module == 'sitepagevideo' || $module == 'sitepagenote' || $module == 'sitebusinessevent' || $module == 'sitebusinessvideo' || $module == 'sitebusinessnote' || $module == 'sitegroupmusic' || $module == 'sitegroupevent' || $module == 'sitegroupvideo' || $module == 'sitegroupnote') {
            $select = $plugin_table->select()
              ->setIntegrityCheck(false)
              ->from($plugin_tableName)
              ->where($fbMixResults['resource_id'] . " =? ", $table_id)
              ->limit(1);
          } elseif (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup') && $action == 'view' && $controllerName == 'topic') {
            $plugin_table = Engine_Api::_()->getDbtable('posts', $module);
            $plugin_tableName = $plugin_table->info('name');
            $select = $plugin_table->select()
              ->setIntegrityCheck(false)
              ->from($plugin_tableName, array('body', 'user_id'))
              ->joinLeft($user_tableName, "$user_tableName.user_id = $plugin_tableName.$modulepostowner", array('photo_id'))
              ->where('topic_id' . ' = ?', $table_id)
              ->order('post_id ASC')
              ->limit(1);
          } else {
            $select = $plugin_table->select()
              ->setIntegrityCheck(false)
              ->from($plugin_tableName);
            //CHECK IF THE PLUIGN TYPE HAS THE PHOTO ID 

            if ($module != 'ynblog')
              $select->joinLeft($user_tableName, "$user_tableName.user_id = $plugin_tableName.$modulepostowner", array('photo_id', 'user_id'));
            else
              $select->joinLeft($user_tableName, "$user_tableName.user_id = $plugin_tableName.$modulepostowner", array('user_id'));

            $select->where($fbMixResults['resource_id'] . " =? ", $table_id)
              ->limit(1);
          }
          $fbmetainfoTable = $plugin_table->fetchRow($select);

          if (!$fbmetainfoTable)
            return;

          $owner_id = $fbmetainfoTable->$modulepostowner;
          if ($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup') {
            if ($action == 'view' && $controllerName == 'topic') {
              $topic = Engine_Api::_()->getItem($module . '_topic', $table_id);
              $fbmeta_title = $topic->title;
              $fbmeta_desc = $fbmetainfoTable->$fbMixResults['module_description'];
              //THE PHOTO OF TOPIC WILL BE THE PHOTO OF THE OWNER OF THIS TOPIC. SO THE PHOTO WILL BE PAGE PHOTO.
              $page = str_replace("site", "", $module);
              $page_id = $page . '_id';
              $pageObject = Engine_Api::_()->getItem($module . '_' . $page, $topic->$page_id);
              $photo_id = $pageObject->photo_id;

              if ($photo_id) {
                $fbmetainfoTable = $pageObject;
              } else {
                $fbmetainfoTable = Engine_Api::_()->getItem('user', $fbmetainfoTable->user_id);
              }
            }
          } else {
            $fbmeta_title = $fbmetainfoTable->$fbMixResults['module_title'];
            $fbmeta_desc = $fbmetainfoTable->$fbMixResults['module_description'];
          }

          if ($module != 'sitepagedocument' && $module != 'sitebusinessdocument' && $module != 'sitegroupdocument' && $module != 'siteeventdocument') {

            if (isset($fbmetainfoTable->photo_id) && !empty($fbmetainfoTable->photo_id)) {
              $photo_id = $fbmetainfoTable->photo_id;
            } else if ($module == 'sitepagenote' || $module == 'sitebusinessnote' || $module == 'sitepagepoll' || $module == 'sitebusinesspoll' || $module == 'sitegroupnote' || $module == 'sitegrouppoll') {
              if ($module == 'sitepagenote' || $module == 'sitepagepoll')
                $pageObject = Engine_Api::_()->getItem('sitepage_page', $fbmetainfoTable->page_id);
              else if ($module == 'sitebusinessnote' || $module == 'sitebusinesspoll')
                $pageObject = Engine_Api::_()->getItem('sitebusiness_business', $fbmetainfoTable->business_id);
              else if ($module == 'sitegroupnote' || $module == 'sitegrouppoll')
                $pageObject = Engine_Api::_()->getItem('sitegroup_group', $fbmetainfoTable->group_id);
              if (!empty($pageObject->photo_id)) {
                $photo_id = $pageObject->photo_id;
              }
            } elseif ($module == 'blog' || $module == 'poll' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitepage' || $module == 'ynblog' || $module == 'sitestore') {
              $userObject = Engine_Api::_()->getItem('user', $fbmetainfoTable->user_id);
              $photo_id = $userObject->photo_id;
            } else {
              if (!isset($photo_id))
                $photo_id = '';
            }
          }
        }

        if ($plugin_type == 'bloglike') {
          //CHECKING IF USER PROFILE PHOTO ID IS EMPTY THEN SET META IMAGE TO ADMIN SELECTED IMAGE.
          if ($module != 'sitepagedocument' && $module != 'sitebusinessdocument' && $module != 'sitegroupdocument' && $module != 'siteeventdocument') {
            if (empty($photo_id)) {
              $safeName = ( 'thumb.main' ? str_replace('.', '_', 'thumb.main') : 'main' );
              $fbmeta_imageUrl = '';

              //CHECKING FOR ADMIN SELECTED META IMAGE FOR THIS EVENT.
              if (!empty($admin_meta_photo_id)) {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
              } else {
                // Default image
                if (!$fbmeta_imageUrl) {
                  if (method_exists($fbmetainfoTable, 'getNoPhotoImage')) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getNoPhotoImage();
                  } else {
                    if ($module == 'blog' || $module == 'poll' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitepage' || $module == 'ynblog' || $module == 'sitestore') {
                      $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . 'User' . '/externals/images/nophoto_user_thumb_profile.png';
                    } else {
                      if ($module == 'music' || $module == 'sitepagemusic' || $module == 'sitebusinessmusic' || $module == 'sitegroupmusic') {
                        $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($module) . '/externals/images/nophoto_' . $fbmetainfoTable->getShortType() . '_main.png';
                      } else if ($module == 'sitepagealbum' || $module == 'sitebusinessalbum' || $module == 'sitegroupalbum') {
                        $parent_module = str_replace("album", '', $module);
                        $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($parent_module) . '/externals/images/nophoto_' . $fbmetainfoTable->getShortType() . '_thumb_normal.png';
                      } else if ($module == 'sitepagepoll' || $module == 'sitebusinesspoll' || $module == 'sitegrouppoll') {
                        $parent_module_tempmodule_1 = str_replace("poll", '', $module);
                        $parent_module_tempmodule_2 = str_replace("site", '', $parent_module_tempmodule_1);
                        $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($parent_module_tempmodule_1) . '/externals/images/nophoto_' . $parent_module_tempmodule_2 . '_' . $safeName . '.png';
                      } else {
                        $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($module) . '/externals/images/nophoto_' . $fbmetainfoTable->getShortType() . '_' . $safeName . '.png';
                      }
                    }
                  }
                }
              }
            } else {

              if ($module == 'sitepagenote' || $module == 'sitebusinessnote' || $module == 'sitepagepoll' || $module == 'sitebusinesspoll' || $module == 'sitegrooupnote' || $module == 'sitegrouppoll') {
                if (!empty($pageObject)) {
                  if (!Engine_Api::_()->seaocore()->isCdn()) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $pageObject->getPhotoUrl('thumb.main');
                  } else {
                    $fbmeta_imageUrl = $pageObject->getPhotoUrl('thumb.main');
                  }
                } else {
                  // Get url
                  if (!Engine_Api::_()->seaocore()->isCdn()) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
                  } else {
                    $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
                  }
                }
              } elseif ($module == 'blog' || $module == 'poll' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitepage' || $module == 'ynblog' || $module == 'sitestore') {
                if (!empty($userObject)) {
                  if (!Engine_Api::_()->seaocore()->isCdn()) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $userObject->getPhotoUrl('thumb.main');
                  } else {
                    $fbmeta_imageUrl = $userObject->getPhotoUrl('thumb.main');
                  }
                } else {
                  // Get url
                  if (!Engine_Api::_()->seaocore()->isCdn()) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
                  } else {
                    $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
                  }
                }
              } else {
                // Get url
                if (!Engine_Api::_()->seaocore()->isCdn()) {
                  $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
                } else {
                  $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
                }
              }
            }
          } else {
            if ($fbmetainfoTable->status == 1) {
              $fbmeta_imageUrl = $fbmetainfoTable->thumbnail;
            } else if (!empty($admin_meta_photo_id)) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $base_url . '/application/modules/' . $module . '/externals/images/' . $module . '_thumb.png';
            }
          }
          //FINDING THE TYPE OF META.
          if ($module == 'blog') {
            $fbmeta_type = 'blog';
          } else if ($module == 'music' || $module == 'sitepagemusic' || $module == 'sitebusinessmusic' || $module == 'sitegroupmusic') {
            $fbmeta_type = 'song';

            //FETCHING THE SONG INFO LIKE SONG TITLE AND SONG PATH.
            $plugin_songinfo_table = Engine_Api::_()->getDbtable('playlistSongs', $module);
            $plugin_songinfo_tableName = $plugin_songinfo_table->info('name');
            $select = $plugin_songinfo_table->select()
              ->setIntegrityCheck(false)
              ->from($plugin_songinfo_tableName, array('file_id', 'title'))
              ->where('playlist_id' . ' = ?', $table_id)
              ->limit(1);
            $fbmetainfosongsTable = $plugin_songinfo_table->fetchRow($select);
            if (!empty($fbmetainfosongsTable)) {

              $song_title = $fbmetainfosongsTable->title;

              //NOW FETCHING THE SONG PATH FROM STORAGE_FILES TABLE.
              if (!empty($fbmetainfosongsTable->file_id)) {
                $plugin_songpath_table = Engine_Api::_()->getDbtable('files', 'storage');
                $plugin_songpath_tableName = $plugin_songpath_table->info('name');
                $song_path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $base_url . '/' . $plugin_songpath_table->select()
                    ->setIntegrityCheck(false)
                    ->from($plugin_songpath_tableName, array('storage_path'))
                    ->where('file_id' . ' = ?', $fbmetainfosongsTable->file_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
              }
            }
          } else if (!empty($admin_meta_types[1])) {
            $fbmeta_type = $admin_meta_types[1];
          }
        } else if ($plugin_type == 'grouplike') {
          $module_temp = $module;

          if (($module == 'forum' || $module == 'ynforum') && empty($table_id)) {
            return false;
          }

          if ($module_temp == 'forum' || $module == 'ynforum') {
            $plugin_forum_table = Engine_Api::_()->getDbtable('forums', $module);
            $plugin_forum_tableName = $plugin_forum_table->info('name');
            $plugin_user_table = Engine_Api::_()->getDbtable('users', 'user');
            $plugin_user_tableName = $plugin_user_table->info('name');
          }

          $select = $plugin_table->select()
            ->setIntegrityCheck(false)
            ->from($plugin_tableName);

          if ($module == 'forum' || $module == 'ynforum') {
            $select->join($plugin_forum_tableName, "$plugin_forum_tableName.forum_id = $plugin_tableName.forum_id", array('category_id'));
            $select->join($plugin_user_tableName, "$plugin_user_tableName.user_id = $plugin_tableName.user_id", array('photo_id'));
          }
          $select->where($fbMixResults['resource_id'] . ' = ?', $table_id)
            ->limit(1);
          $fbmetainfoTable = $plugin_table->fetchRow($select);

          if (empty($fbmetainfoTable))
            return;

          $owner_id = $fbmetainfoTable->$fbMixResults['owner_field'];
          $fbmeta_title = $fbmetainfoTable->$fbMixResults['module_title'];
          $fbmeta_desc = $fbmetainfoTable->$fbMixResults['module_description'];

          if ($controllerName == 'photo') {
            if ($module == 'list') {
              $parent_module = Engine_Api::_()->getItem('list_listing', $fbmetainfoTable->listing_id);
              $category_id = $parent_module->category_id;
              if (empty($fbmeta_title)) {
                $fbmeta_title = $parent_module->title;
                $fbmeta_desc = $parent_module->body;
              }
            } else if ($module == 'recipe') {
              $parent_module = Engine_Api::_()->getItem('recipe', $fbmetainfoTable->recipe_id);
              $category_id = $parent_module->category_id;
              if (empty($fbmeta_title)) {
                $fbmeta_title = $parent_module->title;
                $fbmeta_desc = $parent_module->body;
              }
            }
          } else {
            if (isset($fbmetainfoTable->category_id))
              $category_id = $fbmetainfoTable->category_id;
          }
          if ($module != 'document') {
            if (isset($fbmetainfoTable->photo_id))
              $photo_id = $fbmetainfoTable->photo_id;
          }
          //NOW WE HAVE TO FIND THE TYPE FOR THIS EVENT WHICH ADMIN HAS SELECTED.
          if (!empty($metaTypes_Categories) && isset($category_id)) {
            foreach ($metaTypes_Categories as $key => $cat) {
              if ($key == $category_id) {
                $fbmeta_type = $cat;
              }
            }
          }
          if ($module != 'document' && $module != 'sitereview') {
            if (isset($photo_id) && empty($photo_id)) {
              $safeName = ( 'thumb.main' ? str_replace('.', '_', 'thumb.main') : 'main' );
              $fbmeta_imageUrl = '';
              //finding the photo id from facebook_metainfos table.
              if (!empty($admin_meta_photo_id)) {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
              } else {
                // Default image
                if (!$fbmeta_imageUrl) {
                  if (method_exists($fbmetainfoTable, 'getNoPhotoImage')) {
                    $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getNoPhotoImage();
                  } else {
                    if ($module == 'forum' || $module == 'ynforum') {
                      $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . 'User' . '/externals/images/nophoto_user_thumb_profile.png';
                    } else if ($module == 'sitereview') {
                      $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($module) . '/externals/images/nophoto_listing_' . $safeName . '.png';
                    } else {
                      $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($module) . '/externals/images/nophoto_' . $fbmetainfoTable->getShortType() . '_' . $safeName . '.png';
                    }
                  }
                }
              }
            } else {
              $thumbnailtype = 'thumb.main';

              if ($module == 'ynvideo') {
                $thumbnailtype = 'thumb.large';
              }

              // Get url
              if (!Engine_Api::_()->seaocore()->isCdn()) {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl($thumbnailtype);
              } else {
                $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl($thumbnailtype);
              }
            }
          } elseif ($module == 'document') {
            if ($fbmetainfoTable->status == 1) {
              $fbmeta_imageUrl = $fbmetainfoTable->thumbnail;
            } else if (!empty($admin_meta_photo_id)) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . 'application/modules/Document/externals/images/document_thumb.png';
            }
          } elseif ($module == 'sitereview') {
            // Get url
            if (!Engine_Api::_()->seaocore()->isCdn()) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
            }
          }

          if (($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'recipe' || $module == 'list' || $module == 'sitestore' || $module == 'siteevent') && $controllerName != 'photo') {
            //CHECK FOR PAGE VIEW PRIVACY. IF PAGE IS AVAILABLE FOR VIEW THEN SHOW THE CONTACT DATA INFO OF PAGE.
            switch ($module) {

              case 'sitepage':

                $sitepage = Engine_Api::_()->getItem('sitepage_page', $table_id);
                $canView = Engine_Api::_()->sitepage()->canViewPage($sitepage);
                break;

              case 'sitebusiness':

                $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $table_id);
                $canView = Engine_Api::_()->sitebusiness()->canViewBusiness($sitebusiness);
                break;

              case 'sitegroup':

                $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $table_id);
                $canView = Engine_Api::_()->sitegroup()->canViewGroup($sitegroup);
                break;

              case 'recipe':

                $recipe = Engine_Api::_()->getItem('recipe', $table_id);
                $canView = Engine_Api::_()->authorization()->isAllowed($recipe, 'everyone', 'view');
                break;

              case 'list':
                $list = Engine_Api::_()->getItem('list_listing', $table_id);
                $canView = Engine_Api::_()->authorization()->isAllowed($list, 'everyone', 'view');
                break;

              case 'sitestore':

                $sitestore = Engine_Api::_()->getItem('sitestore_store', $table_id);
                $canView = Engine_Api::_()->sitestore()->canViewStore($sitestore);
                break;

              case 'siteevent':

                $siteevent = Engine_Api::_()->getItem('siteevent_event', $table_id);
                $canView = Engine_Api::_()->authorization()->isAllowed($siteevent, 'everyone', 'view');
                break;
            }

            if ($canView) {
              $params = array('longitude', 'latitude', 'formatted_address', 'city', 'state', 'zipcode', 'country');
              $fbmetainfolocationTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getLocationinfo($module, $fbMixResults['resource_id'], $table_id, $params);
              if (!empty($fbmetainfolocationTable)) {
                $longitude = $fbmetainfolocationTable->longitude;
                $latitude = $fbmetainfolocationTable->latitude;
                $street_address = $fbmetainfolocationTable->formatted_address;
                $locality = $fbmetainfolocationTable->city;
                $region = $fbmetainfolocationTable->state;
                $postal_code = $fbmetainfolocationTable->zipcode;
                $country_name = $fbmetainfolocationTable->country;
                if ($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitestore') {
                  $email = $fbmetainfoTable->email;
                  $phone_no = $fbmetainfoTable->phone;
                }
              }
            }
          }

          if ($module == 'sitestoreproduct') {
            $fbMetaProductId = $fbmetainfoTable->product_id;

            if ($fbmetainfoTable->stock_unlimited == 1) {
              $fbMetaProductAvailability = 'in stock';
            } elseif (!empty($fbmetainfoTable->in_stock)) {
              $fbMetaProductAvailability = 'in stock';
            } else {
              $fbMetaProductAvailability = 'out of stock';
            }

            $fbMetaProductCondition = 'new';
            $fbMetaProductPriceAmount = $fbmetainfoTable->price;
            $fbMetaProductCurrency = Engine_Api::_()->sitestoreproduct()->getCurrencySymbol();

//            echo $fbMetaProductId." | ".$fbMetaProductAvailability." | ".$fbMetaProductCondition." | ".$fbMetaProductPriceAmount." | ".$fbMetaProductCurrency;
//            
//            die;
          }
        } else if ($plugin_type == 'homelike') {
          $fbmeta_title = $admin_meta_title;
          $fbmeta_type = 'website';
          if (!empty($viewer)) {
            $owner_id = $viewer->getIdentity();
          } else {
            $owner_id = '';
          }
          $fbmeta_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/';
          if (!empty($admin_meta_photo_id)) {
            if (!Engine_Api::_()->seaocore()->isCdn()) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = $metainfos->getPhotoUrl('thumb.main');
            }
          } else {
            $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . 'Facebookse' . '/externals/images/nophoto_site_logo_profile.png';
          }
          $fbmeta_desc = $admin_meta_desc;
        } else if ($plugin_type == 'profilelike') {
          if (Engine_Api::_()->core()->hasSubject() && (Engine_Api::_()->core()->getSubject()->getType() == 'user')) {
            $profile_user = Engine_Api::_()->core()->getSubject();
          } else if (!empty($user_id_anonymus)) {
            $profile_user = $viewer = Engine_Api::_()->user()->getUser($user_id_anonymus);
          }

          if (!empty($profile_user)) {
            $fbmeta_type = 'website';
            if (!empty($viewer)) {
              $owner_id = $viewer->getIdentity();
            } else {
              $owner_id = '';
            }

            //FETCHING THE PROFILE USER INFO .

            if (!empty($profile_user->photo_id)) {
              if (!Engine_Api::_()->seaocore()->isCdn()) {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $profile_user->getPhotoUrl('thumb.main');
              } else {
                $fbmeta_imageUrl = $profile_user->getPhotoUrl('thumb.main');
              }
            } else if (!empty($admin_meta_photo_id)) {
              if (!Engine_Api::_()->seaocore()->isCdn()) {
                $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
              } else {
                $fbmeta_imageUrl = $metainfos->getPhotoUrl('thumb.main');
              }
            } else {

              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
            }

            $translate = $view;
            $username = $profile_user->getTitle();

            $fbmeta_title = $translate->translate("%s's Profile - ", $username) . Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));
            $userdisplayname = $translate->translate("%s's Profile on ", $username);
            $fbmeta_desc = $userdisplayname . Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')) . '. ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.description'));
          } else {
            $fbmeta_title = '';
            $fbmeta_desc = '';
            $fbmeta_imageUrl = '';
          }
        } else if ($plugin_type == 'other') {
          //NOW WE HAVE TO FIND THE TYPE FOR THIS EVENT WHICH ADMIN HAS SELECTED. FOR THIS WE HAVE TO FIRST CHECK THAT IF THE CURRENT SUBJECT HAS THE CATEGORY OR NOT IF NOT THEN WE WILL USE THE DEFAULT WHICH ADMIN HAS SET FOR THAT TYPE OF CONTENT.

          $fbmetainfoTable = Engine_Api::_()->getItem($fbMixResults['resource_type'], $table_id);
          if (!$fbmetainfoTable)
            return;
          if (isset($fbmetainfoTable->category_id)) {
            $category_id = $fbmetainfoTable->category_id;
            if (!empty($metaTypes_Categories) && isset($category_id)) {
              foreach ($metaTypes_Categories as $key => $cat) {
                if ($key == $category_id) {
                  $fbmeta_type = $cat;
                }
              }
            }
          } else {
            if (!empty($metaTypes_Categories))
              $fbmeta_type = $metaTypes_Categories['1'];
          }

          $fbmeta_title = '';
          $fbmeta_desc = '';
          if (isset($fbmetainfoTable->$fbMixResults['module_title']))
            $fbmeta_title = $fbmetainfoTable->$fbMixResults['module_title'];

          if (isset($fbmetainfoTable->$fbMixResults['module_description']))
            $fbmeta_desc = $fbmetainfoTable->$fbMixResults['module_description'];

          //IF THE TITLE AND DESCRIPTION IS EMPTY OF THE PHOTO THEN WE WILL SHOW THE TITLE AND DESCRIPTION OF IT'S OWNER.

          $parent_moduleid = $module . '_id';
          if (empty($fbmeta_title) && $controllerName == 'photo') {
            $fbmetainfo_table = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
            $fbmetainfo_tableName = $fbmetainfo_table->info('name');
            $select = $fbmetainfo_table->select()
              ->setIntegrityCheck(false)
              ->from($fbmetainfo_tableName)
              ->where('module = ?', $module)
              ->where('resource_type != ?', $resourcetype);

            $metainfos_parent = $fbmetainfo_table->fetchAll($select);

            if ($metainfos_parent) {
              foreach ($metainfos_parent as $key => $item) {
                $metainfos_parentitem = $item->toArray();
                if (isset($fbmetainfoTable->$metainfos_parentitem['resource_id'])) {
                  //if (Engine_Api::_()->hasItem($metainfos_parentitem['resource_type'])) {
                  $parent_module = Engine_Api::_()->getItem($metainfos_parentitem['resource_type'], $fbmetainfoTable->$metainfos_parentitem['resource_id']);
                  $fbmeta_title = $parent_module->$metainfos_parentitem['module_title'];
                  $fbmeta_desc = $parent_module->$metainfos_parentitem['module_description'];
                  // }
                }
                break;
              }
            }
          }

          if (((isset($fbmetainfoTable->photo_id) && empty($fbmetainfoTable->photo_id)) || (isset($fbmetainfoTable->cover_file_id) && empty($fbmetainfoTable->cover_file_id))) && !empty($admin_meta_photo_id)) {
            $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $metainfos->getPhotoUrl('thumb.main');
          } else if ((isset($fbmetainfoTable->photo_id) && !empty($fbmetainfoTable->photo_id)) || (isset($fbmetainfoTable->cover_file_id) && !empty($fbmetainfoTable->cover_file_id))) {
            if (!Engine_Api::_()->seaocore()->isCdn()) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
            }
          } else if ($resourcetype == 'sitevideo_channel' && (isset($fbmetainfoTable->file_id) && !empty($fbmetainfoTable->file_id)) || (isset($fbmetainfoTable->cover_file_id) && !empty($fbmetainfoTable->cover_file_id))) {
            if (!Engine_Api::_()->seaocore()->isCdn()) {
              $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $fbmetainfoTable->getPhotoUrl('thumb.main');
            } else {
              $fbmeta_imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
            }
          } else {
            $safeName = ( 'thumb.main' ? str_replace('.', '_', 'thumb.main') : 'main' );
            $fbmeta_imageUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/' . ucfirst($module) . '/externals/images/nophoto_' . $fbmetainfoTable->getShortType() . '_' . $safeName . '.png';
          }
        }

        if ($module == 'video' || $module == 'sitepagevideo' || $module == 'sitebusinessvideo' || $module == 'sitegroupvideo') {
          if ($fbmetainfoTable->type == 1) {
            $video = 'https://www.youtube.com/v/' . $fbmetainfoTable->code;
          } else if ($fbmetainfoTable->type == 2) {
            $video = 'http://vimeo.com/' . $fbmetainfoTable->code;
          }
        }

        $metaKeyword_appid = 'fb:app_id';
        $fb_appid = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;
        $metaKeyword_appadmin = 'fb:admins';
        $fb_admin_appadmin = Engine_Api::_()->getApi('settings', 'core')->getSetting('fbadmin.userid');
        //GETTING THE FB:ADMINS OR FB:APP ID ACCORDING TO WHAT ADMIN HAS SET.
        if (!empty($admin_meta_fbadmin_appid)) {
          //FINDING THE FACEBOOK ID OF OWNER IF HE IS CONNECTED TO FACEBOOK THROUGH THIS SITE.
          if (!empty($owner_id)) {
            $fbjoin_table = Engine_Api::_()->getDbtable('facebook', 'user');
            $fbjoin_tableName = $fbjoin_table->info('name');
            $select = $fbjoin_table->select()
              ->setIntegrityCheck(false)
              ->from($fbjoin_tableName)
              ->where('user_id' . ' = ?', $owner_id)
              ->limit(1);

            $fbmetainfoTable = $fbjoin_table->fetchRow($select);
            if (!empty($fbmetainfoTable->facebook_uid)) {
              if (!empty($fb_admin_appadmin) && $fb_admin_appadmin != $fbmetainfoTable->facebook_uid) {
                $fb_admin_appadmin = $fb_admin_appadmin . ',' . $fbmetainfoTable->facebook_uid;
              } else {
                $fb_admin_appadmin = $fbmetainfoTable->facebook_uid;
              }
            }
          }
        }

        //SETTING THE FACEBOOK META TAGS TO HEADER.
        //GET THE CURRENT DOCTYPE
        $currentDocType = $view->doctype()->getDoctype();
        $this->setDocType('XHTML1_RDFA');
        $local_language = Engine_Api::_()->getApi('settings', 'core')->getSetting('fblanguage.id', 'en_US');
        $view->headMeta($local_language, 'og:locale', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        if (!empty($fbmeta_title)) {
          $view->headMeta($fbmeta_title, 'og:title', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        $fbmeta_type = !empty($this->fbObjectMetaType) ? $this->fbObjectMetaType : (isset($fbmeta_type) ? $fbmeta_type : '');

        if (!empty($fbmeta_type)) {
          $view->headMeta($fbmeta_type, 'og:type', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        if (!empty($fbmeta_url)) {
          //EXPLOAD IF THE URL CONTENT THE QUERY STRING:
          //$fbmeta_urltemp = explode ("?", $fbmeta_url);

          $view->headMeta($fbmeta_url, 'og:url', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        if (!empty($fbmeta_imageUrl)) {
//					  if (Engine_Api::_()->seaocore()->isCdn()) { 
//					     $fbmeta_imageUrl = str_replace((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '', $fbmeta_imageUrl);
//					  }
          if (substr_count($fbmeta_imageUrl, 'http') > 1) {
            $fbmeta_imageUrl = str_replace((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '', $fbmeta_imageUrl);
          }
          $view->headMeta($fbmeta_imageUrl, 'og:image', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        if (!empty($fbmeta_site_name)) {
          $view->headMeta($fbmeta_site_name, 'og:site_name', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        if (!empty($fbmeta_desc)) {
          $fbmeta_desc = htmlspecialchars_decode($fbmeta_desc);
          $fbmeta_desc = ( Engine_String::strlen($fbmeta_desc) > 255 ? Engine_String::substr($fbmeta_desc, 0, 255) . '...' : $fbmeta_desc );
          $view->headMeta(strip_tags(htmlspecialchars_decode($fbmeta_desc)), 'og:description', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        $view->headMeta($fb_appid, $metaKeyword_appid, $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        //HERE BEFORE APPENDING THE FB ADMINS META TAG WE WILL CHECK THAT EITHRE FACEBOOK COMMENT BOX IS ENABLED OR NOT FOR THIS PARTICULAR TYPE CONTENT.
        //GETTING THE SETTING FOR COMMENT BOX FOR THIS MODULE.
        if ($module == 'sitealbum') {
          $module = 'album';
        }
        $success_showFBCommentBox = Engine_Api::_()->facebookse()->showFBCommentBox($module);

        if (!$success_showFBCommentBox) {
          if (empty($_SESSION['comment_box']) && !empty($fb_admin_appadmin)) {
            //$this->scrapeFbAdminPage ($fbmeta_url);
            $view->headMeta($fb_admin_appadmin, $metaKeyword_appadmin, $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          } else {
            unset($_SESSION['comment_box']);
          }
        }

        if ($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'recipe' || $module == 'list' || $module == 'sitestore' || $module == 'siteevent') {
          if (!empty($latitude)) {
            $view->headMeta($latitude, 'og:latitude', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($longitude)) {
            $view->headMeta($longitude, 'og:longitude', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($street_address)) {
            $view->headMeta($street_address, 'og:street-address', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($locality)) {
            $view->headMeta($locality, 'og:locality', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($region)) {
            $view->headMeta($region, 'og:region', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($postal_code)) {
            $view->headMeta($postal_code, 'og:postal-code', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if (!empty($country_name)) {
            $view->headMeta($country_name, 'og:country-name', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          }

          if ($module == 'sitepage' || $module == 'sitebusiness' || $module == 'sitegroup' || $module == 'sitestore') {
            if (!empty($email)) {
              $view->headMeta($email, 'og:email', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
            }

            if (!empty($phone_no)) {
              $view->headMeta($phone_no, 'og:phone-number', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
            }
          }
        }

        if ($module == 'sitestoreproduct') {
          $view->headMeta($fbMetaProductId, 'product:retailer_item_id', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta($fbMetaProductPriceAmount, 'product:price:amount', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta($fbMetaProductCurrency, 'product:price:currency', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta($fbMetaProductAvailability, 'product:availability', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta($fbMetaProductCondition, 'product:condition', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }

        if (($module == 'video' || $module == 'sitepagevideo' || $module == 'sitebusinessvideo' || $module == 'sitegroupvideo' ) && !empty($video)) {
          $view->headMeta($video, 'og:video', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta("application/x-shockwave-flash", 'og:video:type', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta(400, 'og:video:width', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
          $view->headMeta(300, 'og:video:height', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }

        if (($module == 'music' || $module == 'sitepagemusic' || $module == 'sitebusinessmusic' || $module == 'sitegroupmusic') && !empty($song_path)) {
          //$view->headMeta($song_path, 'og:audio', $keyType = 'property', $modifiers = array(), $placement = 'APPEND');
        }
        $this->setDocType($currentDocType);
        $isFacebook = $this->isRenderFacebook();

        if ($isFacebook) {
          echo $view->headMeta()->toString();
          die;
        } else {

          if (!empty($resourcetype) && !empty($table_id) && !isset($_SESSION[Engine_Api::_()->core()->getSubject()->getType() . '_' . $table_id])) {
            $_SESSION[Engine_Api::_()->core()->getSubject()->getType() . '_' . $table_id]['image'] = $fbmeta_imageUrl;
            $_SESSION[Engine_Api::_()->core()->getSubject()->getType() . '_' . $table_id]['title'] = $fbmeta_title;
            $_SESSION[Engine_Api::_()->core()->getSubject()->getType() . '_' . $table_id]['description'] = strip_tags(htmlspecialchars_decode($fbmeta_desc));
            $_SESSION[Engine_Api::_()->core()->getSubject()->getType() . '_' . $table_id]['url'] = $fbmeta_url;
          } elseif (!isset($_SESSION['opengraphinfo'])) {
            $_SESSION['opengraphinfo']['image'] = $fbmeta_imageUrl;
            $_SESSION['opengraphinfo']['title'] = $fbmeta_title;
            $_SESSION['opengraphinfo']['description'] = strip_tags(htmlspecialchars_decode($fbmeta_desc));
            $_SESSION['opengraphinfo']['url'] = $fbmeta_url;
          }
        }
        return true;
      }
    }
  }

  public function scrapeFbAdminPage($pageurl) {
    //CHECKING IF THE FILLED APP ID AND APP SECRET IS VALID OR NOT IF NOT THEN SHOW ERROR MESSAGE.

    $graph_url = 'http://developers.facebook.com/tools/lint/?url=' . $pageurl . '&format=json';
    $userAgent = 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)';
    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
      curl_setopt($ch, CURLOPT_URL, $graph_url);
      curl_setopt($ch, CURLOPT_FAILONERROR, true);
      if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
      }
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      $html = curl_exec($ch);
    } catch (Exception $e) {
      curl_close($ch);
    }
    curl_close($ch);
    return 1;
  }

  public function showFBCommentBox($module) {
    $resourcetype = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
      if ($module == 'sitealbum') {
        $module = 'album';
      }

      if ($module == 'sitereview') {
        if ($resourcetype != 'sitereview_listing')
          return false;
        $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
      }
      if ($resourcetype == 'sitepage_album' || $resourcetype == 'sitebusiness_album' || $resourcetype == 'sitegroup_album') {
        $resourcetype = $module . '_photo';
        $module = $module . 'album';
      }

      $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);

      if (empty($enable_managemodule))
        return false;
    }
    if (class_exists('Facebookse_Model_DbTable_Mixsettings')) {

      $permissionTable_Comments = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($module, $resourcetype);

      if ($permissionTable_Comments) {
        $permissionTable_Comments = $permissionTable_Comments->toArray();
      }
      if (!empty($permissionTable_Comments)) {
        $comment_setting = $permissionTable_Comments['commentbox_enable'];
      }

      if (Engine_Api::_()->core()->hasSubject()) {
        $currentpage_subject = Engine_Api::_()->core()->getSubject();
      }

      if (!empty($comment_setting) && ($comment_setting == 1 || $comment_setting == 2)) {
        //CHECKING IF THE VIEWER HAS ALLOWED TO COMMENTS ON THIS PAGE OR NOT.
        if ((!empty($currentpage_subject) && (method_exists($currentpage_subject, 'comments') && method_exists($currentpage_subject, 'likes'))) || empty($permissionTable_Comments['commentbox_privacy'])) {
          $getFBContent = 1;
        } else {
          $comment_setting = 0;
          $getFBContent = 0;
        }
      }

      if (!empty($comment_setting) && !empty($getFBContent)) {
        return $comment_setting;
      } else {
        return false;
      }
    }
  }

  public function showFBLikeButton($module) {
    $resourcetype = '';
    if (Engine_Api::_()->core()->hasSubject()) {
      $resourcetype = Engine_Api::_()->core()->getSubject()->getType();
      if ($module == 'sitealbum') {
        $module = 'album';
      }

      if ($module == 'sitereview') {
        if ($resourcetype != 'sitereview_listing')
          return false;
        $resourcetype = $resourcetype . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
      }
      if ($resourcetype == 'sitepage_album' || $resourcetype == 'sitebusiness_album' || $resourcetype == 'sitegroup_album') {
        $resourcetype = $module . '_photo';
        $module = $module . 'album';
      }

      $enable_managemodule = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->isModuleEnbled($module, $resourcetype);

      if (empty($enable_managemodule))
        return false;
    }
    if (class_exists('Facebookse_Model_DbTable_Mixsettings')) {

      $permissionTable_Likes = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($module, $resourcetype);

      if ($permissionTable_Likes) {
        $permissionTable_Likes = $permissionTable_Likes->toArray();
      }
      $like_setting = 1;
      if (!empty($permissionTable_Likes)) {
        $like_setting = $permissionTable_Likes['enable'];
      }
      return $like_setting;
    }
  }

  public function SiteURLTOScrape($curr_url, $module_type) {
    //We are not using Facebook Like Statistics feature so we are going to comment this. 03/02/2014.
    //SCRAPING THE URL WITH FACEBOOK URL LINTER TO CLEAR THE CACHE FIRST.
    //CHECKING IF IT EXIST IN THE FACEBOOKSE_STATSTIC TABLE OR NOT.
//    $scrape_sitepageurl = Engine_Api::_()->getApi('settings', 'core')->getSetting('scrape.sitepageurl', 0);
//    $tmTable = Engine_Api::_()->getDbtable('statistics', 'facebookse');
//    $tmName = $tmTable->info('name');
//    if (Engine_Api::_()->core()->hasSubject()) { 
//      $resource_type = Engine_Api::_()->core()->getSubject()->getType();
//      $content_id = Engine_Api::_()->core()->getSubject()->getIdentity();
//    }
//    
//    $selectSiteUrl = $tmTable->select()
//                    ->from($tmName, array('url_scrape', 'url_type'))
//                    ->where("(content_id = $content_id AND resource_type = '$resource_type')")     
//                    ->limit(1);
//    $SiteUrl_temp = $tmTable->fetchRow($selectSiteUrl);
//    $SiteUrl = array();
//    if (!empty($SiteUrl_temp)) {
//      $SiteUrl = $SiteUrl_temp->toArray();
//    }
//    if (!empty($SiteUrl_temp)) { 
//      $SiteUrl_array = $SiteUrl;
//      if (!empty($scrape_sitepageurl) && !empty ($SiteUrl_array) && empty($SiteUrl_array['url_scrape'])) { 
//        $scrape_fburlrequest = $this->scrapeFbAdminPage ($curr_url);
//        //UPDATING THE ROW'S UPDATED COLUMN.
//       
//       if (!empty($module_type)) {
//          $db = Engine_Db_Table::getDefaultAdapter();
//          $db->query("UPDATE `engine4_facebookse_statistics` SET `url_scrape` = 1, `url_type` = '$module_type' WHERE (`content_id` = $content_id AND `resource_type` = '$resource_type')");
//        }
//      }
//    }
//    else { 
//      
//      $db = Engine_Db_Table::getDefaultAdapter();
//      $db->beginTransaction();
//      try
//      {
//        
//        // Transaction
//        // insert the statistics entry into the database
//        //if (!empty($scrape_fburlrequest)) {           
//          $current_time = new Zend_Db_Expr('NOW()');
//          $row = $tmTable->createRow();
//          $row->url   =  $curr_url;
//          $row->updated =  $current_time;
//          $row->url_scrape = $scrape_sitepageurl;
//          $row->url_type = $module_type;
//          $row->content_id = $content_id;
//          $row->resource_type = $resource_type;
//          $row->save();
//          $db->commit();
//       // }
//      }	catch( Exception $e )	{
//        $db->rollBack();
//        throw $e;
//      }
//      
//      if (!empty($scrape_sitepageurl))
//        $scrape_fburlrequest = $this->scrapeFbAdminPage ($curr_url);
//    
//    }
  }

  //THIS FUNCTION RETURNS THE SOURCE URL OF THE IMAGE.
  public function itemphoto($fbmetainfoTable, $modulename) {

    $view = Zend_Registry::get('Zend_View');
    $safeName = ( 'thumb.main' ? str_replace('.', '_', 'thumb.main') : 'main' );
    $imageUrl = '';
    //CHECKING FOR ADMIN SELECTED META IMAGE FOR THIS EVENT.
    if (isset($fbmetainfoTable->photo_id) && !empty($fbmetainfoTable->photo_id)) {
      $imageUrl = $fbmetainfoTable->getPhotoUrl('thumb.main');
    } else {
      // Default image
      if (!$imageUrl) {
        if (method_exists($fbmetainfoTable, 'getNoPhotoImage')) {
          $imageUrl = $fbmetainfoTable->getNoPhotoImage();
        } else {
          switch ($modulename) {
            case 'blog':
            case 'forum':
            case 'poll':
            case 'user':
            case 'sitepagediscussion':
            case 'sitepagereview':
            case 'sitepagepoll':
            case 'sitebusinessdiscussion':
            case 'sitebusinessreview':
            case 'sitebusinesspoll':
            case 'sitegroupdiscussion':
            case 'sitegroupreview':
            case 'sitegrouppoll':
            case 'ynblog':
            case 'ynforum':

              $permissionTable_feed = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getActivityFeedInfo($modulename);
              if ($permissionTable_feed && isset($fbmetainfoTable->$permissionTable_feed['owner_field']) && !empty($fbmetainfoTable->$permissionTable_feed['owner_field'])) {
                $permissionTable_feed = $permissionTable_feed->toarray();
                $viewer = Engine_Api::_()->user()->getUser($fbmetainfoTable->$permissionTable_feed['owner_field']);
                $imageUrl = $viewer->getPhotoUrl('thumb.main');
              }
              if (empty($imageUrl))
                $imageUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
              break;

            case 'sitepagedocument':
            case 'sitebusinessdocument':
            case 'siteeventdocument':
            case 'sitegroupdocument':
            case 'document':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/' . $modulename . '_thumb.png';
              break;

            case 'sitepagemusic':
            case 'music':
            case 'sitebusinessmusic':
            case 'sitegroupmusic':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/nophoto_playlist_main.png';
              break;

            case 'sitepagevideo':
            case 'video':
            case 'sitebusinessvideo':
            case 'sitegroupvideo':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/video.png';
              break;

            case 'home':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/images/nophoto_site_logo_icon.png';
              break;

            case 'sitepagenote':
            case 'sitebusinessnote':
            case 'sitegroupnote':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/nophoto_note_thumb_profile.png';
              break;

            case 'sitepageoffer':
            case 'sitebusinessoffer':
            case 'sitegroupoffer':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/offer_thumb.png';
              break;

            case 'sitepagealbum':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_album_thumb_normal.png';
              break;
            case 'sitereview':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/Sitereview/externals/images/nophoto_listing_thumb_profile.png';
              break;

            case 'sitebusinessalbum':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/Sitebusiness/externals/images/nophoto_album_thumb_normal.png';
              break;

            case 'sitegroupalbum':
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_album_thumb_normal.png';
              break;

            default:
              if ($modulename == 'sitepage' || $modulename == 'sitebusiness' || $modulename == 'sitegroup' || $modulename == 'sitestore' || $modulename == 'siteevent') {
                $plugins_temp = str_replace('site', "", $modulename);
              } else if ($modulename != 'sitepagemusic' && $modulename != 'sitebusinessmusic' && $modulename != 'sitegroupmusic') {
                $plugins_temp = str_replace('sitepage', "", $modulename);
                if ($plugins_temp == $modulename) {
                  $plugins_temp = str_replace('sitebusiness', "", $modulename);
                }
                if ($plugins_temp == $modulename) {
                  $plugins_temp = str_replace('sitegroup', "", $modulename);
                }
                if ($plugins_temp == $modulename) {
                  $plugins_temp = str_replace('sitestore', "", $modulename);
                }
              }
              $imageUrl = $view->layout()->staticBaseUrl . 'application/modules/' . ucfirst($modulename) . '/externals/images/nophoto_' . $plugins_temp . '_' . $safeName . '.png';
              break;
          }
        }
      }
    }
    return $imageUrl;
  }

  //check if the current module is default or not.

  public function checkDefaultModule($module) {
    $permissionTable_Comments = Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->getMetainfo($module);
    $module_Default = 0;
    if ($permissionTable_Comments) {
      $permissionTable_Comments = $permissionTable_Comments->toArray();
    }
    if (!empty($permissionTable_Comments) && $permissionTable_Comments['default']) {

      $module_Default = 1;
    }

    return $module_Default;
  }

  //THIS IS THE SPACIAL CASE FOR OUR SITE REVIEW PLUGIN. IF A LISTING TYPE IS CREATED THEN INSERT THE CORROSPONDING ENTRY IN THE FACEBOOKSE_MIXSETTING TABLE ALSO.
  public function addReviewList($listType, $action) {

    if ($action == 'add') {

      $values = array('module' => 'sitereview', 'module_name' => 'Multiple Listing Types - ' . $listType->title_singular, 'resource_type' => 'sitereview_listing_' . $listType->listingtype_id, 'resource_id' => 'listing_id', 'owner_field' => 'owner_id', 'module_title' => 'title', 'module_description' => 'body', 'enable' => '1', 'send_button' => '1', 'like_type' => 'like', 'like_faces' => 1, 'like_width' => 450, 'like_font' => '', 'like_color' => '', 'layout_style' => 'standard', 'opengraph_enable' => 0, 'title' => '', 'photo_id' => 0, 'description' => '', 'types' => '', 'fbadmin_appid' => 1, 'commentbox_enable' => 0, 'commentbox_privacy' => 1, 'commentbox_width' => 450, 'commentbox_color' => 'light', 'module_enable' => 1, 'default' => 1, 'activityfeed_type' => 'sitereview_new_listtype_' . $listType->listingtype_id, 'streampublish_message' => 'View my ' . $listType->title_singular . '!', 'streampublish_story_title' => '{*sitereview_title*}', 'streampublish_link' => '{*sitereview_url*}', 'streampublish_caption' => '{*
actor*} posted a new ' . $listType->title_singular . ' on {*site_title*}: {*site_url*}.', 'streampublish_description' => '{*sitereview_desc*}', 'streampublish_action_link_text' => 'View ' . $listType->title_singular, 'streampublish_action_link_url' => '{*sitereview_url*}', 'streampublishenable' => 1, 'activityfeedtype_text' => 'Posting a new ' . $listType->title_singular);


      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      //$db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    } else {
      $custom_module = Engine_Api::_()->getItemTable('facebookse_mixsettings')->fetchRow(array('resource_type = ?' => 'sitereview_listing_' . $listType->listingtype_id));
      if (!empty($custom_module)) {

        $custom_module->delete();
      }
    }
  }

  public function addComment(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster, $body, $fb_comment_id) {
    $table = Engine_Api::_()->getDbtable('comments', 'core');
    $row = $table->createRow();

    if (isset($row->resource_type)) {
      $row->resource_type = $resource->getType();
    }

    $row->resource_id = $resource->getIdentity();
    $row->poster_type = $poster->getType();
    $row->poster_id = $poster->getIdentity();

    $row->creation_date = date('Y-m-d H:i:s');
    $row->body = $body;
    $row->fb_comment_id = $fb_comment_id;
    $row->save();

    if (isset($resource->comment_count)) {
      $resource->comment_count++;
      $resource->save();
    }

    return $row;
  }

  public function removeComment(Core_Model_Item_Abstract $resource, $comment_id) {
    $row = $this->getComment($resource, $comment_id);
    if (null === $row) {
      throw new Core_Model_Exception('No comment found to delete');
    }

    $row->delete();

    if (isset($resource->comment_count)) {
      $resource->comment_count--;
      $resource->save();
    }

    return $this;
  }

  public function getComment(Core_Model_Item_Abstract $resource, $comment_id) {
    $table = Engine_Api::_()->getDbtable('comments', 'core');
    $select = $table->select()
      ->where('fb_comment_id = ?', $comment_id)
      ->limit(1);

    $comment = $table->fetchRow($select);

    /*
      if( !($comment instanceof Zend_Db_Table_Row_Abstract) || !isset($comment->comment_id) )
      {
      throw new Core_Model_Exception('Invalid argument or comment could not be found');
      }
     */

    return $comment;
  }

  public function isSupportedModule($Type = null) {
    $mixsettingsTable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');
    $select = $mixsettingsTable->select()->where('resource_type = ?', $Type)
      ->orwhere('module = ?', $Type);
    $mixsettingsTable = $mixsettingsTable->fetchRow($select);
    if (!empty($mixsettingsTable)) {
      return true;
    } else
      return false;
  }

  /**
   * check for CDN concept
   *
   * @return path of cdn
   */
  public function getCdnPath() {

    //GET THE STROGE MODULE NAD VERSION OF STROGE MODULE.
    $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
    $storageversion = $storagemodule->version;

    $db = Engine_Db_Table::getDefaultAdapter();
    $type_array = $db->query("SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'")->fetch();
    $cdn_path = "";

    if ($storageversion >= '4.1.6' && !empty($type_array)) {

      $storageServiceTypeTable = Engine_Api::_()->getDbtable('serviceTypes', 'storage');
      $storageServiceTypeTableName = $storageServiceTypeTable->info('name');

      $storageServiceTable = Engine_Api::_()->getDbtable('services', 'storage');
      $storageServiceTableName = $storageServiceTable->info('name');

      $select = $storageServiceTypeTable->select()
        ->setIntegrityCheck(false)
        ->from($storageServiceTypeTableName, array(''))
        ->join($storageServiceTableName, "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id", array('enabled', 'config', 'default'))
        ->where("$storageServiceTypeTableName.plugin != ?", "Storage_Service_Local")
        ->where("$storageServiceTypeTableName.enabled = ?", 1)
        ->limit(1);

      $storageCheck = $storageServiceTypeTable->fetchRow($select);
      if (!empty($storageCheck)) {
        if ($storageCheck->enabled == 1 && $storageCheck->default == 1) {
          $config = Zend_Json::decode($storageCheck->config);
          $config_baseUrl = $config['baseUrl'];
          if (!preg_match('/http:\/\//', $config_baseUrl) && !preg_match('/https:\/\//', $config_baseUrl)) {
            $cdn_path.= "http://" . $config_baseUrl;
          } else {
            $cdn_path.= $config_baseUrl;
          }
        }
      }
    }
    return $cdn_path;
  }

  //CHECK EITHER THE REQUEST IS COMING FROM FACEBOOK OR THE SAME DOMAIN.
  public function isRenderFacebook() {

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
      $isFacebook = strstr($_SERVER['HTTP_USER_AGENT'], "facebook") ? true : false;
    } else {

      $isFacebook = false;
    }

    return $isFacebook;
  }

  //public function to set fb object meta type
  public function setObjectMetaType($fbmetaType) {
    $this->fbObjectMetaType = $fbmetaType;
  }

  public function getDefaultLikeUnlikeIcon($like_unlike, $appendBase = true) {

    //header("Content-type: text/css");
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET THE CDN PATH.
    $cdn_path = Engine_Api::_()->facebookse()->getCdnPath();

    //GET IMAGE SETTING.
    if ($like_unlike == 'like')
      $img_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.likeicon', 'application/modules/Facebookse/externals/images/like.png');
    else
      $img_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.unlikeicon', 'application/modules/Facebookse/externals/images/liked.png');

//	//CHECK FOR IMAGE ID.
//	if(!empty($image_id)) {
//
//		//GET THE IMAGE PATH.
//		$img_path = Engine_Api::_()->storage()->get($image_id, '')->getHref();
//    if(!$appendBase) {
//      $img_path = str_replace($view->baseUrl() . '/', '',  $img_path);
//      return $img_path;
//    }
//		if($cdn_path == "") {
//			$image_path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $img_path;
//		}	else {
//			$img_cdn_path = str_replace($cdn_path, '',  $img_path);
//			$image_path = $cdn_path. $img_cdn_path;
//		}
//	}	else {
//
//		//FOR DEFAULT IMAGE.
//    if($like_unlike == 'like') { 
//      $image_path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Facebookse/externals/images/like.png' ;
//    } else {
//        $image_path = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/application/modules/Facebookse/externals/images/liked.png' ;
//    }
//    
//    if(!$appendBase)
//      $image_path = str_replace((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $view->baseUrl() . '/', '',  $image_path);
//	}
    return $img_path;
  }

  //GET ARRAY OF ALL THE IMAGES UPLOADED IN THE PUBLIC/ADMIN FOLDER
  public function getUPloadedFiles() {
    $logoOptions = array();
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');
    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach ($it as $file) {
      if ($file->isDot() || !$file->isFile())
        continue;
      $basename = basename($file->getFilename());
      if (!($pos = strrpos($basename, '.')))
        continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if (!in_array($ext, $imageExtensions))
        continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }
    return $logoOptions;
  }

  //function for set doctype
  public function setDocType($docType = '') {

    $coreversion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
    if ($coreversion >= '4.8.0') {
      $doctypeHelper = new Zend_View_Helper_Doctype();
      $doctypes = $doctypeHelper->getDoctypes();
      if (isset($doctypes[$docType]))
        $doctypeHelper->doctype($docType);
    }
  }

}
