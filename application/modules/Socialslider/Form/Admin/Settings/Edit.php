<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Socialslider_Form_Admin_Settings_Edit
 *
 * @author isabek
 */
class Socialslider_Form_Admin_Settings_Edit extends Engine_Form {

    public function init() {

        $isEmptyMessage = 'This field is required';

        $this->setTitle('Edit Button')
                ->setAttrib('class', 'global_form_popup');

        $this->addElement('Text', 'button_name', array(
            'label' => 'Button Name',
            'required' => true,
            'filter' => 'StringTrim',
            'validators' => array(
                array('NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $isEmptyMessage
                        )
                ))
            )
        ));

        $this->addElement('File', 'button_file', array(
            'label' => 'Choose a picture',
            'description' => "You can skip this field.",
            'validators' => array(
                array('Extension', true, array('png', 'jpg'))
            )
        ));

        $this->addElement('Text', 'button_color', array(
            'label' => 'Button color',
            'class' => 'color'
        ));

        $this->addElement('Textarea', 'button_code', array(
            'label' => 'Button code',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $isEmptyMessage
                        )
                ))
            )
        ));

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Save Changes',
            'ignore' => true,
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
