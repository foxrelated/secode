<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Pushnotification.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Plugin_Pushnotification {

    /**
     * Variable to exist apple cart.
     *
     * @var String
     */
    protected $_APPLE_CERT;

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
        $tempSitemenuLtype = null;
        $notification = $payload = $event->getPayload();
        $siteapiGlobalView = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.global.view', 0);
        $siteapiLSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.lsettings', 0);
        $siteapiInfoType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.androiddevice.type', 0);
        $siteapiGlobalType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.global.type', 0);
        // By pass for un supported modules.
        $getObject = $notification->getObject();
        if (!empty($getObject)) {
            $objectModName = $this->getNotificationModuleName($notification);
            $getDefaultAPPModules = Engine_Api::_()->getApi('Core', 'siteapi')->getAPIModulesName();
            $getDefaultAPPModules[] = 'activity';
            $getDefaultAPPModules[] = 'core';
            $getDefaultAPPModules[] = 'user';
            if (!in_array($objectModName, $getDefaultAPPModules))
                return;
        }else {
            return;
        }

//        $getDefaultAPPModules = DEFAULT_APP_MODULES;
//        if (!empty($getDefaultAPPModules)) {
//            $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
//        }

        if (!$notification->getTypeInfo()->siteiosapp_pushtype)
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
        if ($this->canSendIosNotification()) {
            $apnUsers = $this->getAPNUser($notification->user_id);
            foreach ($apnUsers as $registrationTokn) {
                $this->sendIosNotification($registrationTokn, $notification);
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
//                // By pass for un supported modules.
//                $getDefaultAPPModules = DEFAULT_APP_MODULES;
//                if (!empty($getDefaultAPPModules)) {
//                    $getDefaultAPPModuleArray = @explode(",", DEFAULT_APP_MODULES);
//                    if (in_array($getTypeInfo->module, $getDefaultAPPModuleArray)) {
//                        $getTempObject = $notification->getObject();
//                        if (isset($getTempObject))
//                            $getObjectModName = $getTempObject->getModuleName();
//                    }else {
//                        $getObjectModName = $getTypeInfo->module;
//                    }
//                }
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
    public function getAPNUser($user_id) {
        $apnTable = Engine_Api::_()->getDbtable('apnusers', 'siteiosapp');
        return $apnTable->getUsers(array('user_id' => $user_id));
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
            foreach ($args as $arg) {
                if (substr($arg, 0, 1) === '$') {
                    $arg = substr($arg, 1);
                    $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
                } else {
                    $helperArgs[] = $arg;
                }
            }

            $helper = $this->getHelper($helper);
            $r = new ReflectionMethod($helper, 'direct');
            $content = $r->invokeArgs($helper, $helperArgs);

            if (isset($params['flag']) && !empty($params['flag'])) { // Make a feed type body params for dynamic Feed Title
                if (isset($helperArgs[0]) && !empty($helperArgs[0])) {
                    if (is_object($helperArgs[0])) {
                        $tempParams['search'] = $tag;
                        $tempParams['label'] = $helperArgs[0]->getTitle();
                        $tempParams['type'] = $helperArgs[0]->getType();
                        $tempParams['id'] = $helperArgs[0]->getIdentity();

                        if (isset($helperArgs[1]) && is_object($helperArgs[1]) && strstr($tag, '{actors:$subject:$object}')) {
                            $tempParams['object']['label'] = $helperArgs[1]->getTitle();
                            $tempParams['object']['type'] = $helperArgs[1]->getType();
                            $tempParams['object']['id'] = $helperArgs[1]->getIdentity();
                        }
                    } else {
                        $tempParams['search'] = $tag;
                        $tempParams['label'] = $helperArgs[0];
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

    public function sendAdminBaseIosNotification($registrationTokn, $params) {
        $APPLE_CERT = $this->canSendIosNotification(true);
        $passValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp.password");

        if (!$APPLE_CERT)
            return;
        try {
            $apns = new Zend_Mobile_Push_Apns();
            $apns->setCertificate($APPLE_CERT);
            $apns->setCertificatePassphrase($passValue);
            $message = new Zend_Mobile_Push_Message_Apns();
            $message->setAlert($params['message']);
            $message->setBadge(1);
            $message->setSound('default');
            $message->setId(time());
            $message->setToken($registrationTokn);

//            try {
//                $apns->send($message);
//            } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
//                // you would likely want to remove the token from being sent to again
//                echo $e->getMessage();
//            } catch (Zend_Mobile_Push_Exception $e) {
//                // all other exceptions only require action to be sent
//                echo $e->getMessage();
//            }
            
            $siteiosappApnMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.apn.mode', 1);
            if (!empty($siteiosappApnMode)) {
                // For the Production           
                try {
                    $apns->send($message);
                } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
                    // you would likely want to remove the token from being sent to again
                    echo $e->getMessage();
                } catch (Zend_Mobile_Push_Exception $e) {
                    // all other exceptions only require action to be sent
                    echo $e->getMessage();
                }
            } else {
                // For the Development
                try {
                    $apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
                    $apns->send($message);
                } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
                    // you can either attempt to reconnect here or try again later
                    exit(1);
                } catch (Zend_Mobile_Push_Exception $e) {
                    echo 'APNS Connection Error:' . $e->getMessage();
                    exit(1);
                }
            }
            
            $apns->close();
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function canSendIosNotification($returnKey = false) {
        $ipaPassword = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.password', null);
        if(empty($ipaPassword))
            return;
        
        if (!$this->_APPLE_CERT) {
            $apnPath = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp.apple.server.apn.key");
            if (@file_exists($apnPath)) {
                $this->_APPLE_CERT = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp.apple.server.apn.key");
            } else
                return false;
        }
        if (!$this->_APPLE_CERT)
            return false;


        return $returnKey ? $this->_APPLE_CERT : true;
    }

    public function sendIosNotification($registrationTokn, $notification) {
        $APPLE_CERT = $this->canSendIosNotification(true);
        if (!$APPLE_CERT)
            return;

        try {
            $notificationTypeInfo = $notification->getTypeInfo();

            $hrefLinks = explode('href', $notification->__toString());
            $hrefArray = explode('"', $hrefLinks[count($hrefLinks) - 1]);

            if (count($hrefArray) == 1)
                $hrefArray = explode('\'', $hrefArray[0]);

            $hreflink = $this->addBaseUrl($hrefArray[1]);

            $user_id = $notification->user_id;
            $user = Engine_Api::_()->user()->getUser($user_id);
            $countUnread = Engine_Api::_()->getDbTable('notifications', 'activity')->hasNotifications($user);
            $apns = new Zend_Mobile_Push_Apns();
            $apns->setCertificate($APPLE_CERT);
// if you have a passphrase on your certificate:
            $passValue = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteiosapp.password");
            $apns->setCertificatePassphrase($passValue);

            $message = new Zend_Mobile_Push_Message_Apns();
            if (4 & (int) $notificationTypeInfo->siteiosapp_pushtype)
                $message->setAlert($this->getContent($notification));
//           if (2 & (int) $notificationTypeInfo->siteiosapp_pushtype)
            $message->setBadge($countUnread);
            if (1 & (int) $notificationTypeInfo->siteiosapp_pushtype)
                $message->setSound('default');
            
            $notificationObject = $notification->getObject();
            $messageAPNData = array(
                'href' => $hreflink,
                'type' => $notificationObject->getType(),
                'id' => $notificationObject->getIdentity()
            );
            $message->setCustomData($messageAPNData);
            $message->setId(time());
            $message->setToken($registrationTokn);

            $siteiosappApnMode = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteiosapp.apn.mode', 1);
            if (!empty($siteiosappApnMode)) {
                // For the Production           
                try {
                    $apns->send($message);
                } catch (Zend_Mobile_Push_Exception_InvalidToken $e) {
                    // you would likely want to remove the token from being sent to again
//                    echo $e->getMessage();
                } catch (Zend_Mobile_Push_Exception $e) {
                    // all other exceptions only require action to be sent
//                    echo $e->getMessage();
                }
            } else {
                // For the Development
                try {
                    $apns->connect(Zend_Mobile_Push_Apns::SERVER_SANDBOX_URI);
                    $apns->send($message);
                } catch (Zend_Mobile_Push_Exception_ServerUnavailable $e) {
                    // you can either attempt to reconnect here or try again later
//                    exit(1);
                } catch (Zend_Mobile_Push_Exception $e) {
//                    echo 'APNS Connection Error:' . $e->getMessage();
//                    exit(1);
                }
            }
            
            $apns->close();
            return true;
        } catch (Exception $e) {
//            echo $e->getMessage();
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
