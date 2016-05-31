<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Pushnotification.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Plugin_Pushnotification {

    /**
     * Variable to exist Google API key.
     *
     * @var String
     */
    public $_GOOGLE_API_KEY;

    /**
     * Loader for parsers
     * 
     * @var Zend_Loader_PluginLoader
     */
    public $_pluginLoader;

    /**
     * Hook: call whenever notification send.
     * 
     * @param object $event
     */
    public function onActivityNotificationCreateAfter($event) {
        $tempSitemenuLtype = 0;
        $notification = $payload = $event->getPayload();
        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.global.view', 0);
        $siteapiLSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.lsettings', 0);
        $siteapiInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.androiddevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.global.type', 0);

        // By pass for un supported modules.
        $getObject = $notification->getObject();
        if (!empty($getObject)) {
//            $objectModName = $this->getNotificationModuleName($notification);
//            $getDefaultAPPModules = Engine_Api::_()->getApi('Core', 'siteapi')->getAPIModulesName();
//            $getDefaultAPPModules[] = 'activity';
//            $getDefaultAPPModules[] = 'core';
//            $getDefaultAPPModules[] = 'user';
//            if (!in_array($objectModName, $getDefaultAPPModules))
//                return;
        } else {
            return;
        }

//        $getDefaultAPPModules = DEFAULT_APP_MODULES;
//        if (!empty($getDefaultAPPModules)) {
//            $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
//        }

        if (!$notification->getTypeInfo()->siteandroidapp_pushtype)
            return;

        if (empty($siteapiGlobalType)) {
            for ($check = 0; $check < strlen($siteapiLSettings); $check++) {
                $tempSitemenuLtype += @ord($siteapiLSettings[$check]);
            }
            $tempSitemenuLtype = $tempSitemenuLtype + $siteapiGlobalView;
        }

        if (empty($siteapiGlobalView) && ($tempSitemenuLtype != $siteapiInfoType))
            return;

        $subject = Engine_Api::_()->user()->getViewer();
        $subjectOwner = $subjectOwner = Engine_Api::_()->getItem('user', $notification->user_id);

        // User could be login in multiple devices that's why we need to send push notification on all devices.
        if ($this->canSendAndroidNotification()) {
            $gcmUsers = $this->getGCMUser($notification->user_id);
            foreach ($gcmUsers as $registrationTokn) {
                $this->sendAndroidNotification($registrationTokn, $notification);
            }
        }
    }

    public function getNotificationModuleName($notification) {
        try {
            $getTypeInfo = $notification->getTypeInfo();
            if ($getTypeInfo->type == 'liked') {
                $getNotificationObj = $notification->getObject();
                if (isset($getNotificationObj->resource_type)) {
                    $isItemTypeAvailable = Engine_Api::_()->hasItemType($getNotificationObj->resource_type);
                    if (!empty($isItemTypeAvailable)) {
                        $item = Engine_Api::_()->getItem($getNotificationObj->resource_type, $getNotificationObj->resource_id);
                        if (!empty($item)) {
                            $getObjectModName = $item->getModuleName();
                        }
                    }
                } else if (isset($getNotificationObj->object_type)) {
                    $getTempObject = $getNotificationObj->getObject();
                    if (!empty($getTempObject))
                        $getObjectModName = $getTempObject->getModuleName();
                }
            }else if (($getTypeInfo->type == 'commented') || ($getTypeInfo->type == 'replied')) {
                $getTempObject = $notification->getObject();
                if (!empty($getTempObject) && isset($getTempObject->object_type)) {
                    $getObjectModName = $getTempObject->getObject()->getModuleName();
                } else if (is_object($getTempObject)) {
                    $getObjectModName = $getTempObject->getModuleName();
                }
            } else {
                // By pass for un supported modules.
                $getDefaultAPPModules = DEFAULT_APP_MODULES;
                if (!empty($getDefaultAPPModules)) {
                    $getDefaultAPPModuleArray = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames(); //@explode(",", DEFAULT_APP_MODULES);
                    if (in_array($getTypeInfo->module, $getDefaultAPPModuleArray)) {
                        $getTempObject = $notification->getObject();
                        if (isset($getTempObject))
                            $getObjectModName = $getTempObject->getModuleName();
                    }else {
                        $getObjectModName = $getTypeInfo->module;
                    }
                }
            }

            $getObjectModName = !empty($getObjectModName) ? strtolower($getObjectModName) : '';
            return $getObjectModName;
        } catch (Exception $ex) {
            return '';
        }
    }

    /**
     * Get the array of registration id's to send push notification on them.
     * 
     * @param intiger $user_id: user ID
     */
    public function getGCMUser($user_id) {
        $gcmTable = Engine_Api::_()->getDbtable('gcmusers', 'siteandroidapp');
        return $gcmTable->getUsers(array('user_id' => $user_id));
    }

    /**
     * Gets the plugin loader
     * 
     * @return Zend_Loader_PluginLoader
     */
    public function getPluginLoader() {
        if (null === $this->_pluginLoader) {
            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
                    . 'modules' . DIRECTORY_SEPARATOR
                    . 'Activity';
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
                'Activity_Model_Helper_' => $path . '/Model/Helper/'
            ));
        }

        return $this->_pluginLoader;
    }

    /**
     * Get a helper
     * 
     * @param string $name
     * @return Activity_Model_Helper_Abstract
     */
    public function getHelper($name) {
        $name = $this->_normalizeHelperName($name);
        if (!isset($this->_helpers[$name])) {
            $helper = $this->getPluginLoader()->load($name);
            $this->_helpers[$name] = new $helper;
        }

        return $this->_helpers[$name];
    }

    /**
     * Normalize helper name
     * 
     * @param string $name
     * @return string
     */
    public function _normalizeHelperName($name) {
        $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
        $name = ucfirst($name);
        return $name;
    }

    /**
     * Add base url in url
     * 
     * @param : string $url
     * @return string
     */
    public function addBaseUrl($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = $this->getHttpHostUrl() . $url;
        }
        return $url;
    }

    /**
     * Getting the notification title.
     * 
     * @param object $notification
     * @param float $flag: send (flag=1) to get subject and object array to redirect after click or (flag=0) to get simple notification title.
     * @return string / array call to the assemble() method.
     */
    public function getContent($notification, $flag = false) {
        $params = array_merge(
                $notification->toArray(), (array) $notification->params, array(
            'subject' => $notification->getSubject(),
            'object' => $notification->getObject()
                )
        );

        $params['flag'] = $flag;

        return $this->assemble($notification->getTypeInfo()->body, $params);
    }

    /**
     * Getting the notification title.
     * 
     * @param string $body
     * @param array $params
     * @return string / array
     */
    public function assemble($body, array $params = array()) {
        $body = $this->getHelper('translate')->direct($body);

        // Do other stuff
        preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
        $feedParams = array();

        foreach ($matches as $match) {
            $tag = $match[0];
            $args = explode(':', $match[1]);
            $helper = array_shift($args);

            $tempParams = $helperArgs = array();
            $tempLabel = $tempType = $tempId = '';
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) === '$') {
                    $arg = substr($arg, 1);
                    $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
                } else {
                    $helperArgs[] = $arg;
                    $tempLabel .= $arg;
                }
            }

            try {
                $helper = $this->getHelper($helper);
                $r = new ReflectionMethod($helper, 'direct');
                $content = $r->invokeArgs($helper, $helperArgs);
            } catch (Exception $ex) {
                return $body;
            }

            if (isset($params['flag']) && !empty($params['flag'])) { // Make a feed type body params for dynamic Feed Title
                if (isset($helperArgs[0]) && !empty($helperArgs[0])) {
                    if (is_object($helperArgs[0])) {

                        $tempParams['search'] = $tag;
                        $tempParams['label'] = (isset($helperArgs[1]) && !empty($helperArgs[1]) && is_string($helperArgs[1])) ? $helperArgs[1] : $helperArgs[0]->getTitle();
                        $tempParams['type'] = $helperArgs[0]->getType();
                        $tempParams['id'] = $helperArgs[0]->getIdentity();

                        if (isset($helperArgs[1]) && is_object($helperArgs[1]) && strstr($tag, '{actors:$subject:$object}')) {
                            $tempParams['object']['label'] = $helperArgs[1]->getTitle();
                            $tempParams['object']['type'] = $helperArgs[1]->getType();
                            $tempParams['object']['id'] = $helperArgs[1]->getIdentity();
                        }
                    } else {
                        $tempParams['search'] = $tag;
                        $tempParams['label'] = preg_replace('/<\/?a[^>]*>/', '', $helperArgs[0]);

                        // In case of GUID, create object and send respective array to client.
                        if (isset($helperArgs[0]) && !empty($helperArgs[0]) && is_string($helperArgs[0]) && strstr($helperArgs[0], '_')) {
                            $explodeItemTypes = @explode("_", $helperArgs[0]);
                            $id = @end($explodeItemTypes);
                            array_pop($explodeItemTypes);
                            $type = @implode("_", $explodeItemTypes);
                            if (!empty($type) && !empty($id)) {
                                $getObj = Engine_Api::_()->getItem($type, $id);
                                if (!empty($getObj)) {
                                    $tempParams['search'] = $tag;
                                    $tempParams['label'] = $getObj->getTitle();
                                    $tempParams['type'] = $getObj->getType();
                                    $tempParams['id'] = $getObj->getIdentity();
                                }
                            }
                        }
                    }
                    $feedParams[] = $tempParams;
                }
            } else { // Make a Feed Title
                $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
                $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
            }
        }

        if (isset($params['flag']) && !empty($params['flag'])) {
            return $feedParams;
        } else {
            $body = strip_tags($body);
            return $body;
        }
    }

    /**
     * Send push notification to users by site administrator.
     * 
     * @param string $registrationTokn: registration ID
     * @param array $params
     * @return boolean
     */
    public function sendAdminBaseAndroidNotification($registrationTokn, $params) {
        $GOOGLE_API_KEY = $this->canSendAndroidNotification(true);
        if (!$GOOGLE_API_KEY)
            return;

        $gcm = new Zend_Mobile_Push_Gcm();
        $gcm->setApiKey($GOOGLE_API_KEY);
        $messageGCM = new Zend_Mobile_Push_Message_Gcm();
        $messageGCMData = array();
        $messageGCM->addToken($registrationTokn);
        $messageGCMData['message'] = $params['message'];
        $view = new Zend_View();
        $messageGCMData['ticker'] = isset($params['ticker']) && $params['ticker'] ? $params['ticker'] : $view->translate('new update');
        $messageGCMData['title'] = isset($params['title']) && $params['title'] ? $params['title'] : $view->translate('new update');
        $messageGCMData['sound'] = 'audios/drip.wav';
        $messageGCMData['alert'] = 'default';

        $messageGCM->setData(
                $messageGCMData
        );

        return $gcm->send($messageGCM);
    }

    /**
     * Set the Google api key to send push notification.
     * 
     */
    public function canSendAndroidNotification($returnKey = false) {
        if (!$this->_GOOGLE_API_KEY) {
            $this->_GOOGLE_API_KEY = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteandroidapp.google.server.api.key");
        }

        if (!$this->_GOOGLE_API_KEY)
            return false;

        return $returnKey ? $this->_GOOGLE_API_KEY : true;
    }

    /**
     * Send push notification to respective GCMUsers.
     * 
     * @param object $event
     */
    public function sendAndroidNotification($registrationTokn, $notification) {
        $hostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.global.view', 0);
        $siteapiManageType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.iosdevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteandroidapp.global.type', 0);

        $GOOGLE_API_KEY = $this->canSendAndroidNotification(true);
        if (!$GOOGLE_API_KEY)
            return;

        try {
            $notificationTypeInfo = $notification->getTypeInfo();

            $hrefLinks = explode('href', $notification->__toString());
            $hrefArray = explode('"', $hrefLinks[count($hrefLinks) - 1]);

            if (count($hrefArray) == 1)
                $hrefArray = explode('\'', $hrefArray[0]);

            $hreflink = $this->addBaseUrl($hrefArray[1]);

            $gcm = new Zend_Mobile_Push_Gcm();
            $gcm->setApiKey($GOOGLE_API_KEY);

            $messageGCM = new Zend_Mobile_Push_Message_Gcm();
            $messageGCMData = array(
                'href' => $hreflink
            );
            $messageGCM->addToken($registrationTokn);



            if (empty($siteapiGlobalType)) {
                for ($check = 0; $check < strlen($hostType); $check++) {
                    $tempHostType += @ord($hostType[$check]);
                }
                $tempHostType = $tempHostType + $siteapiGlobalView;
            }

            $user_id = $notification->user_id;
            $user = Engine_Api::_()->user()->getUser($user_id);

            $translate = Zend_Registry::get('Zend_Translate');
            $oldlanguage = $translate->getLocale();

//            if ($user->language) {
//                try {
//                    $language = Zend_Locale::findLocale($user->language);
//                } catch (Exception $e) {
//                    $language = 'en_US';
//                }
//                $translate->setLocale($language);
//                $localeObject = Zend_Registry::get('Locale');
//                $selectedLanguage = $localeObject->getLanguage();
//                if (($selectedLocale = $localeObject->getRegion())) {
//                    $selectedLanguage .= "_$selectedLocale";
//                }
//
//                if (!$translate->isAvailable($selectedLanguage)) {
//                    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->core_locale_locale;
//                    if (!$translate->isAvailable($defaultLanguage)) {
//                        $translate->setLocale('en_US');
//                    } else {
//                        $translate->setLocale($defaultLanguage);
//                    }
//                } else {
//                    $translate->setLocale($language);
//                }
//            }

            $notificationObject = $notification->getObject();
            $tempMessage['feed_title'] = $this->getContent($notification);
            $tempMessage['object_params'] = array(
                'type' => $notificationObject->getType(),
                'id' => $notificationObject->getIdentity()
            );
            
            if(isset($tempMessage['object_params']['id']) && isset($notification->type) && ($notification->type == 'friend_request'))
                $tempMessage['object_params']['id'] = $notification->subject_id;

            $view = new Zend_View();
            $messageGCMData['ticker'] = $view->translate('new update');
            $translate->setLocale($oldlanguage);
            $countUnread = Engine_Api::_()->getDbTable('notifications', 'activity')->hasNotifications($user);
            $viewer = Engine_Api::_()->user()->getViewer();
            if ($viewer->getIdentity()) {
                $messageGCMData['title'] = $viewer->getTitle();
                $messageGCMData['imgUrl'] = $this->addBaseUrl($viewer->getPhotoUrl('thumb.profile'));
            } else {
                $messageGCMData['title'] = $view->translate(array('%s Update', '%s Updates', $countUnread), $countUnread);
            }

            if (4 & (int) $notificationTypeInfo->siteandroidapp_pushtype) {
                $messageGCMData['message'] = Zend_Json::encode($tempMessage);
            }
            if (2 & (int) $notificationTypeInfo->siteandroidapp_pushtype) {
                $messageGCMData['msgcnt'] = $countUnread;
            }
            if (1 & (int) $notificationTypeInfo->siteandroidapp_pushtype) {
                $messageGCMData['sound'] = 1;
            } else
                $messageGCMData['sound'] = 0;

            // @Todo: 
//            if ( 1 & ( int ) $notificationTypeInfo->siteandroidapp_pushtype ) {
//              $messageGCMData['sound'] = 'audios/drip.wav';
//              $messageGCMData['alert'] = 'default';
//            }

            if ((!empty($siteapiGlobalView)) || ($tempHostType == $siteapiManageType)) {
                $messageGCM->setData(
                        $messageGCMData
                );
            }

            $response = $gcm->send($messageGCM);

            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get the host
     * 
     * @return string
     */
    public function getHttpHostUrl() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request == null) {
            $request = new Zend_Controller_Request_Http();
        }

        return $request->getScheme() . '://' . $request->getHttpHost();
    }

}
