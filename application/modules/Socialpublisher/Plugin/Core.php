<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Socialpublisher
 * @author     YouNet Company
 */

class Socialpublisher_Plugin_Core extends Core_Api_Abstract
{
	public function onItemCreateAfter($event)
	{
		$keyType = 'socialpublisher_resource_type';
		$keyId = 'socialpublisher_resource_id';
		$keyModulename = 'socialpubilsher_module_name';

		$item = $event -> getPayload();

		// get item type
		$item_type = $item -> getType();
		
		$publisherNS = new Zend_Session_Namespace('social_publisher_resource');
		$resource_type = $publisherNS -> $keyType;

		$types = Engine_Api::_() -> socialpublisher() -> getEnabledTypes(array('active' => true));
		// fixed for activity action
		unset($types[0]);
		if ((in_array($item_type, $types) || ($item_type == 'activity_action' && ($item -> type == 'status' || $item -> type == 'post_self'))) && $resource_type != 'contest')
		{
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			$controller_name = $request -> getControllerName();
			$action_name = $request -> getActionName();
			if ($item_type == 'blog' && $controller_name == 'import' && $action_name == 'import')
			{
				// do not support imported blogs
				return;
			}
			$api = Engine_Api::_() -> socialpublisher();
			$temp = array();
			$providers = array(
				'facebook',
				'twitter',
				'linkedin'
			);
			foreach ($providers as $provider)
			{
				if ($api -> isValidProvider($provider))
				{
					$temp[] = $provider;
				}
			}
			// return if there is no valid provider
			if (count($temp) == 0)
			{
				return;
			}
			$publisherNS = new Zend_Session_Namespace('social_publisher_resource');
			$publisherNS -> $keyType = $item -> getType();
			$publisherNS -> $keyId = $item -> getIdentity();
		}
	}

}
