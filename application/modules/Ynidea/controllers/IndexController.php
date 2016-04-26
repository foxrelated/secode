<?php

class Ynidea_IndexController extends Core_Controller_Action_Standard
{

	/**
	 * idea box home
	 */
	public function indexAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function createAction()
	{
		// $this -> _helper -> content -> setNoRender() -> setEnabled();

		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynidea_idea', null, 'create') -> isValid())
			return;

		$form = $this -> view -> form = new Ynidea_Form_CreateIdea;
		
		$categories = Engine_Api::_() -> getItemTable('ynidea_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }
		
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynidea_idea');

		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create idea idea
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), ));

			if (Engine_Api::_() -> ynidea() -> checkTitle($values['title']))
			{
				$form -> getElement('title') -> addError('The title have existed!');
				return;
			}

			$idea = $table -> createRow();
			$idea -> setFromArray($values);
			$idea -> save();

			// Auth
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			if (empty($values['auth_edit']))
			{
				$values['auth_edit'] = 'owner';
			}

			if (empty($values['auth_delete']))
			{
				$values['auth_delete'] = 'owner';
			}

			if (empty($values['auth_vote']))
			{
				$values['auth_vote'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$editMax = array_search($values['auth_edit'], $roles);
			$deleteMax = array_search($values['auth_delete'], $roles);
			$voteMax = array_search($values['auth_vote'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($idea, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($idea, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($idea, $role, 'edit', ($i <= $editMax));
				$auth -> setAllowed($idea, $role, 'delete', ($i <= $deleteMax));
				$auth -> setAllowed($idea, $role, 'vote', ($i <= $voteMax));
			}

			// Set photo
			if (!empty($values['thumbnail']))
			{
				$idea -> setPhoto($form -> thumbnail);
			}

			$version_table = Engine_Api::_() -> getItemTable('ynidea_version');

			$version = $version_table -> createRow();
			$version -> setFromArray($values);
			$version -> idea_id = $idea -> idea_id;
			$version -> idea_version = 1;
			$version -> save();

			$idea -> version_id = $version -> version_id;
			$idea -> version = 1;
			$idea -> version_date = date('Y-m-d H:i:s');
			$idea -> save();

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$idea -> tags() -> addTagMaps($viewer, $tags);

			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'detail',
			'id' => $idea -> idea_id,
			'slug' => $idea -> getSlug()
		), 'ynidea_specific', true);

	}

	/**
	 * get ajax ideas
	 *
	 *
	 */
	public function ajaxIdeasAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$params = array();
		$params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('idea.page', 10);
		$params['direction'] = 'DESC';
		$tab = $this -> _getParam('tab', 1);
		$page = $this -> _getParam('page', 1);
		$trophy_id = $this -> _getParam('trophy_id', 0);
		switch($tab)
		{
			case 1 :
				$params['orderby'] = 'title';
				$params['direction'] = 'ASC';
				break;
			case 2 :
				$params['orderby'] = 'ideal_score';
				break;
			case 3 :
				$params['orderby'] = 'ideal_score';
				break;
			/*case 4:
			 $params['orderby'] = 'vote_ave';
			 break;*/
		}
		$paginator = Engine_Api::_() -> getApi('core', 'ynidea') -> getIdeaPaginator($params);
		$paginator -> setCurrentPageNumber($page);
		echo $this -> view -> partial(Ynidea_Api_Core::partialViewFullPath('_list_nominees.tpl'), array(
			'arr_ideas' => $paginator,
			'tab' => $tab,
			'trophy_id' => $trophy_id
		));
	}

	public function editNewVersionAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> _getParam('id'));
		//if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'edit')->isValid() ) return;
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();

		$this -> view -> form = $form = new Ynidea_Form_EditVersionIdea();
		$form -> removeElement('tags');
		$form -> removeElement('thumbnail');
		//$form->removeElement('cost');
		//$form->removeElement('feasibility');
		//$form->removeElement('reproducible');
		
		$categories = Engine_Api::_() -> getItemTable('ynidea_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }
		
		//get newest version
		$newest_version = $idea -> getNewestVersion();
		// Populate form
		if ($newest_version -> version_id == $idea -> version_id || !$newest_version)
			$form -> populate($idea -> toArray());
		else
		{
			$form -> populate($newest_version -> toArray());
		}

		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynidea_idea');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			if ($values['title'] != $idea -> title)
			{
				if (Engine_Api::_() -> ynidea() -> checkTitle($values['title']))
				{
					$form -> getElement('title') -> addError('The title have existed!');
					return;
				}
			}

			$version_table = Engine_Api::_() -> getItemTable('ynidea_version');

			$val['idea_id'] = $idea -> idea_id;
			$val['title'] = $values['title'];
			$val['body'] = $values['body'];
			$val['description'] = $values['description'];
			$val['cost'] = $values['cost'];
			$val['feasibility'] = $values['feasibility'];
			$val['reproducible'] = $values['reproducible'];
			$val['allow_campaign'] = $values['allow_campaign'];
			if ($newest_version -> version_id == $idea -> version_id || !$newest_version)
			{
				if ($idea -> publish_status == 'publish')
				{
					$version = $version_table -> createRow();
					$version -> setFromArray($val);
					$version -> user_id = $viewer -> user_id;
					$version -> idea_version = $idea -> getNewVersionCount();
					$version -> idea_id = $idea -> idea_id;
				}
				else
				{
					$version = $newest_version;
					$version -> setFromArray($val);
					$version -> user_id = $viewer -> user_id;
					$idea -> setFromArray($val);
					$idea -> save();
				}
			}
			else
			{
				$version = $newest_version;
				$version -> user_id = $viewer -> user_id;
				$version -> setFromArray($val);
			}
			$version -> save();

			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'detail',
			'id' => $idea -> idea_id,
			'slug' => $idea -> getSlug()
		), 'ynidea_specific', true);
	}

	public function editAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> _getParam('id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($idea, $viewer, 'edit') -> isValid())
			return;
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();

		$this -> view -> form = $form = new Ynidea_Form_EditIdea();
		
		$categories = Engine_Api::_() -> getItemTable('ynidea_category') -> getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
            }
        }
		
		// Populate form
		$form -> populate($idea -> toArray());
		$tagStr = '';
		foreach ($idea->tags()->getTagMaps() as $tagMap)
		{
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));
		$this -> view -> tagNamePrepared = $tagStr;

		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);

		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynidea_idea');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			if ($values['title'] != $idea -> title)
			{
				if (Engine_Api::_() -> ynidea() -> checkTitle($values['title']))
				{
					$form -> getElement('title') -> addError('The title have existed!');
					return;
				}
			}

			$idea -> setFromArray($values);
			$idea -> modified_date = date('Y-m-d H:i:s');
			$idea -> save();

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			if (empty($values['auth_edit']))
			{
				$values['auth_edit'] = 'owner';
			}

			if (empty($values['auth_delete']))
			{
				$values['auth_delete'] = 'owner';
			}

			if (empty($values['auth_vote']))
			{
				$values['auth_vote'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);
			$editMax = array_search($values['auth_edit'], $roles);
			$deleteMax = array_search($values['auth_delete'], $roles);
			$voteMax = array_search($values['auth_vote'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($idea, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($idea, $role, 'comment', ($i <= $commentMax));
				$auth -> setAllowed($idea, $role, 'edit', ($i <= $editMax));
				$auth -> setAllowed($idea, $role, 'delete', ($i <= $deleteMax));
				$auth -> setAllowed($idea, $role, 'vote', ($i <= $voteMax));
			}

			// Set photo
			if (!empty($values['thumbnail']))
			{
				$idea -> setPhoto($form -> thumbnail);
			}

			// handle tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$idea -> tags() -> setTagMaps($viewer, $tags);

			// Commit
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(), 'ynidea_myideas', true);
	}

	public function detailAction()
	{
		$subject = null;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			$id = $this -> _getParam('id');
			if (null !== $id)
			{
				$subject = Engine_Api::_() -> getItem('ynidea_idea', $id);

				if ($subject && $subject -> getIdentity() && $subject -> publish_status == 'publish')
				{
					Engine_Api::_() -> core() -> setSubject($subject);
				}
				elseif ($subject -> isOwner($viewer))
				{
					Engine_Api::_() -> core() -> setSubject($subject);
				}
				else
				{
					return $this -> _helper -> requireAuth -> forward();
				}
			}
		}
		$subject = Engine_Api::_() -> core() -> getSubject();
		$this -> _helper -> requireSubject('ynidea_idea');

		if (!$this -> _helper -> requireAuth() -> setAuthParams($subject, $viewer, 'view') -> isValid())
			return;
		if (!$subject -> getOwner() -> isSelf($viewer))
		{
			$subject -> view_count++;
			$subject -> save();
		}
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function uploadPhotoAction()
	{
		// Disable layout
		$this -> _helper -> layout -> disableLayout();

		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$destination = "public/ynidea/";
		if (!is_dir($destination))
		{
			mkdir($destination);
		}
		$destination = "public/ynidea/" . $user_id . "/";
		if (!is_dir($destination))
		{
			mkdir($destination);
		}
		$upload = new Zend_File_Transfer_Adapter_Http();
		$upload -> setDestination($destination);
		$fullFilePath = $destination . time() . '_' . $upload -> getFileName('Filedata', false);

		$image = Engine_Image::factory();
		$image -> open($_FILES['Filedata']['tmp_name']) -> resize(720, 720) -> write($fullFilePath);

		$this -> view -> status = true;
		$this -> view -> name = $_FILES['Filedata']['name'];
		//$this->view->photo_url = Zend_Registry::get('StaticBaseUrl') . $fullFilePath;
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$url = $request -> getScheme() . '://' . $request -> getHttpHost() . $request -> getBaseUrl();
		$this -> view -> photo_url = $url . "/" . $fullFilePath;
		$this -> view -> photo_width = $image -> getWidth();
		$this -> view -> photo_height = $image -> getHeight();
	}

	public function assignAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea_id = (int)$this -> _getParam('id');
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		$this -> view -> idea_id = $idea_id;
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();
		$this -> view -> form = $form = new Ynidea_Form_AssignCoAuthor();
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = Engine_Api::_() -> getDbTable('coauthors', 'ynidea') -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$values = $this -> getRequest() -> getPost();
				//Insert co-authors
				$coauthors = array_unique(explode(',', preg_replace('/\s+/u', '', $values['toValues'])));
				$coauthor_table = Engine_Api::_() -> getItemTable('ynidea_coauthor');
				$coauthor_item['idea_id'] = $values['idea_id'];
				foreach ($coauthors as $co)
				{
					if ($co != '' && $co != 0)
					{
						$idea -> clearVoteCoAuthor($co);
						$coauthor_item['user_id'] = $co;
						$coauthor = $coauthor_table -> createRow();
						$coauthor -> setFromArray($coauthor_item);
						$coauthor -> save();

					}
				}
				$db -> commit();
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => true,
					'format' => 'smoothbox',
					'messages' => array($this -> view -> translate('Assign successfully.'))
				));
			}
			catch (Exception $e)
			{
				$db -> rollback();
				$this -> view -> success = false;
			}
		}
	}

	public function suggestAction()
	{
		$idea_id = (int)$this -> _getParam('idea_id');
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity() || !$idea)
		{
			$data = null;
		}
		else
		{
			$data = array();
			$table = Engine_Api::_() -> getItemTable('user');
			$select = $table -> select();

			if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
			{
				$select -> limit($limit);
			}

			if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
			{
				$select -> where('`' . $table -> info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
			}

			$ids = array();
			foreach ($table->fetchAll($select) as $user)
			{
				if (!$idea -> checkCoauthors($user))
				{
					$data[] = array(
						'type' => 'user',
						'id' => $user -> getIdentity(),
						'guid' => $user -> getGuid(),
						'label' => $user -> getTitle(),
						'photo' => $this -> view -> itemPhoto($user, 'thumb.icon'),
						'url' => $user -> getHref(),
					);
					$ids[] = $user -> getIdentity();
				}
			}
		}
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

	public function downloadPdfAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea_id = (int)$this -> _getParam('id');
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();
		else
		{
			$this -> _helper -> layout -> disableLayout();
			$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		}
		$pdf = new HTML2PDF('P', 'A4', 'fr');
		$pdf -> pdf -> SetDisplayMode('real');

		$site_logo = '';
		$logo = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('idea.sitelogo', '');
		if ($logo)
		{
			$logo_url = $logo;
			$site_logo = '<img src="' . $logo_url . '" alt="" class="thumb_normal">  ';
		}
		$idea_box = $this -> view -> translate("Idea Box");

		//Add rule for html2pdf
		$description = preg_replace("/\<colgroup.*?\<\/colgroup\>/", '', $idea -> description);
		$content = preg_replace("/\<colgroup.*?\<\/colgroup\>/", '', $idea -> body);
		$idea_photo = $idea -> getPhotoUrl('thumb.icon');

		$image_url = "";
		$idea_url_photo = "";
		if ($idea_photo != "")
		{
			if (strpos($idea_photo, 'https://') == FALSE && strpos($idea_photo, 'http://') == FALSE)
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$pageURL .= $_SERVER["SERVER_NAME"];
				$idea_photo = $pageURL . $idea_photo;
			}
			$idea_url_photo = '<img src="' . $idea_photo . '" alt="" class="thumb_icon">  ';
		}
		$pdf -> WriteHTML('<page style="font-family: freeserif"><br />' . nl2br($site_logo . '<h3 style="text-align:center">' . $idea_box . '</h3>') . '<h4>' . nl2br($idea_url_photo . $idea -> title) . '</h4>' . '<h5>' . $this -> view -> translate("Summary") . '</h5>' . nl2br($description) . '<h5>' . $this -> view -> translate("Description") . '</h5>' . nl2br($content) . '</page>');

		$name = "idea_" . $idea -> idea_id . ".pdf";
		$pdf -> Output($name);
	}

	public function reportAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> _getParam('id'));
		if (!$idea || !$viewer -> getIdentity())
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynidea_Form_Report();
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getDbtable('reports', 'Ynidea');
		$db = $table -> getAdapter();
		$db -> beginTransaction();
		$values = $form -> getValues();
		try
		{
			// Create report
			$values = array_merge($form -> getValues(), array(
				'user_id' => $viewer -> getIdentity(),
				'idea_id' => $idea -> idea_id
			));

			$report = $table -> createRow();
			$report -> setFromArray($values);
			$report -> creation_date = date('Y-m-d H:i:s');
			$report -> modified_date = date('Y-m-d H:i:s');
			$report -> save();

			//Send message to admin
			$content = $values['content'];
			$type = $values['type'];
			$users = Ynidea_Api_Core::getAllAdmins();
			if ($users)
			{
				foreach ($users as $user)
				{
					if ($user -> getIdentity() != $viewer -> getIdentity())
					{
						// Create conversation
						$conversation = Engine_Api::_() -> getItemTable('messages_conversation') -> send($viewer, $user, $type, $content, null);

						// Send notifications
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $conversation, 'message_new');

						// Increment messages counter
						Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('messages.creations');
					}
				}
			}

			// Commit
			$db -> commit();

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Report successfully.'))
			));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function giveAwardAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())//!$idea ||
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynidea_Form_Award();
		$this -> view -> idea_id = $this -> _getParam('id');
		$this -> view -> trophy_id = $this -> _getParam('trophy_id');
		$trophy = Engine_Api::_() -> getItem('ynidea_trophy', $this -> _getParam('trophy_id'));
		$this -> view -> onlysilver = 0;
		if ($trophy -> checkGoldAward())
		{
			$form -> award -> setMultiOptions(array(1 => 'Silver'));
			$this -> view -> onlysilver = 1;
		}
	}

	public function historyAction()
	{
		$idea_id = (int)$this -> _getParam('id');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())//!$idea ||
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Check page exist
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();
		$this -> view -> idea = $idea;
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> paginator = $paginator = $idea -> getRevisions();
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(10000000);
	}

	public function previewRevisionAction()
	{
		// Check permission
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$revision = Engine_Api::_() -> getItem('ynidea_version', $this -> _getParam('id'));
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $revision -> idea_id);
		//if( !$this->_helper->requireAuth()->setAuthParams($idea, $viewer, 'view')->isValid() ) {
		//return;
		//}
		if (!$idea || !$idea -> getIdentity())
		{
			//   ($idea->draft && !$idea->isOwner($viewer)) ) {
			return $this -> _helper -> requireSubject -> forward();
		}

		// Prepare data
		$ideaTable = Engine_Api::_() -> getDbtable('ideas', 'ynidea');

		$this -> view -> revision = $revision;
		$this -> view -> idea = $idea;
		$this -> view -> owner = $owner = $idea -> getOwner();
		$this -> view -> viewer = $viewer;

		// Get subject and check auth
		//$subject = Engine_Api::_()->core()->getSubject('ynidea_idea');
		//$this->view->idea = $subject;

		//Get tags
		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tm_table = Engine_Api::_() -> getDbtable('tagMaps', 'core');
		$p_table = Engine_Api::_() -> getItemTable('ynidea_idea');
		$tName = $t_table -> info('name');
		$tmName = $tm_table -> info('name');
		$pName = $p_table -> info('name');

		$filter_select = $tm_table -> select() -> from($tmName, "$tmName.*") -> setIntegrityCheck(false) -> joinLeft($pName, "$pName.idea_id = $tmName.resource_id", '') -> where("$pName.idea_id = ?", $idea -> getIdentity());

		$select = $t_table -> select() -> from($tName, array(
			"$tName.*",
			"Count($tName.tag_id) as count"
		));
		$select -> joinLeft($filter_select, "t.tag_id = $tName.tag_id", '');
		$select -> order("$tName.text");
		$select -> group("$tName.text");
		$select -> where("t.resource_type = ?", "ynidea_idea");
		$this -> view -> tags = $tags = $t_table -> fetchAll($select);

	}

	public function publishAction()
	{
		$idea_id = (int)$this -> _getParam('id');
		// Check page exist
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynidea_Form_PublishIdea();
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}
		if (!$idea)
			return $this -> _helper -> requireAuth -> forward();
		$idea -> publish_status = 'publish';
		$idea -> save();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $idea, 'ynidea_idea_publish');
		if ($action)
		{
			Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $idea);
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Pulish successfully.'))
		));
	}

	public function publishHistoryAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea_id = (int)$this -> _getParam('id');
		// Check page exist
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);
		$old_version = Engine_Api::_() -> getItem('ynidea_version', $this -> _getParam('version_id'));
		if (!$idea || !$old_version)
			return $this -> _helper -> requireAuth -> forward();
		if (!$idea -> authorization() -> isAllowed($viewer, 'edit'))
			return $this -> _helper -> requireAuth -> forward();
		//save vote to before version
		$before_version = Engine_Api::_() -> getItem('ynidea_version', $idea -> version_id);
		if ($before_version)
		{
			$before_version -> vote_ave = $idea -> vote_ave;
			$before_version -> potential_ave = $idea -> potential_ave;
			$before_version -> innovation_ave = $idea -> innovation_ave;
			$before_version -> feasibility_ave = $idea -> feasibility_ave;
			$before_version -> vote_count = $idea -> vote_count;
			$before_version -> cost = $idea -> cost;
			$before_version -> feasibility = $idea -> feasibility;
			$before_version -> reproducible = $idea -> reproducible;
			$before_version -> allow_campaign = $idea -> allow_campaign;
			$before_version -> save();
		}

		$idea -> publish_status = 'publish';
		$idea -> version = $old_version -> idea_version;
		$idea -> version_id = $old_version -> version_id;
		$idea -> version_date = $old_version -> modified_date;
		$idea -> title = $old_version -> title;
		$idea -> body = $old_version -> body;
		$idea -> description = $old_version -> description;
		$idea -> cost = $old_version -> cost;
		$idea -> feasibility = $old_version -> feasibility;
		$idea -> reproducible = $old_version -> reproducible;
		$idea -> allow_campaign = $old_version -> allow_campaign;
		$idea -> save();

		$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $idea, 'ynidea_version_publish');
		if ($action)
		{
			Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $idea);
		}

		// send notification to all follow users
		$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

		$userFollows = $idea -> getFollows();
		foreach ($userFollows as $follow)
		{
			if ($follow -> user_id != $viewer -> getIdentity())
			{
				$userFollow = Engine_Api::_() -> getItem('user', $follow -> user_id);
				$notifyApi -> addNotification($userFollow, $viewer, $idea, 'ynidea_version_publish', array('label' => $idea -> title));
			}
		}

		$voters = Engine_Api::_() -> ynidea() -> getAllVoters($idea -> idea_id);
		foreach ($voters as $voter)
		{
			if ($voter -> user_id != $viewer -> getIdentity())
			{
				$uvoter = Engine_Api::_() -> getItem('user', $voter -> user_id);
				$notifyApi -> addNotification($uvoter, $viewer, $idea, 'ynidea_version_publish', array('label' => $idea -> title));
			}
		}

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Publish successfully.'))
		));
	}

	public function editHistoryAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$version = Engine_Api::_() -> getItem('ynidea_version', $this -> _getParam('version_id'));
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> _getParam('id'));
		if (!$version || !$idea)
			return $this -> _helper -> requireAuth -> forward();
		if (!$idea -> authorization() -> isAllowed($viewer, 'edit'))
			return $this -> _helper -> requireAuth -> forward();
		$this -> view -> form = $form = new Ynidea_Form_EditIdea();
		$form -> removeElement('tags');
		$form -> removeElement('thumbnail');
		//$form->removeElement('cost');
		//$form->removeElement('feasibility');
		//$form->removeElement('reproducible');
		$form -> removeElement('auth_view');
		$form -> removeElement('auth_comment');
		$form -> removeElement('auth_edit');
		$form -> removeElement('auth_delete');

		// Populate form
		$form -> populate($version -> toArray());

		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynidea_version');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			if ($values['title'] != $version -> title)
			{
				if (Engine_Api::_() -> ynidea() -> checkTitle($values['title']))
				{
					$form -> getElement('title') -> addError('The title have existed!');
					return;
				}
			}

			$version -> setFromArray($values);
			$version -> modified_date = date('Y-m-d H:i:s');
			$version -> save();
			$db -> commit();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect
		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'preview-revision',
			'id' => $version -> version_id
		), 'ynidea_specific', true);
	}

	public function deleteHistoryAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$version = Engine_Api::_() -> getItem('ynidea_version', $this -> _getParam('version_id'));
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> _getParam('id'));
		if (!$version || !$idea)
			return $this -> _helper -> requireAuth -> forward();
		if (!$idea -> authorization() -> isAllowed($viewer, 'edit'))
			return $this -> _helper -> requireAuth -> forward();
		try
		{
			$version -> delete();
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Delete successfully.'))
		));
	}

	public function deleteAction()
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $this -> getRequest() -> getParam('id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($idea, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynidea_Form_DeleteIdea();

		if (!$idea)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Idea doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $idea -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> getApi('core', 'ynidea') -> deleteidea($idea);
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Idea has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
				'controller' => 'my-ideas',
				'action' => 'index'
			), 'ynidea_extended', true),
			'messages' => Array($this -> view -> message)
		));
	}

	public function removeCoauthorAction()
	{
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Ynidea_Form_RemoveCoauthor;

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$idea_id = (int)$this -> _getParam('id');
		$idea = Engine_Api::_() -> getItem('ynidea_idea', $idea_id);

		$author_id = (int)$this -> _getParam('author_id');
		$author = Engine_Api::_() -> getItem('ynidea_coauthor', $author_id);

		if (!$idea || !$author)
			return $this -> _helper -> requireAuth -> forward();
		$db = Engine_Api::_() -> getDbtable('coauthors', 'ynidea') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$author -> delete();
			$db -> commit();
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Delete co-author successfully.'))
			));

		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}

	}

}
