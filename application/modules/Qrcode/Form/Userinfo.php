<?php
/**
 * SocialEngine
 *
 * @package    qrcode
 */

/**
 * @package    qrcode
 */
class Qrcode_Form_Userinfo extends Engine_Form
{
	public function init()
	{
	/*	$user_level = Engine_Api::_()->user()->getViewer()->level_id;
		$user = Engine_Api::_()->user()->getViewer();

		// Init form
	$this
		->setTitle('Create a new qrcode')
		->setDescription('Choose option on your screen to create qrcode')
		->setAttrib('id', 'form-upload')
		->setAttrib('name', 'qrcode_create')
		->setAttrib('enctype','multipart/form-data')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

	

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

		$this->addElement('submit', 'submit', array(
      'label' => 'Create',
      'type' => 'submit',
		));*/
	}
}
