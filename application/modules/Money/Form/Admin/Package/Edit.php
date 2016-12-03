<?php
class Money_Form_Admin_Package_Edit extends Money_Form_Admin_Package_Create
{
  public function init()
  {
    parent::init();
    
    $this
      ->setTitle('Edit Subscription Plan')
      ->setDescription('Please note that payment parameters (Price, ' .
          'Recurrence, Duration, Trial Duration) cannot be edited after ' .
          'creation. If you wish to change these, you will have to create a ' .
          'new plan and disable the current one.')
      ;

    // Disable some elements
    $this->getElement('price')
        ->setIgnore(true)
        ->setAttrib('disable', true)
        ->clearValidators()
        ->setRequired(false)
        ->setAllowEmpty(true)
        ;
    
    $this->removeElement('trial_duration');

    // Change the submit label
    $this->getElement('execute')->setLabel('Edit Plan');
  }
}