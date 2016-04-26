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
class Sitegroup_Widget_GroupAdsController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE AD WITH GROUP
  public function indexAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $module = $request->getModuleName();
		$controller = $request->getControllerName();
    $action = $request->getActionName();
    $load_content = 0;
    $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    $this->view->limit = $limit = $this->_getParam('limit', null);

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1)) {
      return $this->setNoRender();
    }
    

    if ($this->view->identity) {
      $limit = 0;
      $this->view->identity_temp =$this->view->identity;
      $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', "communityadid_widget_showads");

      switch ($module) {

        case "sitegroupevent":
         if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventbrowse', 3);
          }
          break;
        case "sitegroupvideo":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideoview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.advideobrowse', 3);
          }
          break;
        case "sitegroupnote":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnoteview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adnotebrowse', 3);
          }
          break;
        case "sitegroupmember":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberwidget', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admemberbrowse', 3);
          }
          break;
        case "sitegrouppoll":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adpollbrowse', 3);
          }
          break;
        case "sitegroupoffer":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adofferlist', 3);
          break;
        case "sitegroupmusic":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.admusicbrowse', 3);
          }
          break;
        case "sitegroupreview":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adreviewbrowse', 3);
          }
          break;
        case "sitegroupdocument":
          if ($action == 'view') {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentview', 3);
          } else {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.addocumentbrowse', 3);
          }
          break;
        case "sitegroupbadge":
          $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adbadgeview', 3);
          break;
        case "sitegroup":
          if ($controller == 'album' && $action == 'view' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumview', 3);
          } elseif($controller == 'album' && $action == 'browse' ) {
            $this->view->limit = $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumbrowse', 3);
          }
          break;
      }
      if (empty($limit)) {
        return $this->setNoRender();
      }
    }

    if (!empty($_GET['load_content']) || empty($communityad_id) || !empty($isajax)) {
      $load_content = 1;
      $this->view->tab = $this->_getParam('tab', null);
      if ($limit == 0 && empty($this->view->identity)) {
        return $this->setNoRender();
      } 
//       elseif (!empty($this->view->identity)) {
//         if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
//           $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adeventwidget', 3);
//           if (empty($limit)) {
//             return $this->setNoRender();
//           }
//         }
//       }

      $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $params = array();
      $params['lim'] = $limit;

      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
        Engine_Api::_()->core()->clearSubject();
      }
      
      $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
      if (!empty($subject)) {
        Engine_Api::_()->core()->clearSubject();
        Engine_Api::_()->core()->setSubject($subject);
      }

      if (!empty($fetch_community_ads)) {
        $this->view->communityads_array = $fetch_community_ads;
      } else {
        return $this->setNoRender();
      }
    }
    $this->view->load_content = $load_content;
  }
}

?>