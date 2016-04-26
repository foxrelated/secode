<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Social Publisher
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @author     trunglt
 */
class Socialpublisher_Api_Core extends Core_Api_Abstract
{
	/*
	 * Returns an array contains types
	 * @return array()
	 */
	public function getEnabledTypes($params = null)
	{
		$item_types = Engine_Api::_() -> getItemTypes();
		$supported_types = $this -> getSupportedTypes();
		if (!empty($params['active']))
		{
			$temp = array();
			foreach ($supported_types as $type)
			{
				$settings = $this -> getTypeSettings($type);
				if ($settings['active'] == 1 && isset($settings['providers']))
				{
					$temp[] = $type;
				}
			}
			$supported_types = $temp;

		}
		return array_intersect($supported_types, $item_types);
	}

	public function getSupportedTypes()
	{
		return array(
			'activity_action',
			'blog',
			'advalbum_album',
			'album',
			'music_playlist',
			'mp3music_album',
			'event',
			'group',
			'video',
			'poll',
			'forum_topic',
			'classified',
			'contest',
			'ynfundraising_campaign',
			'ynauction_product',
			'groupbuy_deal',
			'social_store',
			'social_product',
			'ynwiki_page',
			'file',
			'ynbusinesspages_business',
			'ynlistings_listing',
			'ynjobposting_company',
			'ynjobposting_job',
			'ynultimatevideo_video',
		);
	}

	public function getTypeSettings($type)
	{
		// fix for adv album type
		$type = str_replace(array(
			'advalbum',
			'_'
		), '', $type);
		return Zend_Json::decode(Engine_Api::_() -> getApi('settings', 'core') -> getSetting('socialpublisher.' . $type));
	}

	public function getModuleTitle($type)
	{
		$type = str_replace(array(
			'advalbum',
			'_'
		), '', $type);
		$title = '';
		$translate = Zend_Registry::get('Zend_Translate');
		switch ($type)
		{
			case 'activityaction' :
				$title = $translate -> _('socialpublisher_activity_action');
				break;
			case 'album' :
				$title = $translate -> _('socialpublisher_album');
				break;
			case 'blog' :
				$title = $translate -> _('socialpublisher_blog');
				break;
			case 'poll' :
				$title = $translate -> _('socialpublisher_poll');
				break;
			case 'event' :
				$title = $translate -> _('socialpublisher_event');
				break;
			case 'forumtopic' :
				$title = $translate -> _('socialpublisher_forum_topic');
				break;
			case 'group' :
				$title = $translate -> _('socialpublisher_group');
				break;
			case 'mp3musicalbum' :
				$title = $translate -> _('socialpublisher_mp3music_album');
				break;
			case 'musicplaylist' :
				$title = $translate -> _('socialpublisher_music_playlist');
				break;
			case 'video' :
				$title = $translate -> _('socialpublisher_video');
				break;
			case 'classified' :
				$title = $translate -> _('socialpublisher_classified');
				break;
			case 'contest' :
				$title = $translate -> _('socialpublisher_contest');
				break;
			case 'ynfundraisingcampaign' :
				$title = $translate -> _('socialpublisher_ynfunraising_campaign');
				break;
			case 'ynauctionproduct' :
				$title = $translate -> _('socialpublisher_ynauction_product');
				break;
			case 'groupbuydeal' :
				$title = $translate -> _('socialpublisher_groupbuy_deal');
				break;
			case 'socialstore' :
				$title = $translate -> _('socialpublisher_social_store');
				break;
			case 'socialproduct' :
				$title = $translate -> _('socialpublisher_social_product');
				break;
			case 'ynwikipage' :
				$title = $translate -> _('socialpublisher_ynwiki_page');
				break;
			case 'file' :
				$title = $translate -> _('socialpublisher_file');
                break;
            case 'ynbusinesspagesbusiness' :
                $title = $translate -> _('socialpublisher_business');
                break;
            case 'ynlistingslisting' :
                $title = $translate -> _('socialpublisher_listing');
                break;
            case 'ynjobpostingcompany' :
                $title = $translate -> _('socialpublisher_company');
                break;
            case 'ynjobpostingjob' :
                $title = $translate -> _('socialpublisher_job');
				break;
			case 'ynultimatevideovideo' :
				$title = $translate -> _('socialpublisher_ynultimatevideo_video');
				break;
		}
		return $title;
	}

