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
class List_Widget_MostratedListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//DONT RENDER IF RATING IS NOT ALLOWED
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1)) {  
			return $this->setNoRender(); 
		}

    //FETCH LISTINGS
    $this->view->listings = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Most Rated');

    //DONT RENDER IF LIST COUNT IS ZERO
    if (count($this->view->listings) <= 0) {  
			return $this->setNoRender();  
		}
  }
}