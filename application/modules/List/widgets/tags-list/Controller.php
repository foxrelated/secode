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
class List_Widget_TagsListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('list_listing')) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->list = $list = Engine_Api::_()->core()->getSubject('list_listing');

    //GET USER TAGS
    $this->view->userTags = $list->tags()->getTagsByTagger($list->getOwner());

		if(!Count($this->view->userTags)) {
			return $this->setNoRender();
		}
  }
}