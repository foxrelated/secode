<?php

class Ynfullslider_Form_Admin_Slide_General extends Engine_Form
{
    public function init() {

        $this
            ->setAttrib('class', 'global_form')
        ;

        $this->addElement('text', 'title', array(
            'label'=>'Slide name',
            'maxlength'=>'255',
            'filters'=>array('StringTrim')
        ));

        // BACKGROUND STYLE
        $this->addElement('Radio', 'background_option', array(
            'label' => 'Background',
            'multiOptions' => array(
                '1' => 'Background image',
                '2' => 'Background video',
                '0' => 'Background color',
            ),
            'value' => '1',
            'onclick' => 'updateFields()'
        ));

        // BACKGROUND IMAGE
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

        $this->addElement('Select', 'background_size', array(
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

        $this->addElement('Select', 'background_repeat', array(
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

        $this->addElement('Select', 'background_position', array(
            'multiOptions' => $backgroundPositionOptions,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('background_size', 'background_repeat', 'background_position'), 'background_image_setting', array(
        ));

        // BACKGROUND COLOR
        $this->addElement('Heading', 'slide_background_color_selector', array(
            'value' => '<input value="#000000" type="color" id="slide_background_color_picker" name="slide_background_color_picker"/>',
            'onchange' => "ynfullslider_update_color('slide_background')",
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('text', 'slide_background_color', array(
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->slide_background_color->setAttrib('disabled', true);
        $this->addDisplayGroup(array('slide_background_color_selector', 'slide_background_color'), 'background_colors', array(
        ));

        // BACKGROUND VIDEO
        $this->addElement('hidden', 'photo_id', array(
            'label' => 'Photo File Id',
            'value' => '',
            'order' => '101'
        ));

        $this->addElement('hidden', 'video_file_id', array(
            'label' => 'Video File',
            'value' => '',
            'order' => '102'
        ));

        $this -> addElement('Dummy', 'html5_upload', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_Html5Upload.tpl',
                    'class' => 'form element',
                )
            )),
        ));

        $this->addElement('Checkbox', 'loop', array(
            'label' => 'Loop',
            'value' => 1,
        ));
        $this->addElement('Checkbox', 'autoplay', array(
            'label' => 'Autoplay',
            'value' => 0,
        ));
        $this->addElement('Checkbox', 'muted', array(
            'label' => 'Muted',
            'value' => 0,
        ));

        $this->addDisplayGroup(array('loop', 'autoplay', 'muted'), 'background_video_setting', array(
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Next',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
    }
}
?>