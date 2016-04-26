<?php

class Ynfullslider_Form_Admin_Slider_General extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;

        $this->addElement('text', 'title', array(
            'label'=>'Slider name',
            'maxlength'=>'255',
            'filters'=>array('StringTrim')
        ));

        // width option
        $widthOptions = array(

            '0' => Zend_Registry::get('Zend_Translate')->_('Full width background and normal width slider'),
            '1' => Zend_Registry::get('Zend_Translate')->_('Normal width slider'),
            '2' => Zend_Registry::get('Zend_Translate')->_('Full width slider'),
        );

        $this->addElement('Select', 'width_option', array(
            'label' => 'Slider width',
            'multiOptions' => $widthOptions,
        ));

        // spacing
        $this->addElement('Integer', 'spacing_top', array(
            'label' => 'Spacing (top-bottom)',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 20,
        ));

        $this->addElement('Integer', 'spacing_bottom', array(
            'description' => 'px',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 20,
        ));
        $this->spacing_bottom->getDecorator('Description')->setOption('placement', 'append');

        $this->addDisplayGroup(array('spacing_top', 'spacing_bottom'), 'spacing', array(
            'decorators' => array(
                'FormElements',
            ),
        ));

        $this->addElement('Integer', 'max_height', array(
            'label' => 'Slider max-height',
            'description' => 'px',
            'required'=>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 350,
        ));

        $this->max_height->getDecorator('Description')->setOption('placement', 'append');

        //========================================
        // second section
//        $numberOfItemsOption = array(
//            '1' => $this->getTranslator()->translate("1 item (default)")
//        );
//        for ($i = 2; $i<=5; $i++) {
//            $numberOfItemsOption[$i] = $i . ' ' . $this->getTranslator()->translate("items");
//        }

//        $this->addElement('Select', 'no_of_slides_per_page', array(
//            'label' => 'No. slide per page',
//            'multiOptions' => $numberOfItemsOption,
//        ));

        $this->addElement('Integer', 'delay_time', array(
            'label' => 'Delay time per slide',
            'required'=>true,
            'description' => 'The time one slide stays on the screen in milliseconds',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 5000,
        ));
        $this->delay_time->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Radio', 'shuffle', array(
            'label' => 'Shuffle',
            'multiOptions' => array(
                1 => 'Yes, allow shuffle mode. The slider will be randomized only once at the start',
                0 => 'No, do not allow shuffle mode',
            ),
            'value' => 1,
        ));

        $this->addElement('Checkbox', 'random_transition', array(
            'label' => 'Random transitions',
            'value' => 1,
            'onclick' => "ynfullsliderToggleTransition(this)"
        ));


        // Transitions option
        $twidthOptions = array(
            'fade' => 'Fade',
            'boxfade' => 'Fade Boxes',
            'slotfade-horizontal' => 'Fade Slots Horizontal',
            'slotfade-vertical' => 'Fade Slots Vertical',
            'fadefromright' => 'Fade and Soptionde from Right',
            'fadefromleft' => 'Fade and Soptionde from Left',
            'fadefromtop' => 'Fade and Soptionde from Top',
            'fadefrombottom' => 'Fade and Soptionde from Bottom',
            'fadetoleftfadefromright' => 'Fade To Left and Fade From Right',
            'fadetorightfadetoleft' => 'Fade To Right and Fade From Left',
            'fadetobottomfadefromtop' => 'Fade To Top and Fade From Bottom',
            'fadetotopfadefrombottom' => 'Fade To Bottom and Fade From Top',
            'scaledownfromright' => 'Zoom Out and Fade From Right',
            'scaledownfromleft' => 'Zoom Out and Fade From Left',
            'scaledownfromtop' => 'Zoom Out and Fade From Top',
            'scaledownfrombottom' => 'Zoom Out and Fade From Bottom',
            'zoomout' => 'ZoomOut',
            'zoomin' => 'ZoomIn',
            'slotzoom-horizontal' => 'Zoom Slots Horizontal',
            'slotzoom-vertical' => 'Zoom Slots Vertical',
            'parallaxtoright' => 'Parallax to Right',
            'parallaxtoleft' => 'Parallax to Left',
            'parallaxtotop' => 'Parallax to Top',
            'parallaxtobottom' => 'Parallax to Bottom',
            'soptiondeup' => 'Soptionde To Top',
            'soptiondedown' => 'Soptionde To Bottom',
            'soptionderight' => 'Soptionde To Right',
            'soptiondeleft' => 'Soptionde To Left',
            'soptiondehorizontal' => 'Soptionde Horizontal (depending on Next/Previous)',
            'soptiondevertical' => 'Soptionde Vertical (depending on Next/Previous)',
            'boxsoptionde' => 'Soptionde Boxes',
            'slotsoptionde-horizontal' => 'Soptionde Slots Horizontal',
            'slotsoptionde-vertical' => 'Soptionde Slots Vertical',
            'curtain-1' => 'Curtain from Left',
            'curtain-2' => 'Curtain from Right',
            'curtain-3' => 'Curtain from Middle',
            '3dcurtain-horizontal' => '3D Curtain Horizontal',
            '3dcurtain-vertical' => '3D Curtain Vertical',
            'cubic' => 'Cube Vertical',
            'cubic-horizontal' => 'Cube Horizontal',
            'incube' => 'In Cube Vertical',
            'incube-horizontal' => 'In Cube Horizontal',
            'turnoff' => 'TurnOff Horizontal',
            'turnoff-vertical' => 'TurnOff Vertical',
            'papercut' => 'Paper Cut',
            'flyin' => 'Fly In',
            'random-static' => 'Random Premium',
            'random' => 'Random Flat and Premium',
        );

        $this->addElement('Select', 'transition_id', array(
            'label' => 'Transitions',
            'multiOptions' => $twidthOptions,
        ));

        $this->addElement('Integer', 'transition_duration', array(
            'label' => 'Transition duration',
            'required'=>true,
            'description' => 'The time a transition effect takes to complete in milliseconds',
            'validators' => array(
                array('Between', true, array(0,4000)),
            ),
            'value' => 500,
        ));
        $this->transition_duration->getDecorator('Description')->setOption('placement', 'append');

        // valid time from to
        $this->addElement('Checkbox', 'unlimited', array(
            'label' => 'Unlimited',
            'description' => 'Valid time',
            'value' => 1,
            'onclick' => "ynfullsliderToggleValidFields(this)",
        ));

        $this->addElement('text', 'valid_from', array(
            'placeholder' => Zend_Registry::get('Zend_Translate')->_('From'),
        ));
        $this->addElement('text', 'valid_to', array(
            'placeholder' => Zend_Registry::get('Zend_Translate')->_('To'),
        ));

        $this->addElement('hidden', 'valid_time_error', array(
            'label' => 'Valid Time',
            'order' => '100'
        ));


        //========================================
        // Buttons
        
        $this->addElement('Heading', 'break', array(
             'value' => '<br/>',
        )); 
        
        $this->addElement('Button', 'prev', array(
            'label' => 'Back',
            'ignore' => true,
            'onclick' => 'history.go(-1); return false;',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'Cancel',
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

//      WILL USE LIVE UPDATE NO PREVIEW BUTTON NEEDED
//        $this->addElement('Button', 'preview', array(
//            'label' => 'Preview',
//            'ignore' => true,
//            'onclick' => 'ynfullsliderPreviewSlider()',
//            'decorators' => array(
//                'ViewHelper',
//            ),
//        ));

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