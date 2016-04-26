<?php

class Ynfullslider_AdminSlideController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynfullslider_admin_main', array(), 'ynfullslider_admin_main_sliders');

        // set subject or create a new slider, these wil be used for both create and edit
        if (0 !== ($slide_id = ( int )$this -> _getParam('id')) &&
            null !== ($slide = Engine_Api::_() -> getItem('ynfullslider_slide', $slide_id)))
        {
        } else {
            $slideTable = Engine_Api::_()->getItemTable('ynfullslider_slide');
            $slide = $slideTable->createRow();
            $slide->slide_order = 9999;
        }
        Engine_Api::_() -> core() -> setSubject($slide);
        $this->view->slide = $slide;

        $sliderID = $slide->slider_id ? $slide->slider_id :$this->_getParam('slider_id');
        $slider = Engine_Api::_()->getItem('ynfullslider_slider', $sliderID);
        $this->view->slider = $slider;

        $staticBaseUrl = Zend_Registry::get('StaticBaseUrl');
        $headScript = $this->view->headScript();
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/jquery-1.9.1.min.js');
        $headScript->appendFile($staticBaseUrl . 'application/modules/Ynfullslider/externals/scripts/ynfullslider-actions.js');
    }

    public function cloneAction()
    {
        // disable render
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if (! $slide = Engine_Api::_()->core()->getSubject('ynfullslider_slide')) {
            return;
        }

        $slide->cloneSlide();

        return $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh'=> true,
            'messages' => array('Slide cloned successfully')
        ));
    }

    public function generalAction()
    {
        if (! $slide = Engine_Api::_()->core()->getSubject('ynfullslider_slide')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        $sliderID = $slide->slider_id ? $slide->slider_id :$this->_getParam('slider_id');
        $slider = Engine_Api::_()->getItem('ynfullslider_slider', $sliderID);
        // GET FORM
        $this->view->form = $form = new Ynfullslider_Form_Admin_Slide_General();
        $slideOptions = array_merge($slide->toArray(), $slide->getParams());
        if (!$slideOptions['title'])
            $slideOptions['title']= Zend_Registry::get('Zend_Translate') -> _('slide') . ' ' . ($slider->slide_count + 1);
        $form->populate($slideOptions);

        $request = $this->getRequest();

        if(!$request->isPost()) {
            return;
        }
        if( !$form->isValid($request->getPost()) ) {
            return;
        }

        // PROCESS FORM
        $values = $form->getValues();
        $post = $this->getRequest()->getPost();

        $values['slide_background_color'] = $post['slide_background_color_picker'];
        $values['slider_id'] = $sliderID;
        if (!$values['title'])
            $values['title'] = Zend_Registry::get('Zend_Translate') -> _('slide') . ' ' . ($slider->slide_count + 1);

        if ($values['background_option'] == 1) {
            $form->slide_background_color->setValue($values['slide_background_color']);
            $form->slide_background_color_selector->setValue('<input value="'.$form->slide_background_color->getValue().'" type="color" id="slide_background_color_picker" name="slide_background_color_picker"/>');
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
                    $values['photo_id'] = $photoId;
                }
            }
        } elseif ($values['background_option'] == 2) {
            $form->slide_background_color->setValue($values['slide_background_color']);
            $form->slide_background_color_selector->setValue('<input value="'.$form->slide_background_color->getValue().'" type="color" id="slide_background_color_picker" name="slide_background_color_picker"/>');
            if (!$values['video_file_id']) {
                $form->getElement('video_file_id')->setErrors(array('Please upload a video'));
                return false;
            }
        }
        $db = Engine_Api::_()->getDbTable('slides', 'ynfullslider')->getAdapter();
        $db->beginTransaction();
        try {
            if (!$slide->getIdentity()) {
                $slider->slide_count = $slider->slide_count + 1;
                $slider->save();
            }
            $slide->setFromArray($values);
            $slide->setParams($values);
            $slide->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }

        $db->commit();

        if ($success) {
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynfullslider','controller'=>'slide', 'action'=>'editor', 'id'=>$slide->getIdentity()), 'admin_default', TRUE);
        } else {
            return $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynfullslider','controller'=>'slider', 'action'=>'manage-slides', 'id'=>$sliderID), 'admin_default', TRUE);
        }
    }

    public function editorAction()
    {
        $scriptStaticBaseUrl = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynfullslider/externals/scripts/';
        $this->view->headScript()->appendFile($scriptStaticBaseUrl . 'ynfullslider.base.element.js');
        $this->view->headScript()->appendFile($scriptStaticBaseUrl . 'ynfullslider.button.element.js');
        $this->view->headScript()->appendFile($scriptStaticBaseUrl . 'ynfullslider.image.element.js');
        $this->view->headScript()->appendFile($scriptStaticBaseUrl . 'ynfullslider.text.element.js');
        $this->view->headScript()->appendFile($scriptStaticBaseUrl . 'ynfullslider.video.element.js');

        if (! $slide = Engine_Api::_()->core()->getSubject('ynfullslider_slide')) {
            return $this -> _helper -> requireSubject -> forward();
        }

        $this->view->form = $form = new Ynfullslider_Form_Admin_Slide_Editor();
        $form->populate($slide->toArray());

        // READ THEME WIDTH
        $contentWidth = 1140;
//        $themeTable = Engine_Api::_()->getDbtable('themes', 'core');
//        $themeSelect = $themeTable->select()->where('active = ?', 1)->limit(1);
//        $currentTheme = $themeTable->fetchRow($themeSelect);
//        if ($currentTheme) {
//            $currentThemeName = $currentTheme->name;
//            $cssFile = 'application/themes/' . $currentThemeName . '/constants.css';
//            $pattern = '/theme_content_width:\s*(.*)px/';
//            if (preg_match($pattern, file_get_contents($cssFile), $matches)) {
//                $contentWidth = $matches[1];
//            }
//        }
        $this->view->content_width = $contentWidth;

        // CHECK POST
        $request = $this->getRequest();

        if(!$request->isPost()) {
            return;
        }
        if( !$form->isValid($request->getPost()) ) {
            return;
        }

        // PROCESS FORM
        $values = $form->getValues();
        $slide->setFromArray($values);
        $slide->save();

        return $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynfullslider','controller'=>'slider', 'action'=>'manage-slides', 'id'=>$this->view->slider->getIdentity()), 'admin_default', TRUE);
    }
}