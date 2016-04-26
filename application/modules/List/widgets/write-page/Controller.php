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
class List_Widget_WritePageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
  
		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

    //GET VIEWER ID
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET SUBJCT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

     //GET PREVIOUS TEXT
    $this->view->userListingstext = Engine_Api::_()->getDbtable('writes','list')->writeContent($list->listing_id);    
  }
}