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
class Siteevent_Form_Admin_WhereToBuy_Edit extends Siteevent_Form_Admin_WhereToBuy_Add {

    protected $_item;

    public function getItem() {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item) {
        $this->_item = $item;
        return $this;
    }

    public function init() {
        parent::init();

        $this
                ->setTitle('Edit Where to Buy')
                ->setDescription('Edit your Where to Buy option over here, and then click on "Save Changes" button.')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    }

}