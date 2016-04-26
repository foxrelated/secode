<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Fields.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Custom_Fields extends Fields_Form_Standard {

    public $_error = array();
    protected $_name = 'fields';
    protected $_elementsBelongTo = 'fields';

    public function init() {
        if (!$this->_item) {
            $siteevent_item = new Siteevent_Model_Event(array());
            $this->setItem($siteevent_item);
        }
        parent::init();

        $this->removeElement('submit');
    }

    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this
                    ->addDecorator('FormElements');
        }
    }

}