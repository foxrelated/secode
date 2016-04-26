<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_ThumbphotoSitegroupController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE THUMB PHOTO.
  public function indexAction() {
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    $this->view->showTitle = $this->_getParam('showTitle', 1);
    //GET SITEGROUP SUBJECT
    $this->view->sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
  }

}

?>