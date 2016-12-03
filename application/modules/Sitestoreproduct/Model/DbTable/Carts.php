<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Carts.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Carts extends Engine_Db_Table
{
  protected $_rowClass = 'Sitestoreproduct_Model_Cart';

  /**
   * Return the number of products, which exist in cart.
   */
  public function getProductCounts($cartId = null)
  {
    if( empty($cartId) )
    {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $cartTableName = $this->info('name');    
      $cartId = $this->select()
                     ->from($cartTableName, 'cart_id')
                     ->where('owner_id = ?', $viewer_id)
                     ->query()->fetchColumn();
    }
    
    if( !empty($cartId) ) {
      $cartProductTable = Engine_Api::_()->getDbtable('cartproducts', 'sitestoreproduct');
      
      $cartProductCount = $cartProductTable->select()
                                           ->from($cartProductTable->info('name'), 'SUM(quantity)')
                                           ->where('cart_id =?', $cartId)
                                           ->query()->fetchColumn();

      return $cartProductCount;
    }
    
    return 0;
  }

  /**
   * Return cart id of the current viewer
   *
   * @param $viewer id
   * @return int
   */
  public function getCartId($viewer_id)
  {
    $select = $this->select()->from($this->info('name'), array('cart_id'))->where('owner_id = ?', $viewer_id);
    return $select->query()->fetchColumn();
  }
}