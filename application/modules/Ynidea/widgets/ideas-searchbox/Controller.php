<?php

class Ynidea_Widget_IdeasSearchboxController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
       $this->view->form = $form = new Ynidea_Form_Search();
	   
	   $categories = Engine_Api::_() -> getItemTable('ynidea_category')->getCategories();
        unset($categories[0]);
        foreach ($categories as $category) {
            $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
        }
		
		if (count($categories)) {
			$form->category_id->addMultiOption('0', $this->view->translate('Others'));
		}
	   $requests = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	   $form->isValid($requests);
	   $values = $form->getValues();
       $this->view->formValues = array_filter($values);
    }

}
