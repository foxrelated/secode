<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Album_Photos extends Engine_Form {

    public function init() {

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->addElement('Radio', 'cover', array(
            'label' => 'Album Cover',
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
        ));
    }

}