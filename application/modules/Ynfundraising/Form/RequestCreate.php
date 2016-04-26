<?php
class Ynfundraising_Form_RequestCreate extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Create Fundraising Campaign')->setAttrib('class','global_form_popup');
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$parent_id = $request->getParam('parent_id');
	$parent_type = $request->getParam('parent_type');
	$parent = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType(array('parent_id'=>$parent_id, 'parent_type'=>$parent_type));
	if(!$parent->checkExistRequest())
	{
		switch ($parent_type) {
			case 'idea':
				$this->setDescription('Your request for fundraising campaign creation will be sent to idea owner.');
				break;
			case 'trophy':
				$this->setDescription('Your request for fundraising campaign creation will be sent to trophy owner.');
				break;
			default:
				$this->setDescription('Your request for fundraising campaign creation will be sent to idea owner.');
				break;
		}
	    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
	      	->setMethod('POST');

		$this->addElement('dummy', 'confirm_request',array(
	      'label'=>'Are you sure you want to send this request?',
	     ));

		$this->addElement('Hidden', 'parent_id',array(
	      'value' => $parent_id,
	      'order' => 1
	    ));
		$this->addElement('Hidden', 'parent_type',array(
      'value' => $parent_type,
      'order' => 2
	    ));

	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Send Request',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')
	    ));

	    $this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => Zend_Registry::get('Zend_Translate')->_('or '),
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close()',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
	    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
	    $button_group = $this->getDisplayGroup('buttons');
	 }
	else
	{
		switch ($parent_type) {
			case 'idea':
				$this->setDescription('Your request was be sent to idea owner. Please go to My Request page to check it.');
				break;
			case 'trophy':
				$this->setDescription('Your request was be sent to trophy owner. Please go to My Request page to check it.');
				break;
			default:
				$this->setDescription('Your request was be sent to idea owner. Please go to My Request page to check it.');
				break;
		}
		$this->addElement('Cancel', 'cancel', array(
	      'label' => 'Cancel',
	      'link' => true,
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close()',
	    ));
	}
  }
}