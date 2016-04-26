<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Product.php
 * @author     Minh Nguyen
 */
class Groupbuy_Model_Deal extends Core_Model_Item_Abstract {
    protected static $_curentServerTime;
    
    public static function currentServerTime(){
        if(self::$_curentServerTime == NULL){
            self::$_curentServerTime =  Groupbuy_Api_Core::getCurrentServerTime();
        }
        return self::$_curentServerTime;
    } 
    public function getHref($params = array()) {
        $params = array_merge(array('route' => 'groupbuy_general', 'reset' => true, 'action' => 'detail', 'deal' => $this -> deal_id, ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
    }

    public function getDescription() {
        $tmpBody = strip_tags($this -> description);
        return (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
    }

    public function getSlug($str = NULL, $maxstrlen = 64) {
        return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($this -> title))), '-');
    }

    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     **/
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
    }

    public function getDeals($where = null, $order = null, $limit = null) {

        $table = Engine_Api::_() -> getDbtable('deals', 'groupbuy');
        $rName = $table -> info('name');
        $select = $table -> select();

        if($where)
            $select -> where($where);
        if($order)
            $select -> order($order);
        if($limit)
            $select -> limit($limit);
        $select -> where('is_delete = 0');

        return $table -> fetchAll($select);
    }

    public function addPhoto($file_id) {
        $file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($file_id);
        $album = $this -> getSingletonAlbum();
        $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album -> getIdentity(), 'album_id' => $album -> getIdentity(), 'deal_id' => $this -> getIdentity(), 'user_id' => $file -> user_id, 'file_id' => $file_id);

        $photo = Engine_Api::_() -> getDbtable('photos', 'groupbuy') -> createRow();
        $photo -> setFromArray($params);
        $photo -> save();
        return $photo;
    }

    public function getPhoto($photo_id) {
        $photoTable = Engine_Api::_() -> getItemTable('groupbuy_photo');
        $select = $photoTable -> select() -> where('file_id = ?', $photo_id) -> limit(1);
        $photo = $photoTable -> fetchRow($select);
        return $photo;
    }

    public function getSingletonAlbum() {
        $table = Engine_Api::_() -> getItemTable('groupbuy_album');
        $select = $table -> select() -> where('deal_id = ?', $this -> getIdentity()) -> order('album_id ASC') -> limit(1);

        $album = $table -> fetchRow($select);

        if(null === $album) {
            $album = $table -> createRow();
            $album -> setFromArray(array('title' => $this -> getTitle(), 'deal_id' => $this -> getIdentity()));
            $album -> save();
        }

        return $album;
    }

    function isViewable() {
        return $this -> authorization() -> isAllowed(null, 'view');
    }

    function isEditable() {
        return $this -> authorization() -> isAllowed(null, 'edit');
    }

    function isDeleteable() {
        return $this -> authorization() -> isAllowed(null, 'delete');
    }

    public function setPhoto($photo) {
        if($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
        } else if(is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if(is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Auction_Model_Exception('invalid argument passed to setPhoto');
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array('parent_type' => 'deal', 'parent_id' => $this -> getIdentity());

        // Save
        $storage = Engine_Api::_() -> storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(400, 400) -> write($path . '/p_' . $name) -> destroy();
        
       // Resize image (normal1)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(339, 195) -> write($path . '/in1_' . $name) -> destroy();

        
        // Resize image (normal)
        $image = Engine_Image::factory();
        $image -> open($file) -> resize(170, 140) -> write($path . '/in_' . $name) -> destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image -> open($file);

        $size = min($image -> height, $image -> width);
        $x = ($image -> width - $size) / 2;
        $y = ($image -> height - $size) / 2;

        $image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);
        $iIconNormal1 = $storage -> create($path . '/in1_' . $name, $params);
        $iSquare = $storage -> create($path . '/is_' . $name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');
        $iMain -> bridge($iIconNormal, 'thumb.normal');
        $iMain -> bridge($iIconNormal1, 'thumb.normal1');
        $iMain -> bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/in1_' . $name);
        @unlink($path . '/is_' . $name);
        // Add to album
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $photoTable = Engine_Api::_() -> getItemTable('groupbuy_photo');
        $dealAlbum = $this -> getSingletonAlbum();
        $photoItem = $photoTable -> createRow();
        $photoItem -> setFromArray(array('deal_id' => $this -> getIdentity(), 'album_id' => $dealAlbum -> getIdentity(), 'user_id' => $viewer -> getIdentity(), 'file_id' => $iMain -> getIdentity(), 'collection_id' => $dealAlbum -> getIdentity(), ));
        $photoItem -> save();
        // Update row
        $this -> modified_date = date('Y-m-d H:i:s');
        $this -> photo_id = $photoItem -> file_id;
        $this -> save();

        return $this;
    }

    /**
     * @return   string  image src
     */
    public function getPhotoUrl($type = 'thumb.normal', $baseUrl = '') {
        if($file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> photo_id, $type)) {
            return $file -> map();
        } else {
            return "application/modules/Groupbuy/externals/images/nophoto_deal_$type.png";
        }

    }

    public function getImageHtml($class = "deal_thumb_medium", $type = 'thumb.normal', $width = 339, $height=195, $baseUrl="") {
        return sprintf('<img width="%d" height="%d" class="%s" src="application/modules/Groupbuy/externals/images/background_%s.png" style="background-image: url(%s)" />', $width, $height,  $class, $type, $this -> getPhotoUrl($type, $baseUrl));
    }

    public function getLocation() {
        $location  = Engine_Api::_()->getItem("groupbuy_location",$this -> location_id);
        if($location->parent_id == 1)
            return Engine_Api::_() -> getDbTable('locations', 'Groupbuy') -> getNode($this -> location_id, false);
        else
            return Engine_Api::_() -> getDbTable('locations', 'Groupbuy') -> getNode($this -> location_id, false) . ", " . Engine_Api::_() -> getDbTable('locations', 'Groupbuy') -> getNode($location->parent_id, false);
    }

    public function getCategory() {
        return Engine_Api::_() -> getDbTable('categories', 'Groupbuy') -> getNode($this -> category_id, false);
    }
    public function getCoupon()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();  
        $table = Engine_Api::_() -> getDbTable('coupons','groupbuy');
        $select = $table -> select() 
         -> where('deal_id = ?', $this ->getIdentity())
         -> where('user_id = ?', $viewer -> getIdentity());

        $coupons = $table -> fetchALL($select);
        $cup = "";
        foreach($coupons as $coupon)
            $cup = $cup . $coupon->code. ",";
        return $cup;
    }
    public function getStatusString() {
        $cur_time =  self::currentServerTime();
        if($this->published == 20){
            switch($this->status){
                case 20:
                    if($this->start_time > $cur_time){
                        return 'Upcoming';
                    }else if($this->end_time > $cur_time){
                        return 'Running';
                    }else if($this->min_sold > $this->current_sold){
                        return 'Closed';
                    }else{
                        return 'Canceled';
                    }
                case 30:
                    if($this->end_time > $cur_time){
                        return 'Running';
                    }else if($this->current_sold < $this->min_sold){
                        return 'Canceled';
                    }else{
                        return 'Closed';    
                    }
            }
        }else if ($this->published == 30){
            return 'Canceled';
        }else if($this->published == 10){
            return 'Pending';
        }else{
            return 'Created';
        }
        // XXX: take care about following line.
        switch($this->status) {
            case "10" :
                return "Pending";
            case "20" :
                return 'Upcoming';                
            case "30" :
                return "Running";
                break;
            case "40" :
                return "Closed";
            case "50" :
                return "Canceled";
        }
        return 'Created';
    }

    public function getPublishedString() {
        switch($this->published) {
            case "10" :
                return "Waiting";
            case "20" :
                return "Approved";
            case "30" :
                return "Denied";
        }
        return "Not Published";
    }

    public function hasLatLng() {
        return $this -> latitude && $this -> longitude;
    }

    public function getDirectionHref() {
        return sprintf('http://maps.google.com/maps?daddr=%s,%s&mrsp=1&sz=16&z=16', $this -> latitude, $this -> longitude);
    }

    public function updateToRunning() {
        
        // mass mail to queue to seller and seller
        $mail =Engine_Api::_() -> getApi('Mail', 'Groupbuy'); 
        $mail-> send($this -> getOwner() -> email, 'groupbuy_dealrunning', $this -> toArray(), true, 8);
        //echo sprintf("deail: %s - %s", $this->deal_id, $this->getOwner()->email);
        // send email to seller an seller
        $this -> status = 30;
        $this -> save();
    }

    public function updateToClose() {
        // mass mail to queue to seller and seller
        $mail =Engine_Api::_() -> getApi('Mail', 'Groupbuy'); 
        //$mail-> send($this -> getOwner() -> email, 'groupbuy_sellerdealclosed', $this -> toArray(), true, 8);
        $mail-> send($this -> getOwner() -> email, 'groupbuy_sellerdealclosed', $this -> toArray());
        $params = $this -> toArray();
        foreach($this -> getBuyerEmails() as $buyerEmail) {
                $params['total_amount'] =  $buyerEmail['total_amount'];
                $params['total_number'] =  $buyerEmail['total_number'];
            //$mail-> send($buyerEmail->email, 'groupbuy_buyerdealclosed', $data, 1);
            $mail-> send($buyerEmail['email'], 'groupbuy_buyerdealclosed', $params);
        }    

        $this -> status = 40;
        $this -> save();
    }

    public function updateToCancel() {
                
        // mass mail to queue to seller and seller
        $mail =Engine_Api::_() -> getApi('Mail', 'Groupbuy'); 
        //$mail-> send($this -> getOwner() -> email, 'groupbuy_sellerdealdel', $this -> toArray(), true);
        print_r($this->getOwner()->email);
        $mail-> send($this->getOwner()->email, 'groupbuy_sellerdealdel', $this -> toArray());
        $params = $this -> toArray();
        foreach($this -> getBuyerEmails() as $buyerEmail) {
                $params['total_amount'] =  $buyerEmail['total_amount'];
                $params['total_number'] =  $buyerEmail['total_number'];
        //Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail->email, 'groupbuy_buyerdealdel', $params, true);
                
                Engine_Api::_()->getApi('mail','groupbuy')->send($buyerEmail['email'], 'groupbuy_buyerdealdel', $params);
        }
        $billtable =  Engine_Api::_()->getDbTable('bills','groupbuy');
        // if gift id is null
        $billselect = $billtable->select()->where('item_id=?', $this->deal_id);
        $billresult = $billtable->fetchAll($billselect);
        foreach ($billresult as $billre) {
            $gift = $billre->getGift();
            if (is_object($gift)) {
                $params['number'] = $billre->number;
                $params['user_id'] = $billre->user_id;
                $params['coupon_codes'] = $billre->getCoupons(' - ');
                //print_r($gift->friend_email);
                //print_r($billre->getCoupons(' - '));
                Engine_Api::_()->getApi('mail','groupbuy')->send($gift->friend_email, 'groupbuy_giftunconfirm', $params);
            }
        }                        
        //print_r($billresult->toArray());
        //print_r($this->getOwner()->toArray());
        //print_r($gift->toArray());
        // end email to sender and buyer
        $this -> status = 50;
        $this -> save();
    }
    
    /*public function updateToDelete() {
                
        // mass mail to queue to seller and seller
        $mail =Engine_Api::_() -> getApi('Mail', 'Groupbuy'); 
        $mail-> send($this -> getOwner() -> email, 'groupbuy_sellerdealcancel', $this -> toArray(), true, 8);
        $data = $this -> toArray();
        foreach($this -> getStatBuyers() as $buyer) {
            $data['total_amount'] =  $buyer['total_amount'];
            $data['total_number'] =  $buyer['total_number'];
            $mail-> send($buyer['email'], 'groupbuy_buyerdealcancel', $data, true);
        }

        // end email to sender and buyer
        $this -> is_delete = 50;
        $this -> save();
    }*/

    public function getBuyerEmails() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "select u.email, 
        (select sum(am.amount) from engine4_groupbuy_buy_deals as am where am.item_id =  b.item_id and am.user_id =  u.user_id) as total_amount,
        (select sum(am.number) from engine4_groupbuy_buy_deals as am where am.item_id =  b.item_id and am.user_id =  u.user_id) as total_number 
        from engine4_groupbuy_buy_deals as b
        join engine4_users as u on (u.user_id =  b.user_id)
        where (b.item_id =  {$this->deal_id})
        group by u.user_id";
        return (array) $db -> fetchAll($sql);
    }
    
    public function getStatBuyers(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "select u.email, 
        (select sum(am.amount) as total_amount from engine4_groupbuy_buy_deals as am where am.item_id =  b.item_id and am.user_id =  u.user_id),
        (select sum(am.number) as total_number from engine4_groupbuy_buy_deals as am where am.item_id =  b.item_id and am.user_id =  u.user_id)
        from engine4_groupbuy_buy_deals as b
        join engine4_users as u on (u.user_id =  b.user_id)                
        where (b.item_id =  {$this->deal_id})
        group by u.user_id";
        return (array)$db -> fetchAll($sql);
    }
    public function delete(){
        $this->is_delete = 1;
        $this->save();
        //parent::delete();
    }
    
    public function isSoldOut(){
        return ($this->current_sold >= $this->max_sold);
    }
    
    /**
     * check this item can buy 
     */
    public function canBuy(){
        $cur_time = self::currentServerTime();
        if(
            ($this->status == 20 || $this->status == 30)
            && ($this->start_time < $cur_time)
            && ($this->end_time > $cur_time)
            && ($this->current_sold < $this->max_sold)
            && ($this->is_delete = 0)
            && ($this->stop = 0)
            && ($this->published == 20)
        ){
            return true;
        }
        return false;
    }
    
    public function getCurrency(){
        return Engine_Api::_()->getDbTable('currencies','groupbuy')->getCurrency($this->currency);
    }
    
    public function getMaxBought($viewer = NULL){
       $db =  Engine_Db_Table::getDefaultAdapter();
       $max1 =  $this->max_sold - $this->current_sold;
       @$user_id == NULL;
       if(is_object($viewer)){
            $user_id =  $viewer->getIdentity();       
       }else if(is_number($viewer)){
               $user_id =  $viewer;
       };
       if($user_id){
           $sql = "select SUM(number) as total from engine4_groupbuy_transaction_trackings where item_id={$this->deal_id} and user_buyer={$user_id} and user_seller <> {$user_id};";
        $max2 =  $db->fetchOne($sql);    
       }else{
           $max2 =  0;
       }
              
       $max3 =  $this->max_bought - $max2; 
       
       $max = min($max1, $max3);
       return $max>0?$max:0;
    }
    
    public function getComissionFee($number){
        $commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $this->getOwner(), 'commission');
        if($commission == "")
         {
             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'groupbuy_deal'")
                ->where("level_id = ?",$this->getOwner()->level_id)
                ->where("name = 'commission'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $commission = $mallow_a['value'];
              else
                 $commission = 0;
         }
        return round($commission/100,2)* $this->price * $number;
    }
    
    public function updateVAT(){
        $vat_id = $this->vat_id;
        $vat = Groupbuy_Model_DbTable_Vats::getValue($vat_id);
        $this->vat =  round($vat,2);
        $this->vat_value =  round($this->price* (1+ $vat /  100), 2);
        $this->final_price = round($this->price + $this->vat_value,2);
        return $this;
    }
}
