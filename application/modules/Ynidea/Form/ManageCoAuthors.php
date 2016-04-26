<?php
class Ynidea_Form_ManageCoAuthors extends Engine_Form {
  public function init()
  {
     $this
      ->setTitle('Manage Co-auhtors')
      ->setDescription('Choose the user you want to add co-author to this idea.')
      ->setAttrib('id', 'ynidea_form_manage_coauthor')
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
      'id' => 'authorselectall',
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