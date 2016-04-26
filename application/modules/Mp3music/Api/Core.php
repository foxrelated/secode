<?php
class Mp3music_Api_Core extends Core_Api_Abstract
{

	function updateTotalAmount($request_id,$total_amount,$is_request = true,$prefix)
	{
		$account = $this -> getFinanceAccount($request_id,null,$prefix);
		if($account)
		{
			$paTable = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
			$data = array(
				'total_amount' => $total_amount + $account['total_amount'],
			);
			$where = $paTable->getAdapter()->quoteInto('user_id = ?', $account['user_id']);
			$paTable->update($data, $where);

		}
	}
	function getSettingsSelling($user_group_id,$prefix)
	{
		$sTable = Engine_Api::_()->getDbtable('sellingSettings', 'mp3music');
		$sselect = $sTable->select()->where('user_group_id = ?', $user_group_id);
		$sresults = $sTable->fetchAll($sselect);
		$settings_ar2 = array();
		if($sresults)
		{
			foreach ($sresults as $result) {
				$settings_ar2[] =  array(
					'sellingsetting_id'=>$result->sellingsetting_id,
					'user_group_id'=>$result->user_group_id,
					'module_id'=>$result->module_id,
					'name'=>$result->name,
					'default_value'=>$result->default_value,
					'params'=>$result->params,
				);
			}

			if(count($settings_ar2))
			{
				$settings = array();
				foreach($settings_ar2 as $ar )
				{
					$settings[$ar['name']] = $ar['default_value'];
				}
				return $settings;
			}


		}
		return null;

	}
	function getGroupUser($user_id,$prefix)
	{
		$userTable = Engine_Api::_()->getItemTable('user');
		$uselect = $userTable->select()->where('user_id = ?', $user_id);
		$uresult = $userTable->fetchRow($uselect);
		if($uresult -> getIdentity())
		{
			return $uresult -> level_id;
		}
		return 0;
	}
	function getFinanceAccount($user_id = null,$payment_type = null,$prefix)
	{
		$table = Engine_Api::_()->getDbTable('paymentAccounts', 'mp3music');
		$select = $table->select();
		if($user_id) {
			$select->where('user_id = ?', $user_id);
		}
		if($payment_type) {
			$select->where('payment_type = ?', $payment_type);
		}
		$result = $table->fetchRow($select);
		if($result -> getIdentity()){
			$acc = array(
				'paymentaccount_id'=>$result->paymentaccount_id,
				'account_username'=>$result->account_username,
				'account_password'=>$result->account_password,
				'user_id'=>$result->user_id,
				'payment_type'=>$result->payment_type,
				'is_save_password'=>$result->is_save_password,
				'total_amount'=>$result->total_amount,
				'last_check_out'=>$result->last_check_out,
				'account_status'=>$result->account_status,
			);
			return $acc;
		}
		return null;
	}
	function saveTrackingPayIn($bill,$type,$prefix)
	{
		$ttTable = Engine_Api::_()->getDbtable('transactionTrackings','mp3music');
		switch($type)
		{
			case 'bill':
			default:
				$bill_details = unserialize($bill['params']);
				$acc = $this -> getFinanceAccount($bill_details['user_id'],null,$prefix);
				foreach ($bill_details['items'] as $item)
				{
					$ttTable->insert(array(
						"transaction_date" => $bill['date_bill'],
						"user_seller" => $item['owner_id'],
						"user_buyer" => $bill_details['user_id'],
						"item_id" => $item['item_id'],
						"item_type" => $item['type'],
						"amount" => $item['amount'],
						"account_seller_id" => $item['account_id'],
						"account_buyer_id" => $acc['paymentaccount_id'],
						"transaction_status" => $bill['bill_status'],
						"params" => "buy",
					));

				}

				break;
		}
	}
	function updateHistories($bill,$type,$timestamp,$prefix)
	{
		switch($type)
		{
			case 'bill':
			default:
				$bill_details = unserialize($bill['params']);

				$total = $bill_details['total_amount'];
				$number_songs = 0;
				$number_albums = 0;
				foreach ($bill_details['items'] as $it)
				{
					if ($it['type'] =='song')
					{
						$number_songs++;
					}
					if ($it['type'] =='album')
					{
						$number_albums++;
					}
				}

				//get History
				$historysTable = Engine_Api::_()->getDbtable('sellingHistorys', 'mp3music');
				$hselect = $historysTable->select()->where('selling_datetime = ?', $timestamp);
				$result = $historysTable->fetchRow($hselect);

				$history = null;

				if($result)
				{
					//$his = mysql_fetch_row($result);
					$history = array(
						'sellinghistory_id'=>$result->sellinghistory_id,
						'selling_datetime'=>$result->selling_datetime,
						'selling_total_upload_songs'=>$result->selling_total_upload_songs,
						'selling_total_download_songs'=>$result->selling_total_download_songs,
						'selling_sold_songs'=>$result->selling_sold_songs,
						'selling_sold_albums'=>$result->selling_sold_albums,
						'selling_final_new_account'=>$result->selling_final_new_account,
						'selling_transaction_succ'=>$result->selling_transaction_succ,
						'selling_transaction_fail'=>$result->selling_transaction_fail,
						'selling_total_amount'=>$result->selling_total_amount,
						'params'=>$result->params,
					);
				}

				$params =  $this -> getParamHistory(array('sold_songs'=>$number_songs,
					'sold_albums'=>$number_albums,
					'total_amount'=>$total,
					'transaction_succ'=>$bill['bill_status']));

				if ($history == null)
				{
					//insert new history
					$historysTable->insert(array(
						'selling_datetime'=>$timestamp,
						'selling_total_upload_songs'=>$params['selling_total_upload_songs'],
						'selling_total_download_songs'=>$params['selling_total_download_songs'],
						'selling_sold_songs'=>$params['selling_sold_songs'],
						'selling_sold_albums'=>$params['selling_sold_albums'],
						'selling_final_new_account'=>$params['selling_final_new_account'],
						'selling_transaction_succ'=>$params['selling_transaction_succ'],
						'selling_transaction_fail'=>$params['selling_transaction_fail'],
						'selling_total_amount'=>$params['selling_total_amount'],
						'params'=>$params['params'],
					));


				}
				else
				{
					//update infor
					$params = $history;
					$params['selling_sold_songs'] = $params['selling_sold_songs']+  $number_songs;
					$params['selling_sold_albums'] = $params['selling_sold_albums']+  $number_albums;
					$params['selling_total_amount'] = $params['selling_total_amount']+  $total ;
					$params['selling_transaction_succ'] = $params['selling_transaction_succ']+  $bill['bill_status'] ;
					$where = $historysTable->getAdapter()->quoteInto('sellinghistory_id = ?',$history['sellinghistory_id']);
					$data = array(
						'selling_sold_songs'=>$params['selling_sold_songs'],
						'selling_sold_albums'=>$params['selling_sold_albums'],
						'selling_transaction_succ'=>$params['selling_transaction_succ'],
						'selling_total_amount'=>$params['selling_total_amount'],
					);
					$historysTable->update($data, $where);
				}

				break;
		}
	}
	function getParamHistory($object)
	{
		$params = array();
		if (isset($object['upload_songs']))
		{
			$params['selling_total_upload_songs']  = $object['upload_songs'];
		}
		else
		{
			$params['selling_total_upload_songs'] = 0;
		}
		if (isset($object['download_songs']))
		{
			$params['selling_total_download_songs']  = $object['download_songs'];
		}
		else
		{
			$params['selling_total_download_songs'] = 0;
		}
		if (isset($object['sold_songs']))
		{
			$params['selling_sold_songs']  = $object['sold_songs'];
		}
		else
		{
			$params['selling_sold_songs'] = 0;
		}
		if (isset($object['sold_albums']))
		{
			$params['selling_sold_albums']  = $object['sold_albums'];
		}
		else
		{
			$params['selling_sold_albums'] = 0;
		}
		if (isset($object['new_accounts']))
		{
			$params['selling_final_new_account']  = $object['new_accounts'];
		}
		else
		{
			$params['selling_final_new_account'] = 0;
		}
		if (isset($object['transaction_succ']))
		{
			$params['selling_transaction_succ']  = $object['transaction_succ'];
		}
		else
		{
			$params['selling_transaction_succ'] = 0;
		}
		if (isset($object['transaction_fail']))
		{
			$params['selling_transaction_fail']  = $object['transaction_fail'];
		}
		else
		{
			$params['selling_transaction_fail'] = 0;
		}
		if (isset($object['total_amount']))
		{
			$params['selling_total_amount']  = $object['total_amount'];
		}
		else
		{
			$params['selling_total_amount'] = 0;
		}
		if (isset($object['params']))
		{
			$params['params']  = serialize($object['params']);
		}
		else
		{
			$params['params'] = '';
		}
		return $params;


	}
	function updateBillStatus($prefix,$bill,$status)
	{
		$billTable = Engine_Api::_()->getDbTable('bills', 'mp3music');
		$data = array(
			'bill_status' => 1
		);
		$where = $billTable->getAdapter()->quoteInto('bill_id = ?', $bill['bill_id']);
		$billTable->update($data, $where);

	}
	function moveItems2DownloadList($items,$prefix,$user_id)
	{

		$listTable = Engine_Api::_()->getDbTable('lists','mp3music');
		foreach($items as $key=>$value)
		{
			if ($value['type'] == 'song')
			{
				$row = $listTable -> createRow();
				$row -> setFromArray(array(
					'dl_song_id' => $value['item_id'],
					'dl_album_id' => '0',
					'user_id'	=> $user_id,
				));
				$row -> save();
			}
			if ($value['type'] == 'album')
			{
				$row = $listTable -> createRow();
				$row -> setFromArray(array(
					'dl_song_id' => '0',
					'dl_album_id' => $value['item_id'],
					'user_id'	=> $user_id,
				));
				$row -> save();
			}
		}
	}

