<?php

class Ynmobile_Service_Discussion extends Ynmobile_Service_Base{
	
	public function fetch_topics($aData){
		
		$iParentId  =  intval($iParentId);
		$sParentType =  $sParentType?$sParentType: 'group_topic';
		
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$parent = $this->getWorkingTable('groups','group')->findRow($iGroupId);
		
		// Get paginator
		$table = $this->getWorkingTable('topics','group');
		
		$select = $table->select()
		->where('group_id = ?', $group->getIdentity())
		->order('sticky DESC')
		->order('modified_date DESC');
		
		$paginator = Zend_Paginator::factory($select);
	    $paginator->setItemCountPerPage($iLimit);
	    $paginator->setCurrentPageNumber($iPage);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
		{
			return array();
		}
	    
	    $result = array();
	    foreach ($paginator as $topic)
	    {
	    	$lastpost = $topic->getLastPost();
	    	$lastposter = $topic->getLastPoster();
	    	$modifiedDate = strtotime($topic -> modified_date);
	    	$lastPostedDate = strtotime($lastpost->creation_date);
	    	$result[] = array(
	    			'iTopicId' => $topic->getIdentity(),
	    			'sTitle' => $topic->getTitle(),
	    			'sDescription' => '',
	    			'iViewCount' => $topic->view_count,
	    			'iReplyCount' => $topic->post_count-1,
	    			'iLastUserId' => $lastposter->getIdentity(),
	    			'sLastUserName' => $lastposter->getTitle(),
	    			'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($modifiedDate),
	    			'sLastPostedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp($lastPostedDate),
	    			'iGroupId' => $aData['iGroupId'],
	    			'sGroupTitle' => $group->getTitle(),
	    			'bSticky' => $topic->sticky,
	    	);
	    }
	    return $result;
	}
	
	public function view_topic($aData)
	{
		extract($aData);
		if (!isset($iPage))
		{
			$iPage = 1;
		}
		
		if ($iPage == '0')
		{
			return array();
		}
		
		if (!isset($iLimit))
		{
			$iLimit = 10;
		}
	
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        
		
		$group = $topic->getParentGroup();
		$canEdit = $topic->canEdit(Engine_Api::_()->user()->getViewer());
    	$officerList = $group->getOfficerList();
		$canPost = $group->authorization()->isAllowed($viewer, 'comment');
	
		if( !$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id ) {
			$topic->view_count = new Zend_Db_Expr('view_count + 1');
			$topic->save();
		}
	
		// Check watching
		$isWatching = null;
		if( $viewer->getIdentity() ) {
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
	
		// @todo implement scan to post
		$post_id = (int) $iPostId;
	
		$table = $this->getWorkingTable('posts','group');
		
		$select = $table->select()
			->where('group_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->order('creation_date ASC');
	
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($iPage);
		$paginator->setItemCountPerPage($iLimit);
		$totalPage = (integer) ceil($paginator->getTotalItemCount() / $iLimit);
		if ($iPage > $totalPage)
			return array();
	
		$result = array();
		$view = Zend_Registry::get("Zend_View");
		
		foreach ($paginator as $post)
		{
			$user = Engine_Api::_()->user()->getUser($post->user_id);
			$sUserImageUrl = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
			if ($sUserImageUrl != "")
			{
				$sUserImageUrl = Engine_Api::_() -> ynmobile() ->finalizeUrl($sUserImageUrl);
			}
			else
			{
				$sUserImageUrl = NO_USER_ICON;
			}
			$body = $post->body;
			$body = nl2br($view->BBCode($body, array('link_no_preparse' => true)));
			
			$canPost = false;
			if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($group, null, 'comment') ) 
			{
				$canPost = true;
			}
			if( Engine_Api::_()->authorization()->isAllowed($group, null, 'topic.edit') ) 
			{
				$canEdit = true;
			}
			
			$canEditPost = false; 
			if ( $post->user_id == $viewer->getIdentity() || $group->getOwner()->getIdentity() == $viewer->getIdentity() || $canEdit)
			{
				$canEditPost = true;
			}
			
			$result[] = array(
					'iPostId' => $post->getIdentity(),
					'iTopicId' => $topic->getIdentity(),
					'iUserId' => $post->user_id,
					'sUserName' => $user->getTitle(),
					'sUserPhoto' => $sUserImageUrl,
					'sSignature' =>  '',
					'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
					'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
					'sBody' => $body,
					'sPhotoUrl' => '',
					'bCanPost' => $canPost,
					'bCanEditPost' => $canEditPost,
					'bCanDeletePost' => $canEditPost,
			);
		}
		return $result;
	}
	
	public function post_reply($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		if (!isset($sBody))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
				
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
		
		$group = $topic->getParentGroup();
		
		if( $topic->closed ) 
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This has been closed for posting.")
			);
		}
		
