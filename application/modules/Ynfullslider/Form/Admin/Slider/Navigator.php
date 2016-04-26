<?php

class Ynfullslider_Form_Admin_Slider_Navigator extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;


        $this->addElement('hidden', 'navigator_id', array(
            'value' => 1,
        ));

        $this -> addElement('Dummy', 'navigator', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_slider_edit_navigator.tpl',
                    'class' => 'form element',
                )
            )),
        ));

        $this->addElement('Heading', 'navigator_color_selector', array(
            'label' => 'Navigator color',
            'value' => Zend_Registry::get('Zend_Translate')->_('Navigator color').'<input value="#000000" type="color" id="navigator_color_picker" name="navigator_color_picker"/>',
            'onchange' => "ynfullslider_update_color('navigator')",
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('text', 'navigator_color', array(
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->navigator_color->setAttrib('disabled', true);
        $this->addDisplayGroup(array('navigator_color_selector', 'navigator_color'), 'colors', array(
            
        ));

        // Buttons
        $this->addElement('Button', 'prev', array(
            'label' => 'Back',
            'ignore' => true,
            'onclick' => 'history.go(-1); return false;',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'ignore' => true,
            'link' => true,
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'ynfullslider', 'controller' => 'sliders', 'action' => 'index'), 'admin_default', true),
            'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('prev', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save & Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}
?>