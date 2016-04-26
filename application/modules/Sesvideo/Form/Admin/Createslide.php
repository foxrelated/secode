<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Createslide.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Form_Admin_Createslide extends Engine_Form {
  public function init() {
    $this
            ->setTitle('Upload New Video or Photo')
            ->setDescription("Below, enter the details for the new video or photo.")
            ->setAttrib('id', 'form-create-slide')
            ->setAttrib('name', 'sesvideo_create_slide')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('onsubmit', 'return checkValidation();')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $this->setMethod('post');
    $this->addElement('Text', 'title', array(
        'label' => 'Caption',
        'description' => 'Enter the caption for this video or photo.',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Text', 'title_button_color', array(
        'label' => 'Caption Color',
        'description' => 'Choose the color for the caption.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
        'description' => 'Enter the description for this video or photo.',
        'allowEmpty' => true,
        'required' => false,
    ));
    $this->addElement('Text', 'description_button_color', array(
        'label' => 'Description Color',
        'description' => 'Choose the color for the description.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
    ));

    $slide_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('slide_id', 0);
    if (!$slide_id) {
      $required = true;
      $allowEmpty = false;
    } else {
      $required = false;
      $allowEmpty = true;
    }
    $this->addElement('File', 'thumb', array(
        'label' => 'Thumbnail Icon',
        'description' => 'Upload a thumbnail icon for the video. This icon will be shown in the HTML5 Video Background at user end.(100X100)',
        'allowEmpty' => $allowEmpty,
        'required' => $required,
    ));
    $this->thumb->addValidator('Extension', false, 'jpg,png,jpeg');

    $this->addElement('File', 'file', array(
        'allowEmpty' => $allowEmpty,
        'required' => $required,
        'label' => 'Upload Video or Photo',
        'description' => 'Upload a video or photo [Note: currently this plugin support ".mp4" videos only and photos with extension: “jpg, png and jpeg] only.]',
    ));
    $this->file->addValidator('Extension', false, 'jpg,png,jpeg,mp4');

    //login button code
    $this->addElement('Select', 'login_button', array(
        'label' => 'Show Login Button',
        'description' => 'Do you want to show login button to the non-logged in users in the video or photo?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '1',
        'onChange' => 'log_button(this.value);'
    ));
    $this->addElement('Text', 'login_button_text', array(
        'label' => 'Login Button Text',
        'description' => 'Enter the text for the login button.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Login',
    ));
    $this->addElement('Text', 'login_button_text_color', array(
        'label' => 'Login Button Text Color',
        'description' => 'Choose the color for the login button text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'0295FF',
    ));
    $this->addElement('Text', 'login_button_color', array(
        'label' => 'Login Button Color',
        'description' => 'Choose the color for the login button.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#ffffff',
    ));
    $this->addElement('Text', 'login_button_mouseover_color', array(
        'label' => 'Login Button Mouse-over Color',
        'description' => 'Choose the color for the login button when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#eeeeee',
    ));


    //signup button code
    $this->addElement('Select', 'signup_button', array(
        'label' => 'Show Sign Up Button',
        'description' => 'Do you want to show login button to the non-logged in users in the video or photo?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '1',
        'onChange' => 'sign_button(this.value);'
    ));
		$this->addElement('Text', 'signup_button_text', array(
        'label' => 'Sign Up Button Text',
        'description' => 'Enter the text for the sign up button.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Signup',
    ));
    $this->addElement('Text', 'signup_button_text_color', array(
        'label' => 'Sign Up Button Text Color',
        'description' => 'Choose the color for the login button text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#ffffff',
    ));
    $this->addElement('Text', 'signup_button_color', array(
        'label' => 'Sign Up Button Color',
        'description' => 'Choose the color for the sign up button.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#0295FF',
    ));
    $this->addElement('Text', 'signup_button_mouseover_color', array(
        'label' => 'Signup Button Mouse-over Color',
        'description' => 'Choose the color for the sign up button when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#067FDE',
    ));
    $this->addElement('Select', 'show_register_form', array(
        'label' => 'Show Sign Up Form',
        'description' => 'Do you want to show the sign up registration form in this video or photo?',
        'description' => '',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '0',
        'onChange' => 'register_form(this.value);'
    ));
    $this->addElement('Select', 'position_register_form', array(
        'label' => 'Sign Up Form Placement',
        'description' => 'Choose the placement of the sign up form.',
        'multiOptions' => array('right' => 'Right Side', 'left' => 'Left Side'),
        'value' => 'right',
    ));

    //extra button code
    $this->addElement('Select', 'extra_button', array(
        'label' => 'Show Additional Button',
        'description' => 'Do you want to show an additional button on this video / photo?',
        'multiOptions' => array('1' => 'Yes', '0' => 'No'),
        'value' => '0',
        'onChange' => 'extra_buton(this.value);'
    ));
		$this->addElement('Text', 'extra_button_text', array(
        'label' => 'Button Text',
        'description' => 'Enter the text for the button.',
        'allowEmpty' => true,
        'required' => false,
        'value' => 'Read More',
    ));
    $this->addElement('Text', 'extra_button_text_color', array(
        'label' => 'Button Text Color',
        'description' => 'Choose the color for the button text.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#ffffff',
    ));
    $this->addElement('Text', 'extra_button_color', array(
        'label' => 'Button Color',
        'description' => 'Choose the color for the login button.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#F25B3B',
    ));
    $this->addElement('Text', 'extra_button_mouseover_color', array(
        'label' => 'Button Mouse-over Color',
        'description' => 'Choose the color for the button when users mouse over on it.',
        'class' => 'SEScolor',
        'allowEmpty' => true,
        'required' => false,
				'value'=>'#EA350F',
    ));
    $this->addElement('Text', 'extra_button_link', array(
        'label' => 'Link for Button',
        'description' => 'Enter a link for the button.',
        'allowEmpty' => true,
        'required' => false,
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'onClick' => 'javascript:parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}
