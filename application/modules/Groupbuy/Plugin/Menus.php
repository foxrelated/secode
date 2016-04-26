<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Menus.php
 * @author     Minh Nguyen
 */
class Groupbuy_Plugin_Menus
{
  public function canCreateDeals()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create auction
    if( !Engine_Api::_()->authorization()->isAllowed('groupbuy_deal', $viewer, 'create') ) {
      return false;
    }
    return true;
  }

  public function canViewDeals()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view auction
    if( !Engine_Api::_()->authorization()->isAllowed('groupbuy_deal', $viewer, 'view') ) {
      return false;
    }
    return true;
  }
  public function canManageDeals()
  {
  	 $viewer = Engine_Api::_()->user()->getViewer();
  if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to view auction
    if( !Engine_Api::_()->authorization()->isAllowed('groupbuy_deal', $viewer, 'view') ) {
      return false;
    }
     //Check have account yet?   
   // $account = Groupbuy_Api_Cart::getFinanceAccount($viewer->getIdentity());
   //if(!$account)
   //     return false; 
    return true;
  }
  public function canCreateAccounts()
  {
       $viewer = Engine_Api::_()->user()->getViewer();
  if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    return true;
  }
  public function canViewStatistics()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    return true;
  }
  public function canFaqs()
  {
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( !$viewer || !$viewer->getIdentity() ) {
    //  return false;
    //}
    return true;
  }
  public function canHelp()
  {
     //$viewer = Engine_Api::_()->user()->getViewer();
   // if( !$viewer || !$viewer->getIdentity() ) {
   //   return false;
    //}
    return true;
  }
}