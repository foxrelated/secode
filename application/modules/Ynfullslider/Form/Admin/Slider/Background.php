<?php

class Ynfullslider_Form_Admin_Slider_Background extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;

        // BACKGROUND STYLE
        $this->addElement('Radio', 'background_option', array(
            'label' => 'Background',
            'multiOptions' => array(
                '1' => 'Use image',
                '0' => 'Use color',
            ),
            'value' => '1',
            'onclick' => 'updateFields()'
        ));

        $this->addElement('text', 'background_image_url', array(
            'placeholder'=> Zend_Registry::get('Zend_Translate')->_('Url of background image'),
            'maxlength'=>'255',
            'filters'=>array('StringTrim')
        ));

        $this->addElement('hidden', 'background_image_url_error', array(
            'Label'=>'Background image url',
            'order' => '100'
        ));

        $backgroundSizeOptions = array(
            'auto' => 'Auto',
            '100% 100%' => 'Stretched',
            'cover' => 'Cover',
            'contain' => 'Contain',
        );

        $this->addElement('Select', 'background_image_size', array(
            'multiOptions' => $backgroundSizeOptions,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $backgroundRepeatOptions = array(
            'repeat' => 'Repeat',
            'repeat-x' => 'Repeat X',
            'repeat-y' => 'Repeat Y',
            'no-repeat' => 'No Repeat'
        );

        $this->addElement('Select', 'background_image_repeat', array(
            'multiOptions' => $backgroundRepeatOptions,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $backgroundPositionOptions = array(
            'left top' => ucwords('left top'),
            'left center' => ucwords('left center'),
            'left bottom' => ucwords('left bottom'),
            'right top' => ucwords('right top'),
            'right center' => ucwords('right center'),
            'right bottom' => ucwords('right bottom'),
            'center top' => ucwords('center top'),
            'center center' => ucwords('center center'),
            'center bottom' => ucwords('center bottom'),
        );

        $this->addElement('Select', 'background_image_position', array(
            'multiOptions' => $backgroundPositionOptions,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('background_image_size', 'background_image_repeat', 'background_image_position'), 'background_image_setting', array(
        ));

        // BACKGROUND COLOR
        $this->addElement('Heading', 'background_color_selector', array(
            'value' => '<input value="#000000" type="color" id="background_color_picker" name="background_color_picker"/>',
            'onchange' => "ynfullslider_update_color('background')",
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('text', 'background_color', array(
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->background_color->setAttrib('disabled', true);
        $this->addDisplayGroup(array('background_color_selector', 'background_color'), 'background_colors', array(
        ));


        // BACKGROUND SHADOW
        $this->addElement('hidden', 'background_shadow_id', array(
            'value' => 1,
        ));

        $this -> addElement('Dummy', 'shadow', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_slider_edit_shadow.tpl',
                    'class' => 'form element',
                )
            )),
        ));

        // BACKGROUND BORDER
        $this->addElement('Heading', 'background_border_color_selector', array(
            'value' => Zend_Registry::get('Zend_Translate')->_('Border').'<input value="#000000" type="color" id="background_border_color_picker" name="background_border_color_picker"/>',
            'onchange' => "ynfullslider_update_color('background_border')",
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('text', 'background_border_color', array(
            'description' => 'Color',
            'decorators' => array(
                'ViewHelper',
                'Description'
            ),
        ));
        $this->background_border_color->setAttrib('disabled', true);

        $this->addDisplayGroup(array('background_border_color_selector', 'background_border_color'), 'background_border_colors', array(
        ));

        $this->addElement('Integer', 'background_border_width', array(
            'required'=>true,
            'description'=>'Thickness (Ex: 1)',
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 1,
        ));
        $this->background_border_width->getDecorator('Description')->setOption('placement', 'append');

        $backgroundBorderStyles = array(
            'none' => ucwords('none'),
            'hidden' => ucwords('hidden'),
            'dotted' => ucwords('dotted'),
            'dashed' => ucwords('dashed'),
            'solid' => ucwords('solid'),
            'double' => ucwords('double'),
            'groove' => ucwords('groove'),
            'ridge' => ucwords('ridge'),
            'inset' => ucwords('inset'),
            'outset' => ucwords('outset'),
        );

        $this->addElement('Select', 'background_border_style', array(
            'description'=>'Style',
            'multiOptions' => $backgroundBorderStyles,
        ));
        $this->background_border_style->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Heading', 'break', array(
             'value' => '<br/>',
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