<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesvideo_Widget_PopularityVideosController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
    // Default option for tabbed widget
    if (isset($_POST['params']))
      $params = ($_POST['params']);
    $this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
    $this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'fixedbutton');
    $this->view->limit_data = $limit_data = isset($params['video_limit']) ? $params['video_limit'] : $this->_getParam('video_limit', '10');
    $this->view->limit = ($page - 1) * $limit_data;
    $categoryId = isset($params['category_id']) ? $params['category_id'] : '';
    $show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria', array('like', 'comment', 'rating', 'by', 'title', 'featuredLabel', 'sponsoredLabel','favourite','hotLabel','watchnow'));
    $this->view->show_subcat = $show_subcat = isset($params['show_subcat']) ? $params['show_subcat'] : $this->_getParam('show_subcat', '1');
    $this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'list')));
		if(is_array($show_criterias)){
			foreach ($show_criterias as $show_criteria)
				$this->view->{$show_criteria . 'Active'} = $show_criteria;
		}
    $show_subcatcriterias = isset($params['show_subcatcriteria']) ? $params['show_subcatcriteria'] : $this->_getParam('show_subcatcriteria', array('countVideo', 'icon', 'title'));
		if(is_array($show_subcatcriterias)){
			foreach ($show_subcatcriterias as $show_subcatcriteria)
				$this->view->{$show_subcatcriteria . 'SubcatActive'} = $show_subcatcriteria;
		}
		$this->view->mouse_over_title = $mouse_over_title = isset($params['mouse_over_title']) ? $params['mouse_over_title'] : $this->_getParam('mouse_over_title', '1');
    $this->view->widthSubcat = $widthSubcat = isset($params['widthSubcat']) ? $params['widthSubcat'] : $this->_getParam('widthSubcat', '250px');
    $this->view->heightSubcat = $heightSubcat = isset($params['heightSubcat']) ? $params['heightSubcat'] : $this->_getParam('heightSubcat', '160px');
    
    $this->view->popularity = $popularity = $params['popularity'] = isset($params['popularity']) ? $params['popularity'] : $this->_getParam('popularity', 'creation_date');
    
    
    $this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width', '250px');
    $this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height', '160px');
		$this->view->textVideo = $textVideo = isset($params['textVideo']) ? $params['textVideo'] : $this->_getParam('textVideo', 'Videos we love');
    $params = array('video_limit' => $limit_data, 'pagging' => $loadOptionData, 'show_criterias' => $show_criterias, 'view_type' => $view_type, 'category_id' => $categoryId, 'width' => $width, 'height' => $height, 'show_subcat' => $show_subcat, 'show_subcatcriteria' => $show_subcatcriterias, 'widthSubcat' => $widthSubcat, 'heightSubcat', $heightSubcat,'textVideo'=>$textVideo);
    
    if (Engine_Api::_()->core()->hasSubject()) {
      $this->view->category = $category = Engine_Api::_()->core()->getSubject();
      $category_id = $category->category_id;
    } else {
      $this->view->category = $category = Engine_Api::_()->getItem('sesvideo_category', $params['category_id']);
      $category_id = $params['category_id'];
    }
    $innerCatData = array();
    $columnCategory = 'category_id';
    if (!$is_ajax) {
      if ($category->subcat_id == 0 && $category->subsubcat_id == 0) {
        $innerCatData = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getModuleSubcategory(array('category_id' => $category->category_id, 'column_name' => '*', 'countVideos' => true));
        $columnCategory = 'category_id';
      } else if ($category->subsubcat_id == 0) {
        $innerCatData = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getModuleSubsubcategory(array('countVideos' => true, 'category_id' => $category->category_id, 'column_name' => '*'));
        $columnCategory = 'subcat_id';
      } else
        $columnCategory = 'subsubcat_id';
      $this->view->innerCatData = $innerCatData;
      //breadcum
      $this->view->breadcrumb = $breadcrumb = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getBreadcrumb($category);
    }
    
    if($popularity == 'is_featured') {
	    $criteria = '1';
    } elseif($popularity == 'is_sponsored') {
	    $criteria = '2';
    } elseif($popularity == 'is_hot') {
	    $criteria = '6';
    }
    
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('videos', 'sesvideo')->getVideo(array($columnCategory => $category->category_id, 'status' => 1, 'watchLater' => true, 'popularCol' => $popularity, 'criteria' => $criteria));
    $paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber($page);
    $this->view->widgetName = 'category-view';
    // initialize type variable type
    $this->view->page = $page;
    $params = array_merge($params, array('category_id' => $category_id));
    $this->view->params = $params;
    if ($is_ajax) {
      $this->getElement()->removeDecorator('Container');
    } else {
      // Do not render if nothing to show
      if ($paginator->getTotalItemCount() <= 0) {
        
      }
    }
  }

}
