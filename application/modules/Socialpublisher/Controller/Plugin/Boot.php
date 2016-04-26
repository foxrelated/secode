<?php

class Socialpublisher_Controller_Plugin_Boot extends Zend_Controller_Plugin_Abstract
{

	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
        $publisherNS = new Zend_Session_Namespace('social_publisher_resource');
        $module = $request -> getModuleName();
        $controller = $request -> getControllerName();
        $action = $request -> getActionName();
        $key = $module . '-' . $controller . '-' . $action;
        if(!isset($_SESSION['social_publisher_resource']) || !$_SESSION['social_publisher_resource'] || in_array($key, array('core-widget-index')))
        {
            return;
        }
		
		if(strpos($key, '-place-order'))
		{
			$package_id = $request -> getParam('packageId', 0);
			$type = $module.'_package';
			if(Engine_Api::_() -> hasItemType($type) && $package_id)
			{
				$package = Engine_Api::_() -> getItem($type, $package_id);
				if($package && $package -> price <= 0)
				{
					return;
				}
			}
			else if($module == 'ynlistings')
			{
				$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$publish_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'publish_fee');
			    if ($publish_fee== null) {
					$row = $permissionsTable->fetchRow($permissionsTable->select()
					->where('level_id = ?', $viewer->level_id)
					->where('type = ?', 'ynlistings_listing')
					->where('name = ?', 'publish_fee'));
					if ($row) {
					$publish_fee= $row->value;
					}
				}
				
				$feature_fee = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'ynlistings_listing', 'feature_fee');
			    if ($feature_fee== null) {
					$row = $permissionsTable->fetchRow($permissionsTable->select()
					->where('level_id = ?', $viewer->level_id)
					->where('type = ?', 'ynlistings_listing')
					->where('name = ?', 'feature_fee'));
					if ($row) {
					$feature_fee= $row->value;
					}
				}
				if($publish_fee == 0 && $feature_fee == 0)
				{
					return;
				}
			}
		}
		
		if ($request -> getParam('format', '') == 'smoothbox' && $key == 'socialpublisher-index-share')
		{
			unset($_SESSION['social_publisher_resource']);
			return;
		}
		else if($request -> getParam('format'))
		{
			return;
		}
		$view = Zend_Registry::get('Zend_View');
		$keyType = 'socialpublisher_resource_type';
		$keyId = 'socialpublisher_resource_id';

		$resource_id = $publisherNS -> $keyId;
		$resource_type = $publisherNS -> $keyType;

		unset($_SESSION['social_publisher_resource']);

		if (!empty($resource_id) && !empty($resource_type))
		{
			$resource = Engine_Api::_() -> getItem($resource_type, $resource_id);
			if (!$resource)
			{
				return;
			}
			$api = Engine_Api::_() -> socialpublisher();
			$viewer = Engine_Api::_() -> user() -> getViewer();
			
			$enable_settings = $api -> getTypeSettings($resource_type);
			$module_settings = $api -> getUserTypeSettings($viewer -> getIdentity(), $resource_type);

			$is_popup = ($enable_settings['active'] && count($module_settings['providers']));
			// item privacy satisty
			if ($is_popup)
			{
				switch ($module_settings['option'])
				{
					case Socialpublisher_Plugin_Constants::OPTION_ASK :
						// open popup
						$params = array(
							'action' => 'share',
							'resource_id' => $resource_id,
							'resource_type' => $resource_type,
						);
						$url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'socialpublisher_general');
						$view -> headScript() -> appendScript("
            				window.addEvent('domready',function(e) {
            					Smoothbox.open('$url');
            				});
            			");
						break;
					case Socialpublisher_Plugin_Constants::OPTION_AUTO :
						if (!empty($module_settings['providers']))
						{
							$providers = $module_settings['providers'];
							foreach ($providers as $provider)
							{
								$values = array(
									'service' => $provider,
									'user_id' => $viewer -> getIdentity()
								);
								$obj = Engine_Api::_() -> socialbridge() -> getInstance($provider);
								$token = $obj -> getToken($values);
								$default_status = $api -> getDefaultStatus(array(
									'viewer' => Engine_Api::_() -> user() -> getViewer(),
									'resource' => $resource,
									'title' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core.general.site.title', $view -> translate('SocialEngine Site'))
								));
								$photo_url = $api -> getPhotoUrl($resource);
								$post_data = $api -> getPostData($provider, $resource, $default_status, $photo_url);
								if (!empty($_SESSION['socialbridge_session'][$provider]))
								{
									try
									{
										$obj -> postActivity($post_data);
									}
									catch(Exception $e)
									{
										//echo $e->getMessage();
									}
								}
								else
								{
									$_SESSION['socialbridge_session'][$provider]['access_token'] = $token -> access_token;
									$_SESSION['socialbridge_session'][$provider]['secret_token'] = $token -> secret_token;
									$_SESSION['socialbridge_session'][$provider]['owner_id'] = $token -> uid;
									try
									{
										$obj -> postActivity($post_data);
									}
									catch(Exception $e)
									{
										//echo $e->getMessage();
									}
								}
							}
						}
						break;
					case Socialpublisher_Plugin_Constants::OPTION_NOT_ASK :
						break;
					default :
						break;
				}
			}
		}

	}

}
