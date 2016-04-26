<?php
class Ynevent_FileController extends Core_Controller_Action_Standard {
	protected $_parentType;
	protected $_parentId;
	protected $_viewer;

	public function init() 
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> hasModuleBootstrap('ynfilesharing'))
		{
			return $this -> _helper -> requireSubject -> forward();
		}
		$this -> view -> viewer = $this -> _viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _parentType = "event";

		if (!Engine_Api::_() -> core() -> hasSubject()) 
		{
			if ((0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('event', $event_id))))
			{
				Engine_Api::_() -> core() -> setSubject($event);
				$this -> _parentId = $event_id;
			}

		} else 
		{
			$event = Engine_Api::_() -> core() -> getSubject('event');
			$this -> _parentId = $event -> getIdentity();
		}

		$this -> view -> parentId = $this -> _parentId;	
		$this -> view -> parentType = $this -> _parentType;

		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
		}
	}

	public function listAction() 
	{
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject('event');
		//check auth create
		$canCreate = Engine_Api::_() -> ynevent() -> isOwner($event);
		$this -> view -> canCreate = $canCreate;
		//check both auth remove folder and auth delete file
		$canDeleteRemove = Engine_Api::_() -> ynevent() -> isOwner($event);
		$this -> view -> canDeleteRemove = $canDeleteRemove;

		$messages = $this -> _helper -> flashMessenger -> getMessages();
		if (count($messages)) {
			$message = current($messages);
			$this -> view -> messages = array($message['message']);
			$this -> view -> error = $message['error'];
		}
		$parent = $event;
		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		// Search Params
		$form = new Ynevent_Form_File_Search();
		$this -> view -> form = $form;
		$form -> setAction($this -> view -> url(array('controller' => 'file', 'action' => 'list', 'event_id' => $event -> getIdentity()), 'event_extended', true));
		$params = $this -> getAllParams();
		if($params['type'] == 'folder')
		{
			unset($params['orderby']);
		}
		$form -> isValid($params);
		
		$params = $form -> getValues();
		$params['parent_type'] = $this -> _parentType;
		$params['parent_id'] = $this -> _parentId;
		$folders = $files = array();
		if (isset($params['type'])) 
		{
			switch ($params ['type']) {
				case 'file' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					break;
				case 'folder' :
					$folders = $filesharingApi -> selectFoldesByOptions($params);
					break;
				case 'all' :
					$files = $filesharingApi -> selectFilesByOptions($params);
					$folders = $filesharingApi -> selectFoldesByOptions($params);
			}
		} else 
		{
			$folders = $filesharingApi -> getSubFolders(NULL, $parent);
		}

		$this -> view -> files = $files;
		$this -> view -> subFolders = $filesharingApi -> getFolders($folders, 'view', $this -> _viewer);
		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($folders, $this -> _viewer);
		$totalUploaded = Engine_Api::_() -> ynfilesharing() -> getCurrentFolderSizeOfObject($parent);
		$totalUploaded = number_format($totalUploaded / 1048576, 2);
		$this -> view -> totalUploaded = $totalUploaded;
		$maxSizeKB = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
		$space_limit = 0;
		if($this -> _viewer -> getIdentity())
			$space_limit = (int)Engine_Api::_() -> authorization() -> getPermission($this -> _viewer -> level_id, 'user', 'quota');
		if ($space_limit && $space_limit < $maxSizeKB) {
			$maxSizeKB = $space_limit;
		}
		$maxSizeKB = number_format($maxSizeKB / 1024, 2);
		$this -> view -> maxSizeKB = $maxSizeKB;
	}

	public function viewFolderAction() 
	{
		$folderId = $this -> _getParam('folder_id', 0);
		if ($folderId != 0) {
			$this -> view -> folder = $folder = Engine_Api::_() -> getItem('folder', $folderId);
		}
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject('event');
		
		//check auth create
		$canCreate = Engine_Api::_() -> ynevent() -> isOwner($event);
		$this -> view -> canCreate = $canCreate;
		//check both auth remove folder and auth delete file
		$canDeleteRemove = Engine_Api::_() -> ynevent() -> isOwner($event);
		$this -> view -> canDeleteRemove = $canDeleteRemove;
		$this -> view -> canUpload = $event->authorization()->isAllowed(null, 'file');
		// check download
		$this -> view -> canDownload = $event->authorization()->isAllowed(null, 'view_file');
		$this -> view -> canDelete = $canDelete = $this -> view -> canDeleteRemove;

		$fileTbl = Engine_Api::_() -> getDbTable('files', 'ynfilesharing');

		$parentObject = $event;
		$this -> view -> fileTotal = $fileTotal = $fileTbl -> countAllFilesBy($parentObject);
		$this -> view -> maxFileTotal = $maxFileTotal = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'userfile');
		$folderName = $this -> _viewer -> getGuid();
		$this -> view -> totalSizePerUser = $totalSizePerUser = Ynfilesharing_Plugin_Utilities::getFolderSize(Ynfilesharing_Plugin_Constants::FOLDER_CODE . DIRECTORY_SEPARATOR . $folderName . DIRECTORY_SEPARATOR);
		$quota = (INT)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('folder', $this -> _viewer, 'usertotal');
		$this -> view -> maxTotalSizePerUser = $maxTotalSizePerUser = $quota * 1024;

		if ($folder) 
		{
			if(Engine_Api::_() -> core() -> hasSubject('event'))
				Engine_Api::_()->core()->clearSubject('event');
			Engine_Api::_() -> core() -> setSubject($folder);
		}

		if (!$this -> _helper -> requireSubject('folder') -> isValid()) 
		{
			return;
		}

		if (!$folder -> isAllowed($this -> _viewer, 'view')) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		//check network view
		if(isset($folder -> networks) && !empty($folder -> networks))
		{
			//if user login, check the network
			if ($this->_viewer->getIdentity() && !$folder -> isOwner($this->_viewer))
        	{
        		$user_id = $this->_viewer->getIdentity();
				$network_table = Engine_Api::_()->getDbtable('membership', 'network');
			    $network_select = $network_table->select('resource_id')->where('user_id = ?', $user_id);
			    $network_id_query = $network_table->fetchAll($network_select);
			    $network_id_query_count = count($network_id_query);
			    $network_id_array = array();
			    for($i = 0; $i < $network_id_query_count; $i++) {
			    	$network_id_array[$i] = $network_id_query[$i]['resource_id'];
			    }
				
				$network_array = json_decode($folder -> networks);
		        // Check if Member Networks Match Annoucement Networks
		        if ($network_array != NULL)
				{
					//if user are not belong to any network
					if($network_id_array == NULL || empty($network_id_array) || count($network_id_array) == 0)
					{
						return $this->_helper->requireAuth()->forward();
					}
					
					$networkCheckedSuccess = false;
					
		            foreach ( $network_array as $value) {
		                if( $network_id_array != NULL && in_array( $value, $network_id_array) != FALSE ) {
		                    $networkCheckedSuccess = true;	
		                    break;
		                }
		            }
					if(!$networkCheckedSuccess)
					{
						return $this->_helper->requireAuth()->forward();
					}
				}
			}
		}
		
		// increase the view count
		$folder -> view_count = $folder -> view_count + 1;
		$this -> view -> folderTags = $folder -> tags() -> getTagMaps();
		$folder -> save();

		$filesharingApi = Engine_Api::_() -> ynfilesharing();
		$folders = $filesharingApi -> getSubFolders($folder);

		$this -> view -> subFolders = $subFolders = $filesharingApi -> getFolders($folders);
		$this -> view -> files = $filesharingApi -> getFilesInFolder($folder);
		$foldersArr = array();
		foreach ($folders as $f) {
			array_push($foldersArr, $f);
		}
		array_push($foldersArr, $folder);

		$this -> view -> foldersPermissions = $filesharingApi -> getFoldersPermissions($foldersArr);
		$this -> view -> canEdit = $this -> view -> canEditPerm = true;
	}
}
?>
