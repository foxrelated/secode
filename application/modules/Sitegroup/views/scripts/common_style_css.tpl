<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: common_style_css.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage'))) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business_group.css');
	}
	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitebusiness/externals/styles/common_style_business_group.css');
	} 
	elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_group.css');

	}
  else {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup.css');
  }
?>