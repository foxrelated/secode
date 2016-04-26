<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Social Publisher
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @author     trunglt
 */
class Socialpublisher_AdminManageController extends Core_Controller_Action_Admin
{

	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialpublisher_admin_main', array(), 'socialpublisher_admin_main_manage');
		$this -> view -> types = $types = Engine_Api::_() -> socialpublisher() -> getEnabledTypes();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$params = $this -> getRequest() -> getPost();
		foreach ($types as $type)
		{
			$formatted_type = 'socialpublisher.' . str_replace(array(
				'advalbum',
				'_'
			), '', $type);
			Engine_Api::_() -> getApi('settings', 'core') -> setSetting($formatted_type, Zend_Json::encode($params['values'][$type]));
		}
		$this -> view -> is_post = true;
	}

}
