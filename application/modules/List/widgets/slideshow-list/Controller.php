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
class List_Widget_SlideshowListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //FETCH FEATURED LISTINGS
    $this->view->show_slideshow_object = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Featured Slideshow');
		$this->view->list_featured = $list_featured = Zend_Registry::get('list_featuredslide');

    //RESULTS COUNT
    $this->view->num_of_slideshow = Count($this->view->show_slideshow_object);
    if($this->view->num_of_slideshow <= 0) {
      return $this->setNoRender();
    } 

		if(empty($list_featured)) {
			return $this->setNoRender();
		}

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }
}