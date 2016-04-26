<?php 

class Ynaffiliate_Widget_SourcesMenuController extends Engine_Content_Widget_Abstract{
	public function indexAction(){
		$active_sources_menu = "info";
		
		if(Zend_Registry::isRegistered('active_sources_menu')){
			$active_sources_menu = Zend_Registry::get('active_sources_menu');
		}		
    //echo($active_sources_menu);
		$this->view->active_sources_menu =  $active_sources_menu;
	}
}
