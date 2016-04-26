<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Widget_Middle1AdvancedslideshowsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //GET PAGE ID, SLIDESHOW TYPE AND SLIDESHOW POSITION
    $page_id = Engine_Api::_()->advancedslideshow()->getPageId($this->view->identity);
    $slideType = Zend_Registry::isRegistered('advancedslideshow_middle') ? Zend_Registry::get('advancedslideshow_middle') : null;
    $slide_position = 'middle_column1';

    include APPLICATION_PATH . '/application/modules/Advancedslideshow/settings/widgetController.php';
        $getSlideshowType = $this->view->type;
        if (!empty($advancedslideshow_id) && ($getSlideshowType == 'noob')) {
      $this->view->getContentArray = Engine_Api::_()->advancedslideshow()->getNoobSlidesArray($advancedslideshow);
    }
  }

}
?>