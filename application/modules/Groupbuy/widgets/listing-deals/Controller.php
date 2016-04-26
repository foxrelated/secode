<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: listing auctions
 * @author     Minh Nguyen
 */
class Groupbuy_Widget_ListingDealsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')-> groupbuy_page;
		$this-> view-> items_per_page = $items_per_page;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$values['page'] = $request -> getParam('page');
		$values['published'] = 20;
		$values['status'] = 30;
		if(isset($_SESSION['yngroupbuy_featured']))
		{
			$featured = $_SESSION['yngroupbuy_featured'];
		}
        if($featured)
        {
			$where = " 1 AND deal_id != " . $featured;
			$values['where'] = $where;	
		}
			
		$this -> view -> paginator = $paginator = Engine_Api::_() -> groupbuy() -> getDealsPaginator($values);
		$paginator->setItemCountPerPage($items_per_page);
		$this -> view -> user_id = $viewer -> getIdentity();
	}
}
?>
