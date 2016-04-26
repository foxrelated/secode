<?php
 class Mp3music_Api_Gateway extends Core_Api_Abstract
{
     /**
     * Save setting gateway. Update info if it exists
     * 
     * @param mixed $gateway_name
     * @param mixed $params
     */
     public function saveSettingGateway($gateway_name ='paypal',$params = array()) 
     {
         $gateway = Mp3music_Api_Gateway::getSettingGateway($gateway_name);
         
         if ( $gateway != null)
         {
             if(isset($params['is_from_finance']) && $params['is_from_finance'] == 1)
             {
                 $gateway['admin_account'] = $params['admin_account'];
                 
             }
             else
             {
                
                 $gateway['admin_account'] = $params['admin_account'];
                 $gateway['is_active'] = $params['is_active'];
                 $gateway['params'] = serialize($params['params']) ;
           
                 
             }
            $table  = Engine_Api::_()->getDbtable('gateways', 'mp3music');
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
             $table  = Engine_Api::_()->getDbtable('gateways', 'mp3music');
             $t = $table->createRow();
             $t->gateway_name = $params['gateway_name'];
             $t->admin_account  = $params['admin_account'] ;
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
     public function getSettingGateway($gateway_name = 'paypal')   
     {
          $l_table  = Engine_Api::_()->getDbTable('gateways', 'mp3music'); 
          $select   = $l_table->select()
                       ->from($l_table)->where('gateway_name = ?',$gateway_name);
         $result =  $l_table->fetchAll($select)->toArray();
         return @$result[0];
     }
}   
?>
