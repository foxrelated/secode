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
class List_Widget_MostcommentedListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DON'T RENDER IF DISABLE COMMENT
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('list.checkcomment.widgets', 1)) {
      return $this->setNoRender();
    }

    // GET LIST LIST FOR MOST COMMENTED
    $this->view->listings = Engine_Api::_()->getDbTable('listings', 'list')->getListing('Most Commented');

    //DON'T RENDER IF RESULTS IS ZERO
    if (count($this->view->listings) <= 0) {
      return $this->setNoRender();
    }
  }
}