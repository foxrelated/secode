<?php
 class Mp3music_Api_Account extends Core_Api_Abstract
{
    public function getCurrentInfo($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_mp3music_payment_accounts as account','account.account_username')  
                        ->joinRight('engine4_users','engine4_users.user_id =  account.user_id','engine4_users.*')
                        ->where('engine4_users.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
    public function getCurrentAccount($user_id){
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_mp3music_payment_accounts as account','account.*')  
                        ->where('account.user_id = ?',$user_id);
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0];
    }
    public function getSellingSettings($user_group_id){
        $ss_table = Engine_Api::_()->getDbTable('sellingSettings', 'mp3music');
        $ss_name  = $ss_table->info('name');
        $select = $ss_table->select()
                    ->from($ss_table)->where("$ss_name.user_group_id = ?",$user_group_id);
        $settings_ar = $ss_table->fetchAll($select)->toArray();
        $settings = array();
        foreach($settings_ar as $ar )
        {
             $settings[$ar['name']] = $ar['default_value'];  
        }
        return $settings;
    }
    public function getAmountSeller($user_id){     
        $lst_table = Engine_Api::_()->getDbTable('lists', 'mp3music');  
        $lst_name  = $lst_table->info('name');    
        $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
        $as_name  = $as_table->info('name');
        $ab_table = Engine_Api::_()->getDbTable('albums', 'mp3music');
        $ab_name  = $ab_table->info('name');
        $s_table = Engine_Api::_()->getDbTable('singers', 'mp3music');
        $s_name  = $s_table->info('name');
        $select = $lst_table->select()
                    ->from('engine4_mp3music_lists as lst',array('count(*) as count','lst.dl_song_id','lst.dl_album_id'))
                    ->joinLeft($as_name,"$as_name.song_id = lst.dl_song_id",'')
                    ->joinLeft($s_name,"$s_name.singer_id = $as_name.singer_id",'')
                    ->joinLeft($ab_name,"$ab_name.album_id = lst.dl_album_id",'')
                    ->where("$ab_name.user_id= ?",$user_id)
                    ->orWhere(' 0 < ((SELECT count(*) from engine4_mp3music_album_songs als1 LEFT JOIN engine4_mp3music_albums as ma1 ON als1.album_id = ma1.album_id where ma1.user_id='.$user_id.' and als1.song_id = lst.dl_song_id))')
                    ->group('lst.dl_song_id')
                    ->group('lst.dl_album_id')   
                    ;
         $result =   $lst_table->fetchAll($select)->toArray();
         return count($result);
    }
    public function getHistorySellerSelect($params){
            $tt_table  = Engine_Api::_()->getDbTable('transactionTrackings', 'mp3music');
            $tt_name   = $tt_table->info('name');
            $as_table = Engine_Api::_()->getDbTable('albumSongs', 'mp3music');
            $as_name  = $as_table->info('name');
            $ab_table = Engine_Api::_()->getDbTable('albums', 'mp3music');
            $ab_name  = $ab_table->info('name');
            $s_table = Engine_Api::_()->getDbTable('singers', 'mp3music');
            $s_name  = $s_table->info('name');
            $select   = $tt_table->select()->setIntegrityCheck(false)
                        ->from($tt_table,array('count(*) as count',"$tt_name.transactiontracking_id"))
                        ->joinLeft($as_name,"$as_name.song_id = $tt_name.item_id and $tt_name.item_type = 'song'",array("$as_name.*","TRUNCATE($as_name.filesize/1024/1024,2) as sizemb"))
                        ->joinLeft($s_name,"$s_name.singer_id = $as_name.singer_id","$s_name.title as singer_title")
                        ->joinLeft($ab_name,"$ab_name.album_id = $tt_name.item_id and $tt_name.item_type = 'album'",array("$ab_name.user_id","$ab_name.title as album_title","$ab_name.album_id as album"))
                        ->where("$ab_name.user_id = ? ",$params["user_id"])
                        ->orWhere('0 < ((SELECT count(*) from engine4_mp3music_album_songs als1 LEFT JOIN engine4_mp3music_albums as ma1 ON als1.album_id = ma1.album_id where ma1.user_id='.$params["user_id"].' and als1.song_id = engine4_mp3music_transaction_trackings.item_id and engine4_mp3music_transaction_trackings.item_type='.'"song"'.'))')
                        ->group("$tt_name.item_id");
          return $select;
    }
    public function getHistorySeller($params){
        $sellerPaginator = Zend_Paginator::factory(Mp3music_Api_Account::getHistorySellerSelect($params));
        if( !empty($params['page']) )
        {
          $sellerPaginator->setCurrentPageNumber($params['page']);
        }
        if( !empty($params['limit']) )
        {
          $sellerPaginator->setItemCountPerPage($params['limit']);
        }   
        return $sellerPaginator;
    }
    public function insertRequest($vals=array()){
        $req = Engine_Api::_()->getDbtable('paymentRequests', 'mp3music')->createRow();  
        $req->request_user_id = $vals['request_user_id'];
        $req->request_payment_acount_id = $vals['request_payment_acount_id'];
        $req->request_amount = $vals['request_amount'];
        $req->request_status = $vals['request_status'];
        $req->request_reason = $vals['request_reason'];
        $req->request_date = $vals['request_date'];
        $req->save(); 
        return 1;
    }
    public function getTotalRequest($request_user_id){
        $table  = Engine_Api::_()->getDbtable('paymentRequests', 'mp3music');
        $select = $table->select() ->setIntegrityCheck(false)
                        ->from('engine4_mp3music_payment_requests',array('sum(request_amount) as totalrequest','request_user_id')) 
                        ->where('request_user_id = ?',$request_user_id)
                        ->where('request_status = 0')
                        ->group('request_user_id');
        $result =   $table->fetchAll($select)->toArray();
        return @$result[0]['totalrequest']; 
    }
    public function updateinfo($avals=array()){
        $user   = Engine_Api::_()->user()->getViewer();
        $user->displayname = $avals['displayname'];
        $user->status = $avals['status'];
        $user->email = $avals['email'];
        return $user->save();   
    }
    public function updateusername_account($request_user_id,$account_username){ 
        $table  = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music');
        $data = array(
            'account_username' => $account_username
        );
        $where = $table->getAdapter()->quoteInto('user_id = ?', $request_user_id);
        return $table->update($data, $where);
    }
    public function insertAccount($results = array()){
        $account = Engine_Api::_()->getDbtable('paymentAccounts', 'mp3music')->createRow();  
        $account->account_username = $results['account_username'];
        $account->total_amount = 0;
        $account->account_status = 1;
        $account->payment_type = 2;
        $account->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $account->save(); 
        return 1;
    }
}   
?>
