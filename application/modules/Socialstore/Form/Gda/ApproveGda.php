<?php
class Socialstore_Form_Gda_ApproveGda extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Approve Deal Request')
      ->setAttribs(array(
      'class' => 'global_form_popup',
      'id' => 'socicalstore_approve_request_gda'
      ))
      ;
    
   	$this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
   	  'decorators' => array(
        'ViewHelper',
      ),
    ));
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close()',
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