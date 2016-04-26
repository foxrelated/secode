<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: search auctions
 * @author     Minh Nguyen
 */

class Groupbuy_Widget_SearchDealsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$request = Zend_Controller_Front::getInstance()->getRequest();
    $post = $request -> getParams();
	$this->view->form = $form = new Groupbuy_Form_Search();
	if(!isset($post['status']) || (isset($post['status']) && $post['status'] == 0))
	{
		$post['status']= 30;
	}
    $form->populate($post);
	$form->removeElement('published');
  }
}