	public function getUserTypeSettings($user_id, $type)
	{
		// fix for adv album type
		$type = str_replace('advalbum_', '', $type);
		$table = Engine_Api::_() -> getDbTable('settings', 'socialpublisher');
		$select = $table -> select() -> where('user_id = ?', $user_id) -> where('type = ?', $type);
		// user setting
		$user_settings = $table -> fetchRow($select);
		// admin setting
		$enable_settings = $this -> getTypeSettings($type);
		// return setting
		$settings = array();
		// if do not find this setting in user, use setting in admin
		if (!$user_settings)
		{
			if (isset($enable_settings['active']) && $enable_settings['active'] == 1)
			{
				$settings['type'] = $type;
				$settings['option'] = Socialpublisher_Plugin_Constants::OPTION_ASK;
				$settings['privacy'] = 7;
				$user_provider = array();
				if (isset($enable_settings['providers']))
				{
					foreach ($enable_settings['providers'] as $provider)
					{
						$settings['providers'][] = $provider;
					}
				}
			}
		}
		// compare user setting to admin setting
		else
		{
			if (isset($enable_settings['active']) && $enable_settings['active'] == 1)
			{
				$settings['type'] = $user_settings -> type;
				$settings['option'] = $user_settings -> option;
				$settings['privacy'] = $user_settings -> privacy;
				if (isset($enable_settings['providers']))
				{
					$providers = Zend_Json::decode($user_settings['providers']);
					foreach ($enable_settings['providers'] as $provider)
					{
						if (in_array($provider, $providers))
						{
							$settings['providers'][] = $provider;
						}
					}
				}
			}
		}
		return $settings;
	}

	public function getModule($params = null)
	{
		$table = Engine_Api::_() -> getDbTable('modules', 'socialpublisher');
		$select = $table -> select();
		if (!empty($params['module_name']))
		{
			$select -> where('module_name = ?', $params['module_name']);
		}
		if (!empty($params['enabled']) && $params['enabled'] == 1)
		{
			$select -> where('enabled = 1');
		}
		$row = null;
		if ($this -> checkEnabledModule($params['module_name']))
		{
			$row = $table -> fetchRow($select);
		}
		return $row;
	}

	public function checkEnabledModule($module_name)
	{
		$table = Engine_Api::_() -> getDbTable('modules', 'core');
		$select = $table -> select() -> where('enabled = ?', 1) -> where('name  = ?', $module_name);
		$row = $table -> fetchRow($select);
		if (count($row) > 0)
		{
			return true;
		}
		return false;
	}

	public function getUserSettings($user_id)
	{
		$table = Engine_Api::_() -> getDbtable('settings', 'socialpublisher');
		$select = $table -> select() -> where('user_id = ?', $user_id);
		return $table -> fetchAll($select);
	}

