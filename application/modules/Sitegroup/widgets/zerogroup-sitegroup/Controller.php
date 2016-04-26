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
class Sitegroup_Widget_ZerogroupSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitegroup_group', $viewer, 'create');
    $values['type'] = 'browse_home_zero';
    $values['limit'] = 1;
    $this->view->assign($values);
    
 
    // GET SITEGROUP
    $sitegroup = Engine_Api::_()->sitegroup()->getSitegroupsPaginator($values);

    if ((count($sitegroup) > 0)) {
      return $this->setNoRender();
    }
  }

}

?>