		$allowHtml = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_html', 0);
		$allowBbcode = (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('group_bbcode', 0);
		
		if( isset($iQuoteId) && !empty($iQuoteId) ) 
		{
		    $quote = $this->getWorkingItem('group_post', $iQuoteId);
			
			if($quote->user_id == 0) {
				$owner_name = Zend_Registry::get('Zend_Translate')->_('Deleted Member');
			} else {
				$owner_name = $quote->getOwner()->__toString();
			}
				
			// if( $allowHtml || !$allowBbcode ) {
				// $sBody = "<blockquote><strong>" . "{$owner_name} said:" . "</strong><br />" . $quote->body . "</blockquote><br />" . $sBody;
			// } else {
				// $sBody = "[blockquote][b]" . strip_tags("{$owner_name} said:") . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
			// }
			$sBody = "[blockquote][b]" . "[i]{$owner_name}[/i] said:" . "[/b]\r\n" . htmlspecialchars_decode($quote->body, ENT_COMPAT) . "[/blockquote]\r\n" . $sBody;
			
		}
		$viewer = Engine_Api::_()->user()->getViewer();
        
		if( !$allowHtml ) 
		{
			$filter = new Engine_Filter_HtmlSpecialChars();
		} 
		else 
		{
			$filter = new Engine_Filter_Html();
			$filter->setForbiddenTags();
			$allowed_tags = array_map('trim', explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'advgroup', 'commentHtml')));
            