	public function getThumbnailUrl($photo)
	{
		// get picture url
		$photo_url = $photo -> getPhotoUrl("thumb.profile");
		if ($photo_url)
		{
			if (strpos($photo_url, 'https://') === FALSE && strpos($photo_url, 'http://') === FALSE)
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$pageURL .= $_SERVER["SERVER_NAME"];
				$photo_url = $pageURL . $photo_url;
			}
		}
		return $photo_url;
	}

	public function getMediaType($resource)
	{
		$type = $resource -> getType();
		if (strpos($type, 'ynultimatevideo_video') !== false)
		{
			return 'ultimate video';
		}
		if (strpos($type, 'video') !== false)
		{
			return 'video';
		}
		else
		if (strpos($type, 'blog') !== false)
		{
			return 'blog';
		}
		else
		if (strpos($type, 'event') !== false)
		{
			return 'event';
		}
		else
		if (strpos($type, 'group') !== false)
		{
			return 'group';
		}
		else
		if (strpos($type, 'forum_topic') !== false)
		{
			return 'topic';
		}
		else
		if (strpos($type, 'music_playlist') !== false)
		{
			return 'playlist';
		}
		else
		if (strpos($type, 'mp3music_album') !== false)
		{
			return 'music album';
		}
		else
		if (strpos($type, 'album') !== false)
		{
			return 'photo album';
		}
		else
		if (strpos($type, 'classified') !== false)
		{
			return 'classified listings';
		}
		else
		if (strpos($type, 'contest') !== false)
		{
			return 'contest';
		}
		else
		if (strpos($type, 'ynfundraising_campaign') !== false)
		{
			return 'campaign';
		}
		else
		if (strpos($type, 'ynauction_product') !== false)
		{
			return 'auction';
		}
		else
		if (strpos($type, 'groupbuy_deal') !== false)
		{
			return 'deal';
		}
		else
		if (strpos($type, 'social_store') !== false)
		{
			return 'store';
		}
		else
		if (strpos($type, 'social_product') !== false)
		{
			return 'product';
		}
		else
		if (strpos($type, 'ynwiki_page') !== false)
		{
			return 'page';
		}
		else
		if (strpos($type, 'file') !== false)
		{
			return 'file';
		}
		else
		if (strpos($type, 'activity_action') !== false)
		{
			if ($resource -> attachment_count > 0)
			{
				$attachment = $resource -> getFirstAttachment();
				if ($attachment -> item instanceof Core_Model_Link)
				{
					return 'link';
				}
				if (($attachment -> item instanceof Album_Model_Photo) || ($attachment -> item instanceof Advalbum_Model_Photo))
				{
					return 'photo';
				}
				if (($attachment -> item instanceof Music_Model_PlaylistSong) || ($attachment -> item instanceof Mp3music_Model_AlbumSong))
				{
					return 'song';
				}
				if (($attachment -> item instanceof Video_Model_Video) || ($attachment -> item instanceof Ynvideo_Model_Video))
				{
					return 'video';
				}
				if ($attachment -> item instanceof Ynultimatevideo_Model_Video)
				{
					return 'ultimate video';
				}
			}
			return 'post';
		}
		else
		{
			return 'item';
		}
	}

	public function getDefaultStatus($params = array())
	{
		return "";
	}

	public function getPostLink($resource)
	{
		if (!($resource instanceof Core_Model_Item_Abstract))
		{
			return;
		}
		$req = Zend_Controller_Front::getInstance() -> getRequest();
		$share_link = $req -> getScheme() . '://' . $req -> getHttpHost();
		$resource_type = $resource -> getType();
		if ($resource_type == 'activity_action')
		{
			$media_type = $this -> getMediaType($resource);
			if ($resource -> attachment_count > 0)
			{
				$attachment = $resource -> getFirstAttachment();
				if ($attachment -> item instanceof Core_Model_Link)
				{
					$share_link .= $resource -> getHref();
				}
				elseif ($attachment -> item instanceof Mp3music_Model_AlbumSong)
				{
					$params = array(
						'album_id' => $attachment -> item -> album_id,
						'song_id' => $attachment -> item -> song_id
					);
					$share_link .= Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'mp3music_album_song', true);
				}
				else
				{
					$share_link .= $attachment -> item -> getHref();
				}
			}
			else
			{
				$share_link .= $resource -> getHref();
			}
		}
		else
		{
			$share_link .= $resource -> getHref();
		}
		return $share_link;
	}

	public function getPostTitle($resource)
	{
		if (!($resource instanceof Core_Model_Item_Abstract))
		{
			return;
		}
		$resource_type = $resource -> getType();
		if ($resource_type == 'activity_action')
		{
			$media_type = $this -> getMediaType($resource);
			if ($media_type == 'post')
			{
				$title = $resource -> body;
			}
			else
			{
				$attachment = $resource -> getFirstAttachment();
				// link title
				$title = $attachment -> item -> getTitle();
			}
		}
		else
		{
			$title = $resource -> getTitle();
		}
		return $title;
	}

	public function getPostDescription($resource)
	{
		if (!($resource instanceof Core_Model_Item_Abstract))
		{
			return;
		}
		$description = "";
		$resource_type = $resource -> getType();
		if ($resource_type == 'activity_action')
		{
			$media_type = $this -> getMediaType($resource);
			if ($media_type == 'post')
			{
				$description = "";
			}
			else
			{
				$attachment = $resource -> getFirstAttachment();
				// link title
				$description = $attachment -> item -> getDescription();
				if (!$description)
				{
					$description = $resource -> body;
				}
			}
		}
		else
		if ($resource_type == 'ynfundraising_campaign')
		{
			$description = $resource -> short_description;
		}
		else
		if ($resource_type == 'contest')
		{
			$description = $resource -> description;
		}
		else
		if ($resource_type == 'ynwiki_page')
		{
			$description = $resource -> body;
		}
		else
		{
			$description = $resource -> getDescription();
		}
		return str_replace('&nbsp;', ' ', $description);
	}

	public function getShortBitlyUrl($sLongUrl)
	{
		try
		{
			$sLongUrl = urlencode($sLongUrl);
			$url = "http://api.bitly.com/v3/shorten?login=myshortlinkng&apiKey=R_0201be3efbcc7a1a0a0d1816802081d8&longUrl={$sLongUrl}&format=json";
			$result = @file_get_contents($url);
			$obj = json_decode($result, true);
			return ($obj['status_code'] == '200' ? $obj['data']['url'] : "");
		}
		catch (Exception $e)
		{
			return $sLongUrl;
		}
	}

	public function getPostData($provider, $resource, $status, $photo_url)
	{
		$req = Zend_Controller_Front::getInstance() -> getRequest();
		$resource_type = $resource -> getType();
		$media_type = $this -> getMediaType($resource);
		$share_link = $this -> getPostLink($resource);
		$title = strip_tags($this -> getPostTitle($resource));
		$description = strip_tags($this -> getPostDescription($resource));
        $caption = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', Zend_Registry::get('Zend_Translate')->translate('_SITE_TITLE'));
		$post_data = array();
		switch ($provider)
		{
			case 'twitter' :
				if (!empty($_SESSION['socialbridge_session']['twitter']))
				{
					$share_link = $this -> getShortBitlyUrl($share_link);
                    $tw_message = $status;
                    if(!$tw_message && $resource_type == 'activity_action')
                    {
                        $tw_message = $description;
                    }
					$post_data = array(
						'access_token' => $_SESSION['socialbridge_session']['twitter']['access_token'],
						'secret_token' => $_SESSION['socialbridge_session']['twitter']['secret_token'],
						'message' => $tw_message,
						'link' => $share_link
					);
					if ($resource_type == 'activity_action')
					{
						if ($media_type == 'link')
						{
							unset($post_data['link']);
						}
					}
					$photo_url = '';
					$media_type = $this -> getMediaType($resource);
					if ($media_type == 'photo' || $media_type == 'video' || $media_type == 'link')
					{
						$attachment = $resource -> getFirstAttachment();
						$photo_url = $this -> getThumbnailUrl($attachment -> item);
					}
					else
					{
						$photo_url = $this -> getThumbnailUrl($resource);
					}
					if (!empty($photo_url))
					{
						$content = file_get_contents($photo_url);
						$contentBase64 = base64_encode($content);
    					$post_data['picture'] = $contentBase64;
					}
				}
				break;
			case 'facebook' :
			case 'linkedin' :
				if (!empty($_SESSION['socialbridge_session'][$provider]))
				{
					if ($provider == 'facebook')
					{
						$post_data = array(
							'access_token' => $_SESSION['socialbridge_session']['facebook']['access_token'],
							'link' => $share_link,
							'name' => $title,
							'caption' => $caption,
							'message' => $status,
							'description' => $description
						);
						if (!empty($photo_url))
						{
							$post_data['picture'] = $photo_url;
						}
						if ($resource_type == 'activity_action')
						{
							if ($media_type == 'link')
							{
								unset($post_data['name']);
							}
						}
					}
					elseif ($provider == 'linkedin')
					{
						$post_data = array(
							'access_token' => $_SESSION['socialbridge_session']['linkedin'],
							'comment' => $status,
							'title' => $title,
							'submitted-url' => $share_link,
							'description' => $description
						);
						if (!empty($photo_url))
						{
							$post_data['submitted-image-url'] = $photo_url;
						}
					}

				}
				break;
			default :
				break;
		}
		return $post_data;
	}

	// check if provider API has been provided
	public function isValidProvider($provider)
	{
		$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
		$select = $apiSetting -> select() -> where('api_name = ?', $provider);
		$row = $apiSetting -> fetchRow($select);

		if (!$row)
		{
			return false;
		}
		$api_params = unserialize($row -> api_params);
		if ($api_params['key'] == '' || $api_params['secret'] == '')
		{
			return false;
		}
		return true;
	}

	public function getPhotoUrl($resource)
	{
		$req = Zend_Controller_Front::getInstance() -> getRequest();
		$resource_type = $resource -> getType();
		$media_type = $this -> getMediaType($resource);
		$photo_url = "";
		//get Photo Url
		switch ($resource_type)
		{
			case 'forum_topic' :
			case 'blog' :
			case 'poll' :
				$owner = $resource -> getOwner();
				$photo_url = $this -> getThumbnailUrl($owner);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
				}
				break;
			case 'album' :
			case 'advalbum_album' :
			case 'video' :
			case 'ynultimatevideo_video' :
			case 'contest' :
			case 'ynfundraising_campaign' :
			case 'ynauction_product' :
			case 'groupbuy_deal' :
			case 'social_store' :
			case 'social_product' :
				$photo_url = $this -> getThumbnailUrl($resource);
				break;
			case 'mp3music_album' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Mp3music/externals/images/nophoto_album_main.png';
				}
				break;
			case 'music_playlist' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Music/externals/images/nophoto_playlist_main.png';
				}
				break;
			case 'event' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Event/externals/images/nophoto_event_thumb_profile.png';
				}
				break;
			case 'classified' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Classified/externals/images/nophoto_classified_thumb_profile.png';
				}
				break;
			case 'group' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Group/externals/images/nophoto_group_thumb_profile.png';
				}
				break;
			case 'ynwiki_page' :
				$photo_url = $this -> getThumbnailUrl($resource);
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Ynwiki/externals/images/nophoto_page_thumb_profile.png';
				}
				break;
			case 'file' :
				$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) ."application/modules/Ynfilesharing/externals/images/file_types/" . $resource->getFileIcon();
				break;
			case 'ynbusinesspages_business' :
                $photo_url = $this -> getThumbnailUrl($resource);
                if (empty($photo_url))
                {
                    $photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Ynbusinesspages/externals/images/nophoto_business_thumb_profile.png';
                }
                break;
            case 'ynlistings_listing' :
                $photo_url = $this -> getThumbnailUrl($resource);
                if (empty($photo_url))
                {
                    $photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Ynlistings/externals/images/nophoto_listing_thumb_profile.png';
                }
                break;
            case 'ynjobposting_company' :	
                $photo_url = $this -> getThumbnailUrl($resource);
                if (empty($photo_url))
                {
                    $photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Ynjobposting/externals/images/nophoto_company_thumb_profile.png';
                }
                break;
            case 'ynjobposting_job' :   
                $photo_url = $this -> getThumbnailUrl($resource);
                if (empty($photo_url))
                {
                    $photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Ynjobposting/externals/images/nophoto_job_thumb_profile.png';
                }
                break;
			case 'activity_action' :
				if ($media_type == 'photo' || $media_type == 'video' || $media_type == 'link' || $media_type == 'ultimate video')
				{
					// get picture url
					$attachment = $resource -> getFirstAttachment();
					$photo_url = $this -> getThumbnailUrl($attachment -> item);
				}
				elseif ($media_type == 'song')
				{
					$attachment = $resource -> getFirstAttachment();
					$type = $attachment -> item -> getType();
					if ($type == 'music_playlist_song')
						$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Music/externals/images/nophoto_playlist_main.png';
					else
						$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/Mp3music/externals/images/nophoto_album_main.png';
				}
				if(empty($photo_url))
				{
					$owner = $resource -> getOwner();
					$photo_url = $this -> getThumbnailUrl($owner);
				}
				if (empty($photo_url))
				{
					$photo_url = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Registry::get('Zend_View') -> url(array(), 'default', true) . 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
				}
				break;
			default :
				break;
		}
		return $photo_url;
	}

}
