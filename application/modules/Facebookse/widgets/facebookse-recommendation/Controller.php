<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Facebookse_Widget_FacebookseRecommendationController extends Engine_Content_Widget_Abstract
{
  
  public function indexAction()
  {
		//CHECK IF Facebookse MODULE IS ENABLE OR NOT.WE ARE DOING THIS FOR INTEGRATING FACEBOOK LIKE TO SITE LIKE.
		$enable_facebooksemodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse');
		$facebookse_recomendations = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.recomendation.type');

    $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
		if (!empty($enable_fboldversion)) {
			$socialdnamodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('socialdna');
			$socialdnaversion = $socialdnamodule->version;
			if ($socialdnaversion >= '4.1.1') {
				$enable_fboldversion = 0;
			}
		}
    $this->view->enable_fboldversion = $enable_fboldversion;
    if (empty($enable_facebooksemodule)) {
			return $this->setNoRender();
    }
		$facebookse_siteurl = Zend_Registry::get('facebookse_url');
		
		if( empty($facebookse_recomendations) ) {
			return $this->setNoRender();
		}
		$base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); 
		$siteurl = $_SERVER['HTTP_HOST'] . $base_url ; 
		//FETCHING THE CONTENT FOR THIS TYPE OF WIDGET.
		$permissionTable_widgetsetting = Engine_Api::_()->getDbtable('widgetsettings', 'facebookse');
		$select = $permissionTable_widgetsetting->select()
					->where('widget_type=?', 'recommendation');
		$permissionTable_widgetsetting_array = $permissionTable_widgetsetting->fetchAll($select)->toarray();
		$this->view->siteurl = $siteurl;
		$this->view->recomendations = Zend_Registry::get('facebookse_recomendations');
		$this->view->permissionTable_widgetsetting_array = $permissionTable_widgetsetting_array[0];
		if ( empty($facebookse_siteurl) ) {
			return $this->setNoRender();
		}
	}
}