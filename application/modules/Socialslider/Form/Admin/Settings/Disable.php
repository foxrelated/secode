<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Disable
 *
 * @author isabek
 */
class Socialslider_Form_Admin_Settings_Disable extends Engine_Form {

    public function init() {

        $this->setTitle('Disable button?')
                ->setDescription('Are you sure you want to disable this button?')
                ->setAttrib('class','global_form_popup');

        $this->addElement('Button', 'disable', array(
            'label' => 'Disable',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array('ViewHelper')
        ));

        $this->addDisplayGroup(array('disable', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}

?>
