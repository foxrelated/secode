<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Controller.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Widget_CategoryViewController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
		// Default option for tabbed widget
		if(isset($_POST['params']))
			$params = json_decode($_POST['params'],true);
		$this->view->is_ajax = $is_ajax = isset($_POST['is_ajax']) ? true : false;
		$page = isset($_POST['page']) ? $_POST['page'] : 1 ;
		$this->view->identityForWidget = isset($_POST['identity']) ? $_POST['identity'] : '';
		$this->view->loadOptionData = $loadOptionData = isset($params['pagging']) ? $params['pagging'] : $this->_getParam('pagging', 'auto_load');
		$this->view->limit_data = $limit_data = isset($params['album_limit']) ? $params['album_limit'] :$this->_getParam('album_limit', '10');
 	  $this->view->limit = ($page-1)*$limit_data;
		$categoryId = isset($params['category_id']) ? $params['category_id'] : '';
		$show_criterias = isset($params['show_criterias']) ? $params['show_criterias'] : $this->_getParam('show_criteria',array('like','comment','rating','by','title','featuredLabel','sponsoredLabel','favourite','photo'));
		$this->view->show_subcat = $show_subcat = isset($params['show_subcat']) ? $params['show_subcat'] :$this->_getParam('show_subcat', '1');
		$this->view->view_type = $view_type = (isset($_POST['type']) ? $_POST['type'] : (isset($params['view_type']) ? $params['view_type'] : $this->_getParam('view_type', 'list')));
		foreach($show_criterias as $show_criteria)
			$this->view->{$show_criteria.'Active'} = $show_criteria;
			
		$show_subcatcriterias = isset($params['show_subcatcriteria']) ? $params['show_subcatcriteria'] : $this->_getParam('show_subcatcriteria',array('countAlbums','icon','title'));
		foreach($show_subcatcriterias as $show_subcatcriteria)
			$this->view->{$show_subcatcriteria.'SubcatActive'} = $show_subcatcriteria;
				
		$this->view->widthSubcat = $widthSubcat = isset($params['widthSubcat']) ? $params['widthSubcat'] : $this->_getParam('widthSubcat','250px');
		$this->view->heightSubcat = $heightSubcat = isset($params['heightSubcat']) ? $params['heightSubcat'] : $this->_getParam('heightSubcat','160px');	
		$this->view->width = $width = isset($params['width']) ? $params['width'] : $this->_getParam('width','250px');
		$this->view->height = $height = isset($params['height']) ? $params['height'] : $this->_getParam('height','160px');
		$params  = array('album_limit' => $limit_data,'pagging'=>$loadOptionData,'show_criterias'=>$show_criterias,'view_type'=>$view_type,'category_id' =>$categoryId,'width'=>$width,'height'=>$height,'show_subcat'=>$show_subcat,'show_subcatcriteria'=>$show_subcatcriterias,'widthSubcat'=>$widthSubcat,'heightSubcat'=>$heightSubcat);		
		if(Engine_Api::_()->core()->hasSubject()){
			$this->view->category = $category = Engine_Api::_()->core()->getSubject();
			$category_id = $category->category_id;
		}else{
			$this->view->category = $category =  Engine_Api::_()->getItem('sesalbum_category', $params['category_id']);
			$category_id = $params['category_id'];
		}
		$innerCatData = array();
		if(!$is_ajax){
			if($category->subcat_id == 0 && $category->subsubcat_id == 0){
				$innerCatData	= Engine_Api::_()->getDbtable('categories', 'sesalbum')->getModuleSubcategory(array('category_id' =>$category->category_id,'column_name'=>'*','countAlbums'=>true));
				$columnCategory = 'category_id';
			}else if($category->subsubcat_id == 0){
					$innerCatData = Engine_Api::_()->getDbtable('categories', 'sesalbum')->getModuleSubsubcategory(array('countAlbums'=>true,'category_id' =>$category->category_id,'column_name'=>'*','countAlbums'=>true));
					$columnCategory = 'subcat_id';
			}else
					$columnCategory = 'subsubcat_id';
			$this->view->innerCatData = $innerCatData;
			//breadcum
			$this->view->breadcrumb = $breadcrumb = Engine_Api::_()->getDbtable('categories', 'sesalbum')->getBreadcrumb($category);
		}
		$columnCategory = isset($params['columnCategory']) ? $params['columnCategory'] : $columnCategory;
		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('albums', 'sesalbum')->getalbums(array($columnCategory=>$category_id),false);		
		$paginator->setItemCountPerPage($limit_data);
    $paginator->setCurrentPageNumber($page);
		$this->view->widgetName = 'category-view';
		// initialize type variable type
		$this->view->page = $page ;
		$this->view->params = array_merge($params,array('category_id'=>$category_id,'columnCategory'=>$columnCategory));
		if($is_ajax){
			$this->getElement()->removeDecorator('Container');
		}else{
			// Do not render if nothing to show
			if( $paginator->getTotalItemCount() <= 0 ) {			
			}
		}    
  }
}