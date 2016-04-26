<?php

class Ynfullslider_AdminSliderController extends Core_Controller_Action_Admin
{
    // define steps when edit/create slider, change these to change order, 'general' should be placed first
    protected $_steps = array(

    );

    public function init()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynfullslider_admin_main', array(), 'ynfullslider_admin_main_sliders');

        // set subject or create a new slider, these wil be used for both create and edit
        if (0 !== ($slider_id = ( int )$this -> _getParam('id')) &&
            null !== ($slider = Engine_Api::_() -> getItem('ynfullslider_slider', $slider_id)))
        {
        } else {
            $sliderTable = Engine_Api::_()->getItemTable('ynfullslider_slider');
            $slider = $sliderTable->createRow();
            $slider->unlimited = 1;
        }
        Engine_Api::_() -> core() -> setSubject($slider);

        $headScript = $this->view->headScript();
        $headLink = $this->view->headLink();
        $staticBaseUrl = Zend_Registry::get('StaticBaseUrl');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/jquery-1.9.1.min.js');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/jquery-ui.min.js');
        $headLink->appendStylesheet($staticBaseUrl . 'application/modules/Ynfullslider/externals/styles/jquery-ui.min.css');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/ynfullslider-actions.js');
        $headLink->appendStylesheet($staticBaseUrl . 'application/modules/Ynfullslider/externals/styles/navstylechange.css');
        $headLink->appendStylesheet($staticBaseUrl . 'application/modules/Ynfullslider/externals/styles/settings_1.css');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/jquery.themepunch.plugins.min.js');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/jquery.themepunch.revolution.min.js');

        $this->_steps = array(
            'general' => Zend_Registry::get('Zend_Translate')->_('General Information'),
            'navigator' => Zend_Registry::get('Zend_Translate')->_('Navigator'),
            'background' => Zend_Registry::get('Zend_Translate')->_('Slider Background'),
            'finish' => Zend_Registry::get('Zend_Translate')->_('Finish Slider'),
        );
        $this->view->slider = $slider;
        $this->view->steps = $this->_steps;
        $this->view->currentStepIndex = $this->_getCurrentStepIndex() + 1;
    }

    // Step navigator
    private function _getCurrentStep()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        return $request->getActionName();
    }

    private function _getCurrentStepIndex()
    {
        $keys = array_keys($this->_steps);
        return array_search($this->_getCurrentStep(), $keys);
    }

    private function _getPrevStep()
    {
        $keys = array_keys($this->_steps);
        $ordinal = ($this->_getCurrentStepIndex() - 1) % count($keys);
        $prev = $keys[$ordinal];
        return $prev;
    }

    private function _getNextStep()
    {
        $keys = array_keys($this->_steps);
        $ordinal = ($this->_getCurrentStepIndex() + 1) % count($keys);
        $next = $keys[$ordinal];
        return $next;
    }

    private function goNext($slider_id)
    {
        $nextStep = $this->_getNextStep();
        if ($nextStep){
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynfullslider','controller'=>'slider', 'action'=>$nextStep, 'id'=>$slider_id), 'admin_default', TRUE);
        } else {
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynfullslider','controller'=>'sliders', 'action'=>'index'), 'admin_default', TRUE);
        }
    }

    public function manageSlidesAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        // delete selected slides
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key=>$value) {
                if ($key == 'delete_' . $value)
                {
                    $slide = Engine_Api::_()->getItem('ynfullslider_slide', $value);
                    if ($slide)
                        $slide->delete();
                }
            }
        }

        // search form
        $this->view->form = $form = new Ynfullslider_Form_Admin_Search;
        $form->title->setAttrib('placeholder','Search slide item');
        $params = $this->_getAllParams();
        $form->populate($params);

        $this->view->slides = $slides = $slider->getSlides($params, true);
        $this->view->slider = $slider;
        $this->view->formParams = $params;
    }

    public function updateSlideOrderAction()
    {
        $order = $this -> _getParam('order');
        $table = Engine_Api::_()->getDbTable('slides', 'ynfullslider');
        $slideOrder = explode(',', $order);
        $table->updateSlideOrder($slideOrder);
    }

    public function generalAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        // GET FORM
        $this->view->form = $form = new Ynfullslider_Form_Admin_Slider_General();
        $sliderParams = array_merge($slider->toArray(), $slider->getParams());
        if (!$sliderParams['title'])
            $sliderParams['title']= Zend_Registry::get('Zend_Translate') -> _('Untitled');

        // INIT DATETIME FORMAT
        if ($slider->valid_from != 0)
            $sliderParams['valid_from'] = date('Y-m-d', strtotime($slider->valid_from));
        else
            $sliderParams['valid_from'] = '';
        if ($slider->valid_to != 0)
            $sliderParams['valid_to'] = date('Y-m-d', strtotime($slider->valid_to));
        else
            $sliderParams['valid_to'] = '';

        $form->populate($sliderParams);
        $request = $this->getRequest();

        // CHECK POST
        if(!$request->isPost()) {
            return;
        }
        if( !$form->isValid($request->getPost()) ) {
            return;
        }

        // PROCESS FORM
        $values = $form->getValues();

        $valid_from_time = $values['valid_from'];
        $valid_to_time = $values['valid_to'];
        if ($valid_from_time)
            $values['valid_from'] = date('Y-m-d H:i:s', strtotime($valid_from_time));
        if ($valid_to_time)
            $values['valid_to'] = date('Y-m-d H:i:s', strtotime($valid_to_time));

        // VALIDATE VALID TIME
        if (!$values['unlimited']) {
            if (!$values['valid_from']) {
                $form->getElement('valid_time_error')->setErrors(array('"Valid Time (From)" field must be valid and can\'t be empty'));
                return false;
            }
            if (!$values['valid_to']) {
                $form->getElement('valid_time_error')->setErrors(array('"Valid Time (To)" field must be valid and can\'t be empty'));
                return false;
            }

            if ($valid_from_time >= $valid_to_time)
            {
                $form->getElement('valid_time_error')->setErrors(array('"Valid Time (From)" must be less than "Valid Time (To)"'));
                return false;
            }
        }

        // SET DEFAULT TITLE IF USER DOES NOT ENTER IT
        if (!$values['title'])
            $values['title'] = Zend_Registry::get('Zend_Translate') -> _('Untitled');

        $db = Engine_Api::_()->getDbTable('sliders', 'ynfullslider')->getAdapter();
        $db->beginTransaction();
        try {
            $slider->setFromArray($values);
            $slider->setParams($values);
            $slider->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->commit();

        if ($success) {
            $this->goNext($slider->getIdentity());
        }

        return true;
    }

    public function navigatorAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        // get form
        $this->view->form = $form = new Ynfullslider_Form_Admin_Slider_Navigator();
        $form->populate(array_merge($slider->toArray(), $slider->getParams()));

        $request = $this->getRequest();

        if(!$request->isPost()) {
            return;
        }
        if( !$form->isValid($request->getPost()) ) {
            return;
        }

        // processing form
        $values = $form->getValues();
        $post = $this->getRequest()->getPost();

        $values['navigator_color'] = $post['navigator_color_picker'];

        $db = Engine_Api::_()->getDbTable('sliders', 'ynfullslider')->getAdapter();
        $db->beginTransaction();
        try {
            $slider->setParams($values);
            $slider->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->commit();

        if ($success) {
            $this->goNext($slider->getIdentity());
        }

        return true;
    }

    public function backgroundAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        // get form
        $this->view->form = $form = new Ynfullslider_Form_Admin_Slider_Background();
        $form->populate(array_merge($slider->toArray(), $slider->getParams()));

        $request = $this->getRequest();

        if(!$request->isPost()) {
            return;
        }
        if( !$form->isValid($request->getPost()) ) {
            return;
        }

        // processing form
        $values = $form->getValues();
        $post = $this->getRequest()->getPost();

        $values['background_color'] = $post['background_color_picker'];
        $values['background_border_color'] = $post['background_border_color_picker'];

        // REPOPULATE FORM IN CASE OF ERROR OCCUR
                $form->background_color->setValue($values['background_color']);
        $form->background_color_selector->setValue('<input value="'.$form->background_color->getValue().'" type="color" id="background_color_picker" name="background_color_picker"/>');
                $form->background_border_color->setValue($values['background_border_color']);
                $form->background_border_color_selector->setValue(Zend_Registry::get('Zend_Translate')->_('Border styles').'<input value="'.$form->background_border_color->getValue().'" type="color" id="background_border_color_picker" name="background_border_color_picker"/>');
        if ($values['background_option']) {
            if (!$values['background_image_url']) {
                $form->getElement('background_image_url_error')->setErrors(array('Value is required and can\'t be empty'));
                return false;
            } else {
                // DOWNLOAD BACKGROUND PHOTO AND CONVERT TO THUMB
                $photoId = Engine_Api::_()->getApi('image', 'ynfullslider')->_fetchImage($values['background_image_url']);
                if (!$photoId) {
                    $form->getElement('background_image_url_error')->setErrors(array('No image found at this location'));
                    return false;
                } else {
                    $slider->photo_id = $photoId;
                }
            }
        }

        $db = Engine_Api::_()->getDbTable('sliders', 'ynfullslider')->getAdapter();
        $db->beginTransaction();
        try {
            $slider->setParams($values);
            $slider->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->commit();

        if ($success) {
            $this->goNext($slider->getIdentity());
        }

        return true;
    }

    public function finishAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        return true;
    }

    public function cloneAction()
    {
        if (! $slider = Engine_Api::_()->core()->getSubject('ynfullslider_slider')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        // Check post
        if( $this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $newSliderTitle = $values['title'];
            $slider->cloneSlider($newSliderTitle);

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        //Out put
        $this->renderScript('admin-slider/clone.tpl');
    }
}