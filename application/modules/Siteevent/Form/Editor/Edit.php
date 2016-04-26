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
class Siteevent_Form_Editor_Edit extends Siteevent_Form_Editor_Create {

    public $_error = array();
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
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        $this->loadDefaultDecorators();
        $siteevent_title = "<b>" . $siteevent->title . "</b>";

        $this
                ->setTitle('Edit an Editor Review')
                ->setDescription(sprintf(Zend_Registry::get('Zend_Translate')->_("You can edit the editor review for %s below:"), $siteevent_title))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->getDecorator('Description')->setOption('escape', false);

        $this->submit->setLabel('Save Changes');
    }

}