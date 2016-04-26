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
class List_Widget_InfoListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
		//DONT RENDER IF SUBJECT IS NOT SET
		$list_info = Zend_Registry::get('list_info');
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }
 
    $this->view->expiry_setting = Engine_Api::_()->list()->expirySettings();

    //GET SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

		//GET CATEGORY TABLE
		$this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'list');

    //GET CATEGORIES NAME
		$this->view->category_name = $this->view->subcategory_name = $this->view->subsubcategory_name = '';

		if(!empty($list->category_id)) {
			if($this->view->tableCategory->getCategory($list->category_id))
			$this->view->category_name = $this->view->tableCategory->getCategory($list->category_id)->category_name;

			if(!empty($list->subcategory_id)) {
				if($this->view->tableCategory->getCategory($list->subcategory_id))
				$this->view->subcategory_name = $this->view->tableCategory->getCategory($list->subcategory_id)->category_name;

				if(!empty($list->subsubcategory_id)) {
					if($this->view->tableCategory->getCategory($list->subsubcategory_id))
					$this->view->subsubcategory_name = $this->view->tableCategory->getCategory($list->subsubcategory_id)->category_name;
				}
			}
		}

    //GET LISTING TAGS
    $this->view->listTags = $list->tags()->getTagMaps();

		//GET OTHER DETAILS
		$this->view->list_description = Zend_Registry::get('list_descriptions');
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($list);
		if(empty($list_info)){ return $this->setNoRender(); }
  }
}