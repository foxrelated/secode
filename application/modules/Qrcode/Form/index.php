<?php
/**
 * SocialEngine
 *
 * @package    qrcode
 */

/**
 * @package    qrcode
 */
class Qrcode_Form_Index extends Engine_Form
{
	public function init()
	{
		/*$user_level = Engine_Api::_()->user()->getViewer()->level_id;
		$user = Engine_Api::_()->user()->getViewer();

		// Init form
		$this
		->setTitle('Create a new qrcode')
		->setDescription('Choose option on your screen to create qrcode')
		->setAttrib('id', 'form-upload')
		->setAttrib('name', 'qrcode_create')
		->setAttrib('enctype','multipart/form-data')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

		//Init Radio
		$formOptions = array('0'=>'Website','1' => 'Phone','2'=>'Address','3'=>'Status','4'=>'Contact');
		$this->addElement('Radio', 'field', array(
      'label' => 'Choose option',
      'multiOptions' => $formOptions,
		'onChange' => 'check();',
		//  'label' => 'Choose option',
		//  'multiOptions' => $formOptions,

		));


		// Init text
		$this->addElement('Text', 'text', array(
      'label' => 'Enter value',

      'maxlength' => '40',
      'filters' => array(
		//new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
		new Engine_Filter_Censor(),
		new Engine_Filter_StringLength(array('max' => '63')),
		)
		));
		//init checkbox
		$this->addElement('Check', 'display', array(
     	 'label' => 'Choose option',
      	// 'multiOptions' => $formOptions,
		// 'onChange' => 'check();',
		));
		$this->addElement('submit', 'submit', array(
      'label' => 'Create',
      'type' => 'submit',
		));*/
	}
}
