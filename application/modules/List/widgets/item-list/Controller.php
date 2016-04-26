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
class List_Widget_ItemListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET ITEM OF THE DAY
    $this->view->dayitem = Engine_Api::_()->getDbtable('listings', 'list')->getItemOfDay();

    //DONT RENDER IF ITEM OF THE DAY IS NOT FOUND
    if (count($this->view->dayitem) <= 0) {
      return $this->setNoRender();
    }
  }

}