<?php

class Ynfullslider_Form_Admin_Slide_Editor extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;

        $this->addElement('hidden', 'slide_elements', array(
            'value' => '',
            'order' => 104
        ));

        $this->addElement('hidden', 'elements_order', array(
            'value' => '',
            'order' => 105
        ));

        $this->addElement('hidden', 'animation_order', array(
            'value' => '',
            'order' => 106
        ));

        $this->addElement('hidden', 'elements_count', array(
            'value' => '',
            'order' => 107
        ));

        // Buttons
        $this->addElement('Button', 'preview', array(
            'label' => 'Preview',
            'onclick' => 'ynfullsliderPreviewSlide()',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save',
            'onclick' => 'ynfullsliderSaveSlide()',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

//        $this->addElement('Cancel', 'cancel', array(
//            'label' => 'cancel',
//            'link' => true,
//            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(),
//            'prependText' => ' or ',
//            'decorators' => array(
//                'ViewHelper',
//            ),
//        ));
//
//        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
//            'decorators' => array(
//                'FormElements',
//                'DivDivDivWrapper',
//            ),
//        ));
    }
}
?>