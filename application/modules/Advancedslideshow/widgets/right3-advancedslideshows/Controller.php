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
class Advancedslideshow_Widget_Right3AdvancedslideshowsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //GET PAGE ID, SLIDESHOW TYPE AND SLIDESHOW POSITION
    $page_id = -1;
    $slideType = Zend_Registry::isRegistered('advancedslideshow_right') ? Zend_Registry::get('advancedslideshow_right') : null;
    $slide_position = 'right_column3';

    include APPLICATION_PATH . '/application/modules/Advancedslideshow/settings/widgetController.php';
    
            $getSlideshowType = $this->view->type;
        if (!empty($advancedslideshow_id) && ($getSlideshowType == 'noob')) {
    
      $this->view->getContentArray = Engine_Api::_()->advancedslideshow()->getNoobSlidesArray($advancedslideshow);
    }
  }

}
?>