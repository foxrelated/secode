<?php
class Ynfundraising_Form_Photo_Manage extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get('Zend_Translate');
    // Init form
    $this
      ->setTitle('Gallery')
      ->setAttribs(array(
      'style' => 'width: 700px'))
      ;
  	$this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));

	// video_url
	$this->addElement ( 'Text', 'video_url', array (
			'label' => 'YouTube URL',
			'alt' => $translate->_("Enter YouTube URL..."),
			'required' => false,
			'style'    => 'width: 400px;',
	));

   	$this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => $translate->_('or '),
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'create'), 'ynfundraising_general', true),
      'onclick' => '',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

	 $this->addDisplayGroup(array(
      'submit',
    	'cancel',
      ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }
}