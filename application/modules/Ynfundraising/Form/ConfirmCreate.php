<?php
class Ynfundraising_Form_ConfirmCreate extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Create Fundraising Campaign')->setAttrib('class','global_form_popup');
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$parent_id = $request->getParam('parent_id');
	$parent_type = $request->getParam('parent_type');
	switch ($parent_type) {
		case 'idea':
			$this->setDescription('Are you sure you want to create fundraising campaign for this idea?');
			break;
		case 'trophy':
			$this->setDescription('Are you sure you want to create fundraising campaign for this trophy?');
			break;
		default:
			$this->setDescription('Are you sure you want to create fundraising campaign for this idea?');
			break;
	}

    $url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'create-step-one','parent_id'=>$parent_id,'parent_type'=>$parent_type),'ynfundraising_general');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Campaign',
      'type' => 'button',
      'ignore' => true,
      'onclick'=>"(function(){parent.location.href='{$url}';parent.Smoothbox.close();})();",
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
}