			$filter->setAllowedTags($allowed_tags);
		}
        
        $sBody = $filter->filter($sBody);
        
		if ($sBody == '')
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Post content is invalid!")
			);
		}
		
		// Process
		$viewer = Engine_Api::_()->user()->getViewer();
		$topicOwner = $topic->getOwner();
		$isOwnTopic = $viewer->isSelf($topicOwner);
		
		$postTable = $this->getWorkingTable('posts','group');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
		
		$userTable = Engine_Api::_()->getItemTable('user');
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
		
		$values['body'] = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
		$values['user_id'] = $viewer->getIdentity();
		$values['group_id'] = $group->getIdentity();
		$values['topic_id'] = $topic->getIdentity();
		$values['watch'] =  (isset($iWatch) && $iWatch == '1') ? 1 : 0;
		
		$watch = (bool) $values['watch'];
		$isWatching = $topicWatchesTable
		->select()
		->from($topicWatchesTable->info('name'), 'watch')
		->where('resource_id = ?', $group->getIdentity())
		->where('topic_id = ?', $topic->getIdentity())
		->where('user_id = ?', $viewer->getIdentity())
		->limit(1)
		->query()
		->fetchColumn(0)
		;
		
		$db = $group->getTable()->getAdapter();
		$db->beginTransaction();
		
		try
		{
			// Create post
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
		
			// Watch
			if( false === $isWatching ) {
				$topicWatchesTable->insert(array(
						'resource_id' => $group->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if( $watch != $isWatching ) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $group->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			// Activity
			$action = $activityApi->addActivity($viewer, $group, $this->getActivityType('group_topic_reply'), null, array('child_id' => $topic->getIdentity()));
			if( $action ) 
			{
				$action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
		
		
			// Notifications
			$notifyUserIds = $topicWatchesTable->select()
			->from($topicWatchesTable->info('name'), 'user_id')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('watch = ?', 1)
			->query()
			->fetchAll(Zend_Db::FETCH_COLUMN)
			;
		
			$view = Zend_Registry::get("Zend_View");
			
			foreach( $userTable->find($notifyUserIds) as $notifyUser ) 
			{
				if( $notifyUser->isSelf($viewer) ) 
				{
					continue;
				}
				if( $notifyUser->isSelf($topicOwner) ) 
				{
					$type = 'group_discussion_response';
				} else 
				{
					$type = 'group_discussion_reply';
				}
				$notifyApi->addNotification($notifyUser, $viewer, $topic, $this->getActivityType($type), array(
						'message' => $view->BBCode($post->body),
				));
			}
		
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => Zend_Registry::get('Zend_Translate') -> _("Posted reply successfully!"),
					'iPostId' => $post->getIdentity(),
					'iTopicId' => $iTopicId,
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 1,
					'error_message' => $e->getMessage()
			);
		}
	}
	
	public function edit_post($aData)
	{
		extract($aData);
		
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		if (!isset($sBody) || $sBody == "")
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("sBody is required and can't be empty")
			);
		}
		
		$post = $this->getWorkingItem('group_post', $iPostId);
		
		$group = $post->getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($viewer, 'topic.edit') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to edit this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$post->modified_date = date('Y-m-d H:i:s');
			$post->body = html_entity_decode($sBody, ENT_QUOTES, 'UTF-8');
			$post->save();
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Edited post successfully!"),
				'iPostId' => $post->getIdentity(),
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function delete_post($aData)
	{
		extract($aData);
		
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
		
		$post = $this->getWorkingItem('group_post', $iPostId);
		
		$group = $post->getParent('group');
		$viewer = Engine_Api::_()->user()->getViewer();
		
		if( !$group->isOwner($viewer) && !$post->isOwner($viewer) && !$group->authorization()->isAllowed($user, 'topic.edit') )
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this post")
			);
		}
		
		// Process
		$table = $post->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		$topic_id = $post->topic_id;
	
		try
		{
			$post->delete();
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get('Zend_Translate') -> _("Deleted post successfully!"),
				'iTopicId' => $topic_id,
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_watch($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iWatch))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iWatch!")
			);
		}
		
        $topic = $this->getWorkingItem('group_topic', $iTopicId);
			
		$group = $this->getWorkingItem('group', $topic->group_id);
        
		$viewer = Engine_Api::_()->user()->getViewer();
		
		$watch = ( isset($iWatch) && $iWatch == '1' ) ? true : false;

		$topicWatchesTable = $this->getWorkingTable('topicWatches','group');
		
		$db = $topicWatchesTable->getAdapter();
		$db->beginTransaction();
		
		try
		{
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
		
			if( false === $isWatching ) {
				$topicWatchesTable->insert(array(
						'resource_id' => $group->getIdentity(),
						'topic_id' => $topic->getIdentity(),
						'user_id' => $viewer->getIdentity(),
						'watch' => (bool) $watch,
				));
			} else if( $watch != $isWatching ) {
				$topicWatchesTable->update(array(
						'watch' => (bool) $watch,
				), array(
						'resource_id = ?' => $group->getIdentity(),
						'topic_id = ?' => $topic->getIdentity(),
						'user_id = ?' => $viewer->getIdentity(),
				));
			}
		
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => ($watch) 
					? Zend_Registry::get('Zend_Translate')->_("Set watching successfully")
					: Zend_Registry::get('Zend_Translate')->_("Unset watching successfully")
			);
		}
		
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
		
	}
	
	public function topic_sticky($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iSticky))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iSticky!")
			);
		}
		
		
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$sticky = ( isset($iSticky) && $iSticky == '1' ) ? true : false;
		
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->sticky = (bool) $sticky;
			$topic->save();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => ($sticky)
					? Zend_Registry::get('Zend_Translate')->_("Set sticky successfully")
					: Zend_Registry::get('Zend_Translate')->_("Unset sticky successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_close($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($iClosed))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iClosed!")
			);
		}
		
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$closed = ( isset($iClosed) && $iClosed == '1' ) ? true : false;
		
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->closed = (bool) $closed;
			$topic->save();
	
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => '',
					'message' => ($closed)
					? Zend_Registry::get('Zend_Translate')->_("Closed topic successfully")
					: Zend_Registry::get('Zend_Translate')->_("Opened topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	}
	
	public function topic_rename($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		if (!isset($sTitle))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing sTitle!")
			);
		}
		
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        
		$viewer = Engine_Api::_()->user()->getViewer();
	
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$topic->title = htmlspecialchars($sTitle);
			$topic->save();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("Renamed topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
	}
	
	public function topic_delete($aData)
	{
		extract($aData);
		if (!isset($iTopicId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iTopicId!")
			);
		}
		
		$topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        
		
		$viewer = Engine_Api::_()->user()->getViewer();
	
		$table = $topic->getTable();
		$db = $table->getAdapter();
		$db->beginTransaction();
	
		try
		{
			$group = $topic->getParent('group');
			$topic->delete();
			$db->commit();
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get('Zend_Translate')->_("Deleted topic successfully")
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
				'error_code' => 1,
				'error_message' => $e->getMessage()
			);
		}
	
	}
	
	public function create_topic($aData)
	{
		extract($aData);
		if (!isset($iGroupId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iGroupId!")
			);
		}
        $table = $groupTbl = $this->getWorkingTable('groups','group');
		$group = $table->findRow($iGroupId);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->authorization()->isAllowed($group, $viewer, 'comment'))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have any permission to create topic!")
			);
		}
	
		if (!isset($sTitle) || empty($sTitle))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic title!")
			);
		}
		
		if (!isset($sBody) || empty($sBody))
		{
			return array(
					'error_code' => 2,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing topic body!")
			);
		}
		
		// Process
		$values = array(
			'title' => $sTitle, 
			'body' => $sBody,
			'watch' => (isset($iWatch) && ($iWatch == '0' || $iWatch == '1')) ? $iWatch : 1,  	
		);
		
		$values['user_id'] = $viewer->getIdentity();
		$values['group_id'] = $group->getIdentity();
	
		$topicTable = $this->getWorkingTable('topics', 'group');
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        $postTable = $this->getWorkingTable('posts','group');
		
	
		$db = $group->getTable()->getAdapter();
		$db->beginTransaction();
	
		try
		{
			// Create topic
			$topic = $topicTable->createRow();
			$topic->setFromArray($values);
			$topic->save();
	
			// Create post
			$values['topic_id'] = $topic->topic_id;
	
			$post = $postTable->createRow();
			$post->setFromArray($values);
			$post->save();
	
			// Create topic watch
			$topicWatchesTable->insert(array(
					'resource_id' => $group->getIdentity(),
					'topic_id' => $topic->getIdentity(),
					'user_id' => $viewer->getIdentity(),
					'watch' => (bool) $values['watch'],
			));
	
			// Add activity
			$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
			$action = $activityApi->addActivity($viewer, $group, $this->getActivityType('group_topic_create'), null, array('child_id' => $topic->getIdentity()));
			if( $action ) {
				$action->attach($topic, Activity_Model_Action::ATTACH_DESCRIPTION);
			}
	
			$db->commit();
			return array(
				'error_code' => 0,
				'error_message' => '',
				'message' => Zend_Registry::get("Zend_Translate")->_("Created topic successfully!"),
				'iTopicId' => $topic->getIdentity()
			);
		}
	
		catch( Exception $e )
		{
			$db->rollBack();
			return array(
					'error_code' => 3,
					'error_message' => $e->getMessage(),
			);
		}
	}
	
	public function topic_info($aData)
	{
		extract($aData);
		if (!isset($iTopicId) || empty($iTopicId))
		{
			return array(
					'error_code' => 0,
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing Topic identity!")
			);
		}
        
        $topic = $this->getWorkingItem('group_topic', $iTopicId);
        $topicWatchesTable = $this->getWorkingTable('topicWatches','group');
        if( is_null($topic) )
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This topic is not existed!")
			);
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		$group = $topic->getParentGroup();
	
		// Check watching
		$isWatching = null;
		if( $viewer->getIdentity() ) {
			
			
			$isWatching = $topicWatchesTable
			->select()
			->from($topicWatchesTable->info('name'), 'watch')
			->where('resource_id = ?', $group->getIdentity())
			->where('topic_id = ?', $topic->getIdentity())
			->where('user_id = ?', $viewer->getIdentity())
			->limit(1)
			->query()
			->fetchColumn(0)
			;
			if( false === $isWatching ) {
				$isWatching = null;
			} else {
				$isWatching = (bool) $isWatching;
			}
		}
	
		// Auth for topic
		$canPost = false;
		$canEdit = false;
		$canDelete = false;
		if( !$topic->closed && Engine_Api::_()->authorization()->isAllowed($group, null, 'comment') ) 
		{
			$canPost = true;
		}
		$canDelete = $canEdit = $topic->canEdit($viewer);
		return array(
				'iTopicId' => $iTopicId,
				'sTopicTitle' => $topic->getTitle(),
				'iGroupId' => $group->getIdentity(),
				'sGroupTitle' => $group->getTitle(),
				'bCanPost' => $canPost,
				'bCanEdit' => $canEdit,
				'bCanDelete' => $canDelete,
				'bIsWatching' => $isWatching,
				'bIsSticky' => ($topic->sticky) ? true : false,
				'bIsClosed' => ($topic->closed) ? true : false,
		);
	}
	
	public function post_info($aData)
	{
		extract($aData);
		if (!isset($iPostId))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("Missing iPostId!")
			);
		}
	    
        $post  =  $this->getWorkingTable('posts','group')->findRow($iPostId);
	
		if (is_null($post))
		{
			return array(
					'error_code' => 1,
					'error_message' => Zend_Registry::get('Zend_Translate') -> _("This post is not existed!")
			);
		}
		
		$user = $post->getOwner();
		$body = $post->body;
		$doNl2br = false;
		if( strip_tags($body) == $body ) {
			$body = nl2br($body);
		}
			
		$postPhoto = "";
		$userPhoto = $user -> getPhotoUrl(TYPE_OF_USER_IMAGE_NORMAL);
		if ($userPhoto != "")
		{
			$userPhoto = Engine_Api::_() -> ynmobile() -> finalizeUrl($userPhoto);
		}
		else
		{
			$userPhoto = NO_USER_ICON;
		}
		
		return array(
				'iPostId' => $iPostId,
				'iUserId' => $post->user_id,
				'sUserName' => $user->getTitle(),
				'sUserPhoto' => $userPhoto,
				'sSignature' =>  "",
				'sCreationDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->creation_date)),
				'sModifiedDate' => Engine_Api::_() -> ynmobile() -> calculateDefaultTimestamp(strtotime($post->modified_date)),
				'sBody' => $body,
				'sPhotoUrl' => $postPhoto,
		);
	}
	
	
}
