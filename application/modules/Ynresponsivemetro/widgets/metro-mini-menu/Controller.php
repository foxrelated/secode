<?php
class Ynresponsivemetro_Widget_MetroMiniMenuController extends Engine_Content_Widget_Abstract
{
	private $_mode;
	public function getMode()
	{
		if (null === $this -> _mode)
		{
			$this -> _mode = 'page';
		}
		return $this -> _mode;
	}

	public function indexAction()
	{
		if (YNRESPONSIVE_ACTIVE != 'ynresponsive-metro')
		{
			return $this -> setNoRender(true);
		}
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('core_mini');
		$this -> view -> viewer_id = $viewer -> getIdentity();
		//Search
		$require_check = Engine_Api::_() -> getApi('settings', 'core') -> core_general_search;
		if (!$require_check)
		{
			if ($viewer -> getIdentity())
			{
				$this -> view -> search_check = true;
			}
			else
			{
				$this -> view -> search_check = false;
			}
		}
		else
		{
			$this -> view -> search_check = true;
		}
		
		$front = Zend_Controller_Front::getInstance();
		$module = $front -> getRequest() -> getModuleName();
		$action = $front -> getRequest() -> getActionName();
		$controller = $front -> getRequest() -> getControllerName();
		$this -> view -> isPost = $front -> getRequest() -> isPost();

		if (($module == 'user' && $controller == 'auth' && $action == 'login') || ($module == 'core' && $controller == 'error' && $action == 'requireuser') || $viewer -> getIdentity())
		{
			$this -> view -> isUserLoginPage = true;
		}
		if ($module == 'user' && $controller == 'signup' && $action == 'index')
		{
			$this -> view -> isUserSignupPage = true;
		}
	}

}
