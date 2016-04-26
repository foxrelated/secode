<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/

class List_Widget_CategoriesListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET LIST API
		$this->view->list_api = $list_api = Engine_Api::_()->list();

		//SEARCH FORM WIDGET IS EXIST OR NOT
		$this->view->search_form_widget = $list_api->existWidget('browse_categories', $this->view->identity);

		//GET LISTING CATEGORY TABLE
    $this->view->categoryTable = $tableCategory = Engine_Api::_()->getDbTable('categories', 'list');

		//GET STORAGE API
		$this->view->storage = Engine_Api::_()->storage();

		//GET TRUNCATION LIMIT
		$this->view->catTruncLimit = 37;
		$this->view->subCatTruncLimit = $this->view->subsubCatTruncLimit = 21; 

    $categories = array();
    $category_info = $tableCategory->getCategories(null);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $category_info2 = $tableCategory->getAllCategories($value['category_id'], 'subcategory_id', 0, 'subcategory_id', 0, 0, null, null);
      foreach($category_info2 as $subresults) {
        $treesubarray = array();
        $subcategory_info2 = $tableCategory->getAllCategories($subresults['category_id'], 'subcategory_id', 0, 'subcategory_id', 0, 0, null, null);
        $treesubarrays[$subresults->category_id] = array();
        foreach($subcategory_info2 as $subvalues) {
           $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
            'tree_sub_cat_name' => $subvalues->category_name,
            'order' => $subvalues->cat_order,
						'file_id' => $subvalues->file_id,
            );
        }

        $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'order' => $subresults->cat_order,
						'file_id' => $subresults->file_id,
						);
        $sub_cat_array[] = $tmp_array;
      }

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'sub_categories' => $sub_cat_array,
					'file_id' => $value->file_id,
					);
    }
    
    $this->view->categories = $categories;
    $this->view->subcategorys = 0;
    $this->view->category = 0;
    $this->view->subsubcategorys = 0;

		$request = Zend_Controller_Front::getInstance()->getRequest();

    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);

    if($request->getParam('category') ) {
	    	
	    $categoryidtemp = $request->getParam('category');	
	    $subcategoryidtemp = $request->getParam('subcategory');
      if($request->getParam('subsubcategory')) {
        $subsubcategoryidtemp = $request->getParam('subsubcategory');
      } else {
        $subsubcategoryidtemp = $request->getParam('subsubcategory_id');
      }
	    if(!empty($categoryidtemp)) {
	    	$this->view->category = $categoryidtemp; 
	    	$this->view->subcategorys = $subcategoryidtemp;
        $this->view->subsubcategorys = $subsubcategoryidtemp;
	    }
    } elseif($request->getParam('category_id')) {
	    $categoryid = $request->getParam('category_id');
	    $subcategoryid = $request->getParam('subcategory_id');
      $subsubcategoryid = $request->getParam('subsubcategory_id');
	
	    if (!empty($categoryid)) {
	      $_GET['category_id'] = $this->view->category = $categoryid;
	      $_GET['categoryname'] = $categoryname;
	    } 
	    
	    if (!empty($subcategoryid)) {  
	      $_GET['subcategory_id'] = $this->view->subcategorys = $subcategoryid;
	      $_GET['subcategoryname'] = $subcategoryname;
	    }
      
      if (!empty($subsubcategoryid)) {
	      $_GET['subsubcategory_id'] = $this->view->subsubcategorys = $subsubcategoryid;
	      $_GET['subsubcategoryname'] = $subsubcategoryname;
	    }

	    if (!empty($_GET)) {
	      if (!empty($_GET['subcategory_id'])) {
	        $this->view->subcategorys = $_GET['subcategory_id'];
	      }
	      if (!empty($_GET['category_id'])) {
	        $this->view->category = $_GET['category_id'];
	      }
        if (!empty($_GET['subsubcategory_id'])) {
	        $this->view->subsubcategorys = $_GET['subsubcategory_id'];
	      }
	    }
    }
    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = $this->view->category = 0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = 0;
			$_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }
       
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }

}