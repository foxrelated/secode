<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_EventAdsController extends Engine_Content_Widget_Abstract {

    //ACTION FOR SHOWING THE AD WITH PAGES
    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $load_content = 0;
        $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
        $this->view->isajax = $isajax = $this->_getParam('isajax', null);
        $this->view->limit = $limit = $this->_getParam('limit', 3);

        $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        if (!$enable_ads) {
            return $this->setNoRender();
        }

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.communityads', 1)) {
            return $this->setNoRender();
        }

        if ($this->view->identity) {
            $this->view->identity_temp = $this->view->identity;
            $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', "communityadid_widget_showads");
            $isajax = 1;
        }

        if (!empty($_GET['load_content']) || empty($communityad_id) || !empty($isajax)) {
            $load_content = 1;
            $this->view->tab = $this->_getParam('tab', null);
            if ($limit == 0 && empty($this->view->identity)) {
                return $this->setNoRender();
            }
//      elseif (!empty($this->view->identity)) {
//        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
//          $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventview', 3);
//          if (empty($limit)) {
//            return $this->setNoRender();
//          }
//        }
//      }

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