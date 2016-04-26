<?php
class Ynfundraising_Form_SearchSupporters extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_popup',
      ))
      ->setMethod('GET')
      ;

    $this->addElement('Text', 'name', array(
      'label' => '',
      'alt' => Zend_Registry::get('Zend_Translate')->_("Search..."),
      'value' => "",
      'style' => 'width: 200px',
      'onchange' => 'this.form.submit();',
    ));
  }
}