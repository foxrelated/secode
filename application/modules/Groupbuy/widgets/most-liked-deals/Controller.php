<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: most rated auctions
 * @author     Minh Nguyen
 */
class Groupbuy_Widget_MostLikedDealsController extends Groupbuy_Content_Widget_Listing
{
  public function indexAction()
  {
  	$this->init();
	$limit = $this->getLimit();
	
  	$btable = Engine_Api::_()->getDbtable('deals', 'groupbuy');
    $ltable  = Engine_Api::_()->getDbtable('likes', 'core');
    $bName = $btable->info('name');
    $lName = $ltable->info('name');
    $select = $btable->select()->from($bName)  ;
    $select
    ->joinLeft($lName, "resource_id = deal_id",'')
    ->where("resource_type  LIKE 'groupbuy_deal'")        
    ->group("resource_id")
    ->order("Count(resource_id) DESC")
	->limit($limit);
	
    $select->where('is_delete = 0')
	->where('status=30')
	->where('published=20');
    $this->view->paginator = $btable->fetchAll($select); 
	
 }
}
?>