<?php

class Ynfullslider_AdminSlidesController extends Core_Controller_Action_Admin
{

  public function init()
  {
    // not show navigation for now
  }

  public function indexAction()
  {
    return false;
  }

  public function deleteAction() {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->slide_id=$id;
    // Check post
    if( $this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $slide = Engine_Api::_()->getItem('ynfullslider_slide', $id);
        $slide->delete();
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
    $this->renderScript('admin-slides/delete.tpl');
  }

  public function toggleShowSlideAction()
  {
    $show = $this -> _getParam('show');
    $slideId = $this -> _getParam('slide_id');
    $slide = Engine_Api::_()->getItem('ynfullslider_slide', $slideId);
    if ($slide) {
      $slide->show_slide = $show;
      $slide->save();
    }
  }

  public function previewSlideAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);


    $slideId = $this->_getParam('id');
    $slide = Engine_Api::_()->getItem('ynfullslider_slide', $slideId);
    $slider = $slide->getParentSlider();

    $layers = get_object_vars(json_decode($this->_getParam('layers')));

    $content = '';

    $view = Zend_Registry::get('Zend_View');
    $view->slider = $slider;
    $view->slide = $slide;
    $view->layers = $layers;
    $content .= $view->render('_slide_preview.tpl');

    echo $content;
  }
}