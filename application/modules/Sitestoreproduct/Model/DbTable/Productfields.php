<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Productfields.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Model_DbTable_Productfields extends Engine_Db_Table {

  protected $_rowClass = "Sitestoreproduct_Model_Productfield";
  
  public function getOptionId($product_id) {
    
    return $this->select()->from($this->info('name'), 'option_id')
            ->where('product_id = ?', $product_id)
            ->query()
            ->fetchColumn()
            ;
  }

}