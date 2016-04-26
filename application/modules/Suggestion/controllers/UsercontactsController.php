<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UsercontactsController.php (var) 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_UsercontactsController extends Core_Controller_Action_Standard
{
	
 
	//FUNCTION FOR GETTING GOOGLE CONTACTS OF THE REQUESTED USER.
  public function getgooglecontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Gmail/googleapi.php';
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =1;	
		$session->aolredirect = 0;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;
		$viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
   // the domain that you entered when registering your application for redirecting from google site.
	 $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true);
	//HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO GOOGLE SITE.FOR GETTING TOKEN.
    if (empty($_POST['task'])) {
      //CHECK IF ADMIN HAS SET THE THE GOOGLE API KEYS THERE:
      $google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
      if (!empty($google_Apikey)) {
        $google_redirect_URL = 'https://accounts.google.com/o/oauth2/auth?client_id=' . $google_Apikey . '&redirect_uri=' . urlencode($callback) . '&scope=' . urlencode('https://www.google.com/m8/feeds') . '&response_type=token';
      }
      else {
         $scope  = "http://www.google.com/m8/feeds/contacts/default/";
        $google_redirect_URL = Zend_Gdata_AuthSub::getAuthSubTokenUri($callback,urlencode($scope),
  0, 1);
      }
        
        header('location: ' . $google_redirect_URL);
    }
    
		//IF THE TASK IS TO SHOWING THE LIST OF FRIENDS.
		if (!empty($_POST['task']) && $_POST['task'] == 'get_googlecontacts') {
			//IF WE GET THE TOKEN MEANS GOOGLE HAS RESPOND SUCCESSFULLY.THIS IS ONE TIME USES TOKEN
			if (!empty($_POST['token'])) { 
			   //CHECK IF ADMIN HAS SET THE THE GOOGLE API KEYS THERE:
         $google_Apikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('google.apikey', '');
         if (empty($google_Apikey))
				   $token = urldecode($_POST['token']);
				 else
				   $token = $_POST['token'];   
       
				//CHECKING THE AUTHENTICITY OF REQUESTED USER EITHER THIS TOKEN IS VALID OR NOT.
				$result = GoogleContactsAuth ($token); 
				//$result = 1;
				if (!empty($result)) {
						$session->googleredirect = 0;
					//FETCHING THE ALL GOOGLE CONTACTS OF THIS USER.
					$GoogleContacts =  GoogleContactsAll($result, $token);
				
					//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
					if (!empty($GoogleContacts)) {
            $SiteNonSiteFriends = $this->parseUserContacts ($GoogleContacts);
						 
						if (!empty($SiteNonSiteFriends[0]))  {
							$this->view->task = 'show_sitefriend';
							$this->view->addtofriend = $SiteNonSiteFriends[0];
						}
						if (!empty($SiteNonSiteFriends[1])) { 
							$this->view->addtononfriend = $SiteNonSiteFriends[1];
						}
					}
					if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  	$this->view->errormessage = true;
					}
				}
			}
		}
  }
  
  
  //FUNCTION FOR GETTING FACEBOOK CONTACTS OF THE REQUESTED USER.
  public function getfacebookcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =0;
	  $session->facebookredirect =1;	
		$session->aolredirect = 0;
		$session->linkedinredirect = 0;
		$viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
   
	 $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true) . '?redirect_fbinvite=1';
	//HERE WE ARE CHECKING IF REQUEST IS NOT AN AJAX REQUEST THEN WE WILL REDIRECT TO FACEBOOK SITE.FOR GETTING TOKEN.
    if (empty($_POST['task'])) {  
      
      $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
  	  $facebook = $facebookInvite->getFBInstance();
    	$FBloginURL = $facebook->getLoginUrl(array(
          'redirect_uri' => $callback,
          'scope' => join(',', array(
            'email',
            'user_birthday',
            'user_status',
            'publish_stream',
            'status_update',
            'read_stream'
            //'offline_access',
         )),
    ));
     
 
      header('location: ' . $FBloginURL);
    }
    
		//IF THE TASK IS TO SHOWING THE LIST OF FRIENDS.
		if (!empty($_POST['task']) && $_POST['task'] == 'get_facebookcontacts') { 
		  $session->facebookredirect =0;
		  $facebookapi = new Seaocore_Api_Facebook_Facebookinvite();
			$friends_invite = $facebookapi->facebook_invitefriend($callback);
			echo Zend_Json::encode(array('friends_invite' => $friends_invite)); 
			exit();
		}
      exit();          
  }

  //FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function getyahoocontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
   	$session = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		// the domain that you entered when registering your application for redirecting from google site.
	 	$callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true);
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Yahoo/getreqtok.php';
		//STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM YAHOO.
		if (empty($_POST['oauth_verifier'])) {
			$session->windowlivemsnredirect = 0;		
			$session->yahooredirect = 1;
			$session->googleredirect = 0;	
			$session->aolredirect = 0;
			$session->facebookredirect =0;
			$session->linkedinredirect = 0;
			// Get the request token using HTTP GET and HMAC-SHA1 signature
			$retarr = get_request_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
																	$callback, false, true, true);
			
			if (! empty($retarr) ) { 
				unset($session->oauth_token_secret);	
				unset($session->oauth_token);				
				$session->oauth_token_secret = $retarr[3]['oauth_token_secret'];
				$session->oauth_token = $retarr[3]['oauth_token'];
				$redirecturl = urldecode($retarr[3]['xoauth_request_auth_url']);
				header('location: ' . $redirecturl);
			}
		}
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
		else if (!empty($_POST['oauth_verifier'])) {
			$session->redirect = 0;
			$request_token=$session->oauth_token;
			$request_token_secret=$session->oauth_token_secret;
			$oauth_verifier= $_POST['oauth_verifier'];
			//STEP:3 AFTER GETTING OAUTH VERIFIER AND OTHER TOKENS WE WILL AGAIN CALL YAHOO API TO GET OAUTH VERIFY SUCCESS TOKEN.
			$retarr = get_access_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                           $request_token, $request_token_secret,
                           $oauth_verifier, false, true, true);	
			
       if (!empty($retarr)) {
					$guid=$retarr[3]['xoauth_yahoo_guid'];
					$access_token=urldecode($retarr[3]['oauth_token']);
					$access_token_secret=$retarr[3]['oauth_token_secret'];
          // Call Contact API
					$YahooContacts = callcontact(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                      $guid, $access_token, $access_token_secret,
                      false, true);
           $session->yahooredirect = 0;      		
					//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
					if (!empty($YahooContacts)) {
            $SiteNonSiteFriends = $this->parseUserContacts ($YahooContacts);
						   
						if (!empty($SiteNonSiteFriends[0]))  {
							$this->view->task = 'show_sitefriend';
							$this->view->addtofriend = $SiteNonSiteFriends[0];
						}
						if (!empty($SiteNonSiteFriends[1])) { 
							$this->view->addtononfriend = $SiteNonSiteFriends[1];
						}
					}
					if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  	$this->view->errormessage = true;
					}
				}
			}
	}
	
	//FUNCTION FOR GETTING YAHOO CONTACTS OF THE REQUESTED USER.
  public function getlinkedincontactsAction () {
		ini_set('display_errors', TRUE);
		error_reporting(E_ALL);
   	$session = new Zend_Session_Namespace();
		$this->_helper->layout->disableLayout();
		
		// the domain that you entered when registering your application for redirecting from google site.
	 	$callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true);
		
		$API_CONFIG = array(
    'appKey'       => Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.apikey'),
	  'appSecret'    =>  Engine_Api::_()->getApi('settings', 'core')->getSetting('linkedin.secretkey'),
	  'callbackUrl'  => $callback 
  );

			$OBJ_linkedin = new Seaocore_Api_Linkedin_Linkedin($API_CONFIG); 
		//STEP:1 FIRST WE WILL GET REQUEST VALID OAUTH TOKEN OAUTH TOKEN SECRET FROM YAHOO.
		if (empty($_POST['oauth_verifier'])) {
			$session->windowlivemsnredirect = 0;		
			$session->linkedinredirect = 1;
			$session->googleredirect = 0;	
			$session->aolredirect = 0;
			$session->facebookredirect =0;
			$session->yahooredirect =0;
			// Get the request token using HTTP GET and HMAC-SHA1 signature
		  
		
			$response = $OBJ_linkedin->retrieveTokenRequest();
			
			if($response['success'] === TRUE) {
        // store the request token
        $_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
      
        // redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
        header('Location: ' . Seaocore_Api_Linkedin_Linkedin::_URL_AUTH . $response['linkedin']['oauth_token']);
      } else  { 
       echo Zend_Registry::get('Zend_Translate')->_("There are some problem in processing your request. Please try again after some time.");die;
     }
		}
    //STEP:2 AFTER GETTING REQUESTED OAUTH TOKE AND OAUTH TOKEN SECRET WE WILL GET OAUTH VERIFIER BY GRANTING ACCESS TO THIRD PARTY FOR FATCHING YAHOO CONTACTS.
		else if (!empty($_POST['oauth_verifier'])) {
			$session->redirect = 0;
			$response = $OBJ_linkedin->retrieveTokenAccess($_SESSION['oauth']['linkedin']['request']['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $_POST['oauth_verifier']);
		
			
			if($response['success'] === TRUE) { 
			    $session->linkedinredirect = 0;
          // the request went through without an error, gather user's 'access' tokens
          $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
          
          // set the user as authorized for future quick reference
          $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
            
          $Api_linkedin = new Seaocore_Api_Linkedin_Api();
          $LinkedinContacts = $Api_linkedin->retriveContacts ($OBJ_linkedin); 
          
          if (!empty($LinkedinContacts[0]))  {
						$this->view->task = 'show_sitefriend';
						$this->view->addtofriend = $LinkedinContacts[0];
					}
					if (!empty($LinkedinContacts[1])) { 
						$this->view->addtononfriend = $LinkedinContacts[1];
					}
					
					if (empty($LinkedinContacts[0]) && empty($LinkedinContacts[1])) {
				  	$this->view->errormessage = true;
					}
        
         
        } else {
          // bad token access
          echo  Zend_Registry::get('Zend_Translate')->_("There are some problem in retriving your contacts. Please try again after some time.");die;
        }
			}
	}
	
	//FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
	public function getwindowlivecontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$session = new Zend_Session_Namespace();
		$session->windowlivemsnredirect = 1;		
		$session->yahooredirect = 0;
		$session->googleredirect = 0;
		$session->aolredirect = 0;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;		
		$this->_helper->layout->disableLayout();
		// the domain that you entered when registering your application for redirecting from google site.
	  $callback = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true);
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Windowlive/contacts_fn.php';
		$cookie = @$_COOKIE[$COOKIE];
		//WE WILL INCLUDE THIS FILE AFTER WE WILL GET BACK FROM WINDOW LIVE SITE HAVING SUCCESSFULL AUTHENTICATION.THIS FILE IS USED TO SET THE COOKIES AFTER GETTING SUCCESSFULL AUTHENTICATION AND THEN REDIRECTING TO AT CALLBACK URL.
		if ($session->redirect) {
			include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Windowlive/delauth-handler.php';
		}
		//initialize Windows Live Libraries
		$wll = WindowsLiveLogin::initFromXml($KEYFILE);
		// If the raw consent token has been cached in a site cookie, attempt to
		// process it and extract the consent token.
		$token = null;
		if ($cookie) {
			$token = $wll->processConsentToken($cookie);
		}
		//Check if there's consent and, if not, redirect to the login page
		if ($token && !$token->isValid()) {
			$token = null;
		}
		if ($token==null) {
			$session->redirect = 1;
			$consenturl = $wll->getConsentUrl($OFFERS);	
			header( 'Location:'.$consenturl) ;
		}
    
    //HERE WE ARE CHECKING IF THE VALID TOKEN IS ALREADY COOKIED AND ACTIVE AND REQUEST IS NOT AJAX THEN WE WILL REDIRECT IT TO CALLBACK URL.
		if ($token && empty($_POST['task'])) {
			header("Location: $callback");
		}
   
		if (!empty ($_POST['task'])) {
		  $session->windowlivemsnredirect = 0;
		}
		
		//IF REQUEST IS AJAX FOR SHOWING WINDOW LIVE CONTACTS.
		if ($token) { 
			$WindowLiveContacts = get_people_array($token);
			$session->windowliveredirect = 0;		
			//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
			if (!empty($WindowLiveContacts)) {
				$SiteNonSiteFriends = $this->parseUserContacts ($WindowLiveContacts);
				if (!empty($SiteNonSiteFriends[0]))  {
					$this->view->task = 'show_sitefriend';
					$this->view->addtofriend = $SiteNonSiteFriends[0];
				}
				if (!empty($SiteNonSiteFriends[1])) { 
					$this->view->addtononfriend = $SiteNonSiteFriends[1];
				}
			}
			if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  $this->view->errormessage = true;
			}
		}
	}


	//FUNCTION FOR GETTING WINDOW LIVE  CONTACTS OF THE REQUESTED USER.
	public function getaolcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
		$this->_helper->layout->disableLayout();
		$session = new Zend_Session_Namespace();
    $session->windowlivemsnredirect = 0;		
	  $session->yahooredirect = 0;
	  $session->googleredirect =0;	
		$session->aolredirect = 1;
		$session->facebookredirect =0;
		$session->linkedinredirect = 0;	
		include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Aol/aolapi.php';
		if (!empty($session->aol_email)) {
			$AolContacts = getaolcontacts ($session->aol_email, $session->aol_password, false);
			//NOW WE WILL PARSE THE RESULT ACCORDING TO HOW MANY USERS ARE MEMBERS OF THIS SITE.
			if (!empty($AolContacts)) { 
			  if (!empty ($_POST['task'])) {
    		  $session->aolredirect = 0;
    		}
				$SiteNonSiteFriends = $this->parseUserContacts ($AolContacts);
				if (!empty($SiteNonSiteFriends[0]))  {
					$this->view->task = 'show_sitefriend';
					$this->view->addtofriend = $SiteNonSiteFriends[0];
				}
				if (!empty($SiteNonSiteFriends[1])) { 
					$this->view->addtononfriend = $SiteNonSiteFriends[1];
				}
			}
			if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
				  $this->view->errormessage = true;
			}
		}

  }


  //FUNTION FOR GETTING USERNAME AND PASSWORD OF AOL MAIL.
  public function aolloginAction() {
		$this->_helper->layout->disableLayout();
		$this->view->form = $form = new Suggestion_Form_Aollogin();
		if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
			include_once APPLICATION_PATH . '/application/modules/Seaocore/Api/Aol/aolapi.php';
	    $values = $form->getValues();
			$session = new Zend_Session_Namespace();
			$session->windowlivemsnredirect = 0;		
			$session->yahooredirect = 0;
			$session->googleredirect =0;	
			$session->aolredirect = 1;
			$session->facebookredirect =0;
			$session->linkedinredirect = 0;	
			$loginsuccess = getaolcontacts($values['email'], $values['password'], true);
			if ($loginsuccess) {
				$session->aol_email = $values['email'];
				$session->aol_password = $values['password'];		
				// the domain that you entered when registering your application for redirecting from google site.
				$callback = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array(), 'friends_suggestions_viewall', true) . '?redirect_aol=1';
				header('location:'. $callback);
			}
			else {
        $this->view->error = Zend_Registry::get('Zend_Translate')->_("Incorrect Username or Password");
			}
		}
  }
	
	
 //FUNCTION FOR PARSING USER CONTACTS IN 2 PARTS SITE MEMBERS AND NONSITE MEMBERS.
  public function parseUserContacts ($UserContacts) {
    $viewer =  Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		$table_user = Engine_Api::_()->getitemtable('user');
		$tableName_user = $table_user->info('name');
		$table_user_memberships = Engine_Api::_()->getDbtable('membership' , 'user');
		$tableName_user_memberships = $table_user_memberships->info('name');
		$SiteNonSiteFriends[] = '';
		foreach ($UserContacts as $values) {

			//FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
			$select = $table_user->select()
			->setIntegrityCheck(false)
			->from($tableName_user, array('user_id', 'displayname', 'photo_id'))
			->where('email = ?', $values['contactMail']);

			$is_site_members = $table_user->fetchAll($select);
      if (empty($user_id)) {
				if (!empty($is_site_members[0]->user_id)) {
					continue;
        }

      }
			//NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.
			if (!empty($is_site_members[0]->user_id) && $is_site_members[0]->user_id != $user_id) {
				$contact =  Engine_Api::_()->user()->getUser($is_site_members[0]->user_id);
				// check that user has not blocked the member
				if(!$viewer->isBlocked($contact)) {
					// The contact should not be my friend, and neither of us should have sent a friend request to the other.
					$select = $table_user_memberships->select()
					->setIntegrityCheck(false)
					->from($tableName_user_memberships, array('user_id'))
					->where($tableName_user_memberships . '.resource_id = ' . $user_id .' AND ' . $tableName_user_memberships . '.user_id = ' . $is_site_members[0]->user_id )
					->orwhere($tableName_user_memberships . '.resource_id = ' . $is_site_members[0]->user_id .' AND ' . $tableName_user_memberships . '.user_id = ' .$user_id );
					$already_friend = $table_user->fetchAll($select);
					
					//IF THIS QUERY RETURNS EMPTY RESULT MEANS THIS USER IS SITE MEMBER BUT NOT FRIEND OF CURRENTLY LOGGEDIN USER SO WE WILL SEND HIM TO FRIENDSHIP REQUEST.
					if (empty($already_friend[0]->user_id)) { 
						$SiteNonSiteFriends[0][] = $is_site_members;
					}
				}
			}
			//IF USER IS NOT SITE MEMBER .
			else if (empty($is_site_members[0]->user_id)) {
			  
				$SiteNonSiteFriends[1][] = $values;
			}
		}
		$result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));
		$result[0] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[0])));
    return $result;
	}

	//FUNCTION FOR GETTING CSV FILE CONTACTS OF THE REQUESTED USER.
	function getcsvcontactsAction () {
		ini_set('display_errors', FALSE);
		error_reporting(0);
    $this->_helper->layout->disableLayout();
    $session = new Zend_Session_Namespace();
    $filebaseurl = APPLICATION_PATH.'/public/suggestion/csvfiles/';
		//$filebaseurl = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/suggestion/csvfiles/'; 
    $validator = new Zend_Validate_EmailAddress();
		//READING THE CSV FILE AND FINDING THE EMAIL FOR CORROSPONDING ROW.
	  //WE ARE READING THE FILE FOR VERIOUS DELIMITERS TYPE. 
		$probable_delimiters = array(",", ";", "|", " ");
    foreach ($probable_delimiters as $delimiter) {
    	$fp = fopen($filebaseurl . $session->filename,'r') or die("can't open file");
			$k = 0;	
			while($csv_line = fgetcsv($fp,4096, $delimiter)) {
				for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
					try {
  					if ($validator->isValid($csv_line[$i])) {
  						$usercontacs_csv[$k]['contactMail'] =  $csv_line[$i];
  						$usercontacs_csv[$k]['contactName'] =  $csv_line[$i];
  						$k++;
  						break;
  					}
				  }
				  catch (Exception $e) {
				    continue;
				  }
				}
			}
			if (!empty($usercontacs_csv[0]['contactMail'])) {
				break;	
			}
		
			//CLOSING THE FILE AFTER READING. 
			fclose($fp) or die("can't close file");
		}
		//AFTER READING THE FILE WE ARE UNLINKING THE FILE.
		$filebaseurl = APPLICATION_PATH.'/public/suggestion/csvfiles/'.$session->filename;
		@unlink($filebaseurl);
		unset($session->filename);
		if (!empty($usercontacs_csv)) {
		  sort($usercontacs_csv);
			$SiteNonSiteFriends = $this->parseUserContacts ($usercontacs_csv);
			if (!empty($SiteNonSiteFriends[0]))  {
				$this->view->task = 'show_sitefriend';
				$this->view->addtofriend = $SiteNonSiteFriends[0];
			}
			if (!empty($SiteNonSiteFriends[1])) { 
				$this->view->addtononfriend = $SiteNonSiteFriends[1];
			}
		}
		if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
			$this->view->errormessage = true;
		}
	}

	public function uploadsAction() { 
    // Prepare
    if( empty($_FILES['Filedata']) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File failed to upload. Check your server settings (such as php.ini max_upload_filesize).');
      return;
    }
    $session = new Zend_Session_Namespace();
    $session->filename = $this->view->filename = $_FILES['Filedata']['name'];		
     $file_path = APPLICATION_PATH . '/public/suggestion/csvfiles';
		if( !is_dir($file_path) && !mkdir($file_path, 0777, true) ) {
        //$filename = APPLICATION_PATH . "/application/languages/$localeCode/custom.csv";
        mkdir(dirname($file_path));
        chmod(dirname($file_path), 0777);
        touch($file_path);
        chmod($file_path, 0777);
      }	
    
    // Prevent evil files from being uploaded
    $disallowedExtensions = array('php');
    if (in_array(end(explode(".", $_FILES['Filedata']['name'])), $disallowedExtensions)) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('File type or extension forbidden.');
      return;
    }
		
    $info = $_FILES['Filedata'];
    $targetFile = $file_path . '/' . $info['name'];
    $vals = array();

    if( file_exists($targetFile) ) {
      $deleteUrl = $this->view->url(array('action' => 'delete')) . '?path=' . $relPath . '/' . $info['name'];
      $deleteUrlLink = '<a href="'.$this->view->escape($deleteUrl) . '">' . Zend_Registry::get('Zend_Translate')->_("delete") . '</a>';
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("File already exists. Please %s before trying to upload.", $deleteUrlLink);
      return;
    }

    if( !is_writable($file_path) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Path is not writeable. Please CHMOD 0777 the public/admin directory.');
      return;
    }
    
    // Try to move uploaded file
    if( !move_uploaded_file($info['tmp_name'], $targetFile) ) {
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Unable to move file to upload directory.");
      return;
    }
    
    $this->view->status = 1;

//     if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
//       return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
//     }
  }
}

?>
