<?php
class Ynidea_Form_ManageNominees extends Engine_Form {
  public function init()
  {
     $this
      ->setTitle('Manage Nominees')
      ->setDescription('Choose the idea you want to nominees to this trophy.')
      ->setAttrib('id', 'ynidea_form_trophy_ideas')
	  ->setAttrib('action','javascript:;')
      ;
    /*
	$this->addElement('Text','search',array(
		'description' =>'(Filter the search box and press Enter to search)',
		'onkeypress' => 'updateIdeaList(event)',
	));
	$this->search->getDecorator("Description")->setOption("placement", "append");
	*/
    $this->addElement('Checkbox', 'all', array(
      'id' => 'ideaselectall',
      'label' => 'Choose All',
      'ignore' => true
    ));

    $this->addElement('MultiCheckbox', 'ideas', array(
      'label' => '',
    ));

    $this->addElement('Button', 'button', array(
      'label' => 'Save Change',
      'onclick'=>'submitForm()',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('button', 'cancel'), 'ideas_buttons');
  }
}