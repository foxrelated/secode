<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageurl_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR SENDING THE EMAIL
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageurl_admin_main', array(), 'sitepageurl_admin_main_settings');
   $db = Engine_Db_Table::getDefaultAdapter();

    //GET FORM
    $this->view->form = $form = new Sitepageurl_Form_Admin_Global();

    if( !$this->getRequest()->isPost() ) {
			$includeModules = array("sitepage" => "sitepage","sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music","sitebusiness" => "sitebusiness","sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music","list"=>"list");
				$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
				$select = $moduleTable->select()->where('enabled = ?', 1);
				$enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
				$enableModules = array_intersect(array_keys($includeModules), $enableAllModules);
				foreach ($enableAllModules as $moduleName) {
					if(!in_array($moduleName,$enableModules)) {
						$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
						if (@file_exists($file_path)) {
							$ret = include $file_path;
							$is_exist = array();
							if (isset($ret['routes'])) {
								foreach ($ret['routes'] as $item) {
									$route = $item['route'];
									$route_array =  explode('/',$route);
									$route_url = strtolower($route_array[0]);
									if(!empty($route_url) && !in_array($route_url,$is_exist)) {
										$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
									}
									$is_exist[] = $route_url;
								}
							}
						} 
					}
					else {
						if($moduleName == 'sitepage' || $moduleName == 'sitebusiness') {
							$name = $moduleName .'.manifestUrlS';
						}
						else {
							$name = $moduleName .'.manifestUrl';
						}
						$select = new Zend_Db_Select($db);
						$value = $select
													->from('engine4_core_settings','value')
													->where('name = ?', $name)
													->query()
													->fetchColumn();
						$route_url = strtolower($value);
						if(!empty($route_url)) {
							$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
						}
					}
				}
      }
     // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    // Okay, save
    if(($values['sitepage_likelimit_forurlblock'] >= 5)) {
			foreach( $values as $key => $value ) {
				if($value != '') {
					Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
				}
			}
    }
    else {
			$error = Zend_Registry::get('Zend_Translate')->_('The value that you enter for Likes Limit for Active Short URL should be 5 or greater.');
			$form->getDecorator('errors')->setOption('escape', false);
			$form->addError($error);
			return;
    }
    
    Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageurl_is_enable', 1);
    $this->view->form = $form = new Sitepageurl_Form_Admin_Global();
    $form->addNotice('Your changes have been saved.');
    $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageurl.is.enable', 0);
    $query_flag = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageurl.query.flag', 0);
    	
		if(!empty($is_element) && empty($query_flag)) {
			Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageurl.query.flag', 1);
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`,
				`menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
				("sitepageurl_admin_blockurl", "sitepageurl", "Banned URLs", "",
				\'{"route":"admin_default","module":"sitepageurl","controller":"settings","action":"banningurl"}\',
				"sitepageurl_admin_main", "", 1,0,2),
				( "sitepageurl_admin_main_url", "sitepageurl", "Pages with Banned URLs", NULL,
				\'{"route":"admin_default","module":"sitepageurl","controller":"settings","action":"pageurl"}\',
				"sitepageurl_admin_main", NULL, 1, 0, 3);');
				return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
		}
  }

  //ACTION FOR FAQ
  public function faqAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageurl_admin_main', array(), 'sitepageurl_admin_main_faq');
  }

   //ACTION FOR BANNEDURL
  public function banningurlAction() {

    //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepageurl_admin_main', array(), 'sitepageurl_admin_blockurl');

    $this->view->formFilter = $formFilter = new Sitepageurl_Form_Admin_Manage_Filter();
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    if (isset($_POST['search'])) {
      if (!empty($_POST['word'])) {
        $this->view->word = $_POST['word'];
        $values['word'] = $_POST['word'];
      }
    }
  
    $values = array_merge(array(
                'order' => 'bannedpageurl_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

     // Load all words
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $this->view->paginator = $paginator = $bannedPageurlsTable->getWords($values);
    $page=$this->_getParam('page',1);
    $this->view->paginator->setItemCountPerPage(500);
    $this->view->paginator->setCurrentPageNumber($page);
 
  }

  public function createUrlAction() {

     // Get form
    $this->view->form = $form = new Sitepageurl_Form_Admin_Settings_Blockurl();

     // Load all words
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			try {
				$values = $form->getValues();

				// Save Banned Words
				$bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);
			
				$words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

				$data = $bannedPageurlsTable->select()
																		->from($bannedPageurlsTable, 'word')
																		->query()
																		->fetchAll(Zend_Db::FETCH_COLUMN);
				if(in_array($words[0],$data)) {
					$form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
						return;
				}
				$bannedPageurlsTable->setWords($bannedWordsNew);
				$db->commit();
				//$form->addNotice('Your changes have been saved.');
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }
 
  }

  public function editUrlAction() {

    // Get form
    $this->view->form = $form = new Sitepageurl_Form_Admin_Settings_Blockurl();
    $url_id = $this->_getParam('id');
    $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
    $data = $bannedPageurlsTable->select()
        ->from($bannedPageurlsTable)
        ->where('bannedpageurl_id =?',$url_id)
        ->query()
        ->fetchAll();
    $form->setField($data);

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			
			try {
				$values = $form->getValues();
				// Save Banned Words
				$bannedWordsNew = preg_split('/\s*[,\n]+\s*/', $values['bannedwords']);
			
				$words = array_map('strtolower', array_filter(array_values($bannedWordsNew)));

				$data = $bannedPageurlsTable->select()
					->from($bannedPageurlsTable, 'word')
					->where('bannedpageurl_id !=?',$url_id)
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);
				if(in_array($words[0],$data)) {
					$form->addError(Zend_Registry::get('Zend_Translate')->_('This URL already exists.'));
						return;
				}
				$bannedPageurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
				$db->commit();
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }

  }

  public function deleteUrlAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID AND CHECK VALIDATION
    $this->view->id = $id = $this->_getParam('id');

    if(empty($id)) {
    	die('No identifier specified');
    }
    if( $this->getRequest()->isPost()) {

			//BEGIN TRANSACTION
    	$db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
				$selectdata = $bannedPageurlsTable->select()
        ->where('bannedpageurl_id =?',$id);
        $result_url = $bannedPageurlsTable->fetchRow($selectdata);
       $result_url->delete();
				//COMMIT
        $db->commit();
      }

      catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

  }
  
   public function addUrlAction() {

     // Get form
    $this->view->form = $form = new Sitepageurl_Form_Admin_Settings_Addurl();

     if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
			$bannedPageurlsTable = Engine_Api::_()->getDbtable('BannedPageurls', 'seaocore');
			// Process
			$db = Engine_Api::_()->getDbtable('settings', 'core')->getAdapter();
			$db->beginTransaction();
			
			
			try {
				$values = $form->getValues();

        $includeModules = array("sitepage" => "sitepage","sitepagedocument" => 'Documents', "sitepageoffer" => 'Offers', "sitepageform" => "Form", "sitepagediscussion" => "Discussions", "sitepagenote" => "Notes", "sitepagealbum" => "Photos", "sitepagevideo" => "Videos", "sitepageevent" => "Events", "sitepagepoll" => "Polls", "sitepageinvite" => "Invite & Promote", "sitepagebadge" => "Badges", "sitepagelikebox" => "External Badge", "sitepagemusic" => "Music","sitebusiness" => "sitebusiness","sitebusinessdocument" => 'Documents', "sitebusinessoffer" => 'Offers', "sitebusinessform" => "Form", "sitebusinessdiscussion" => "Discussions", "sitebusinessnote" => "Notes", "sitebusinessalbum" => "Photos", "sitebusinessvideo" => "Videos", "sitebusinessevent" => "Events", "sitebusinesspoll" => "Polls", "sitebusinessinvite" => "Invite & Promote", "sitebusinessbadge" => "Badges", "sitebusinesslikebox" => "External Badge", "sitebusinessmusic" => "Music","list"=>"list");
				$moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
				$select = $moduleTable->select()->where('enabled = ?', 1);
				$enableAllModules = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
				$enableModules = array_intersect(array_keys($includeModules), $enableAllModules);

        if(!in_array($values['module_name'],$enableModules)) {
					$file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($values['module_name']) . "/settings/manifest.php";
					if (@file_exists($file_path)) {
						$ret = include $file_path;
						$is_exist = array();
						if (isset($ret['routes'])) {
							foreach ($ret['routes'] as $item) {
								$route = $item['route'];
								$route_array =  explode('/',$route);
								$route_url = strtolower($route_array[0]);
								
								$data = $bannedPageurlsTable->select()
																						->from($bannedPageurlsTable, 'bannedpageurl_id')
																						->where('word =?',$route_url)
																						->query()
																						->fetchAll(Zend_Db::FETCH_COLUMN);

								if(empty($data)) { 
									if(!empty($route_url) && !in_array($route_url,$is_exist)) {
										$db->query("INSERT IGNORE INTO `engine4_seaocore_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
									}
								}
								else {
									$bannedPageurlsTable->update(array('word' => $route_url), array('bannedpageurl_id = ?' => $data[0]));
								}
								$is_exist[] = $route_url;
							}
						}
					} 
        }
        else {
						if($moduleName == 'sitepage' || $moduleName == 'sitebusiness') {
							$name = $moduleName .'.manifestUrlS';
						}
						else {
							$name = $moduleName .'.manifestUrl';
						}
						$settingTable = Engine_Api::_()->getDbtable('settings', 'core');
						$select = $settingTable->select()
                                   ->from($settingTable,'value')
                                  ->where('name = ?', $name);
						$route_url = strtolower($select->query()->fetchAll(Zend_Db::FETCH_COLUMN));
						if(!empty($route_url)) {
							$db->query("INSERT IGNORE INTO `engine4_sitepage_bannedpageurls` (`bannedpageurl_id`, `word`) VALUES ('','".$route_url. "')");
						}
					}


			  /*data = $bannedPageurlsTable->select()
					->from($bannedPageurlsTable, 'word')
					->where('word !=?',$values[])
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);*/
				$bannedPageurlsTable->update(array('word' => $words[0]), array('bannedpageurl_id = ?' => $url_id));
				$db->commit();
			} catch( Exception $e ) {
				$db->rollback();
				throw $e;
			}
			$this->_forward('success', 'utility', 'core', array(
						'smoothboxClose' => 10,
						'parentRefresh'=> 10,
						'messages' => array('')
				));
    }


  }

  public function pageurlAction() {
    
     //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepageurl_admin_main', array(), 'sitepageurl_admin_main_url');
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepageurl_Form_Admin_Manage_Filter();
     $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'bannedpageurl_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);
  
    $this->view->paginator = $paginator = Engine_Api::_()->sitepageurl()->getBlockUrl($values);
    $page=$this->_getParam('page',1);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($page);
  }

}

?>