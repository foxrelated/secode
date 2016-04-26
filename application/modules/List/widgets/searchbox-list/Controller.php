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
class List_Widget_SearchboxListController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    //PREPARE FORM
    $this->view->form = new List_Form_Searchbox();

	}
}