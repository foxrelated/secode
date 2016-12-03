<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestorereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestorereview_Widget_SearchSitestorereviewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {


    $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4","4" => "5"));

    $sitestore_searching = Zend_Registry::isRegistered('sitestore_searching') ? Zend_Registry::get('sitestore_searching') : null;

    $sitestoretable = Engine_Api::_()->getDbtable('stores', 'sitestore');
    $sitestoreName = $sitestoretable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
  
    //FORM CREATION
    $this->view->form = $form = new Sitestorereview_Form_Searchwidget(array('type' => 'sitestore_store'));
    $populateValue = 0;
    $commentedreview = Zend_Controller_Front::getInstance()->getRequest()->getParam('commentedreview', null);
    if(!empty($commentedreview)) {
      $populateValue = 1;
			$form->orderby_browse->setValue("comment_count");
    }
    $viewedreview = Zend_Controller_Front::getInstance()->getRequest()->getParam('viewedreview', null);
    if(!empty($viewedreview)) {
      $populateValue = 1;
			$form->orderby_browse->setValue("view_count");
    }
    $likedreview = Zend_Controller_Front::getInstance()->getRequest()->getParam('likedreview', null);
    if(!empty($likedreview)) {
      $populateValue = 1;
			$form->orderby_browse->setValue("like_count");
    }    

    $sitestore_post = Zend_Registry::isRegistered('sitestore_post') ? Zend_Registry::get('sitestore_post') : null;
    if (!empty($sitestore_post)) {
      $this->view->sitestore_post = $sitestore_post;
    }
    
    $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);
    $cattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);

    if(!empty($cattemp)) 
    {
    	$this->view->category_id  = $_GET['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category');
    	$row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
	    if (!empty($row->category_name)) {
	      $categoryname = $this->view->category_name  = $_GET['categoryname'] = $row->category_name;
	    }
	    
	    $categorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
	    $subcategorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
	    if (!empty($categorynametemp)) {
		    $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
	    }
			if (!empty($subcategorynametemp)) {
		    $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
	    }
	  } else {
      if($categoryname)
	    $this->view->category_name = $_GET['categoryname'] =  $categoryname;      
	    if($category) {
	      $this->view->category_id = $_GET['category_id'] =  $category;
        $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name  = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }	    
    }
    
    $subcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);

    if(!empty($subcattemp)) 
    {
    	$this->view->subcategory_id = $_GET['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subcategory_name = $row->category_name;
	    }
    } else {
        if($subcategoryname)
				  $this->view->subcategory_name = $_GET['subcategoryname'] =  $subcategoryname;        
        if($subcategory) {
          $this->view->subcategory_id = $_GET['subcategory_id'] =  $subcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subcategory_name  = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
          }
       }		   
    }

    $subsubcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);

    if(!empty($subsubcattemp))
    {
    	$this->view->subsubcategory_id = $_GET['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subsubcategory_name = $row->category_name;
	    }
    } else {
        if($subsubcategoryname)
				  $this->view->subsubcategory_name = $_GET['subsubcategoryname'] =  $subsubcategoryname;

        if($subsubcategory) {
          $this->view->subsubcategory_id = $_GET['subsubcategory_id'] =  $subsubcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategory($this->view->subsubcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subsubcategory_name  = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
          }
       }
    }

    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id =  0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }
    
    if ((!isset($_POST['orderby_browse']) || empty($_POST)) && empty($populateValue)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestorereview.order', 1);
      if($order == 1) {
				$form->orderby_browse->setValue("creation_date");
      }
    }

    if (!empty($_GET))
      $form->populate($_GET);  
      
    if (empty($sitestore_searching)) {
      return $this->setNoRender();
    }
  }
}

?>