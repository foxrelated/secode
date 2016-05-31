<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchentry.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Controller_Action_Helper_Searchentry extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {

    //GET NAME OF MODULE, CONTROLLER AND ACTION
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $action = $front->getRequest()->getActionName();
    $controllerName = $front->getRequest()->getControllerName();

    $modulesArray = array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore', 'sitestoreproduct', 'siteevent', 'sitereview', 'list', 'recipe', 'feedback', 'sitefaq', 'document', 'sitetutorial', 'blog', 'classified', 'poll', 'user', 'sitepagedocument', 'sitepageevent', 'sitepageoffer', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessoffer', 'sitegroupdocument', 'sitegroupevent', 'sitegroupoffer', 'sitestoredocument', 'sitestoreoffer', 'sitevideo');

    if ((in_array($module, $modulesArray) && $action == 'view') || ($module == 'event' && $controllerName == 'index' && $action == 'index') || ($module == 'user' && $action == 'index' && $controllerName == 'index'))
      Engine_Api::_()->siteadvsearch()->getSearchableContent();
  }

}