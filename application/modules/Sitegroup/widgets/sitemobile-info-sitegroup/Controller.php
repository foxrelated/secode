<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitegroup_Widget_SitemobileInfoSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');

		//GET CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->category_id);
    if (!empty($row->category_name)) {
      $this->view->category_name = $row->category_name;
    }

		//GET SUB-CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subcategory_name = $row->category_name;
    }     

    //GET SUB-SUB-CATEGORY
    $row = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategory($sitegroup->subsubcategory_id);
    if (!empty($row->category_name)) {
      $this->view->subsubcategory_name = $row->category_name;
    }
    
    //GET TAGS
    $this->view->sitegroupTags = $sitegroup->tags()->getTagMaps();

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitegroup/View/Helper', 'Sitegroup_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitegroup);
  }
}