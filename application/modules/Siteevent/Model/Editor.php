<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editor.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Editor extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;
    protected $_user;

    public function getUser() {

        //GET USER OBJECT
        if (empty($this->_user))
            $this->_user = Engine_Api::_()->getItem('user', $this->user_id);
        return $this->_user;
    }

    public function getTitle() {
        return $this->getUser()->getTitle();
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.editorprofile', 1)) {
            return $this->getUser()->getHref();
        } else {
            $slug = $this->getSlug();
            $params = array_merge(array(
                'route' => "siteevent_review_editor_profile",
                'reset' => true,
                'username' => $this->getUsername($this->user_id),
                'user_id' => $this->user_id,
                    ), $params);
            $route = $params['route'];
            $reset = $params['reset'];
            unset($params['route']);
            unset($params['reset']);
            return Zend_Controller_Front::getInstance()->getRouter()
                            ->assemble($params, $route, $reset);
        }
    }

    public function getUsername($user_id) {

        //GET USER OBJECT
        $user = Engine_Api::_()->getItem('user', $user_id);

        if (isset($user->username) && '' !== trim($user->username)) {
            return Engine_Api::_()->seaocore()->getSlug($user->username, 64);
        } else if (isset($user->displayname) && '' !== trim($user->displayname)) {
            return Engine_Api::_()->seaocore()->getSlug($user->displayname, 64);
        } else {
            return Zend_Registry::get('Zend_Translate')->_("deleted-member");
        }
    }

    public function getUserTitle($user_id) {

        return $this->getUser()->getTitle();
    }

    public function getUserEmail($user_id) {

        return $this->getUser()->email;
    }

}
