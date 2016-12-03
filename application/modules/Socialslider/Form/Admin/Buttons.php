<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author isabek
 */

/**
 * Global Settings Form
 * 
 *   Form Elements
 * 
 * Disable menu? (CheckBox, default:cheked)
 * Show (Select Box, default:all [registered members, unregistered members])
 * Location(RadioButtons[2], default: right[left])
 */
class Socialslider_Form_Admin_Buttons extends Engine_Form {

    public function init() {
        $isEmptyMessage = 'This field is required';

        $this->addElement('Text', 'button_name', array(
            'label' => 'Button name',
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
            'required' => true,
            'validators' => array(
                array('Extension', false, array('jpg', 'png'))
            )
        ));

        $this->addElement('Text', 'button_color', array(
            'label' => 'Button color',
            'attribs' => array(
                'class' => 'color'
            )
        ));

        $this->addElement('Textarea', 'button_code', array(
            'label' => 'Button code',
            'required'=>true,
            'filter'=>'StringTrim',
            'validators'=>array(
                array('NotEmpty', true , array(
                    'messages'=>array(
                        'isEmpty'=>$isEmptyMessage
                    )
                ))
            )
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Create',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}

?>
