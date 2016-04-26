<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupalbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupalbum_MobiController extends Core_Controller_Action_Standard {

  public function init() {

    //HERE WE CHECKING THE SITEGROUP ALBUM IS ENABLED OR NOT
    $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
    if (!$sitegroupalbumEnabled) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
            ->addActionContext('rate', 'json')
            ->addActionContext('validation', 'html')
            ->initContext();
    $group_id = $this->_getParam('group_id', $this->_getParam('id', null));

    //PACKAGE BASE PRIYACY START
    if (!empty($group_id)) {
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      if ($sitegroup) {
        Engine_Api::_()->core()->setSubject($sitegroup);      
        if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
          if (!Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupalbum")) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        } else {
          $isGroupOwnerAllow = Engine_Api::_()->sitegroup()->isGroupOwnerAllow($sitegroup, 'spcreate');
          if (empty($isGroupOwnerAllow)) {
            return $this->_forward('requireauth', 'error', 'core');
          }
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if (Engine_Api::_()->core()->hasSubject() != null) {
        $photo = Engine_Api::_()->core()->getSubject();
        $album = $photo->getCollection();
        $group_id = $album->group_id;
      }
    }
  }

  //ACTION FOR VIEW THE VIDEO
  public function viewAction() {
    
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>