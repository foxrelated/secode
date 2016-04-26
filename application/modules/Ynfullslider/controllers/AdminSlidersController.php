<?php

class Ynfullslider_AdminSlidersController extends Core_Controller_Action_Admin
{

    public function init()
    {
        // not show navigation for now
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynfullslider_admin_main', array(), 'ynfullslider_admin_main_sliders');
    }

    public function indexAction()
    {
        // delete selected sliders
        if ($this->getRequest()->isPost())
        {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key=>$value) {
                if ($key == 'delete_' . $value)
                {
                    $slider = Engine_Api::_()->getItem('ynfullslider_slider', $value);
                    $slider->deleteAllSlides();
                    $slider->delete();
                }
            }
        }

        $page = $this->_getParam('page',1);
        $params = $this->_getAllParams();

        $this->view->form = $form = new Ynfullslider_Form_Admin_Search;
        $form->populate($params);
        $formValues = $form->getValues();

        $table = Engine_Api::_()->getItemTable('ynfullslider_slider');
        $this->view->paginator = $table->getSliderPaginator($params);
        // set default number of items per page @TODO: consider adding a setting for number of sliders per page
        $this->view->itemCountPerPage = $itemCountPerPage = 9;
        $this->view->paginator->setItemCountPerPage($itemCountPerPage);
        $this->view->paginator->setCurrentPageNumber($page);
        $this->view->formValues = $formValues;
    }

    public function settingAction()
    {
        //@TODO: will navigate to general action
        return false;
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->slider_id=$id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $slider = Engine_Api::_()->getItem('ynfullslider_slider', $id);
                // @TODO: delete all related slides (and elements)
                $slider->deleteAllSlides();
                $slider->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-sliders/delete.tpl');
    }

    public function previewSliderAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $preview_params = get_object_vars(json_decode($this->_getParam('preview_params')));

        $view = Zend_Registry::get('Zend_View');
        $content = '';
        $view->params = $preview_params;
        $content .= $view->render('_slider_preview.tpl');

        echo $content;
  }
}