<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: menu auctions
 * @author     Minh Nguyen
 */
class Groupbuy_Widget_MenuDealsController extends Engine_Content_Widget_Abstract
{
   protected $_navigation;
  public function indexAction()
  {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('groupbuy_main');
  }
}