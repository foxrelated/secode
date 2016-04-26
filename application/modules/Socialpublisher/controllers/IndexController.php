<?php

class Socialpublisher_IndexController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
	}

	public function settingsAction()
	{
		// Render
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$settings = Engine_Api::_() -> socialpublisher();

		$table = Engine_Api::_() -> getDbtable('settings', 'socialpublisher');
		$select = $table -> select();
		$this -> view -> types = $types = $settings -> getEnabledTypes(array('active' => 1));

		// If not post
		if ($this -> getRequest() -> isPost())
		{
			$params = $this -> getRequest() -> getPost();
			$table = Engine_Api::_() -> getDbTable('settings', 'socialpublisher');
			foreach ($params['values'] as $type => $values)
			{
				$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> where('type = ?', str_replace('advalbum_', '', $type));
				$row = $table -> fetchRow($select);
				if (!is_object($row))
				{
					$row = $table -> createRow();
					$row -> user_id = $viewer -> getIdentity();
					$row -> type = str_replace('advalbum_', '', $type);
				}
				$row -> option = $values['option'];
				
				$row -> privacy = 7;
				$row -> providers = Zend_Json::encode($values['providers']);
				$row -> save();
			}
			$this -> view -> is_post = true;
		}
	}

	public function shareAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$params = $this -> _getAllParams();
		$resource_id = $params['resource_id'];
		$resource_type = $params['resource_type'];
		if (!empty($resource_id) && !empty($resource_type))
		{
			$resource = Engine_Api::_() -> getItem($resource_type, $resource_id);
			if (!empty($resource))
			{
				Engine_Api::_() -> core() -> setSubject($resource);
			}
		}
		if (!$this -> _helper -> requireSubject() -> isValid($resource_type))
		{
			return;
		}
		
		$api = Engine_Api::_() -> socialpublisher();

		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> module_settings = $module_settings = $api -> getUserTypeSettings($viewer -> getIdentity(), $resource_type);
		$this -> view -> resource_type = $resource_type;
		$this -> view -> userTypeSettings = $userTypeSettings = $api -> getUserTypeSettings($viewer -> getIdentity(), $resource_type);
		$this -> view -> default_status = $api -> getDefaultStatus(array(
			'viewer' => $viewer,
			'resource' => $resource,
			'title' => $this -> view -> layout() -> siteinfo['title']
		));
		$req = $this -> getRequest();
		$front = Zend_Controller_Front::getInstance();
		$shareLink = $api -> getPostLink($resource);
		$title = $api -> getPostTitle($resource);
		$des = $api -> getPostDescription($resource);
		$this -> view -> shareLink = $shareLink;
		$this -> view -> title = $title;
		$this -> view -> des = $des;
		$photo_url = $api -> getPhotoUrl($resource);
		$this -> view -> photo_url = $photo_url;
		$this -> view -> callbackUrl = $req -> getScheme() . '://' . $req -> getHttpHost() . $front -> getRouter() -> assemble(array('action' => 'share'), 'socialpublisher_general', true);
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		// save new settings
		$table = Engine_Api::_() -> getDbTable('settings', 'socialpublisher');
		$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity()) -> where('type = ?', str_replace('advalbum_', '', $resource_type));
		$row = $table -> fetchRow($select);
		if (!$row)
		{
			$row = $table -> createRow();
			$row -> type = $resource_type;
			$row -> privacy = 7;
			$row -> user_id = $viewer -> getIdentity();
		}
		$option = Socialpublisher_Plugin_Constants::OPTION_ASK;
		if(isset($params['publish']))
		{
			if(isset($params['check']))
			{
				$row -> option = $option = Socialpublisher_Plugin_Constants::OPTION_AUTO;
				$row -> save();
			}
			else 
			{
				$row -> save();
			}
		}
		else if(isset($params['cancel']))
		{
			if(isset($params['check']))
			{
				$row -> option = $option = Socialpublisher_Plugin_Constants::OPTION_NOT_ASK;
				$row -> save();
			}
		}
		$response_message = array();
		
		if (isset($params['providers']) && isset($params['publish']) && $option != Socialpublisher_Plugin_Constants::OPTION_NOT_ASK)
		{
			$status = $params['message'];
			foreach ($params['providers'] as $provider)
			{
				if (!empty($_SESSION['socialbridge_session'][$provider]))
				{
					$obj = Socialbridge_Api_Core::getInstance($provider);
					$post_data = $api -> getPostData($provider, $resource, $status, $photo_url);
					try
					{
						$post_status = $obj -> postActivity($post_data);
						if ($post_status !== true)
						{
							$response_message[] = $this -> view -> translate('Can not publish to %s', ucfirst($provider));
						}
						// User credit integration
	                    $module = 'yncredit';
						$modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
	                    $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
	                    $module_result = $modulesTable->fetchRow($mselect);
	                    if(count($module_result) > 0)    
	                    {
						   $credit_params = array();
	                       $credit_params['rule_name'] = 'socialpublisher_publish';
						   $credit_params['user_id'] = $viewer -> getIdentity();
	                       $credit_params['item_id'] = $params['resource_id'];
	                       $credit_params['item_type'] = $params['resource_type'];
	                       Engine_Hooks_Dispatcher::getInstance()->callEvent('onPublishItemAfter', $credit_params);
	                    } 
					}
					catch(Exception $e)
					{
					}
				}
			}
		}
		if(count($params['providers']))
		{
			if (count($response_message) == 0 && isset($params['publish']) && $option != Socialpublisher_Plugin_Constants::OPTION_AUTO)
			{
				$response_message[] = $this -> view -> translate('Publish successfully');
			}
			if (count($response_message) == 0 && isset($params['publish']) && $option == Socialpublisher_Plugin_Constants::OPTION_AUTO)
			{
				$response_message[] = $this -> view -> translate('Publish successfully, auto publish for next times');
			}
		}
		else
		{
			$response_message[] = $this -> view -> translate('No provider is selected');
		} 
		if(count($response_message) == 0 && isset($params['cancel']) && $option == Socialpublisher_Plugin_Constants::OPTION_NOT_ASK)
		{
			$response_message[] = $this -> view -> translate("Don't publish, don't ask me again");
		}
		if(count($response_message) == 0 && isset($params['cancel']) && $option != Socialpublisher_Plugin_Constants::OPTION_NOT_ASK)
		{
			$response_message[] = $this -> view -> translate("Don't publish");
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => false,
			'messages' => $response_message
		));

	}

}
