<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_VideoController extends Core_Controller_Action_Standard
{

	public function init()
	{
		// Must be able to use videos
		if (!$this -> _helper -> requireAuth() -> setAuthParams('video', null, 'view') -> isValid())
		{
			return;
		}

		// Get subject
		$video = null;
		$id = $this -> _getParam('video_id', $this -> _getParam('id', null));
		if ($id)
		{
			$video = Engine_Api::_() -> getItem('video', $id);
			if ($video)
			{
				Engine_Api::_() -> core() -> setSubject($video);
			}
		}

		// Require subject
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		// Require auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}
	}

	public function embedAction()
	{

		// Get subject
		$this -> view -> video = $video = Engine_Api::_() -> core() -> getSubject('video');

		// Check if embedding is allowed
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynvideo.embeds', 1))
		{
			$this -> view -> error = 1;
			return;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$this -> view -> error = 2;
			return;
		}

		// Get embed code
		$this -> view -> embedCode = $video -> getEmbedCode();
	}

	public function externalAction()
	{
		// Get subject
		$this -> view -> video = $video = Engine_Api::_() -> core() -> getSubject('video');

		// Check if embedding is allowed
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynvideo.embeds', 1))
		{
			$this -> view -> error = 1;
			return;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$this -> view -> error = 2;
			return;
		}

		// Get embed code
		$embedded = "";
		if ($video -> status == 1)
		{
			$video -> view_count++;
			$video -> save();
			$embedded = $video -> getRichContent(true);
		}

		// Track views from external sources
		Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('ynvideo.embedviews');

		// Get file location
		if ($video -> type == 3 && $video -> status == 1)
		{
			if (!empty($video -> file_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$this -> view -> video_location = $storage_file -> map();
				}
			}
		}

		$this -> view -> rating_count = Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> videoEmbedded = $embedded;
		if ($video -> category_id != 0)
		{
			$this -> view -> category = Engine_Api::_() -> ynvideo() -> getCategory($video -> category_id);
		}
	}

	public function sendToFriendAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$video = Engine_Api::_() -> core() -> getSubject('video');
		$user_emails = $this -> _getParam('send_emails');
		$emails = explode(',', $user_emails);
		$user_emails = array();
		foreach ($emails as $email)
		{
			$e = trim($email);
			if (!in_array($e, $user_emails))
			{
				array_push($user_emails, $e);
			}
		}

		$data = array();
		if (count($user_emails) > 0)
		{
			$params = array(
				'host' => $_SERVER['HTTP_HOST'],
				'date' => time(),
				'sender_title' => $viewer -> getTitle(),
				'sender_link' => $viewer -> getHref(),
				'object_title' => $video -> getTitle(),
				'object_link' => $video -> getHref(),
				'object_description' => $video -> description,
				'message' => $this -> _getParam('send_message')
			);
			try
			{
				$result = Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($user_emails, 'ynvideo_send_video_to_friends', $params);
				$data = array(
					'result' => '1',
					'message' => Zend_Registry::get('Zend_Translate') -> _('The emails are sent successfully !!!')
				);
			}
			catch (Engine_Exception $e)
			{
				throw $e;
			}
		}
		else
		{
			$data = array(
				'result' => '0',
				'message' => Zend_Registry::get('Zend_Translate') -> _('There is no email entered !!!')
			);
		}
		return $this -> _helper -> json($data);
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

}
