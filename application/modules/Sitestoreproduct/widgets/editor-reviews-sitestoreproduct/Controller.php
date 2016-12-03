<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_EditorReviewsSitestoreproductController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //CHECK SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitestoreproduct_product')) {
      return $this->setNoRender();
    }

    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SUBJECT
    $this->view->sitestoreproduct = $sitestoreproduct = Engine_Api::_()->core()->getSubject();

    //GET EDITOR REVIEW ID
    $params = array();
    $params['resource_id'] = $sitestoreproduct->product_id;
    $params['resource_type'] = $sitestoreproduct->getType();
    $params['viewer_id'] = 0;
    $params['type'] = 'editor';

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 1 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.reviews', 2) == 3) {
      $this->view->addEditorReview = $editor_review_id = Engine_Api::_()->getDbTable('reviews', 'sitestoreproduct')->canPostReview($params);
    } else {
      $this->view->addEditorReview = $editor_review_id = 0;
    }

    $params = $this->_getAllParams();
    $this->view->params = $params;

    $element = $this->getElement();
    $overViewColumn = Engine_Api::_()->sitestoreproduct()->getLanguageColumn('overview');
    $this->view->overview = $temp_overview = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, $overViewColumn);
    if(!empty ($temp_overview)){
      $this->view->overview = $temp_overview; 
    }else{
      $this->view->overview = Engine_Api::_()->getDbTable('otherinfo', 'sitestoreproduct')->getColumnValue($sitestoreproduct->product_id, 'overview');
    }
    $titleWidget = null;
    if ($editor_review_id) {
      $titleWidget = $this->_getParam('titleEditor');
    } elseif ($this->view->overview && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestoreproduct.overview', 1)) {
      $titleWidget = $this->_getParam('titleOverview');
    } else {
      $titleWidget = $this->_getParam('titleDescription');
    }
    if (!empty($titleWidget))
      $element->setTitle($titleWidget);
    
    //GET SLIDESHOW WIDTH HEIGHT
    $this->view->slideshow_width = $this->_getParam('slideshow_width', 600);
    $this->view->slideshow_height = $this->_getParam('slideshow_height', 400);
    $this->view->showCaption = $this->_getParam('showCaption', 1);
    $this->view->captionTruncation = $this->_getParam('captionTruncation', 200);    
    $this->view->showButtonSlide = $this->_getParam('showButtonSlide', 0);
    $this->view->mouseEnterEvent = $this->_getParam('mouseEnterEvent', 0);
    $this->view->thumbPosition = $this->_getParam('thumbPosition', 'bottom');
    $this->view->autoPlay = $this->_getParam('autoPlay', 0);
    $this->view->slidesLimit = $this->_getParam('slidesLimit', 20);

    $this->view->show_slideshow = $this->_getParam('show_slideshow', 1);
    if ($this->_getParam('loaded_by_ajax', false)) {
      $this->view->loaded_by_ajax = true;
      if ($this->_getParam('is_ajax_load', false)) {
        $this->view->is_ajax_load = true;
        $this->view->loaded_by_ajax = false;
        if (!$this->_getParam('onloadAdd', false))
          $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Container');
      } else {
        return;
      }
    }
    $this->view->showContent = true;

    //GET REVIEW
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->review = $review = Engine_Api::_()->getItem('sitestoreproduct_review', $editor_review_id);
    if (!empty($review)) {
      $this->view->editor = Engine_Api::_()->getDbTable('editors', 'sitestoreproduct')->getEditor($review->owner_id);

      $this->view->current = $page = $request->getParam('page', 1);

      $encoded_code = Zend_Json_Decoder::decode($review->body_pages);
      $this->view->body_pages = $encoded_code[$page - 1];
      $this->view->pageCount = $total_page = Count($encoded_code);

      $this->view->pagesInRange = array();
      for ($i = 1; $i <= $total_page; $i++) {
        $this->view->pagesInRange[] = $i;
      }

      if ($page > 1) {
        $this->view->previous = $page - 1;
      }

      if ($page < $total_page) {
        $this->view->next = $page + 1;
      }

      if ($total_page == $page) {
        $this->view->showconclusion = true;
      }

      //GET RATING TABLE
      $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitestoreproduct');
      $this->view->userRatingDataTopbox = $ratingTable->ratingbyCategory($sitestoreproduct->product_id, 'user', $sitestoreproduct->getType());

      if (!empty($review->profile_type_review)) {
        //CUSTOM FIELDS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitestoreproduct/View/Helper', 'Sitestoreproduct_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($review);
      }
    }
    $this->view->isAjax = $isAjax = $request->getParam('isAjax', 0);
    $this->view->showComments = $this->_getParam('showComments', 1);
    if (!empty($isAjax)) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }
  }

}