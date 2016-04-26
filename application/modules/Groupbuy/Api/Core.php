<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Core.php
 * @author     Minh Nguyen
 */
class Groupbuy_Api_Core extends Core_Api_Abstract
{
    const IMAGE_WIDTH = 720;
    const IMAGE_HEIGHT = 720;

    const THUMB_WIDTH = 170;
    const THUMB_HEIGHT = 140;

    static public function getDefaultCurrency(){
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.currency', 'USD');
    }

    public function getDealsPaginator($params = array())
    {
        $paginator = Zend_Paginator::factory($this->getDealsSelect($params));

        if( !empty($params['page']) )
        {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    /**
     * XXX: need to implement this to viewer type.
     */
    public function getMaxAllowCategory(){
        return Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.allowCategory', 3);
    }
    public function getDealsSelect($params = array())
    {
        $table = Engine_Api::_()->getDbtable('deals', 'groupbuy');
        $rName = $table->info('name');

        $target_distance = $base_lat = $base_lng = "";
        if (isset($params['lat'])) {
            $base_lat = $params['lat'];
        }
        if (isset($params['long'])) {
            $base_lng = $params['long'];
        }
        //Get target distance in miles
        if (isset($params['within'])) {
            $target_distance = $params['within'];
        }

        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select = $table->select() -> from("$rName", array("$rName.*", "( 3959 * acos( cos( radians('$base_lat')) * cos( radians( $rName.latitude ) ) * cos( radians( $rName.longitude ) - radians('$base_lng') ) + sin( radians('$base_lat') ) * sin( radians( $rName.latitude ) ) ) ) AS distance"));
            $select -> where("$rName.latitude <> ''");
            $select -> where("$rName.longitude <> ''");
        } else {
            $select = $table->select()->from($rName);
        }
        $select->setIntegrityCheck(false);
        $select->joinLeft('engine4_groupbuy_categories', "$rName.category_id = engine4_groupbuy_categories.category_id", 'engine4_groupbuy_categories.title as cat_title');
        $select->joinLeft('engine4_groupbuy_locations', "$rName.location_id = engine4_groupbuy_locations.location_id", 'engine4_groupbuy_locations.title as location_title');
        // by search

        if(@$params['search'] && $search = trim($params['search']))
        {
            $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ?", '%'.$search.'%');
        }
        if(@$params['title'] && $title = trim($params['title']))
        {
            $select->where($rName.".title LIKE ? ", '%'.$title.'%');
        }
        if( isset($params['featured']) && $params['featured'] != ' ')
        {
            $select->where($rName.".featured = ? ",$params['featured']);
        }
        if( isset($params['stop']) && $params['stop'] != ' ')
        {
            $select->where($rName.".stop = ? ",$params['stop']);
        }
        // by where
        if(isset($params['where']) && $params['where'] != "")
            $select->where($params['where']);
        // by User
        if(!empty($params['user_id']) && is_numeric($params['user_id']))
            $select->where("$rName.user_id = ?",$params['user_id']);
        // by Buyer
        if(!empty($params['buyer_id']) && is_numeric($params['buyer_id']))
        {
            $select->joinLeft('engine4_groupbuy_buy_deals', "$rName.deal_id = engine4_groupbuy_buy_deals.item_id", array('engine4_groupbuy_buy_deals.status as status_buy','engine4_groupbuy_buy_deals.buydeal_id as item','engine4_groupbuy_buy_deals.number as number','engine4_groupbuy_buy_deals.amount as total'));
            $select->where("engine4_groupbuy_buy_deals.user_id = ?",$params['buyer_id']);
            $select->where("engine4_groupbuy_buy_deals.status != -1");
        }
        // by Category
        if(!empty($params['category']) && $params['category'] > 0)
        {
            $ids=  Engine_Api::_()->getDbTable('categories','groupbuy')->getDescendent($params['category']);
            $select->where("$rName.category_id in (?)", $ids);
        }
        if(!empty($params['location']) && $params['location'] > 0)
        {
            $ids=  Engine_Api::_()->getDbTable('locations','groupbuy')->getDescendent($params['location']);
            $select->where("$rName.location_id in (?)", $ids);
        }

        // by status
        $status = 30;
        if(isset($params['status'])){
            $status =  $params['status'];
        }

        $cur_time = Groupbuy_Api_Core::getCurrentServerTime();
        if($status == 20){
            $select->where("$rName.status =? ",20)->where("$rName.published=?",20)->where("$rName.start_time>?", $cur_time)
                ->where("$rName.current_sold < $rName.max_sold");
        }else if ($status == 30){
            $select->where("$rName.status in (20,30) ")->where("$rName.published=?",20)->where("$rName.start_time<=?", $cur_time)->where("$rName.end_time>=?", $cur_time)
                ->where("$rName.current_sold < $rName.max_sold");
        }else if($status == 40){
            $select->where("(($rName.status in (20,30) and $rName.published=20 and (($rName.end_time< '$cur_time') or ($rName.current_sold >= $rName.max_sold))) or ($rName.status = 40))");
        }else if($status == -1){
            $select->where("($rName.published=20 and not $rName.status in (50,10))");
        }else if($status == 0){
            $select->where("$rName.status=0 and $rName.end_time > '$cur_time'");
        }else if($status ==  10){
            $select->where("$rName.status=10 and $rName.end_time > '$cur_time'");
        }else if($status == -2 ){

        }else if($status == -3){

        }else{
            $select->where("$rName.status =  ?", $status);
        }
        // by publish
        if(isset($params['published']) && $params['published'] != ' ' && $params['published'] != '')
        {
            $published = $params['published'];
            $select->where("$rName.published = ?",$published);
        }
        if(isset($params['order']) && $params['order'])
        {
            if($params['order'] == 'cat_title')
            {

            }
            if($params['order'] == 'location_title')
            {

            }
            if($params['order'] == 'username')
            {
                $select->joinLeft('engine4_users', "$rName.user_id = engine4_users.user_id", 'engine4_users.user_id');
            }
            $select->order($params['order'].' '.$params['direction']);
        }
        else
        {
            // order
            if(isset($params['orderby']) && $params['orderby'])
                $select->order($params['orderby'].' DESC');
            else
            {
                $select->order("$rName.creation_date DESC");
            }
        }
        $select->where("$rName.is_delete = 0");


        if ($base_lat && $base_lng && $target_distance && is_numeric($target_distance)) {
            $select -> having("distance <= $target_distance");
            $select -> order("distance ASC");
        }

        if(getenv('DEVMODE') == 'localdev'){
            print_r($params);
            echo $select;
        }
        return $select;
    }
    public function getCategories()
    {
        $table = Engine_Api::_()->getDbTable('categories', 'groupbuy');
        return $table->fetchAll($table->select()->order('title ASC'));
    }
    public function getLocations()
    {
        $table = Engine_Api::_()->getDbTable('locations', 'groupbuy');
        return $table->fetchAll($table->select()->order('title ASC'));
    }
    public function getAVGrate($deal_id)
    {
        $rateTable = Engine_Api::_()->getDbtable('rates', 'groupbuy');
        $select = $rateTable->select()
            ->from($rateTable->info('name'), 'AVG(rate_number) as rates')
            ->group("deal_id")
            ->where('deal_id = ?', $deal_id);
        $row = $rateTable->fetchRow($select);
        return ((count($row) > 0)) ? $row->rates : 0;
    }
    public function canRate($row,$user_id)
    {
        if ($row->user_id == $user_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.rate', 0) == 0)
            return 0;
        $rateTable = Engine_Api::_()->getDbtable('rates', 'groupbuy');
        $select = $rateTable->select()
            ->where('deal_id = ?', $row->getIdentity())
            ->where('poster_id = ?', $user_id);

        return (count($rateTable->fetchAll($select)) > 0)?0:1;
    }
    public function createPhoto($params, $file)
    {
        if( $file instanceof Storage_Model_File )
        {
            $params['file_id'] = $file->getIdentity();
        }

        else
        {
            // Get image info and resize
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $extension = ltrim(strrchr($file['name'], '.'), '.');

            $mainName = $path.'/m_'.$name . '.' . $extension;
            $profileName = $path.'/p_'.$name . '.' . $extension;
            $thumbName = $path.'/t_'.$name . '.' . $extension;
            $thumbName1 = $path.'/t1_'.$name . '.' . $extension;

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
                ->write($mainName)
                ->destroy();
            // Resize image (profile)
            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                ->resize(400, 400)
                ->write($profileName)
                ->destroy();
            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                ->resize(339,195)
                ->write($thumbName1)
                ->destroy();

            $image = Engine_Image::factory();
            $image->open($file['tmp_name'])
                ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->write($thumbName)
                ->destroy();

            // Store photos
            $photo_params = array(
                'parent_id' => $params['deal_id'],
                'parent_type' => 'deal',
            );
            $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
            $profileFile = Engine_Api::_()->storage()->create($profileName, $photo_params);
            $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
            $thumbFile1 = Engine_Api::_()->storage()->create($thumbName1, $photo_params);
            $photoFile->bridge($profileFile, 'thumb.profile');
            $photoFile->bridge($thumbFile, 'thumb.normal');
            $photoFile->bridge($thumbFile1, 'thumb.normal1');
            $params['file_id'] = $photoFile->file_id; // This might be wrong
            $params['photo_id'] = $photoFile->file_id;

            // Remove temp files
            @unlink($mainName);
            @unlink($profileName);
            @unlink($thumbName);
            @unlink($thumbName1);

        }
        $row = Engine_Api::_()->getDbtable('photos', 'groupbuy')->createRow();
        $row->setFromArray($params);
        $row->save();
        return $row;
    }
    public function getDealStatistics($status)
    {
        $return = array();
        for($i = 0 ; $i < 5; $i ++)
        {
            $total = $this->getDealBuyStatus($i,$status);
            if(!$total)
                $total = 0;
            $return[$i] = $total;
        }
        return $return;
    }
    function getDealBuyStatus($time,$status)
    {
        if(!$status)
            $status = 0;
        $table = Engine_Api::_()->getDbtable('deals', 'groupbuy');
        $rName = $table->info('name');
        $select = $table->select()->from($rName,"Count($rName.deal_id) as total");
        if($time == 0)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("MONTH($rName.modified_date) = MONTH(NOW())")
                ->where("DAY($rName.modified_date) = DAY(NOW())");
        }
        else if($time == 1)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("WEEKOFYEAR($rName.modified_date) = WEEKOFYEAR(NOW())");
        }
        else if($time == 2)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("MONTH($rName.modified_date) = MONTH(NOW())");
        }
        else if($time == 3)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())");
        }
        $select ->where("$rName.is_delete = 0")
            ->where("$rName.status = ?",$status);
        $total =  $table->fetchRow($select);
        return $total->total;
    }
    public function getAmounts($status)
    {
        $return = array();
        for($i = 0 ; $i < 5; $i ++)
        {
            $total = $this->getAmountBuyStatus($i,$status);
            if(!$total)
                $total = 0;
            $return[$i] = $total;
        }
        return $return;
    }
    function getAmountBuyStatus($time,$status)
    {
        if(!$status)
            $status = 0;
        $table = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy');
        $rName = $table->info('name');
        $select = $table->select()->from($rName,"Sum($rName.amount) as total");
        if($time == 0)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("MONTH($rName.transaction_date) = MONTH(NOW())")
                ->where("DAY($rName.transaction_date) = DAY(NOW())");
        }
        else if($time == 1)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("WEEKOFYEAR($rName.transaction_date) = WEEKOFYEAR(NOW())");
        }
        else if($time == 2)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("MONTH($rName.transaction_date) = MONTH(NOW())");
        }
        else if($time == 3)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())");
        }
        if($status == "total")
        {
            $select->where("($rName.params = 'Paid amount to Buyer' OR $rName.params = 'Paid amount to Seller')");
        }
        else
        {
            $select->where("$rName.params LIKE '$status%'");
        }

        $select->where("$rName.transaction_status = 1");
        $total =  $table->fetchRow($select);
        return $total->total;
    }
    public function getRequests($status)
    {
        $return = array();
        for($i = 0 ; $i < 5; $i ++)
        {
            $total = $this->getRequestBuyStatus($i,$status);
            if(!$total)
                $total = 0;
            $return[$i] = $total;
        }
        return $return;
    }
    function getRequestBuyStatus($time,$status)
    {
        if(!$status)
            $status = 0;
        $table = Engine_Api::_()->getDbTable('transactionTrackings', 'groupbuy');
        $rName = $table->info('name');
        $select = $table->select()->from($rName,"Count(*) as total");
        if($time == 0)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("MONTH($rName.transaction_date) = MONTH(NOW())")
                ->where("DAY($rName.transaction_date) = DAY(NOW())");
        }
        else if($time == 1)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("WEEKOFYEAR($rName.transaction_date) = WEEKOFYEAR(NOW())");
        }
        else if($time == 2)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())")
                ->where("MONTH($rName.transaction_date) = MONTH(NOW())");
        }
        else if($time == 3)
        {
            $select ->where("YEAR($rName.transaction_date) = YEAR(NOW())");
        }
        if($status == "total")
        {
            $select->where("($rName.params = 'Paid amount to Buyer' OR $rName.params = 'Paid amount to Seller')");
        }
        else
        {
            $select->where("$rName.params = ?",$status);
        }
        $select->where("$rName.transaction_status = 1");
        $total =  $table->fetchRow($select);
        return $total->total;
    }
    public function getPublished($published)
    {
        $return = array();
        for($i = 0 ; $i < 5; $i ++)
        {
            $total = $this->getPublishBuyStatus($i,$published);
            if(!$total)
                $total = 0;
            $return[$i] = $total;
        }
        return $return;
    }
    function getPublishBuyStatus($time,$published)
    {
        if(!$published)
            $published = 0;
        $table = Engine_Api::_()->getDbtable('deals', 'groupbuy');
        $rName = $table->info('name');
        $select = $table->select()->from($rName,"Count($rName.deal_id) as total");
        if($time == 0)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("MONTH($rName.modified_date) = MONTH(NOW())")
                ->where("DAY($rName.modified_date) = DAY(NOW())");
        }
        else if($time == 1)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("WEEKOFYEAR($rName.modified_date) = WEEKOFYEAR(NOW())");
        }
        else if($time == 2)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())")
                ->where("MONTH($rName.modified_date) = MONTH(NOW())");
        }
        else if($time == 3)
        {
            $select ->where("YEAR($rName.modified_date) = YEAR(NOW())");
        }
        $select ->where("$rName.is_delete = 0")
            ->where("$rName.published = ?",$published);
        $total =  $table->fetchRow($select);
        return $total->total;
    }

    public function getStatistics($deal,  $codeallow = null)
    {
        //$params['deal_id'] = $deal;
        $params = $deal;
        $statistics = Groupbuy_Api_Cart::getTrackingTransaction($params, $codeallow);
        return $statistics;
    }

    /**
     * @var string   datetime string
     */
    protected static $_currentServerTime;

    /**
     * get current server datetime string
     */
    public static function getCurrentServerTime(){

        if(self::$_currentServerTime == NULL){
            $time =  time();
            self::$_currentServerTime =date('Y-m-d H:i:s', $time);
        }
        return self::$_currentServerTime;

    }

    public function approveDeal($deal_id)
    {
        $deal = Engine_Api::_()->getItem('deal', $deal_id);
        $deal->published = 20;
        $deal->status = 20;
        $deal->modified_date = date('Y-m-d H:i:s');
        $deal->save();
        //add activity feed.
        $table = Engine_Api::_()->getItemTable('deal');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $user =  Engine_Api::_()->getItem('user', $deal->user_id);
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $deal, 'groupbuy_new');
            if( $action != null ) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $deal);
            }
            $db->commit();
        }

        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }
        $sendTo = $deal->getOwner()->email;
        $params = $deal->toArray();
        //Engine_Api::_()->getApi('mail','groupbuy')->send($sendTo, 'groupbuy_approvedeal',$params);
    }

    /**
     * @param  int      $user_id        user id
     * @param  string   $payment_type   check payment type
     * @return Groupbuy_Model_PaymentAccount
     */
    function getFinanceAccount($user_id = null,$payment_type = null) {
        $Table =  new Groupbuy_Model_DbTable_PaymentAccounts;
        $select = $Table->select();

        if($user_id)
        {
            $select->where('user_id=?', $user_id);
        }
        if($payment_type)
        {
            $select->where('payment_type=?', $payment_type);
        }

        $account =  $Table->fetchRow($select);

        // check is there finnance account
        return $account;
    }

    function savePublishTrackingPayIn($bill, $transactionId, $gateway) {
        $acc = $this->getFinanceAccount($bill->user_id,2);

        $superAdmins = Engine_Api::_()->user()->getSuperAdmins()->toArray();
        $superAdminId = $superAdmins[0]['user_id'];

        $table  =  new Groupbuy_Model_DbTable_TransactionTrackings;
        $item =    $table->fetchNew();
        // them transaction tracking
        $item->transaction_date =    date('Y-m-d H:i:s');
        $item->user_seller = $bill->owner_id;
        $item->user_buyer  = $bill->user_id;
        $item->item_id     = $bill->item_id;
        $item->amount      = $bill->amount;
        $item->commission_fee  = $bill->commission_fee;
        $item->currency      = $bill->currency;
        $item->account_seller_id = 0;
        $item->account_buyer_id  = $acc->paymentaccount_id;
        $item->transaction_status = 1;
        $item->params   = sprintf('Pay fee publish Deal %s #%s', $gateway, $transactionId);
        $item->save();
        return $item;
    }

    function updatePublishBillStatus($bill, $status) {
        $bill->bill_status = $status;
        $bill->save();
    }

    function updatePublishDisplay($item_id) {
        $auto = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.approveAuto', 0);
        if($auto > 0) {
            $this->approveDeal($item_id);
        }
        else {
            $item = Engine_Api::_()->getItem('deal', $item_id);
            if ($item) {
                $item->published = 10;
                $item->status = 10;
                $item->save();
            }

        }
    }

    /**
     * @param  Groupbuy_Model_Bill
     * @param  Groupbuy_Model_Deal
     * @return Groupbuy_Model_PaymentAccount
     */
    function updateTotalAmount($bill) {
        $account = $this->getFinanceAccount($bill->owner_id,2);
        $account->total_amount = $account->total_amount + $bill->amount - $bill->commission_fee;
        $account->total_price_amount += $bill->item_price * $bill->number - $bill->commission_fee;
        $account->save();
        return $account;
    }

    /**
     * @param   Groupbuy_Model_Bill  $bill
     * @return  Groupbuy_Model_Deal
     *
     */
    function updateTotalBuy($bill){
        $Table =  new Groupbuy_Model_DbTable_Deals;
        $deal =  $Table->find($bill->item_id)->current();

        if(!is_object($deal)){
            throw new Exception ("the deal does not found!");
        }

        $deal->current_sold = $deal->current_sold + $bill->number;

        if( $deal->current_sold >= $deal->max_sold ){
            $deal->status = 40;
            $deal->end_time =  date('Y-m-d H:i:s');
        }
        $deal->save();
        return $deal;
    }

    /**
     * add to transaction tracking.
     *
     * @param   Groupbuy_Model_Bill   $bill
     * @param   Groupbuy_Model_TransactionTracking
     */
    function saveTrackingPayIn($bill, $transaction_ID, $gateway) {
        // buyer account
        $account = $this->getFinanceAccount($bill->user_id,2);
        $account_buyer_id = ($account) ? $account->paymentaccount_id : 0;

        // seller account.
        $accSell = $this->getFinanceAccount($bill->owner_id,2);
        $table  =  new Groupbuy_Model_DbTable_TransactionTrackings;
        $item =    $table->fetchNew();


        // them transaction tracking
        $item->transaction_date =    date('Y-m-d H:i:s');
        $item->user_seller = $bill->owner_id;
        $item->user_buyer  = $bill->user_id;
        $item->item_id     = $bill->item_id;
        $item->amount      = $bill->amount;
        $item->commission_fee      = $bill->commission_fee;
        $item->currency      = $bill->currency;
        $item->number      = $bill->number;
        $item->account_seller_id = $accSell->paymentaccount_id;
        $item->account_buyer_id  = $account_buyer_id;
        $item->transaction_status = 1;
        $item->params   = sprintf('%s #%s',$gateway, $transaction_ID);
        $item->save();
        return $item;
    }

    /**
     * @param Groupbuy_Model_Bill $bill
     * @param number   $status    status [0,1]
     * @param string   $transid   transaction id
     * @return null
     */
    function updateBillStatus($bill , $status, $tranid){
        $bill->bill_status = $status;
        $bill->save();
        for ($i = 1; $i <= $bill->number; $i++) {
            $coupon_code =  Engine_Api::_()->getDbTable('coupons','groupbuy')->addCoupon($bill->user_id,$bill->item_id,$bill->bill_id, 0, $tranid);
        }
    }

    /**
     * @param  Groupbuy_Model_Bill $bill
     * @return Groupbuy_Model_BuyDeal
     */
    function insertBuy($bill){
        $Buys =  new Groupbuy_Model_DbTable_BuyDeals;
        $buy  = $Buys->fetchNew();
        $buy->setFromArray($bill->toArray());
        $buy->buy_date = date('Y-m-d H:i:s');
        $buy->save();
        return $buy;
    }

    public function processPublishPayment($sercurity, $invoice, $transactionId, $gateway) {
        //get bill
        $Bills  =  new Groupbuy_Model_DbTable_Bills;
        $select =  $Bills->select()->where('sercurity=?', $sercurity)->where('invoice=?', $invoice);
        $bill =  $Bills->fetchRow($select);

        if($bill){
            //update status of bill
            $this->updatePublishBillStatus($bill, 1);
            $this->updatePublishDisplay($bill->item_id);
            $bill->bill_status = 1;
            //saveTracking
            $this->savePublishTrackingPayIn($bill, $transactionId, $gateway);

            /**
             * Call Event from Affiliate
             */
            $module = 'ynaffiliate';
            $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
            $mselect = $modulesTable->select()
                ->where('enabled = ?', 1)
                ->where('name  = ?', $module);
            $module_result = $modulesTable->fetchRow($mselect);
            if(count($module_result) > 0)	{
                $deal = Engine_Api::_()->getItem('deal', $bill->item_id);
                $params['module'] = 'groupbuy';
                $params['user_id'] = $deal->user_id;
                $params['rule_name'] = 'publish_deal';
                $params['currency'] = $deal->currency;
                $params['total_amount'] = number_format($bill->amount,2);
                Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
            }
        }
    }

    public function processPayment($sercurity, $invoice, $transactionId, $giftId, $gateway) {
        //get bill
        $Bills  =  new Groupbuy_Model_DbTable_Bills;
        $select =  $Bills->select()->where('sercurity=?', $sercurity)->where('invoice=?',$invoice);
        $bill =  $Bills->fetchRow($select);
        if($bill && $bill->bill_status == 0) {
            // insert to buy deals table
            // @see engine4_groupbuy_buydeals
            $buy  = $this->insertBuy($bill);
            $deal = $this->updateTotalBuy($bill);
            $this->updateTotalAmount($bill, $deal);
            $tracking = $this->saveTrackingPayIn($bill,$transactionId, $gateway);
            $this->updateBillStatus($bill,1,$tracking->getIdentity());

            // send a bill to user.
            $billInfo =  $bill->toArray();
            $billInfo['code'] = $transactionId;
            $billInfo['coupon_codes'] =  $bill->getCoupons(' - ');
            $buyer = Engine_Api::_()->getItem('user', $bill->user_id);
            $seller = Engine_Api::_()->getItem('user', $bill->owner_id);
            // get mail service object
            $mailService = Engine_Api::_()->getApi('mail','groupbuy');

            // always send to seller.
            $gift =  $bill->getGift($giftId);
            if(is_object($gift)){
                // update gift status
                $gift->bill_id =  $bill->getIdentity();
                $gift->save();
                $mailService->send($buyer->email, 'groupbuy_buygiftbuyer',$billInfo);
                // send notification to the gift's receiver.
                $mailService->send($gift->friend_email, 'groupbuy_giftconfirm',$billInfo);
                // send notification to buyer
                $mailService->send($seller->email, 'groupbuy_buygiftseller',$billInfo);

            }else{
                // send notification to seller.
                $mailService->send($seller->email, 'groupbuy_buydealseller',$billInfo);
                // send notification to buyer
                $mailService->send($buyer->email, 'groupbuy_buydealbuyer',$billInfo);
            }
            /**
             * Call Event from Affiliate
             */
            $module = 'ynaffiliate';
            $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
            $mselect = $modulesTable->select()
                ->where('enabled = ?', 1)
                ->where('name  = ?', $module);
            $module_result = $modulesTable->fetchRow($mselect);
            $params = array();
            if(count($module_result) > 0)	{
                $params['module'] = 'groupbuy';
                $params['user_id'] = $bill->user_id;
                $params['rule_name'] = 'buy_deal';
                $deal = Engine_Api::_()->getItem('deal', $bill->item_id);
                $params['currency'] = $deal->currency;
                $params['total_amount'] = number_format($bill->amount,2);
                Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $params);
            }
            /**
             * End Call Event from Affiliate
             */
            // User credit integration
            $module = 'yncredit';
            $mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
            $module_result = $modulesTable->fetchRow($mselect);
            if(count($module_result) > 0)
            {
                $params['rule_name'] = 'groupbuy_buy';
                $deal = Engine_Api::_()->getItem('deal', $bill->item_id);
                $params['item_id'] = $deal -> getIdentity();
                $params['item_type'] = $deal -> getType();
                Engine_Hooks_Dispatcher::getInstance()->callEvent('onPurchaseItemAfter', $params);
            }
        }
    }

    public function getGateway($gateway_id) {
        return $this -> getPlugin($gateway_id) -> getGateway();
    }

    public function getPlugin($gateway_id) {
        if (null === $this -> _plugin) {
            if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id))) {
                return null;
            }
            Engine_Loader::loadClass($gateway -> plugin);
            if (!class_exists($gateway -> plugin)) {
                return null;
            }
            $class = str_replace('Payment', 'Groupbuy', $gateway -> plugin);

            Engine_Loader::loadClass($class);
            if (!class_exists($class)) {
                return null;
            }

            $plugin = new $class($gateway);
            if (!($plugin instanceof Engine_Payment_Plugin_Abstract)) {
                throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
            }
            $this -> _plugin = $plugin;
        }
        return $this -> _plugin;
    }
}