	public function processPayment($order){

		$sercurity = $order -> security_code;
		$invoice = $order -> invoice_code;

		$file = APPLICATION_PATH . '/application/settings/database.php';
		$options = include $file;
		$prefix = $options['tablePrefix'];

		//get bill
		$Bills  =  new Mp3music_Model_DbTable_Bills;
		$select =  $Bills->select()->where('sercurity=?', $sercurity)->where('invoice=?', $invoice);
		$bill =  $Bills->fetchRow($select);
		if($bill)
		{
			//update status of bill
			$this -> updateBillStatus($prefix,$bill,1);

			$cartitem = unserialize($bill['params']);
			$this -> moveItems2DownloadList($cartitem['items'],$prefix,$bill['user_id']);
			//save to history
			$type = 'bill';
			$date = date('Y-m-d');
			$arrtoDate = explode('-',$date);
			$timestamp = mktime(12,0,0,$arrtoDate[1],$arrtoDate[2],$arrtoDate[0]);
			$bill['bill_status'] = 1;
			$this -> updateHistories($bill,$type,$timestamp,$prefix);
			//saveTracking
			$this -> saveTrackingPayIn($bill,$type,$prefix);

			//pay for owner of item
			$pta = array();
			$totsl = 0;
			foreach($cartitem['items'] as $itc)
			{
				if ( isset($pta[$itc['owner_id']]))
				{
					$pta[$itc['owner_id']] = $pta[$itc['owner_id']] + $itc['amount'];
				}
				else
				{
					$pta[$itc['owner_id']] = $itc['amount'];
				}
				$totsl += $itc['amount'];
			}
			foreach($pta as $key=>$value)
			{
				$user_group_id = $this -> getGroupUser($key,$prefix);
				$settings = $this -> getSettingsSelling($user_group_id,$prefix);

				if ( !isset($settings['comission_fee']))
				{
					$fee = 0;
				}
				else
				{
					$fee = $settings['comission_fee'];
				}
				$coupon = $cartitem['coupon_code']['value'];
				$coupon = $coupon/$totsl;
				$coupon = round($coupon,2);
				$val = ($value-$coupon*$value);
				$fee = $fee*$val/100;
				$fee = round($fee,2);
				$valuer = $val-$fee;
				$this -> updateTotalAmount($key,$valuer,false,$prefix)   ;

				// Affiliate integration
				$module = 'ynaffiliate';
				$modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
				$mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
				$module_result = $modulesTable->fetchRow($mselect);
				if(count($module_result) > 0)
				{
					$affi_params = array();
					$affi_params['module'] = 'mp3music';
					$affi_params['user_id'] = $bill['user_id'];
					$affi_params['rule_name'] = 'buy_mp3music';
					$affi_params['total_amount'] = $totsl;
					$affi_params['currency'] = 'USD';
					Engine_Hooks_Dispatcher::getInstance()->callEvent('onPaymentAfter', $affi_params);
				}

				// User credit integration
				$module = 'yncredit';
				$mselect = $modulesTable->select()->where('enabled = ?', 1)->where('name  = ?', $module);
				$module_result = $modulesTable->fetchRow($mselect);
				if(count($module_result) > 0)
				{
					$params = array();
					$params['user_id'] = $bill['user_id'];
					$params['rule_name'] = 'buy_mp3music';
					$params['total_amount'] = $totsl;
					Engine_Hooks_Dispatcher::getInstance()->callEvent('onPurchaseItemAfter', $params);
				}
			}
		}
	}

