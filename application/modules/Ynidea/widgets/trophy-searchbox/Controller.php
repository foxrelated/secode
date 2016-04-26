<?php

class Ynidea_Widget_TrophySearchboxController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
       $this->view->form = $form = new Ynidea_Form_SearchTrophy();
	   $requests = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	   $form->isValid($requests);
	   $values = $form->getValues();
       $this->view->formValues = array_filter($values);
    }

}
