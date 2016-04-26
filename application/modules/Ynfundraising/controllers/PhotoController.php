<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Fundraising
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PhotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Minh Nguyen
 */
class Ynfundraising_PhotoController extends Core_Controller_Action_Standard {
	public function init() {
		/*if(!Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
		 {
		 return $this->_helper->requireAuth->forward ();
		 }*/
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('ynfundraising_photo', $photo_id))) {
				Engine_Api::_() -> core() -> setSubject($photo);
			} else if (0 !== ($campaign_id = (int)$this -> _getParam('campaign_id')) && null !== ($campaign = Engine_Api::_() -> getItem('ynfundraising_campaign', $campaign_id))) {
				Engine_Api::_() -> core() -> setSubject($campaign);
			}
		}
	}

	public function listAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> campaign = $campaign = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $campaign -> getSingletonAlbum();

		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		$this -> view -> canUpload = $campaign -> authorization() -> isAllowed(null, 'photo.upload');
	}

	public function viewAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> group = $group = $photo -> getGroup();
		$this -> view -> canEdit = $photo -> authorization() -> isAllowed(null, 'photo.edit');
	}

	public function uploadAction() {

		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		$campaign = Engine_Api::_() -> core() -> getSubject();

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$campaign = Engine_Api::_() -> getItem('ynfundraising_campaign', (int)$campaign -> getIdentity());
		if ($campaign -> user_id == $viewer -> getIdentity()) {
			$this -> view -> canUpload = true;
		}
		$album = $campaign -> getSingletonAlbum();
		$this -> view -> campaign_id = $campaign -> getIdentity();

		$this -> view -> form = $form = new Ynfundraising_Form_Photo_Upload();
		$form -> campaign_id -> setValue($campaign -> getIdentity());
		//$form -> video_url -> setValue($campaign -> video_url);

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('ynfundraising_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		try {
			$arr_photo_id = array();			
			$values = $form -> getValues();
			$arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
				
			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			
			$params = array('campaign' => $campaign -> getIdentity(), 'user_id' => $viewer -> getIdentity(), );

			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $campaign, 'ynfundraising_photo_upload', null, array('count' => count($values['file'])));

			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id) {
				$photo = Engine_Api::_() -> getItem("ynfundraising_photo", $photo_id);

				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> save();

				if ($campaign -> photo_id == 0) {
					$campaign -> photo_id = $photo -> file_id;
					$campaign -> save();
				}

				if ($action instanceof Activity_Model_Action && $count < 8) {
					$api -> attachActivity($campaign, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}
			if (isset($values['video_url']) && $values['video_url'] != "") {
				$campaign -> video_url = $values['video_url'];
				$campaign -> save();
			}

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'create-step-two', 'campaignId' => "{$campaign->getIdentity()}"), 'ynfundraising_general');
	}

	public function uploadPhotoAction() {

		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		if (!$this -> getRequest() -> isPost()) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
		}

		$campaign = Engine_Api::_() -> getItem('ynfundraising_campaign', (int)$_REQUEST['campaign_id']);

		// @todo check auth
		//$campaign

		if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'ynfundraising') -> getAdapter();
		$db -> beginTransaction();

		try {
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album = $campaign -> getSingletonAlbum();

			$params = array(
			// We can set them now since only one album is allowed
				'collection_id' => $album -> getIdentity(), 'album_id' => $album -> getIdentity(), 'campaign_id' => $campaign -> campaign_id, 'user_id' => $viewer -> getIdentity(), );
			$temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);
			$photo_id = Engine_Api::_() -> ynfundraising() -> createPhoto($params, $temp_file) -> photo_id;

			if (!$campaign -> photo_id) {
				$campaign -> photo_id = $photo_id;
				$campaign -> save();
			}

			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
	
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));

		} catch( Exception $e ) {
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
		}
	}
	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynfundraising_photo', $this -> getRequest() -> getParam('photo_id'));
		
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynfundraising') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction() {
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireAuth -> forward();
		}
		$photo = Engine_Api::_() -> core() -> getSubject();

		$this -> view -> form = $form = new Ynfundraising_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost()) {
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynfundraising') -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array('Changes saved'), 'layout' => 'default-simple', 'parentRefresh' => true, 'closeSmoothbox' => true, ));
	}

	public function removeAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$photo_id = (int)$this -> _getParam('photo_id');
		$photo = Engine_Api::_() -> getItem('ynfundraising_photo', $photo_id);

		$db = $photo -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$photo -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}
	}

}
