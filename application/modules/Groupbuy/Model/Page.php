<?php
class Groupbuy_Model_Page extends Core_Model_Item_Abstract
{
    public function getHref($params = array()) {
		$params = array_merge(array('route' => 'groupbuy_page', 'reset' => true, 'action' => 'view-page', 'page_name' => $this -> name, ), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
}
?>