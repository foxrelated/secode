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
 
class List_Widget_ArchivesListController extends Engine_Content_Widget_Abstract {

  public function indexAction()
  { 
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}

		//GET SUBJECT
		$list = Engine_Api::_()->core()->getSubject();
		$owner = $list->getOwner();

    //SHOW ARCHIVES
		$this->view->archive_list = Engine_Api::_()->getDbtable('listings', 'list')->getArchiveList($owner);

		if( Count($this->view->archive_list) <= 0 ) {
			return $this->setNoRender();
		}
	}

}