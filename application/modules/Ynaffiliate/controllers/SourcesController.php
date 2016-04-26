<?php

class Ynaffiliate_SourcesController extends Core_Controller_Action_Standard
{
	public function init()
	{
		// private page
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$affiliate = new Ynaffiliate_Plugin_Menus;
		if (!$affiliate -> canView())
		{
			$this -> _redirect('/affiliate/index');
		}
	}

	public function indexAction()
	{
		$this -> _helper -> content -> setEnabled();
		$suggests = Engine_Api::_() -> ynaffiliate() -> getSuggestLinks();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> suggests = $suggests;
	}

	public function dynamicAction()
	{
		$this -> _helper -> content -> setEnabled();
	}

	public function getAffiliateLinkAction()
	{
		$target_link = $this -> _getParam('target_link');
		$target_header = get_headers($target_link);
		$target_status = $target_header[0];
		$this -> view -> status = $target_status;
		if ($target_status == null || strpos($target_status, '404') === true)
		{
			$this -> view -> error = 1;
			$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _('The Url format is not valid!');
		}
		else
		{
			$request = Zend_Controller_Front::getInstance() -> getRequest();
			$host = $request -> getHttpHost();
			$parse_url = parse_url($target_link);
			$base_url = $request -> getBaseUrl();
			if ($base_url == '')
			{
				$base_url = "/";
			}
			$pos = strpos($target_link, $base_url);
			if ($host != $parse_url['host'] || (!$pos))
			{
				$this -> view -> error = 2;
				$this -> view -> text = Zend_Registry::get('Zend_Translate') -> _('The Url domain is not valid!');
			}
			else
			{
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$affiliate_url = Engine_Api::_() -> ynaffiliate() -> getAffiliateUrl($target_link, $viewer -> getIdentity());
				$this -> view -> error = 0;
				$this -> view -> affiliate_url = $affiliate_url;
			}
		}
	}

}
