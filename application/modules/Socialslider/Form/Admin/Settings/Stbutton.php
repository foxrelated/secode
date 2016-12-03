<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stbutton
 *
 * @author isabek
 */
class Socialslider_Form_Admin_Settings_Stbutton extends Engine_Form {

    public function init() {

        $this->setTitle('')
                ->setDescription('')
                ->setAttrib('class', 'global_form_popup');
        
        $this->addElement('Hidden','hidden',array(
            
        ));

        $this->addElement('Text', 'code', array(
            'label' => "http://www.facebook.com/facebook",
            'description' => '',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => 'This field is required.'
                        )
                ))
            ),
            'attribs' => array(
                'style' => 'width: 330px;'
            )
        ));



        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'href' => '',
            'link' => true,
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array('ViewHelper')
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }

}

?>
