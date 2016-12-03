<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Socialslider_Form_Admin_Settings_Delete
 *
 * @author isabek
 */
class Socialslider_Form_Admin_Settings_Delete extends Engine_Form {

    public function init() {

        $this->setTitle('Delete Button')
                ->setDescription('Are you sure you want to delete this button?')
                ->setAttrib('class', 'global_form_popup');

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Remove Button',
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => 'true',
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array('ViewHelper')
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}

?>
