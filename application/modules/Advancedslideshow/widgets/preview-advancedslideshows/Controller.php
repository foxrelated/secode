<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 6590 2010-08-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Widget_PreviewAdvancedslideshowsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GENERATE SEARCH FORM
    //$this->view->form = $form = new Advancedslideshow_Form_Search();

    if (isset($_POST['submit'])) {
      $this->view->type = $_POST['slideshow_type'];
      $this->view->thumb = $_POST['slideshow_thumb'];
    } else {
      $this->view->type = Engine_Api::_()->getApi('settings', 'core')->advancedslideshow_type;
      $this->view->thumb = Engine_Api::_()->getApi('settings', 'core')->advancedslideshow_thumb;
    }
  }

}
?>