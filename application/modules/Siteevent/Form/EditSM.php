<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_EditSM extends Siteevent_Form_CreateSM {

    public $_error = array();
    protected $_item;
    protected $_defaultProfileId;
    protected $_editFullEventDate;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function getDefaultProfileId() {
        return $this->_defaultProfileId;
    }

    public function setDefaultProfileId($default_profile_id) {
        $this->_defaultProfileId = $default_profile_id;
        return $this;
    }

    public function setEditFullEventDate($editFullEventDate) {
        $this->_editFullEventDate = $editFullEventDate;
        return $this;
    }

    public function getFullEventDate() {
        return $this->_editFullEventDate;
    }

    public function init() {

        parent::init();
        $this->setTitle("Edit Event Info")
                ->setDescription("Edit the information of your event using the form below.");

        if ($this->location)
            $this->removeElement('location');
        $this->execute->setLabel('Save Changes');
    }

}