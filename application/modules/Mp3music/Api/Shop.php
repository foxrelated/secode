<?php
 class Mp3music_Api_Shop extends Core_Api_Abstract
{
     /**
    * Check coupon code is valid ?
    * 
    * @param mixed $coupon
    */
    public function checkCouponCode($coupon ="")
    {
        $now = time();
        $c_table = Engine_Api::_()->getDbTable('coupons', 'mp3music');
        $c_name  = $c_table->info('name');
        
        $select = $c_table->select()
                    ->from($c_table)
                    ->where('coupon_code = "'.$coupon .'" AND start_date <= '.$now .' AND end_date >= '.$now.' AND coupon_status = 1')
                    ;
         $coup =  $c_table->fetchAll($select)->toArray();   
        
         if ($coup != null)
         {
             return $coup['coupon_value'];
         }
         return 0;
    }
    /**
    * Init value for shoping cart
    * 
    * @param mixed $security_code
    */
    public function initCartShopSession($security_code = null)
    {
       
            $_SESSION['musicsharing_cart'] = array(
                        'user_id'=>Engine_Api::_()->user()->getViewer()->getIdentity(),
                        'total_amount' => 0,
                        'coupon_code'=>array(
                                'code' => '',
                                'value' =>0,
                                ),
                        'items' =>array(
                                
                                ),
                    );
       
    }
    public function getTotalAmount()
    {
        if(!isset($_SESSION['musicsharing_cart']))
         {
            Mp3music_Api_Shop::initCartShopSession();
         }
         return $_SESSION['musicsharing_cart']['total_amount'];
    }
    /**
    * Get current cart user id
    * 
    */
    public function getCurrentUserCart()
    {
         if(!isset($_SESSION['musicsharing_cart']['user_id']))
         {
             return -1;
         }
         return $_SESSION['musicsharing_cart']['user_id'];
    }
    /**
    * get Coupon for cart.
    * 
    */
    public function getCouponCodeCart()
    {
         if(!isset($_SESSION['musicsharing_cart']))
         {
             Mp3music_Api_Shop::initCartShopSession();
         }
         return  $_SESSION['musicsharing_cart']['coupon_code'];
    }
    /**
    * Put coupon code to your bill
    * 
    * @param mixed $coupon_code
    * @param mixed $value
    */
    public function updateCouponCode($coupon_code = '' ,$value = 0)
    {
         if(!isset($_SESSION['musicsharing_cart']))
         {
             $this->initCartShopSession();
         }
         if($_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())
         {
               $_SESSION['musicsharing_cart']['coupon_code'] = array('code'=>$coupon_code,'value'=>$value);
         }
         
    }
    public function checkExistCouponCode($coupon_code = '')
    {
         if(!isset($_SESSION['musicsharing_cart']))
         {
             $this->initCartShopSession();
         }
         if($_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())                   
         {
               if ( $_SESSION['musicsharing_cart']['coupon_code']['code'] == $coupon_code)
               {
                   return true;
               }
         }
         return false;
         
    }
    /**
    * Update total Amount from cart.
    * 
    * @param mixed $value
    * @param mixed $add
    * @return mixed
    */
    public function updateTotalAmount($value,$add = true)
    {
         if(!isset($_SESSION['musicsharing_cart']))
         {
             Mp3music_Api_Shop::initCartShopSession();
         }
         if($_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())                   
         {
                if($add == true)
                 {
                    $_SESSION['musicsharing_cart']['total_amount'] = $_SESSION['musicsharing_cart']['total_amount'] + $value; 
                 }
                 else
                 {
                     $_SESSION['musicsharing_cart']['total_amount'] = $_SESSION['musicsharing_cart']['total_amount'] - $value;
                 }
                 if ($_SESSION['musicsharing_cart']['total_amount'] < 0 )
                    $_SESSION['musicsharing_cart']['total_amount'] = 0   ;
         }
         
         return $_SESSION['musicsharing_cart']['total_amount'];
            
            
    }
    /**
    * Get cart item from current session 
    * 
    */
    public function getCartItems()
    {
       
        if(isset($_SESSION['musicsharing_cart']))            
        {
            if($_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())
            {
                return $_SESSION['musicsharing_cart']['items'];
            }
            else
            {
                Mp3music_Api_Shop::initCartShopSession();
                return array();
            }
        }
        else
            return array();
        
    }
    /**
    * get information of cart list
    * 
    * @param mixed $cartlist
    */
    public function getCartItemsInfo($cartlist)
    {
        $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
        $as_name  = $as_table->info('name');
        $ab_table = Engine_Api::_()->getDbTable('albums', 'mp3music');
        $ab_name  = $ab_table->info('name');
        $songs = "(-1";
        $albums = "(-1";
        $total = 0;
        foreach ($cartlist as $ct)
        {
            if ( $ct['type'] =='song')
            {
               $songs .=",".$ct['item_id'];
            }
            if ( $ct['type'] =='album')
            {
                $albums.=",".$ct['item_id'];
            }
            $total = $total + $ct['amount'];
        }
        $songs .=')';
        $albums .=')';
    
         $select = $as_table->select()->setIntegrityCheck(false)
                    ->from($as_table,array('song_id as item_id','title','price as amount','album_id'))
                    ->joinLeft($ab_name,"$ab_name.album_id = $as_name.album_id", '')
                    //->joinLeft('engine4_users',"engine4_users.user_id = $ab_name.user_id", 'engine4_users.*')
                    ->having("$as_name.song_id IN $songs ");
       $songs = $as_table->fetchAll($select)->toArray();
       $song_list = array();
       foreach($songs as $song)
       {
            $song['type'] =  'song';
            $song_list[] =  $song;
       }
       $select = $ab_table->select()
                    ->from($ab_name,array('album_id as item_id','title','price as amount','album_id'))
                    //->joinLeft('engine4_users',"engine4_users.user_id = $ab_name.user_id",'engine4_users.*')
                    ->having("$ab_name.album_id IN $albums");
        $albums = $ab_table->fetchAll($select)->toArray();
        $album_list = array();
        foreach($albums as $album)
        {
            $album['type'] =  'album';
            $album_list[] = $album;
        }
        
         if (isset($_SESSION['musicsharing_cart']['user_id']) && $_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())
         {
            if($total < $_SESSION['musicsharing_cart']['total_amount'])
            	$_SESSION['musicsharing_cart']['total_amount'] = $total;
            else
            	$total = $_SESSION['musicsharing_cart']['total_amount'];
            $coupon = Mp3music_Api_Shop::getCouponCodeCart();
            $total = $total - $coupon['value']  ;
         }
         else
         {
             $total = 0;
         }
         
         $item_info = array_merge($album_list,$song_list) ;
         return array($total,$item_info);
    }
    /**
    * add item to cart;

    * @param mixed $item
    * $item is array (
    *               'item_id' =>
    *               'type' =>
    *               'owner_id'=>
    *               'amount' =>
    *           )
    */
    public function setCartItem($item)
    {
        if(isset($_SESSION['musicsharing_cart']) && $_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())            
        {
             $_SESSION['musicsharing_cart']['items'][$item['type'].'_'.$item['item_id']] = $item;  
        }
        else
        {
            Mp3music_Api_Shop::initCartShopSession();
            $_SESSION['musicsharing_cart']['items'][$item['type'].'_'.$item['item_id']] = $item;
        } 
        Mp3music_Api_Shop::updateTotalAmount($item['amount'],true)  ;
        return true;        
    }
    /**
    * Clear All Item from cart shop
    * 
    */
    public function clearCart()
    {
        Mp3music_Api_Shop::initCartShopSession();
    }
    public function removeCartItem($item_id,$type)
    {
         if(isset($_SESSION['musicsharing_cart']['items']))
        {
            $amount = $_SESSION['musicsharing_cart']['items'][$type.'_'.$item_id]['amount'];
            Mp3music_Api_Shop::updateTotalAmount($amount,false);
            unset($_SESSION['musicsharing_cart']['items'][$type.'_'.$item_id]);
            return true;
        }
        return false;
    }
    public function checkExist($item_id,$type)
    {
        if(isset($_SESSION['musicsharing_cart']['items'][$type.'_'.$item_id])
         && $_SESSION['musicsharing_cart']['items'][$type.'_'.$item_id]['item_id']>0)
        {
             return true;
        }
        else
        {
             return false; 
        }
    }
    /**
    * If the items are in downloadlist or in cart. User can add them again.
    * 
    * @param mixed $type
    * @param mixed $user_id
    */
    public function getHiddenCartItem($type,$user_id)
    {
        $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
        $as_name  = $as_table->info('name');
        $ab_table = Engine_Api::_()->getDbTable('albums', 'mp3music');
        $ab_name  = $ab_table->info('name');
        $l_table = Engine_Api::_()->getDbTable('lists', 'mp3music');
        $l_name  = $l_table->info('name');
        
        $select = $l_table->select()
                    ->from($l_table)->where("$l_name.user_id = ?",$user_id);
        if ($type =='song')
        {
            $select->where("$l_name.dl_song_id > ?",0);
            $sl = 'dl_song_id';
        }
        else
        {
             $select->where("$l_name.dl_album_id > ?",0);
             $sl = 'dl_album_id';
        }
        
        $result =  $l_table->fetchAll($select)->toArray();
        $listHidden = array();
        foreach($result as $res)
        {
            $listHidden[] = $res[$sl];
        }
        if($type =='song')
        {
            $select = $as_table->select()
                    ->from($as_table)
                    ->join($l_name,"$l_name.dl_album_id = $as_name.album_id",'')
                    ->where("$l_name.dl_album_id <> ?",0)
                    ->where("$l_name.user_id = ?",$user_id);
            $result = $as_table->fetchAll($select)->toArray();
           
           foreach($result as $res)
           {
              $listHidden[]= $res['song_id'];
           }
          
        }
        
        if( isset($_SESSION['musicsharing_cart']['items']))
        {
            if($_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())
            {
                foreach($_SESSION['musicsharing_cart']['items'] as $ct)
                {
                    if ($ct['type'] == $type)
                    {
                        $listHidden[] = $ct['item_id'];
                    }
                }    
            }
            
        }

        return $listHidden;
    }
    /**
    * Rebuild the cart.
    * 
    * @param mixed $cartsec
    * @param mixed $hiddencartalbum
    * @param mixed $hiddencartsong
    */
    public function reCheckCart($cartsec,$hiddencartalbum = array(),$hiddencartsong = array())
    {
         foreach($cartsec as $ct)
         {
             if ($ct['type'] == 'song' && in_array($ct['item_id'],$hiddencartsong))
             {
                 Mp3music_Api_Shop::removeCartItem($ct['item_id'],'song');
             }
             if ($ct['type'] == 'album' && in_array($ct['item_id'],$hiddencartalbum))
             {
                 Mp3music_Api_Shop::removeCartItem($ct['item_id'],'album');
             }
         }
         return $cartsec;
    }
    public function getCart()
    {
        if(isset($_SESSION['musicsharing_cart']) && $_SESSION['musicsharing_cart']['user_id'] == Engine_Api::_()->user()->getViewer()->getIdentity())
        {
              return $_SESSION['musicsharing_cart'];
        }
    }
    /**
    * Create a bill from cart item;
    * 
    * @param mixed $cart
    * @param mixed $receiver
    * @return mixed
    */
    public function makeBillFromCart($cart,$receiver)
    {
         $insert_item = array();
         foreach ($receiver as $re)
         {
             $seliz = serialize($cart);
             list($iCnt,$receiver_account) = Mp3music_Api_Cart::getFinanceAccounts('ni.account_username = "'.$re['email'].'"');
             if ( !isset($receiver_account[0]) || @$receiver_account[0]['paymentaccount_id']<=0)
             {
                 return -1;
             }
           
             $insert_item[] = array($re['invoice'],$_SESSION['payment_sercurity'],Mp3music_Api_Shop::getCurrentUserCart(),0,
                                  $re['email'],$receiver_account[0]['paymentaccount_id'],time(),0,$seliz);
         }
         $db = Engine_Db_Table::getDefaultAdapter();
         $db->beginTransaction(); 
          $b_table = Engine_Api::_()->getDbTable('bills', 'mp3music'); 
          $bill = $b_table->createRow();
          $bill->invoice     = $insert_item[0][0];
          $bill->sercurity    = $insert_item[0][1];              
          $bill->user_id     = $insert_item[0][2];
          $bill->finance_account_id       = $insert_item[0][3];
          $bill->emal_receiver  = $insert_item[0][4];
          $bill->payment_receiver_id  = $insert_item[0][5];
          $bill->date_bill  = $insert_item[0][6];
          $bill->bill_status  = $insert_item[0][7];
          $bill->params  = $insert_item[0][8];
          $bill->save(); 
         try {
              $db->commit();
          } catch (Exception $ex) {
              $db->rollback();
              break;
          }  
         return $bill;
         
    }
    /**
    * Get a bill 
    * 
    * @param mixed $invoice
    * @param mixed $security
    */
    public function getBill($security = null,$invoice = null)
    {
        $b_table = Engine_Api::_()->getDbTable('bills', 'mp3music');
        $b_name  = $b_table->info('name');
        $select = $b_table->select()
                    ->from($b_table);
        if ($invoice != null)
        {
            $select->where('invoice = ?',$invoice);
        }
        if ($security != null)
        {
            $select->where('sercurity = ?',$security);   
        }
        $select->where('bill_status = 0');      
       
        $results =  $b_table->fetchAll($select)->toArray(); 
        return  $results;
        
    }
    public function updateBillStatus($bill,$status)
    {
        $table  = Engine_Api::_()->getDbtable('bills', 'mp3music');
        $data = array(
            'bill_status' => $status
        );
        $where = $table->getAdapter()->quoteInto('bill_id = ?', $bill[0]['bill_id']);
        return $table->update($data, $where);
    }
     public function getDownloadList($type,$user_id)
    {
        
        $table  = Engine_Api::_()->getDbtable('lists', 'mp3music');
        $select = $table->select();
        if ($type =='song')
        {
            $select->where('dl_song_id > 0');
            $sl = 'dl_song_id';
        }
        else
        {
            $select->where('dl_album_id > 0');
             $sl = 'dl_album_id';
        }
        $select->where('user_id = ?',$user_id);   
        $result = $table->fetchAll($select);
        $listHidden = array();
        foreach($result as $res)
        {
            $listHidden[] = $res[$sl];
        }
        return $listHidden;
    }
}   
?>
