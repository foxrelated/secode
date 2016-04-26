<?php 
class Socialstore_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'social_store';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
     parent::indexAction();
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Store Question');
      $display = $form->getElement('display');
      $display->setLabel('Show on store page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on store page',
          0 => 'Hide on store page'
        )));
    }
   if(isset($form->search)){
        $form->removeElement('search');
    }
    if(isset($form->show)){
        $form->removeElement('show');
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Store Question');
      $form->removeElement('search');
      $form->removeElement('show');
      $display = $form->getElement('display');
      $display->setLabel('Show on store page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on store page',
          0 => 'Hide on store page'
        )));
    }
  }
}
?>
