<?php
 class Mp3music_Api_Coupon extends Core_Api_Abstract
{
   /**
    * Auto generate Coupon Codes
    * 
    */
    public function generateCode()
    {
        return phpfox::getService('musicsharing.cart.music')->getSecurityCode();
    }
    /**
    * Add new Coupon Code
    * 
    * @param mixed $coupon
    */
    public function addNewCoupon($coupon)
    {
        return phpfox::getLib('phpfox.database')->insert(
                phpfox::getT('m2bmusic_coupon'),$coupon
            );
    }
    /**
    * Delete coupon by coupon_ids
    * 
    * @param mixed $coupon_id
    */
    public function deleteCoupon($coupon_id)
    {
       return phpfox::getLib('phpfox.database')->delete(
                phpfox::getT('m2bmusic_coupon'),'coupon_id = '.$coupon_id
            );
    }
    /**
    * Update coupon codes 
    * 
    * @param mixed $coupon
    */
    public function updateCoupon($coupon)
    {
        return phpfox::getLib('phpfox.database')->update(
                phpfox::getT('m2bmusic_coupon'),$coupon,'coupon_id = '.$coupon['coupon_id']
            );
    }
    /**
    * Get list of coupon
    * 
    * @param mixed $aConds
    * @param mixed $sSort
    * @param mixed $iPage
    * @param mixed $sLimit
    * @param mixed $bCount
    */
    public function getCoupons($aConds = array(),$sSort = 'coupon_id DESC', $iPage = '', $sLimit = '', $bCount = true)
    {
         
         phpfox::getLib('phpfox.database')->query('SET character_set_results=utf8 ');    
         $iCnt = ($bCount ? 0 : 1);
         $items = array();
         if ($bCount )
         { 
             $iCnt = phpfox::getLib('phpfox.database')->select('COUNT(*)')
                    ->from(phpfox::getT('m2bmusic_coupon'),'cou')
                    ->where($aConds)
                    ->execute('getField');
         }
         if ($iCnt)
         {
            $items = phpfox::getLib('phpfox.database')->select('*,DATE( FROM_UNIXTIME(start_date) ) AS sDate,DATE( FROM_UNIXTIME(end_date) ) AS tDate')
                    ->from(phpfox::getT('m2bmusic_coupon'),'cou')
                    ->where($aConds)
                    ->order($sSort)
                    ->limit($iPage, $sLimit, $iCnt)
                    ->execute('getSlaveRows');  
         }
         if (!$bCount)
         {
            return $items;
         }
         
         return array($iCnt, $items);
    }
}   
?>
