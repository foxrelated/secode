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
class Facebookse_Widget_FacebookseWebsitelikeController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //CHECK IF Facebookse MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
        $enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');

        $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
        if (!empty($enable_fboldversion)) {
            $socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
            $socialdnaversion = $socialdnamodule->version;
            if ($socialdnaversion >= '4.1.1') {
                $enable_fboldversion = 0;
            }
        }
        $this->view->enable_fboldversion = $enable_fboldversion;

        if (empty($enable_facebooksemodule)) {
            return $this->setNoRender();
        }
        $front = Zend_Controller_Front::getInstance();

        $module = $front->getRequest()->getModuleName();
        $action = $front->getRequest()->getActionName();

        $curr_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebook.home.url');

        if (empty($curr_url))
            $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getRequest()->getBaseUrl();
        $this->view->websitelike_type = $websitelike_type = Zend_Registry::get('facebookse_liketype');
        $this->view->sitelike = $sitelike = Zend_Registry::get('facebookse_sitelike');

        //CHECKOUT IF THE RENDRING PAGE IS A VIEW PAGE OF PERTICULAR CONTENT OR NOT.
        if (($module == 'core' && ($action == 'index' || $action == 'requireuser')) || ($module == 'user' && $action == 'home')) {
            if (!Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkLikeButton('home', ''))
                return $this->setNoRender();
            //FINDING THE ADMIN SETTINGS FOR THIS MODULE.
            $this->view->LikeSettings = $LikeSetting = Engine_Api::_()->facebookse()->getLikeSetting('home', '', $curr_url);
            $button = Engine_Api::_()->facebookse()->getFBLikeCode();
            $LikeSetting = json_decode($LikeSetting);

            $button = '<div class="fb-like" data-href="'.$curr_url.'" data-layout="standard" data-action="'.$LikeSetting->like_type.'" data-show-faces="'.$LikeSetting->like_faces.'" data-share="true"></div>';
            
            $this->view->like_button = $button;
        } else {
            return $this->setNoRender();
        }
    }

}
