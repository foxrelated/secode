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
class List_Widget_MostdiscussionListController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {
		//FETCH RESULTS
  	$this->view->listings = Engine_Api::_()->getDbTable('listings', 'list')->getDiscussedListing();

		//DONT RENDER IF RESULTS IS EMPTY
    if (count($this->view->listings) <= 0) {
			return $this->setNoRender(); 
		}
  }
}