	public function getGateway($gateway_id)
	{
		return $this -> getPlugin($gateway_id) -> getGateway();
	}

	public function getPlugin($gateway_id)
	{
		if (null === $this -> _plugin)
		{
			if (null == ($gateway = Engine_Api::_() -> getItem('payment_gateway', $gateway_id)))
			{
				return null;
			}
			Engine_Loader::loadClass($gateway -> plugin);
			if (!class_exists($gateway -> plugin))
			{
				return null;
			}
			$class = str_replace('Payment', 'Mp3music', $gateway -> plugin);

			Engine_Loader::loadClass($class);
			if (!class_exists($class))
			{
				return null;
			}

			$plugin = new $class($gateway);
			if (!($plugin instanceof Engine_Payment_Plugin_Abstract))
			{
				throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' . 'implement Engine_Payment_Plugin_Abstract', $class));
			}
			$this -> _plugin = $plugin;
		}
		return $this -> _plugin;
	}

	//Get all artist
	public function getArtistRows($limit = null)
	{
		$arrArtist = array();
		$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
		if ($allow_artist)
		{
			$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
			$ab_name = $ab_table -> info('name');
			$select = $ab_table -> select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART) -> setIntegrityCheck(false);
			$select -> from('engine4_users', array(
				"DISTINCT(engine4_users.user_id)",
				"engine4_users.displayname"
			)) -> join($ab_name, "$ab_name.user_id = engine4_users.user_id") -> order("Count($ab_name.album_id) DESC ") -> group("engine4_users.user_id");
			if ($limit)
				$select -> limit($limit);
			$arrArtist = $ab_table -> fetchAll($select) -> toArray();
		}
		else
		{
			$table = Engine_Api::_() -> getDbtable('artists', 'mp3music');
			$select = $table -> select() -> order('artist_id ASC');
			if ($limit)
				$select -> limit($limit);
			$arrArtist = $table -> fetchAll($select);
		}
		return $arrArtist;
	}

	//check version of SE
	public function checkVersionSE()
	{
		$c_table = Engine_Api::_() -> getDbTable('modules', 'core');
		$c_name = $c_table -> info('name');
		$select = $c_table -> select() -> where("$c_name.name LIKE ?", 'core') -> limit(1);

		$row = $c_table -> fetchRow($select) -> toArray();
		$strVersion = $row['version'];
		$intVersion = (int)str_replace('.', '', $strVersion);
		return $intVersion >= 410 ? true : false;
	}

	//get service
	public function getDefaultService()
	{
		$serviceTable = Engine_Api::_() -> getDbtable('services', 'storage');
		$nameService = $serviceTable -> info('name');
		$select = $serviceTable -> select() -> where("$nameService.servicetype_id = ?", 1) -> where("$nameService.enabled = ?", 1) -> where("$nameService.default = ?", 1);
		return $serviceTable -> fetchRow($select);
	}

	//Create album song
	public function createSong($file, $params = array())
	{
		if (is_array($file))
		{
			if (!is_uploaded_file($file['tmp_name']))
			{
				throw new Engine_Exception('Invalid upload or file too large');
			}
			$filename = $file['name'];
		}
		else
			if (is_string($file))
			{
				$filename = $file;
			}
			else
			{
				throw new Engine_Exception('Invalid upload or file too large');
			}

		// Check file extension
		if (!preg_match('/\.(mp3|m4a|aac|mp4)$/iu', $filename))
		{
			throw new Engine_Exception('Invalid file type');
		}
		// upload to storage system
		$params = array_merge(array(
			'type' => 'song',
			'name' => $filename,
			'parent_type' => 'mp3music_song',
			'parent_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity(),
			'user_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity(),
			'extension' => substr($filename, strrpos($filename, '.') + 1),
		), $params);


		$song = Engine_Api::_() -> storage() -> create($file, $params);

		//Create file trial
		if ($this -> getDefaultService())
		{
			$file_path = $song -> storage_path;
		}
		else
		{
			$file_path = $song -> map();
		}
		$tmp_file = APPLICATION_PATH . '/public/temporary/' . $file['name'];
		copy($file_path, $tmp_file);

		$size = filesize($tmp_file);
		if ($size)
		{
			$percent = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.timePreviewSong', 30);
			$preview = 100 / $percent;
			// Preview time of 30%

			$handle = fopen($tmp_file, 'r');
			$content = fread($handle, $size);

			$length = strlen($content);

			$length = round($length / $preview);
			$content = substr($content, 0, $length);

			$filepath_trial = "public/mp3music_song/" . md5($song -> storage_path) . ".mp3";
			$fp = fopen($filepath_trial, 'w');
			fwrite($fp, $content);
			fclose($fp);

			@unlink($tmp_file);
			//end
		}

		return $song;
	}

	/**
	 *  Get Select
	 */
	//get list Cat to manage
	public function getCatSelect($params = array())
	{
		$table = Engine_Api::_() -> getDbtable('cats', 'mp3music');
		$select = $table -> select() -> where('parent_cat = ?', $params['parent_cat']) -> order('title ASC');
		return $select;
	}

	//Select albums
	public function getAlbumSelect($params = array())
	{
		$ab_table = Engine_Api::_() -> getDbTable('albums', 'mp3music');
		$ab_name = $ab_table -> info('name');

		$select = $ab_table -> select() -> from($ab_table) -> group("$ab_name.album_id");
		// get all albums have updated from wall
		if (!empty($params['wall']))
			$select -> where('composer = 1');
		// get all albums have updated from user
		if (!empty($params['user']))
		{
			if (is_object($params['user']))
				$select -> where('user_id = ?', $params['user'] -> getIdentity());
			elseif (is_numeric($params['user']))
				$select -> where('user_id = ?', $params['user']);
		}
		else
			if (empty($params['admin']))
			{
				$select -> where('search = 1');
			}
		$select -> where('is_delete = 0');
		// SORT
		if (!empty($params['sort']))
		{
			$sort = $params['sort'];
			if ('recent' == $sort)
				$select -> order('creation_date DESC');
			elseif ('popular' == $sort)
				$select -> order("$ab_name.play_count DESC");
		}
		// STRING SEARCH
		if (!empty($params['title']))
		{
			$key = stripslashes($params['title']);
			$select -> where("$ab_name.title LIKE ?", "%{$key}%");
		}
		if (!empty($params['owner']))
		{
			$key = stripslashes($params['owner']);
			$select -> join('engine4_users as u', "u.user_id = $ab_name.user_id", '') -> where("u.displayname LIKE ?", "%{$key}%");
		}
		return $select;
	}

	//Select playlists
	public function getPlaylistSelect($params = array())
	{
		$p_table = Engine_Api::_() -> getDbTable('playlists', 'mp3music');
		$p_name = $p_table -> info('name');
		$select = $p_table -> select() -> from($p_table) -> group("$p_name.playlist_id");
		// USER SEARCH
		if (!empty($params['user']))
		{
			if (is_object($params['user']))
				$select -> where('user_id = ?', $params['user'] -> getIdentity());
			elseif (is_numeric($params['user']))
				$select -> where('user_id = ?', $params['user']);
		}
		else
			if (empty($params['admin']))
			{
				$select -> where('search = 1');
			}
		// SORT
		if (!empty($params['sort']))
		{
			$sort = $params['sort'];
			if ('recent' == $sort)
				$select -> order('creation_date DESC');
			elseif ('popular' == $sort)
				$select -> order("$p_name.play_count DESC");
		}
		// STRING SEARCH
		if (!empty($params['search']))
			$select -> where("$p_name.title LIKE ?", "%{$params['search']}%");
		if (!empty($params['title']))
			$select -> where("$p_name.title LIKE ?", "%{$params['title']}%");
		if (!empty($params['owner']))
		{
			$select -> join('engine4_users as u', "u.user_id = $p_name.user_id", '') -> where("u.displayname LIKE ?", "%{$params['owner']}%");
		}
		return $select;
	}

	/**
	 * Get Paginator
	 */
	//Paging Category
	public function getCatPaginator($params = array())
	{
		$catPaginator = Zend_Paginator::factory($this -> getCatSelect($params));
		if (!empty($params['page']))
		{
			$catPaginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$catPaginator -> setItemCountPerPage($params['limit']);
		}
		return $catPaginator;
	}

	//Paging Album
	public function getAlbumPaginator($params = array())
	{
		$albumPaginator = Zend_Paginator::factory($this -> getAlbumSelect($params));
		if (!empty($params['page']))
		{
			$albumPaginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$albumPaginator -> setItemCountPerPage($params['limit']);
		}
		return $albumPaginator;
	}

	//Paging Playlist
	public function getPaginator($params = array())
	{
		$paginator = Zend_Paginator::factory($this -> getPlaylistSelect($params));
		if (!empty($params['page']))
		{
			$paginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$paginator -> setItemCountPerPage($params['limit']);
		}
		return $paginator;
	}

	//Paging song
	public function getSongPaginator($params = array())
	{
		$songPaginator = Zend_Paginator::factory(Mp3music_Model_Album::getListSong($params));
		if (!empty($params['page']))
		{
			//echo $params['page'];
			$songPaginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{

			$songPaginator -> setItemCountPerPage($params['limit']);
		}
		return $songPaginator;
	}

	//Paging top playlist
	public function getTopPlaylistPaginator($params = array())
	{
		$playlistPaginator = Zend_Paginator::factory(Mp3music_Model_Playlist::getPlaylists($params));
		if (!empty($params['page']))
		{
			$playlistPaginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$playlistPaginator -> setItemCountPerPage($params['limit']);
		}
		return $playlistPaginator;
	}

	//paging new album
	public function getNewAlbumPaginator($params = array())
	{
		$newAlbumPaginator = Zend_Paginator::factory(Mp3music_Model_Album::getAlbums());
		if (!empty($params['page']))
		{
			$newAlbumPaginator -> setCurrentPageNumber($params['page']);
		}
		if (!empty($params['limit']))
		{
			$newAlbumPaginator -> setItemCountPerPage($params['limit']);
		}
		return $newAlbumPaginator;
	}

	/**
	 * Get all
	 */
	//Get all albums
	public function getAlbumRows($params = array())
	{
		return Engine_Api::_() -> getDbTable('albums', 'mp3music') -> fetchAll($this -> getAlbumSelect($params));
	}

	//Get all playlists
	public function getPlaylistRows($params = array())
	{
		$playlists = Engine_Api::_() -> getDbTable('playlists', 'mp3music') -> fetchAll($this -> getPlaylistSelect($params));
		$temp_playlist = array();
		foreach ($playlists as $playlist)
		{
			$flag = true;
			foreach ($playlist->getPSongs() as $song)
			{
				if ($song -> album_song_id == $params['song_id'])
					$flag = false;
			}
			if ($flag == true)
				$temp_playlist[] = $playlist;
		}
		return $temp_playlist;
	}

	public function checkDownload($obj, $type = null)
	{
		$user = Engine_Api::_() -> user() -> getViewer();
		$user_id = $user -> getIdentity();
		$album = null;
		if ($type == 'song')
		{
			$song = $obj;
			$album_id = $song -> album_id;
			$album = Engine_Api::_() -> getItem('mp3music_album', $album_id);
			$song_id = $song -> song_id;

			//check song download list
			$l_table = Engine_Api::_() -> getDbTable('lists', 'mp3music');
			$l_name = $l_table -> info('name');
			$select = $l_table -> select() -> from($l_table, array("$l_name.list_id")) -> where("$l_name.user_id = ? ", $user_id) -> where("($l_name.dl_song_id = $song_id)");
			$results = $l_table -> fetchAll($select);
			if (Count($results) > 0)
				return true;

			//check album download list
			$l_table = Engine_Api::_() -> getDbTable('lists', 'mp3music');
			$l_name = $l_table -> info('name');
			$select = $l_table -> select() -> from($l_table, array("$l_name.list_id")) -> where("$l_name.user_id = ? ", $user_id) -> where("($l_name.dl_album_id = $album_id)");
			$results = $l_table -> fetchAll($select);
			if (Count($results) > 0)
				return true;

		}
		else
			if ($type == 'album')
			{
				$album = $obj;
				$album_id = $album -> album_id;
				//check download list
				$l_table = Engine_Api::_() -> getDbTable('lists', 'mp3music');
				$l_name = $l_table -> info('name');
				$select = $l_table -> select() -> from($l_table, array("$l_name.list_id")) -> where("$l_name.user_id = ? ", $user_id) -> where("($l_name.dl_album_id = $album_id)");
				$results = $l_table -> fetchAll($select);
				if (Count($results) > 0)
					return true;
			}

		//don't have buy song or album
		$allowed_download = (bool)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'is_download');
		if ($album -> is_download == 0 || $allowed_download == false)
			return false;
		else
			if ($type == 'song' && $song -> price <= 0)
				return true;
			else
				if ($album -> price <= 0 && $type == 'album')
					return true;

		return false;
	}

	/**
	 * Create song trial
	 * params $path full, $path_trial
	 */
	public function createSongTrial($path, $path_trial)
	{
		//Create file trial
		$tmp_file = APPLICATION_PATH . '/public/temporary/' . time(). '.mp3';
		copy($path, $tmp_file);

		$size = filesize($tmp_file);
		if ($size)
		{
			$preview_time = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.timePreviewSong', 30);
			$preview = 100 / $preview_time;
			// Preview time of 30%

			$handle = fopen($tmp_file, 'r');
			$content = fread($handle, filesize($tmp_file));

			$length = strlen($content);

			$length = round($length / $preview);
			$content = substr($content, 0, $length);

			$fp = fopen($path_trial, 'w');
			fwrite($fp, $content);
			fclose($fp);

			@unlink($tmp_file);
		}
	}

	//get list song for player
	public function getServiceSongs($album = null, $idsong = null)
	{
		$musiclist = $album -> getSongs($idsong);
		$songs = array();
		$user = Engine_Api::_()->user()->getViewer();
		foreach ($musiclist as $index => $music)
		{
			if(Engine_Api::_()->authorization()->isAllowed($music, $user, 'play'))
			{
				$path = $music -> getFilePath();
				if ($music -> price != 0)
				{
					$path_full = $path;
					$file = Engine_Api::_() -> getItem('storage_file', $music -> file_id);
					$path_preview = "public/mp3music_song/" . md5($file -> storage_path) . ".mp3";
					if (!file($path_preview))
					{
						$path = $this -> createSongTrial($path_full, $path_preview);
					}
					else
					{
						$path = $path_preview;
					}
				}
				$songs[$index]['filepath'] =  $path;

				$user = Engine_Api::_() -> user() -> getViewer();
				$user_id = $user -> getIdentity();
				$votes = Mp3music_Model_Rating::checkUservote($music -> song_id, $user_id);

				if (count($votes) > 0)
					$songs[$index]['vote'] = number_format($votes[0] -> rating);
				else
				{
					$votes_song = Mp3music_Model_Rating::getVotes($music -> song_id);
					$avgVote = 0;
					foreach ($votes_song as $vote_info)
					{
						$avgVote += $vote_info -> rating;
					}
					if (count($votes_song) > 0)
						$avgVote = floor($avgVote / count($votes_song));
					$songs[$index]['vote'] = $avgVote;
				}

				if (count($votes) > 0 || $user -> getIdentity() <= 0)
				{
					$songs[$index]['isvote'] = false;
				}
				else
				{
					$songs[$index]['isvote'] = true;
				}

				$allowed_download = (bool)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('mp3music_album', $user, 'is_download');
				if ($album -> is_download == 1 && $allowed_download == true && $music -> price == 0)
					$is_download = 'true';
				else
					$is_download = 'false';

				$songs[$index]['isdownload'] = $is_download;

				$string = $album -> getOwner() -> getTitle();

				$songs[$index]['artist'] = $string;
				$songs[$index]['albumname'] = $album -> title;
				if ($user -> getIdentity() > 0)
				{
					$songs[$index]['isadd'] = true;
				}
				else
				{
					$songs[$index]['isadd'] = false;
				}
				$songs[$index]['order'] = $music -> order;
				$songs[$index]['song_id'] = $music -> song_id;
				$songs[$index]['title'] = $music -> title;
				$songs[$index]['play_count'] = $music -> play_count;
				if ($user -> getIdentity() > 0)
				{
					$hiddencartsong = Mp3music_Api_Shop::getHiddenCartItem('song', $user -> getIdentity());
					$selling_settings = Mp3music_Api_Cart::getSettingsSelling($user -> level_id);
					$acc = Mp3music_Api_Cart::getFinanceAccount($album -> user_id);
					$cart = 3;
					if ($selling_settings['can_buy_song'] == 1)
					{
						if ($music -> price == 0 || $acc == null)
						{
							$cart = 0;
						}
						else if (in_array($music -> song_id, $hiddencartsong))
						{
							$cart = 1;
						}
					}
					else
					{
						$cart = 2;
					}
				}
				else
				{
					$cart = 2;
				}
				$songs[$index]['iscart'] = $cart;
				$songs[$index]['price'] = $music -> price;
			}
		}
		return $songs;
	}

}
?>
