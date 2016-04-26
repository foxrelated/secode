<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_GroupProfileBreadcrumbController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_group')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    //GET CATEGORY TABLE
    $this->view->tableCategory = Engine_Api::_()->getDbTable('categories', 'sitegroup');
    if (!empty($sitegroup->category_id)) {
      $this->view->category_name = $this->view->tableCategory->getCategory($sitegroup->category_id)->category_name;

      if (!empty($sitegroup->subcategory_id)) {
        $this->view->subcategory_name = $this->view->tableCategory->getCategory($sitegroup->subcategory_id)->category_name;

        if (!empty($sitegroup->subsubcategory_id)) {
          $this->view->subsubcategory_name = $this->view->tableCategory->getCategory($sitegroup->subsubcategory_id)->category_name;
        }
      }
    }
  }

}