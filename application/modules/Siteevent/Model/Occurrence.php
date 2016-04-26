<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Event.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Model_Occurrence extends Core_Model_Item_Abstract {

    protected $_parent_type = 'siteevent_event';
    protected $_searchTriggers = array();
    protected $_parent_is_owner = false;

    public function getHref() {
        return $this->getParent()->getHref(array('occurrence_id' => $this->getIdentity()));
    }

    public function getTitle() {
        return $this->getParent()->getTitle();
    }

    public function getItemDate() {
        $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium');
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        return $view->translate('%1$s at %2$s', $view->locale()->toDate($this->starttime, array('size' => $datetimeFormat)), $view->locale()->toEventTime($this->starttime, array('size' => $datetimeFormat))
        );
    }

}