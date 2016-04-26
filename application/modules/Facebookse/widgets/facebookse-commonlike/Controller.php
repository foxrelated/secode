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
class Facebookse_Widget_FacebookseCommonlikeController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $this->view->isajax = $isajax = $this->_getParam('isajax', 0);
        $front = Zend_Controller_Front::getInstance();
        $subjectType = '';
        $identity = '';
        $this->view->resource_type = '';
        $this->view->resource_identity = '';
        $subject = $this->_getParam('subject', null);
        if (($subject = $this->_getParam('subject'))) {
            $Subject_id = explode('_', $subject);
            if (count($Subject_id) >= 3) {
                $type = $Subject_id[0] . '_' . $Subject_id[1];
                $id = $Subject_id[2];
            } else
                list($type, $id) = explode('_', $subject);
            $subject = Engine_Api::_()->getItem($type, $id);
            if (!Engine_Api::_()->core()->hasSubject()) {
                Engine_Api::_()->core()->setSubject($subject);
            }
            $this->view->resource_type = $type;
            $this->view->resource_identity = $id;
        }
        //WE CAN GET THE VIEWER ID.
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $this->view->resource_type = $subjectType = Engine_Api::_()->core()->getSubject()->getType();
            $this->view->resource_identity = $identity = Engine_Api::_()->core()->getSubject()->getIdentity();
        }
        $moduleName = $this->_getParam('module_current', '');
        if ($moduleName != 'home') {
            if (empty($subject))
                return $this->setNoRender();
        }
        if (isset($_GET['redirect_postlike'])) {
            $this->view->fb_uid = '';
            $this->view->redirect_postlike = $redirect_postlike = $_GET['redirect_postlike'];
            $facebook = $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
            $fb_checkconnection = Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebook_userfeed);

            if (($facebook_userfeed && $fb_checkconnection)) {
                try {

                    $this->view->fb_uid = $facebook->getUser();
                } catch (Exception $e) {
                    $this->view->fb_uid = '';
                }
            }
        }
        //CHECKING IF IT EXIST IN THE FACEBOOKSE_STATSTIC TABLE OR NOT.
        if ($subject)
            $FacebookseScrapeUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
        else
            $FacebookseScrapeUrl = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $front->getBaseUrl();
        $this->view->FacebookseScrapeUrl = Engine_Api::_()->facebookse()->getFBLikeURL($FacebookseScrapeUrl);
        $this->view->scrape_sitepageurl = Engine_Api::_()->getApi('settings', 'core')->getSetting('scrape.sitepageurl', 0);
        if (!$isajax) {

            $this->view->websitelike_type = $websitelike_type = Zend_Registry::get('facebookse_liketype');
            $this->view->sitelike = $sitelike = Zend_Registry::get('facebookse_sitelike');
            
            $default = 2;
            if (!empty($subject)) {
                $module = strtolower($subject->getModuleName());
                //CHECK if the request module is default module or thirdparty module
                if (Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkDefaultModule($moduleName))
                    $default = 1;
            }
            else {
                $this->view->module = $module = $front->getRequest()->getModuleName();
                $default = 2;
            }

            //CHECK if the request module is default module or thirdparty module

            $array_params = array('isajax' => $default, 'format' => 'html', 'module_current' => $module, 'action_current' => $front->getRequest()->getActionName(), 'requested_uri' => $subject->getHref());
            
            $this->view->getallparams = array_merge($array_params, $this->_getAllParams());
            $this->view->currurl_orig = $currurl_orig = $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
            $this->view->curr_url = $curr_url = Engine_Api::_()->facebookse()->getFBLikeURL($curr_url);
            if ($module == 'siteestore')
                $fbbutton_commentbox = false;
            else
                $fbbutton_commentbox = true;
            $this->view->fbbutton_commentbox = $this->_getParam('fbbutton_commentbox', $fbbutton_commentbox);
            $isajax = $this->_getParam('isajax', 0);
            //CHECK IF Facebookse MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
            $enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
            if ($module == 'sitereview') {
                $subjectType = $subjectType . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
            }

            if (empty($enable_facebooksemodule) || !Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkLikeButton($module, $subjectType)) {
                return $this->setNoRender();
            }
        } else {
            $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');

            //HERE WE CAN FOUND THE MODULE NAME AND MODULE IS ENABLE.
            $moduleName = $this->_getParam('module_current');

            if ($moduleName == 'sitereview') {
                $subjectType = $subjectType . '_' . Engine_Api::_()->core()->getSubject()->listingtype_id;
            }

            if (!Engine_Api::_()->getDbtable('mixsettings', 'facebookse')->checkLikeButton($moduleName, $subjectType))
                return $this->setNoRender();

            if ($isajax == 1) {
                $mixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');

                if (!empty($subjectType))
                    $LikeSetting = $mixsettingstable->fetchRow(array('resource_type = ?' => $subjectType))->toArray();
                else
                    $LikeSetting = $mixsettingstable->fetchRow(array('module = ?' => $moduleName))->toArray();
                if ($LikeSetting['enable'] == 0)
                    return $this->setNoRender();
            } else {

                $LikeSetting['enable'] = $this->_getParam('enable', 1);
                if ($LikeSetting['enable'] == 0)
                    return $this->setNoRender();
                $showfaces = $this->_getParam('show_faces', array('0' => 1));
                if (is_array($showfaces))
                    $LikeSetting['like_faces'] = $showfaces[0];
                else
                    $LikeSetting['like_faces'] = $showfaces;
                if (empty($LikeSetting['like_faces'])) {
                    $LikeSetting['like_faces'] = 'false';
                }
                //THIS IS AN SPECIAL CASE FOR MAGENTO MODULE.
                if ($moduleName == 'siteestore') {
                    $send_default = array('0' => 0);
                    $layout_default = 'button_count';
                } else {
                    $send_default = array('0' => 1);
                    $layout_default = 'standard';
                }
                $sendbutton = $this->_getParam('send_button', $send_default);
                if (empty($sendbutton))
                    $sendbutton[0] = 0;
                $LikeSetting['send_button'] = $sendbutton[0];

                if (empty($LikeSetting['send_button'])) {
                    $LikeSetting['send_button'] = 'false';
                } else {
                    $LikeSetting['send_button'] = 'true';
                }
                //DEFAULT FB LIKE BUTTON OPTIONS..
                if ($fbLikeButton == 'default') {

                    $LikeSetting['like_type'] = $this->_getParam('like_verb_display', 'like');
                    $LikeSetting['like_font'] = $this->_getParam('like_font', '');
                    $LikeSetting['like_color'] = $this->_getParam('like_color_scheme', 'light');
                    $LikeSetting['layout_style'] = $this->_getParam('layout_style', $layout_default);
                    $LikeSetting['like_width'] = $this->_getParam('like_width');
                } else {

                    //CUSTOM LIKE BUTTON OPTIONS          
                    $LikeSetting['action_type'] = $this->_getParam('action_type', 'og.likes');
                    if ($LikeSetting['action_type'] == 'custom')
                        $LikeSetting['action_type'] = $this->_getParam('actiontype_custom', 'og.likes');
                    if (empty($LikeSetting['action_type']))
                        $LikeSetting['action_type'] = 'og.likes';
                    
                    $LikeSetting['object_type'] = $this->_getParam('object_type', 'object');
                    $LikeSetting['fbbutton_liketext'] = $this->_getParam('fbbutton_liketext', 'Like');
                    $LikeSetting['fbbutton_unliketext'] = $this->_getParam('fbbutton_unliketext', 'Unlike');
                    $LikeSetting['fbbutton_likeicon'] = $this->_getParam('fbbutton_likeicon', null);
                    $LikeSetting['fbbutton_unlikeicon'] = $this->_getParam('fbbutton_unlikeicon', null);
                    $LikeSetting['show_customicon'] = $this->_getParam('show_customicon', 1);
                    $LikeSetting['like_commentbox'] = $this->_getParam('fbbutton_commentbox', '1');
                }
            }
            $LikeSetting['module'] = $moduleName;

            $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
            if (!empty($enable_fboldversion)) {
                $socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
                $socialdnaversion = $socialdnamodule->version;
                if ($socialdnaversion >= '4.1.1') {
                    $enable_fboldversion = 0;
                }
            }
            $this->view->enable_fboldversion = $enable_fboldversion;

            $this->view->currurl_orig = $currurl_orig = $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->_getParam('requested_uri');

            //IF THE FB LIKE BUTTON IS DEFAULT THEN GET THE LIKE BUTTON CODE
            if ($fbLikeButton == 'default') {

                //GET THE LIKE BUTTON CODE:
                $button = Engine_Api::_()->facebookse()->getFBLikeCodeDefault($enable_fboldversion, $LikeSetting, $currurl_orig);

                $this->view->like_button = $button;
            } else
                $this->view->likeSettinginfo = $LikeSetting;

            $this->view->curr_url = $curr_url = Engine_Api::_()->facebookse()->getFBLikeURL($currurl_orig);

            //CHECK IF THE USER HAS ACTIVE FACEBOOK SESSION:
            if ($fbLikeButton == 'custom') {
                $facebook = $facebook_userfeed = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
                $fb_checkconnection = Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebook_userfeed);
                if (($facebook_userfeed && $fb_checkconnection)) {
                    try {
                        $this->view->fbuser_active = 1;
                        $accessToken = $facebook->getAccessToken();

                        $this->view->fb_uid = $facebook->getUser();
                        $this->view->fb_access_token = $accessToken;

                        //CHECK IF THE CURRENTLY LOGGED IN USER FRIENDS HAS LIKED THIS CONTENT.
                        $FBFriends = $facebook->api('/me/friends', 'GET', array('access_token' => $accessToken, 'limit' => 5));


                        $likedFBFriens = array();
                        if (!empty($FBFriends['data'])) {
                            foreach ($FBFriends['data'] as $friend) {
                                $isLiked = $facebook->api('/' . $friend['id'] . '/' . $LikeSetting['action_type'], 'GET', array('access_token' => $accessToken, 'object' => $curr_url));
                                if (!empty($isLiked['data'])) {
                                    $likedFBFriens[] = $friend;
                                }
                            }
                        }

                        $this->view->likedFBFriens = $likedFBFriens;

                        //CHECK IF THE CURRENT ACTIVE USER LIKED THE CONTENT

                        $isLiked = $facebook->api('/me/' . $LikeSetting['action_type'], 'GET', array('access_token' => $accessToken, 'object' => $curr_url));

                        if (!empty($isLiked['data'])) {
                            $this->view->hasliked = true;
                            $this->view->like_actionId = $isLiked['data'][0]['id'];
                            $this->view->fb_uid = $isLiked['data'][0]['from']['id'];
                        } else {
                            $this->view->hasliked = false;
                            $this->view->like_actionId = 0;
                        }
                    } catch (Exception $e) {
                        $this->view->fbuser_active = 0;
                    }
                } else {
                    $this->view->fbuser_active = 0;
                }

                if ($facebook) {

                    // fql IS DEPRECATED FOR THE VERSION 2.1 AND HIGHER 
                    $fql = "SELECT like_count FROM link_stat WHERE url='{$curr_url}'";
                    $likeCount = 3; //$facebook->api("/fql?q=" . urlencode($fql) . "&format=json-strings");

                    if (isset($likeCount['data'][0]))
                        $this->view->likeCount = $likeCount['data'][0]['like_count'];
                    else
                        $this->view->likeCount = 0;
                }
            }
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
    }

}

?>