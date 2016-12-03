<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tax.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Tax extends Engine_Form {

    public function init() {
        $this->setAttrib('id', 'event_tax_form')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('Checkbox', 'is_tax_allow', array(
            'description' => Zend_Registry::get('Zend_Translate')->_('Tax enabled'),
            'value' => '1',
            'allowEmpty' => false,
            'required' => true,
            'onclick' => 'javascript:showOtherElements(this.value)',
        ));

        $this->addElement('Text', 'tax_rate', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Rate (%)'),
            'maxlength' => 5,
            'required' => true,
            'allowEmpty' => false,
            'value' => '0',
            'validators' => array(
                array('Float', true),
                array('GreaterThan', false, array('min' => '0')),
            )
        ));

        $this->addElement('Text', 'tax_id_no', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Taxpayer Identification Number (TIN)'),
            'maxlength' => 15,
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Int', false),
                array('GreaterThan', false, array('min' => '0')),
            ),
        ));

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('Save'),
            'type' => 'submit',
            'ignore' => true,
            'class' => 'fleft',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Dummy', 'tax_loading_image', array(
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '_taxLoadingImage.tpl',
                        'class' => 'form element'
                    ),
                )
            ),
        ));
    }

}
