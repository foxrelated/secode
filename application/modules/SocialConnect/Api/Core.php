<?php

class SocialConnect_Api_Core
{
	public function getCentralServiceUrl()
	{
		return 'http://openid.younetid.com/v4/connect/se4.php';
	}
	
	/**
	 * Check Social Bridge Plugin
	 */
	public function checkSocialBridgePlugin()
	{
		$module = 'socialbridge';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Check Contact Importer Plugin
	 */
	public function checkContactimporterPlugin()
	{
		$module = 'contactimporter';
		$modulesTable = Engine_Api::_() -> getDbtable('modules', 'core');
		$mselect = $modulesTable -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module);
		$module_result = $modulesTable -> fetchRow($mselect);
		if (count($module_result) > 0)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * build session data to start process
	 * @param array $data  data that response from centralize server
	 * @param string/int $identity
	 * @param string $service etc: facebook, twitter, ...
	 * @return void
	 * @throws Exceptions
	 */
	public function buildSessionSignupData($data, $identity, $service)
	{
		$standardSignup =  Engine_Api::_()->getApi('Settings','Core')->getSetting('socialconnect.standardsignup',true);
		 
		$account = array();
		$profile = array();

		if (isset($_SESSION['TemporaryProfileImg']))
		{
			unset($_SESSION['TemporaryProfileImg']);
			unset($_SESSION['TemporaryProfileImgProfile']);
			unset($_SESSION['TemporaryProfileImgSquare']);
		}

		$fields = $this -> getAccountFieldstructure();

		foreach ($fields as $field => $name)
		{
			if (isset($data[$field]))
			{
				$account[$field] = $data[$field];
			}
		}
		$fields = $this -> getProfileFieldStructure($service);
		
		foreach ($fields as $key => $type)
		{
			if (isset($data[$type]))
			{
				$profile[$key] = $data[$type];
			}
		}
		
		$_SESSION['User_Plugin_Signup_Account'] = array(
			'data' => $account,
			'active' => true
		);

		$_SESSION['User_Plugin_Signup_Fields'] = array(
			'data' => $profile,
			'active' => $standardSignup
		);
		
		$_SESSION['User_Plugin_Signup_Photo'] = array(
			'data' => array(),
			'active' => $standardSignup
		);
		
		$_SESSION['User_Plugin_Signup_Invite'] = array(
			'data' => array(),
			'active' => $standardSignup
		);
		
		$_SESSION['Contactimporter_Plugin_Signup_Invite'] = array(
			'data' => array(),
			'active' => $standardSignup
		);
		
		if (isset($data['photo_url']) && $data['photo_url'])
		{
			try
			{
				Engine_Api::_() -> getApi('Image', 'SocialConnect') -> _fetchImage($data['photo_url']);
			}
			catch(Exception $e)
			{
			}
		}
	}

	/**
	 * get account structure from table: engine4_users
	 * that will be mapped with data
	 * @return array
	 */
	public function getAccountFieldstructure()
	{
		return array(
			'email' => 'email',
			'username' => 'username',
			'displayname' => 'displayname',
			'status' => 'status',
			'locale' => 'locale',
			'timezone' => 'timezone',
			'language' => 'language',
		);
	}

	/**
	 * get data that mapped from custom fields, it's is diffucult version and we should continue work to make it's goto crazy way.
	 * @return array
	 */
	public function getProfileFieldStructure($service)
	{
		$service = $this -> getMapService($service);
		return Engine_Api::_() -> getDbTable('Fields', 'SocialConnect') -> getProfileFieldStructure($service);
	}

	/**
	 * drop all mapped account from user_id
	 * @param int $userId
	 * @return void
	 */
	public function dropAccount($userId)
	{

		$table = Engine_Api::_() -> getDbtable('Accounts', 'SocialConnect');
		$table -> delete("user_id='{$user_id}'");

		if (class_exists('User_Model_Db_Table_Facebook'))
		{
			$table = new User_Model_Db_Table_Facebook;
			$table -> delete('user_id=?', $user_id);
		}

		if (class_exists('User_Model_Db_Table_Twitter'))
		{
			$table = new User_Model_Db_Table_Twitter;
			$table -> delete('user_id=?', $user_id);
		}

	}

	/**
	 * @param string $email
	 * @return User_Model_User|NULL
	 */
	public function getUserByEmail($email)
	{
		$table = Engine_Api::_() -> getItemTable('user');
		$select = $table -> select() -> where('email=?', (string)$email);
		return $table -> fetchRow($select);
	}

	/**
	 * it's not neccessary to check if table facebook, twitter, jainrain is exists.
	 * get user id of revelant $identity and $service
	 * @param string/int $identity
	 * @param string $service etc: twitter, facebook, google,...
	 * @return User_Model_User | NULL
	 */
	public function getUserByIdentityAndService($identity, $service)
	{
		$user = NULL;
		if (!$user)
		{
			$table = Engine_Api::_() -> getDbtable('Accounts', 'SocialConnect');
			$select = $table -> select() -> where('identity=?', (string)$identity) -> where('service=?', (string)$service);
			$account = $table -> fetchRow($select);

			if (is_object($account) && $account -> user_id)
			{
				$user = Engine_Api::_() -> getItemTable('user') -> find($account -> user_id) -> current();
			}
		}

		if (!$user && $service == 'facebook')
		{
			if (class_exists('User_Model_DbTable_Facebook'))
			{
				$table = new User_Model_DbTable_Facebook;
				$account = $table -> fetchRow($table -> select() -> where('facebook_uid=?', $identity));
				if (is_object($account) && $account -> user_id)
				{
					$user = Engine_Api::_() -> getItemTable('user') -> find($account -> user_id) -> current();
				}
			}

		}

		if (!$user && $service == 'twitter')
		{
			if (class_exists('User_Model_DbTable_Twitter'))
			{
				$table = new User_Model_DbTable_Twitter;
				$account = $table -> fetchRow($table -> select() -> where('twitter_uid=?', $identity));
				if (is_object($account) && $account -> user_id)
				{
					$user = Engine_Api::_() -> getItemTable('user') -> find($account -> user_id) -> current();
				}
			}
		}

		return $user;
	}

	/**
	 * create mapped account to revelant process
	 * @param int $userId
	 * @param string $service
	 * @param string $identity
	 */
	public function createAccount($userId, $identity, $service, $data = array())
	{
		$table = Engine_Api::_() -> getDbtable('Accounts', 'SocialConnect');

		$table -> delete("identity='{$identity}' AND service='{$service}'");

		$table -> insert(array(
			'user_id' => $userId,
			'identity' => $identity,
			'service' => $service,
			'profile' => json_encode($data)
		));

		switch($service)
		{
			case 'twitter' :
				if (class_exists('User_Model_Db_Table_Twitter'))
				{
					$table = new User_Model_Db_Table_Twitter;
					$table -> delete('user_id=?', $user_id);
					$table -> insert(array(
						'user_id' => $user_id,
						'twitter_uid' => $identity,
						'twitter_token' => '',
						'twitter_secret' => ''
					));
				}
				break;
			case 'facebook' :
				if (class_exists('User_Model_Db_Table_Facebook'))
				{
					$table = new User_Model_Db_Table_Facebook;
					$table -> delete('user_id=?', $user_id);
					$table -> insert(array(
						'user_id' => $user_id,
						'facebook_uid' => $identity,
						'access_token' => '',
						'code' => '',
						'expires' => '',
					));
				}
				break;
		}
	}

	/**
	 * force login from $user
	 * quick login this method is predecated because we need process some other business rule after user logedin.
	 * @param User_Model_User
	 * @return void
	 */
	public function simpleLogin($user)
	{
		if (!$user)
		{
			return FALSE;
		}

		// Register login
		$auth = Zend_Auth::getInstance();

		$auth -> getStorage() -> write($user -> getIdentity());
	}

	/**
	 * get map service
	 * @return string avaialbe options
	 */
	public static function getMapService($service)
	{
		$oauths = array(
			"facebook",
			"twitter",
			"myspace",
			"google",
			"yahoo",
			"live",
			"hyves",
			"friendster",
			"linkedin",
			"flickr"
		);
		if (array_search($service, $oauths) === false)
		{
			return 'openid';
		}
		return $service;
	}

	/**
	 * get mapping field
	 */
	public function getServiceFields($service)
	{
		if (isset(self::$_serviceFields[$service]))
		{
			return self::$_serviceFields[$service];
		}
		return self::$_serviceFields['openid'];
	}

	/**
	 * @var array
	 */
	static $_serviceFields = array(
		'facebook' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'id' => 'Facebook UID',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'twitter' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'id' => 'Twitter UID',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'linkedin' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'id' => 'Twitter UID',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'google' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'openid' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'hyves' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'id' => 'Hyves UID',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
		'myspace' => array(
			'' => 'None',
			'website' => 'Website',
			'facebook' => 'facebook',
			'aim' => 'AIM',
			'gender' => 'Gender',
			'birthdate' => 'Birthdate',
			'about_me' => 'About',
			'twitter' => 'Twitter',
			'id' => 'MySpace UID',
			'displayname' => 'Name',
			'username' => 'Username',
			'language' => 'Language',
			'country' => 'Country',
			'email' => 'Email',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'about' => 'About Me',
			'status' => 'Status'
		),
	);
	public function getAccountLinked($user_id = 0, $service = "")
	{
		$table = Engine_Api::_() -> getDbtable('Accounts', 'SocialConnect');
		$select = $table->select()->where('user_id = ?', $user_id)->where('service = ?', $service);
		return $table->fetchRow($select);
	}
	
}
