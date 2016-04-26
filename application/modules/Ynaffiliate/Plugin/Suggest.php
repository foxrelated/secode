<?php

class Ynaffiliate_Plugin_Suggest{
	
	public function memberHomePage($user, $option){
		$siteUrl = Engine_Api::_()->ynaffiliate()->getSiteUrl();
		$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'home',true);
	   	$targetUrl = $siteUrl. $url;
	   	return $targetUrl;
	}
	
	public function memberProfilePage($user, $option){
		$siteUrl = Engine_Api::_()->ynaffiliate()->getSiteUrl();
		$url = $user->getHref();
	   	$targetUrl = $siteUrl. $url;
	   	return $targetUrl;
	}
	
	public function getTargetUrl($user, $option)
	{
		$siteUrl = Engine_Api::_()->ynaffiliate()->getSiteUrl();
		if (!$option->href) 
		{
			$module = $option->module;
			if ($module == 'mp3music')
			{
				return $siteUrl. Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'mp3music_browse',true);
			}
			$table = Engine_Api::_()->getDbtable('menuItems', 'core');
	    	$select = $table->select()
			    -> where('menu = ?', 'core_main')
		   	    -> where('module = ?', $module);
		   	$route_select = $table->fetchRow($select);
			if(!$route_select)
			{
				return false;
			}
		   	$route_array = (array) $route_select->params;
		   	$route = $route_array['route'];
	   		$url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),$route,true);
		   	$targetUrl = $siteUrl. $url;
		}
		else {
			$href = $option->href;
			if ((substr($href, 0, 7) == 'http://') || (substr($href, 0, 7) == 'https://' )) {
				$targetUrl = $href;
			} 
			else {
				$request =  Zend_Controller_Front::getInstance()->getRequest();
				$targetUrl = $request->getScheme().'://'.$href;
			}
		}
	   	return $targetUrl;
	}
}
