<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_SearchboxSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    // Prepare form
    $this->view->form = $form = new Sitegroup_Form_Searchbox();
    $content_id =  $this->view->identity;
    $widgetname = 'sitegroup.searchbox-sitegroup';
    $filtercategory_id = Engine_Api::_()->sitegroup()->getSitegroupCategoryid($content_id,$widgetname);
    if(!empty($filtercategory_id)) {
      $this->view->category_id = $filtercategory_id;
    }
    else {
      $this->view->category_id = 0;
    }
	}
}

?>