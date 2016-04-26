<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Gateway.php
 * @author     Minh Nguyen
 */
class Groupbuy_Api_Gateway extends Core_Api_Abstract
{
     /**
     * Save setting gateway. Update info if it exists
     * 
     * @param mixed $gateway_name
     * @param mixed $params
     */
     public function saveSettingGateway($gateway_name ='Paypal',$params = array()) 
     {
         $gateway = Groupbuy_Api_Gateway::getSettingGateway($gateway_name);
         
         if ( $gateway != null)
         {
             if(isset($params['is_from_finance']) && $params['is_from_finance'] == 1)
             {
                 $gateway['admin_account'] = $params['admin_account'];
                 
             }
             else
             {
                
                 $gateway['admin_account'] = $params['admin_account'];
                 $gateway['currency'] = $params['currency'];
                 $gateway['is_active'] = $params['is_active'];
                 $gateway['params'] = serialize($params['params']) ;
           
                 
             }
            $table  = Engine_Api::_()->getDbtable('gateways', 'groupbuy');
            $where = $table->getAdapter()->quoteInto('gateway_name = ?',$gateway_name);
            $table->update($gateway, $where); 
         }
         else
         {
             if(isset($params['is_from_finance']) && $params['is_from_finance'] == 1)
             {
                unset($params['is_from_finance']) ;
             }
             $params['gateway_name'] = $gateway_name;
             $params['params'] = serialize($params['params']);
             $table  = Engine_Api::_()->getDbtable('gateways', 'groupbuy');
             $t = $table->createRow();
             $t->gateway_name = $params['gateway_name'];
             $t->admin_account  = $params['admin_account'] ;
             $t->currency  = $params['currency'] ;
             $t->is_active =   1;
             $t->params   =    $params['params'];
             $t->save();
         }
             
     }
     /**
     * Get setting of gateway 
     * 
     * @param mixed $gateway_name
     */
     public function getSettingGateway($gateway_name = 'Paypal')   
     {
          $l_table  = Engine_Api::_()->getDbTable('gateways', 'groupbuy'); 
          $select   = $l_table->select()
                       ->from($l_table)->where('gateway_name = ?',$gateway_name);
         $result =  $l_table->fetchAll($select)->toArray();
         return $result[0];
     }
     public function getActiveGateway($gateway_name = 'Paypal')   
     {
          $l_table  = Engine_Api::_()->getDbTable('gateways', 'groupbuy'); 
          $select   = $l_table->select()
                       ->from($l_table)->where('is_active = 1')->where('gateway_name = ?',$gateway_name);
         $result =  $l_table->fetchAll($select)->toArray();
         if(Count($result) > 0)
            return true;
         else
            return false;
     }
}   
?>
