<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Priceinfo_Add extends Engine_Form {

    public $_error = array();

    public function init() {

        $this->setTitle('Add Where to Buy Option');
        $this->setDescription('Add Where to Buy option for this event using the form below. Enter the name of the Store and other details where this event is available.');



        $whereToBuyArray = array();
        $otherTitle = null;
        $whereToBuy = Engine_Api::_()->getItemTable('siteevent_wheretobuy')->getList(array('enabled' => 1));
        foreach ($whereToBuy as $item):
            if ($item->getIdentity() == 1):
                $otherTitle = $item->getTitle();
                continue;
            endif;
            $whereToBuyArray[$item->getIdentity()] = $item->getTitle();
        endforeach;
        if ($otherTitle)
            $whereToBuyArray[1] = $otherTitle;

        $this->addElement('Select', 'wheretobuy_id', array(
            'label' => 'Store',
            'description' => "Select the Store where this event is available.",
            'required' => true,
            'allowEmpty' => false,
            'multioptions' => $whereToBuyArray,
            'onchange' => 'otherWhereToBuy(this.value)'
        ));
        $this->addElement('Text', 'title', array(
            'label' => 'Name',
            'style' => 'width:200px;',
        ));

        $this->addElement('Text', 'url', array(
            'label' => 'Store URL',
            'description' => "Enter the URL of the Store's webpage where this event is available. (We recommend you to enter complete URL.)",
            'style' => 'width:200px;',
            'required' => true,
            'allowEmpty' => false,
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.wheretobuy', 0) == 2) {
            $localeObject = Zend_Registry::get('Locale');
            $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
            $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
            $this->addElement('Text', 'price', array(
                'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price %s'), $currencyName),
                'style' => 'width:100px;',
                //        'required' => true,
                //        'allowEmpty' => false,
                'validators' => array(
                    array('Float', true),
                //  array('GreaterThan', true, array(0)),
                ),
            ));
        }

        $this->addElement('Text', 'address', array(
            'label' => 'Address', 'style' => 'width:200px;',
            'description' => 'Enter the address of the store.'
        ));
        $this->addElement('Text', 'contact', array(
            'label' => 'Phone', 'style' => 'width:200px;',
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Add',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array(
            'execute',
            'cancel',
                ), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

}