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
class Sitegroup_Widget_InfoSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    $sitegroup_info = Zend_Registry::isRegistered('sitegroup_info') ? Zend_Registry::get('sitegroup_info') : null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    $this->view->isManageAdmin = Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup);
    
		//SEND DATA TO TPL
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitegroup()->getwidget($layout, $sitegroup->group_id);
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.info-sitegroup', $sitegroup->group_id, $layout);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
    $this->view->showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitegroup');
    $this->view->category_name = $this->view->subcategory_name == $this->view->subsubcategory_name = '';
    if($sitegroup->category_id) {
        $categoriesNmae = $tableCategories->getCategory($sitegroup->category_id);
        if (!empty($categoriesNmae->category_name)) {
          $this->view->category_name = $categoriesNmae->category_name;
        }
        
        if($sitegroup->subcategory_id) {
            $subcategory_name = $tableCategories->getCategory($sitegroup->subcategory_id);
            if (!empty($subcategory_name->category_name)) {
              $this->view->subcategory_name = $subcategory_name->category_name;
            }
            
            //GET SUB-SUB-CATEGORY
            if($sitegroup->subsubcategory_id) {
                $subsubcategory_name = $tableCategories->getCategory($sitegroup->subsubcategory_id);
                if (!empty($subsubcategory_name->category_name)) {
                  $this->view->subsubcategory_name = $subsubcategory_name->category_name;
                }
            }
        }
    }
    
    //GET TAGS
    $this->view->sitegroupTags = $sitegroup->tags()->getTagMaps();

		//CUSTOM FIELD WORK
    $this->view->sitegroup_description = Zend_Registry::isRegistered('sitegroup_descriptions') ? Zend_Registry::get('sitegroup_descriptions') : null;
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitegroup/View/Helper', 'Sitegroup_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitegroup);
    if (empty($sitegroup_info)) {
      return $this->setNoRender();
    }
  }
}

?>