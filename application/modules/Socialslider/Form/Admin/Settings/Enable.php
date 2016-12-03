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
class Socialslider_Form_Admin_Settings_Enable extends Engine_Form {

    public function init() {

        $this->setTitle('Enable button?')
                ->setDescription('Are you sure you want to enable this button?')
                ->setAttrib('class','global_form_popup');

        $this->addElement('Button', 'enable', array(
            'label' => 'Enable',
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

        $this->addDisplayGroup(array('enable', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}

?>
