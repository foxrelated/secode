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
class List_Widget_RandomListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET LISTINGS
    $this->view->listings = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Random List');
    $list_random = Zend_Registry::get('list_random');

		//DONT RENDER IF LISTING COUNT IS ZERO
    if (count($this->view->listings) <= 0 || empty($list_random)) {
      return $this->setNoRender();
    }
  }

}