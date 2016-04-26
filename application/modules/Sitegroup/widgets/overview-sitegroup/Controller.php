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
class Sitegroup_Widget_OverviewSitegroupController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitegroup_group') {
    	$this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
    }
    else {
      $this->view->sitegroup = $sitegroup = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //START MANAGE-ADMIN CHECK   
    $this->view->can_edit_overview = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'overview');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $this->view->can_edit = $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

    if (empty($this->view->can_edit) && empty($sitegroup->overview)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//SEND OTHER DETAIL TO TPL
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $this->view->widgets = Engine_Api::_()->sitegroup()->getwidget($layout, $sitegroup->group_id);
    $this->view->content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.overview-sitegroup', $sitegroup->group_id, $layout);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
    $this->view->showtoptitle = Engine_Api::_()->sitegroup()->showtoptitle($layout, $sitegroup->group_id);
  }
}

?>