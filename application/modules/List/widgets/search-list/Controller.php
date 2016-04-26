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
class List_Widget_SearchListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $list_searching = Zend_Registry::get('list_searching');

    $listtable = Engine_Api::_()->getDbtable('listings', 'list');
    $listName = $listtable->info('name');

		$categoryTable = Engine_Api::_()->getDbTable('categories', 'list');
    
    $this->view->getCategoriesCount = $categoryTable->getCategoriesCount();
    if($this->view->getCategoriesCount == 0) {
      $this->view->getDefaultProfileType = Engine_Api::_()->getDbTable('metas', 'list')->getDefaultProfileType();
    }

    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();

    //FORM CREATION
    $this->view->form = $form = new List_Form_Search(array('type' => 'list_listing'));

    $list_post = Zend_Registry::isRegistered('list_post') ? Zend_Registry::get('list_post') : null;
    if (!empty($list_post)) {
      $this->view->list_post = $list_post;
    }

		$request = Zend_Controller_Front::getInstance()->getRequest();
    $category = $request->getParam('category_id', null);
    $subcategory = $request->getParam('subcategory_id', null);
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategory = $request->getParam('subsubcategory_id', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);
    $cattemp = $request->getParam('category', null);
		$subcattemp = $request->getParam('subcategory', null);
    $subsubcattemp = $request->getParam('subsubcategory', null);

    if (!empty($cattemp)) {
      $this->view->category_id = $_GET['category'] = $request->getParam('category');
      $row = $categoryTable->getCategory($this->view->category_id);
      if (!empty($row->category_name)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
      }

      $categorynametemp = $request->getParam('categoryname', null);
      $subcategorynametemp = $request->getParam('subcategoryname', null);
      if (!empty($categorynametemp)) {
        $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
      }
      if (!empty($subcategorynametemp)) {
        $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
      }
    } else {
      if ($categoryname)
        $this->view->category_name = $_GET['categoryname'] = $categoryname;
      if ($category) {
        $this->view->category_id = $_GET['category_id'] = $category;
        $row = $categoryTable->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }
    }

    if (!empty($subcattemp)) {
      $this->view->subcategory_id = $_GET['subcategory_id'] = $request->getParam('subcategory');
      $row = $categoryTable->getCategory($this->view->subcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subcategory_name = $row->category_name;
      }
    } else {
      if ($subcategoryname)
        $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;
      if ($subcategory) {
        $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
        $row = $categoryTable->getCategory($this->view->subcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
        }
      }
    }

    if (!empty($subsubcattemp)) {
      $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $request->getParam('subsubcategory');
      $row = $categoryTable->getCategory($this->view->subsubcategory_id);
      if (!empty($row->category_name)) {
        $this->view->subsubcategory_name = $row->category_name;
      }
    } else {
      if ($subsubcategoryname)
        $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

      if ($subsubcategory) {
        $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
        $row = $categoryTable->getCategory($this->view->subsubcategory_id);
        if (!empty($row->category_name)) {
          $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
        }
      }
    }

    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = $categoryname;
      $_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }

    if (!isset($_POST['orderby']) || empty($_POST)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.browseorder', 1);
      switch ($order) {
        case "1":
          $form->orderby->setValue("creation_date");
          break;
        case "2":
          $form->orderby->setValue("view_count");
          break;
        case "3":
          $form->orderby->setValue("title");
          break;
      }
    }

    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.status.show', 1);
    if ($stusShow == 0) {
      $form->removeElement('closed');
    }
    
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
			$tag_id = $_GET['tag_id'];
      $page = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
      }

			$_GET['tag'] = $tag;
			$_GET['tag_id'] = $tag_id;	
      $_GET['page'] = $page;
    }

		$orderBy = $request->getParam('orderby', null);

		if(!empty($orderBy)) {
			$_GET['orderby'] = $orderBy;
		}

    if (!empty($_GET))
      $form->populate($_GET);

    if (!$viewer) {
      $form->removeElement('show');
    }

    if (empty($list_searching)) {
      return $this->setNoRender();
    }

		//SHOW PROFILE FIELDS ON DOME READY
    $category_search = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('list', 'category_id');
    if (!empty($category_search) && !empty($category_search->display) && !empty($_GET['category'])) {
			//GET PROFILE MAPPING ID
			$this->view->profileType = Engine_Api::_()->getDbTable('profilemaps', 'list')->getProfileType($_GET['category']);
		}
  }

}