<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminSettingsController extends Core_Controller_Action_Admin {

    protected $_ISFORMVALID = false;
    
    public function __call($method, $params) {

        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitegroup_Form_Admin_Global') {
					if(!empty($params) && isset($params[0]) && isset($params[1]) && !empty($params[0])) {
						$form = $params[0];
						$isformvalid = $form->isValid($params[1]);
						if(!empty($isformvalid)) {							
							$this->_ISFORMVALID = true;
						}else {
						return false;
						}
					}
        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
  
    $this->view->hasLanguageDirectoryPermissions = $hasLanguageDirectoryPermissions = Engine_Api::_()->getApi('language', 'sitegroup')->hasDirectoryPermissions();
    
    $this->view->groupPluginEnabled = Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('group');
    	$group = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.group", "group");
	$groups = Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.groups", "groups"); 
	    $oldLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.city', "World");
    $previousLocationFieldSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1);  
    
    $redirectionPrevious = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.redirection', 'home');    
      
    $pluginName = 'sitegroup';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
      
    include_once APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license1.php';
    
    $this->view->subnavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main_settings', array(), 'sitegroup_admin_main_general');
    
    if ($this->getRequest()->isPost() && !empty($this->_ISFORMVALID)) {
        
       if(!empty($_POST['sitegroup_package_information'])) {
            if(Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitegroup_package_information')) {
                Engine_Api::_()->getApi('settings', 'core')->removeSetting('sitegroup_package_information');
            }
            Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.package.information', $_POST['sitegroup_package_information']);
      }
        
      if ($_POST['sitegroup_lsettings']) {
        $_POST['sitegroup_lsettings'] = trim($_POST['sitegroup_lsettings']);
      }
      
      if(isset($_POST['sitegroup_locationfield']) && $previousLocationFieldSetting != $_POST['sitegroup_locationfield']) {
        if (isset($_POST['sitegroup_locationfield']) && $_POST['sitegroup_locationfield'] == '0') {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '0' WHERE `engine4_core_menuitems`.`name` = 'sitegroup_main_location' LIMIT 1 ;");
        } else {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = 'sitegroup_main_location' LIMIT 1 ;");
        }
      }

	if (isset($_POST['language_phrases_groups']) && $_POST['language_phrases_groups'] != $groups && isset($_POST['language_phrases_group']) && $_POST['language_phrases_group'] != $group && !empty($this->view->hasLanguageDirectoryPermissions)) {
						//Work for raplace facebook plugin.
				if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'facebooksefeed' ) && Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'facebookse' )) {
					$facebookseMixsettingstable = Engine_Api::_()->getDbtable('mixsettings', 'facebookse');

					$select = $facebookseMixsettingstable->select()->from($facebookseMixsettingstable->info('name'), array('streampublish_message', 'streampublish_caption', 'streampublish_action_link_text', 'activityfeedtype_text', 'resource_type', 'module'))
					->where('module LIKE ?', '%' . 'sitegroup' . '%'); 
					$results = $select->query()->fetchAll();
					$replaceWord = ucfirst($_POST['language_phrases_group']);
					$orignal_word = ucfirst($group);
					foreach($results as $result) {

						$streampublish_message = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_message"]); 

						$streampublish_caption = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_caption"]);

						$streampublish_action_link_text = str_replace(" $orignal_word", " $replaceWord", $result["streampublish_action_link_text"]);

						$activityfeedtype_text = str_replace(" $orignal_word", " $replaceWord", $result["activityfeedtype_text"]);

						$db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_message` =  \''.$streampublish_message.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

						$db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_caption` =  \''.$streampublish_caption.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

						$db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `streampublish_action_link_text` =  \''.$streampublish_action_link_text.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');

						$db->query('UPDATE  `engine4_facebookse_mixsettings` SET  `activityfeedtype_text` =  \''.$activityfeedtype_text.'\' WHERE  `engine4_facebookse_mixsettings`.`resource_type` =\''.$result["resource_type"].'\' and `engine4_facebookse_mixsettings`.`module` =\''.$result["module"].'\';');
					}
				}
				
				$db->query('UPDATE  `engine4_core_menuitems` SET  `label` =  \''.ucfirst($_POST['language_phrases_groups']).'\' WHERE  `engine4_core_menuitems`.`name` ="core_main_sitegroup";');
				
				$language_pharse = array('text_groups' => '$plural$' , 'text_group' => '$singular$'); 

				Engine_Api::_()->getApi('language', 'sitegroup')->setTranslateForListType($language_pharse, '', $group, $groups);

        $language_pharse = array('text_groups' => $_POST['language_phrases_groups'] , 'text_group' => $_POST['language_phrases_group']); 

        Engine_Api::_()->getApi('language', 'sitegroup')->setTranslateForListType($language_pharse, '', '$singular$', '$plural$');
      }
      
      $redirectionNew = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.redirection', 'home');
      if($redirectionPrevious != $redirectionNew) {
          $db = Zend_Db_Table_Abstract::getDefaultAdapter();
          $db->update('engine4_core_menuitems', array('params' => '{"route":"sitegroup_general","action":"'.$redirectionNew.'"}'), array('name = ?' => 'core_main_sitegroup'));
      }              
      
    }    

    if( !empty($addGroupPrivacy) ) {
      // Write Post Code Here
    }
    $newLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.city', "World");
    $this->setDefaultMapCenterPoint($oldLocation, $newLocation);
    $this->view->isModsSupport = Engine_Api::_()->getApi('suggestion', 'sitegroup')->isModulesSupport();
  }
  
  public function createEditAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_settings');

    //GET NAVIGATION
    $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main_settings', array(), 'sitegroup_admin_main_createedit');

    $this->view->form = $form = new Sitegroup_Form_Admin_CreateEdit();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            if ($coreSettings->hasSetting($key))
              $coreSettings->removeSetting($key);
            $coreSettings->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }
  }    
    

  public function widgetPlaced($group_id, $widgetname, $parent_content_id, $order, $params) {

  	$table = Engine_Api::_()->getDbtable('content', 'core');
	  $contentTableName = $table->info('name');
		$contentWidget = $table->createRow();
		$contentWidget->page_id = $group_id;
		$contentWidget->type = 'widget';
		$contentWidget->name = $widgetname;
		$contentWidget->parent_content_id = $parent_content_id;
		$contentWidget->order = $order;
		$contentWidget->params = "$params";
		$contentWidget->save();

  }
  
  //ACTION FOR FAQ
  public function faqAction() {

    //TABS CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_faq');

    $this->view->faq = 1;
    $this->view->faq_type = $this->_getParam('faq_type', 'general');
  }

  //ACTION FOR GETTING THE CATGEORIES AND SUBCATEGORIES
  public function sitegroupcategoriesAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_sitegroupcategories');

    //GET TASK
    if (isset($_POST['task'])) {
      $task = $_POST['task'];
    } elseif (isset($_GET['task'])) {
      $task = $_GET['task'];
    } else {
      $task = "main";
    }

    //GET STORAGE API
		$this->view->storage = Engine_Api::_()->storage();
    
    //GET CATEGORIES TABLE
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitegroup');

    //GET CATEGORIES TABLE NAME
    $tableCategoriesName = $tableCategories->info('name');

    //GET GROUP TABLE
    $tableGroup = Engine_Api::_()->getDbTable('groups', 'sitegroup');

    if ($task == "savecat") {
      //GET CATEGORY ID
      $cat_id = $_GET['cat_id'];

      $cat_title_withoutparse = $_GET['cat_title'];

      //GET CATEGORY TITLE
      $cat_title = Engine_Api::_()->sitegroup()->parseString($_GET['cat_title']);

      //GET CATEGORY DEPENDANCY
      $cat_dependency = $_GET['cat_dependency'];
      $subcat_dependency = $_GET['subcat_dependency'];
      if ($cat_title == "") {
        if ($cat_id != "new") {
          if ($cat_dependency == 0) {
            $row_ids = Engine_Api::_()->getDbtable('categories', 'sitegroup')->getSubCategories($cat_id);
            foreach ($row_ids as $values) {
              $tableCategories->delete(array('subcat_dependency = ?' => $values->category_id, 'cat_dependency = ?' => $values->category_id));
              $tableCategories->delete(array('category_id = ?' => $values->category_id));
            }

            $tableGroup->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
            $tableCategories->delete(array('category_id = ?' => $cat_id));

            //START SITEGROUPREVIEW CODE
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
              Engine_Api::_()->sitegroupreview()->deleteCategory($cat_id);
            }
            //END SITEGROUPREVIEW CODE
          } else {
            $tableCategories->update(array('category_name' => $cat_title), array('category_id = ?' => $cat_id, 'cat_dependency = ?' => $cat_dependency));
            $tableGroup->update(array('category_id' => 0, 'subcategory_id' => 0), array('category_id = ?' => $cat_id));
            $tableCategories->delete(array('cat_dependency = ?' => $cat_id, 'subcat_dependency = ?' => $cat_id));
            $tableCategories->delete(array('category_id = ?' => $cat_id));
          }
        }
        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.removecat('$cat_id');";
        echo "</script></head><body></body></html>";
        exit();
      } else {
        if ($cat_id == 'new') {
          $row_info = $tableCategories->fetchRow($tableCategories->select()->from($tableCategoriesName, 'max(cat_order) AS cat_order'));
          $cat_order = $row_info['cat_order'] + 1;
          $row = $tableCategories->createRow();
          $row->category_name = $cat_title_withoutparse;
          $row->cat_order = $cat_order;
          $row->cat_dependency = $cat_dependency;
          $row->subcat_dependency = $subcat_dependency;
          $newcat_id = $row->save();
        } else {
          $tableCategories->update(array('category_name' => $cat_title_withoutparse), array('category_id = ?' => $cat_id));
          $newcat_id = $cat_id;
        }

        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.savecat_result('$cat_id', '$newcat_id', '$cat_title', '$cat_dependency', '$subcat_dependency');";
        echo "</script></head><body></body></html>";
        exit();
      }
    } elseif ($task == "changeorder") {
      $divId = $_GET['divId'];
      $sitegroupOrder = explode(",", $_GET['sitegrouporder']);
      //RESORT CATEGORIES
      if ($divId == "categories") {
        for ($i = 0; $i < count($sitegroupOrder); $i++) {
          $cat_id = substr($sitegroupOrder[$i], 4);
          $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
        }
      } elseif (substr($divId, 0, 7) == "subcats") {
        for ($i = 0; $i < count($sitegroupOrder); $i++) {
          $cat_id = substr($sitegroupOrder[$i], 4);
          $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
        }
      } elseif (substr($divId, 0, 11) == "treesubcats") {
        for ($i = 0; $i < count($sitegroupOrder); $i++) {
          $cat_id = substr($sitegroupOrder[$i], 4);
          $tableCategories->update(array('cat_order' => $i + 1), array('category_id = ?' => $cat_id));
        }
      }
    }

    $categories = array();
    $category_info = Engine_Api::_()->getDbTable('categories', 'sitegroup')->getCategories(1);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $subcategories = Engine_Api::_()->getDbtable('categories', 'sitegroup')->getAllCategories($value->category_id, 'subcategory_id', 0, 'subcategory_id', null, 0, 0);
      foreach ($subcategories as $subresults) {
        $subsubcategories = Engine_Api::_()->getDbtable('categories', 'sitegroup')->getAllCategories($subresults->category_id, 'subsubcategory_id', 0, 'subsubcategory_id', null, 0, 0);
        $treesubarrays[$subresults->category_id] = array();
        foreach ($subsubcategories as $subsubcategoriesvalues) {
          $treesubarray = array('tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
              'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
              'order' => $subsubcategoriesvalues->cat_order,
              'count' => $subsubcategoriesvalues->count);
          
          if(isset($subsubcategoriesvalues->file_id)){
             $treesubarray = array_merge($treesubarray,array('file_id' => $subsubcategoriesvalues->file_id));
          }
          $treesubarrays[$subresults->category_id][] = $treesubarray;
        }

        $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'count' => $subresults->count,
            'order' => $subresults->cat_order);
        
          if(isset($subresults->file_id)){
             $tmp_array = array_merge($tmp_array,array('file_id' => $subresults->file_id));
          }
        $sub_cat_array[] = $tmp_array;
      }

      $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $value->count,
          'sub_categories' => $sub_cat_array);
      
          if(isset($value->file_id)){
             $category_array = array_merge($category_array,array('file_id' => $value->file_id));
          }
      $categories[] = $category_array;
    }

    include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
  }

  public function readmeAction() {

    $this->view->faq = 0;
    $this->view->faq_type = $this->_getParam('faq_type', 'general');
  }

  //ACTION FOR SHOWING THE GROUP STATISTICS
  public function statisticAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_statistic');

    //GET GROUP TABLE
    $tableGroup = Engine_Api::_()->getDbTable('groups', 'sitegroup');

    //GET TOTAL GROUPES    
    $this->view->totalSitegroup = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalgroup'))->totalgroup;

    //GET PUBLISH GROUPES
    $this->view->totalPublish = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalpublish')->where('draft =?', 1))->totalpublish;

    //GET DRAFTED GROUPES
    $this->view->totalDrafted = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totaldrafted')->where('draft =?', 0))->totaldrafted;

    //Get CLOSED GROUPES
    $this->view->totalClosed = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalclosed')->where('closed =?', 1))->totalclosed;

    //Get OPEN GROUPES
    $this->view->totalopen = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalopen')->where('closed =?', 0))->totalopen;

    //GET APPROVED GROUPES
    $this->view->totalapproved = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalapproved')->where('approved =?', 1))->totalapproved;

    //GET DISAPPROVED GROUPES
    $this->view->totaldisapproved = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totaldisapproved')->where('approved =?', 0))->totaldisapproved;

    //GET FEATURED GROUPES
    $this->view->totalfeatured = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalfeatured')->where('featured =?', 1))->totalfeatured;

    //GET SPONSORED GROUPES	
    $this->view->totalsponsored = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'count(*) AS totalsponsored')->where('sponsored =?', 1))->totalsponsored;

    //GET TOTAL COMMENTS IN GROUPES	
    $this->view->totalcommentpost = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'sum(comment_count) AS totalcomments'))->totalcomments;

    //GET TOTAL LIKES IN GROUPES	
    $this->view->totallikepost = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'sum(like_count) AS totallikes'))->totallikes;

    //GET TOTAL VIEWS IN GROUPES	
    $this->view->totalviewpost = $tableGroup->fetchRow($tableGroup->select()->from($tableGroup->info('name'), 'sum(view_count) AS totalviews'))->totalviews;

    //CHECK THAT SITEGROUP REVIEW IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
      //GET REVIEW TABLE TABLE
      $tableReview = Engine_Api::_()->getDbtable('reviews', 'sitegroupreview');

      //GET TOTAL REVIEWS IN GROUPES	
      $this->view->totalreview = $tableReview->fetchRow(Engine_Api::_()->getDbtable('reviews', 'sitegroupreview')->select()->from($tableReview->info('name'), 'count(*) AS totalreview'))->totalreview;
    }

    //CHECK THAT SITEGROUP DISCUSSION IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
      //GET DISCUSSION TABLE
      $tableDiscussion = Engine_Api::_()->getDbtable('topics', 'sitegroup');

      //GET TOTAL DISCUSSION IN GROUPES
      $this->view->totaldiscussion = $tableDiscussion->fetchRow($tableDiscussion->select()->from($tableDiscussion->info('name'), 'count(*) AS totaldiscussion'))->totaldiscussion;

      //GET DISCUSSION POST TABLE
      $tableDiscussionPost = Engine_Api::_()->getDbtable('posts', 'sitegroup');

      //GET TOTAL DISCUSSION POST (REPLY)IN GROUPES       
      $this->view->totaldiscussionpost = $tableDiscussionPost->fetchRow($tableDiscussionPost->select()->from($tableDiscussionPost->info('name'), 'count(*) AS totalpost'))->totalpost;
    }

    //GET PHOTO TABLE
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitegroup');

    //GET THE TOTAL PHOTO IN GROUPES
    $this->view->totalphotopost = $tablePhoto->fetchRow($tablePhoto->select()->from($tablePhoto->info('name'), 'count(*) AS totalphoto')->where('collection_id <>?', 0))->totalphoto;

    //CHECK THAT SITEGROUP ALBUM IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
      //GET ALBUM TABLE
      $tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitegroup');

      //GET THE TOTAL ALBUM IN GROUPES
      $this->view->totalalbumpost = $tableAlbum->fetchRow($tableAlbum->select()->from($tableAlbum->info('name'), 'count(*) AS totalalbum'))->totalalbum;
    }

    //CHECK THAT SITEGROUP NOTE IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
      //GET NOTE TABLE
      $tableNote = Engine_Api::_()->getDbtable('notes', 'sitegroupnote');

      //GET THE TOTAL NOTE IN GROUPES
      $this->view->totalnotepost = $tableNote->fetchRow($tableNote->select()->from($tableNote->info('name'), 'count(*) AS totalnotes'))->totalnotes;
    }

    //CHECK THAT SITEGROUP VIDEO IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
      //GET VIDEO TABLE
      $tableVideo = Engine_Api::_()->getDbtable('videos', 'sitegroupvideo');

      //GET THE TOTAL VIDEO IN GROUPES
      $this->view->totalvideopost = $tableVideo->fetchRow($tableVideo->select()->from($tableVideo->info('name'), 'count(*) AS totalvideos'))->totalvideos;
    }

    //CHECK THAT SITEGROUP DOCUMENT IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
      //GET DOCUMENT TABLE
      $tableDocument = Engine_Api::_()->getDbtable('documents', 'sitegroupdocument');

      //GET THE TOTAL DOCUMENT IN GROUPES
      $this->view->totaldocumentpost = $tableDocument->fetchRow($tableDocument->select()->from($tableDocument->info('name'), 'count(*) AS totaldocuments'))->totaldocuments;
    }

    //CHECK THAT SITEGROUP EVENT IS ENABLED OR NOT
    if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitegroup_group', 'item_module' => 'sitegroup')))) {
			if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
				//GET EVENT TABLE
				$tableEvent = Engine_Api::_()->getDbtable('events', 'sitegroupevent');

				//GET THE TOTAL EVENT IN PAGES
				$this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents'))->totalevents;
			} else {
				//GET EVENT TABLE
				$tableEvent = Engine_Api::_()->getDbtable('events', 'siteevent');

				//GET THE TOTAL EVENT IN PAGES
				$this->view->totaleventpost = $tableEvent->fetchRow($tableEvent->select()->from($tableEvent->info('name'), 'count(*) AS totalevents')->where('parent_type =?', 'sitegroup_group'))->totalevents;
      }
    }

    //CHECK THAT SITEGROUP VIDEO IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
      //GET PLAYLIST TABLE
      $tablePlaylist = Engine_Api::_()->getDbtable('playlists', 'sitegroupmusic');

      //GET THE TOTAL PLAYLIST IN GROUPES
      $this->view->totalplaylists = $tablePlaylist->fetchRow($tablePlaylist->select()->from($tablePlaylist->info('name'), 'count(*) AS totalplaylists'))->totalplaylists;

      //GET PLAYLIST TABLE
      $tableSongs = Engine_Api::_()->getDbtable('playlistSongs', 'sitegroupmusic');

      //GET THE TOTAL PLAYLIST IN GROUPES
      $this->view->totalsongs = $tableSongs->fetchRow($tableSongs->select()->from($tableSongs->info('name'), 'count(*) AS totalsongs'))->totalsongs;
    }

    //CHECK THAT SITEGROUP POLL IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
      //GET POLL TABLE
      $tablePoll = Engine_Api::_()->getDbtable('polls', 'sitegrouppoll');

      //GET THE TOTAL POLL IN GROUPES
      $this->view->totalpollpost = $tablePoll->fetchRow($tablePoll->select()->from($tablePoll->info('name'), 'count(*) AS totalpolls'))->totalpolls;
    }

    //CHECK THAT SITEGROUP OFFER IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
      //GET OFFER TABLE
      $tableOffer = Engine_Api::_()->getDbtable('offers', 'sitegroupoffer');

      //GET THE TOTAL OFFER IN GROUPES
      $this->view->totalofferpost = $tableOffer->fetchRow($tableOffer->select()->from($tableOffer->info('name'), 'count(*) AS totaloffers'))->totaloffers;
    }
  }

  public function graphAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_graph');
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_Graph();

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
    }
  }

  //ACTION FOR EMAIL THE DETAIL
  public function emailAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_email');
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_Email();

    //check if comments should be displayed or not
    $show_comments = Engine_Api::_()->sitegroup()->displayCommentInsights();

    //check if Sitemailtemplates Plugin is enabled
    $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
            ->from($rtasksName, array('processes', 'timeout'))
            ->where('title = ?', 'Sitegroup Insight Mail')
            ->where('plugin = ?', 'Sitegroup_Plugin_Task_InsightNotification')
            ->limit(1);
    $prefields = $taskstable->fetchRow($taskstable_result);

    //populate form
//     $form->populate(array(
//         'sitegroup_insightemail' => $prefields->processes,
//     ));

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup_insightemail', $values['sitegroup_insightemail']);

      //check if Sitemailtemplates Plugin is enabled
      $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');

      if(empty($sitemailtemplates)) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup_bg_color', $values['sitegroup_bg_color']);
      }
      include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
      if ($values['sitegroup_demo'] == 1 && $values['sitegroup_insightemail'] == 1) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

        //check if Sitemailtemplates Plugin is enabled
        $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');
        $site_title = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.site.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1));

        $insights_string = '';
				$template_header = "";
				$template_footer = "";
        if(!$sitemailtemplates) {
					$site_title_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.color', "#ffffff");
					$site_header_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.header.color', "#79b4d4");

					//GET SITE "Email Body Outer Background" COLOR
					$site_bg_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.bg.color', "#f7f7f7");
					$insights_string.= "<table cellpadding='2'><tr><td><table cellpadding='2'><tr><td><span style='font-size: 14px; font-weight: bold;'>" . $view->translate("Sample Group") . "</span></td></tr>";

					$template_header.= "<table width='98%' cellspacing='0' border='0'><tr><td width='100%' bgcolor='$site_bg_color' style='font-family:arial,tahoma,verdana,sans-serif;padding:40px;'><table width='620' cellspacing='0' cellpadding='0' border='0'>";
					$template_header.= "<tr><td style='background:" . $site_header_color . "; color:$site_title_color;font-weight:bold;font-family:arial,tahoma,verdana,sans-serif; padding: 4px 8px;vertical-align:middle;font-size:16px;text-align: left;' nowrap='nowrap'>" . $site_title . "</td></tr><tr><td valign='top' style='background-color:#fff; border-bottom: 1px solid #ccc; border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; font-family:arial,tahoma,verdana,sans-serif; padding: 15px;padding-top:0;' colspan='2'><table width='100%'><tr><td colspan='2'>";

          $template_footer.= "</td></tr></table></td></tr></td></table></td></tr></table>";
        }

        if ($values['sitegroup_insightmail_options'] == 1) {
          $vals['days_string'] = $view->translate('week');
        } elseif ($values['sitegroup_insightmail_options'] == 2) {
          $vals['days_string'] = $view->translate('month');
        }
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $view->baseUrl();
        $insight_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Visit your Insights Group') . "</a>";
        $update_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Send an update to people who like this') . "</a>";

        //check if Communityad Plugin is enabled
        $sitegroupcommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
        $adversion = null;
        if ($sitegroupcommunityadEnabled) {
          $communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
          $adversion = $communityadmodulemodule->version;
          if ($adversion >= '4.1.5') {
            $promote_Ad_link = "<a style='color: rgb(59, 89, 152); text-decoration: none;' href='" . $path . "'>" . $view->translate('Promote with %s Ads', $site_title) . "</a>";
          }
        }

        $insights_string.= "<table><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $vals['days_string'] . $view->translate(array('ly active user', 'ly active users', 2), 2) . "</span></td></tr><tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" .  $view->translate(array('person likes this', 'people like this', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
        if (!empty($show_comments)) {
          $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);'>" . $view->translate(array('comment', 'comments', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '2' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr>";
        }
        $insights_string.= "<tr><td><span style='font-size: 24px; font-family: arial;'>" . '10' . "</span>\t <span style='color: rgb(85, 85, 85);'>" . $view->translate(array('visit', 'visits', 2), 2) . "</span>&nbsp;<span style='font-size: 18px; font-family: arial;' >" . '5' . "</span>\t<span style='color: rgb(85, 85, 85);' >" . $view->translate('since last') . "\t" . $vals['days_string'] . "</span></td></tr></table><table><tr><td>" . "<ul style=' padding-left: 5px;'><li>" . $insight_link . "</li><li>" . $update_link;

        //check if Communityad Plugin is enabled
        if ($sitegroupcommunityadEnabled && $adversion >= '4.1.5') {
          $insights_string.= "</li><li>" . $promote_Ad_link;
        }
        $insights_string.= "</li></ul></td></tr></table>";
        $days_string = ucfirst($vals['days_string']);
        $owner_name = Engine_Api::_()->user()->getViewer()->getTitle();
        $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($values['sitegroup_admin'], 'SITEGROUP_INSIGHTS_EMAIL_NOTIFICATION', array(
            'recipient_title' => $owner_name,
            'template_header' => $template_header,
            'message' => $insights_string,
            'template_footer' => $template_footer,
            'site_title' => $site_title,
            'days' => $days_string,
            'email' => $email,
            'queue' => true));
      }
    }
  }

  //ACTION FOR AD SHOULD BE DISPLAY OR NOT ON GROUPES
  public function adsettingsAction() {

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_adsettings');

    //FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Adsettings();

    //CHECK THAT COMMUNITY AD PLUGIN IS ENABLED OR NOT
    $communityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if ($communityadEnabled) {
      $this->view->ismoduleenabled = $ismoduleenabled = 1;
    } else {
      $this->view->ismoduleenabled = $ismoduleenabled = 0;
    }

    //CHECK THAT SITEGROUP DOCUMENT PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument') && $ismoduleenabled) {
      $this->view->isdocumentenabled = 1;
    } else {
      $this->view->isdocumentenabled = 0;
    }

    //CHECK THAT SITEGROUP NOTE PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote') && $ismoduleenabled) {
      $this->view->isnoteenabled = 1;
    } else {
      $this->view->isnoteenabled = 0;
    }

    //CHECK THAT SITEGROUP ALBUM PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum') && $ismoduleenabled) {
      $this->view->isalbumenabled = 1;
    } else {
      $this->view->isalbumenabled = 0;
    }

    //CHECK THAT SITEGROUP VIDEO PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo') && $ismoduleenabled) {
      $this->view->isvideoenabled = 1;
    } else {
      $this->view->isvideoenabled = 0;
    }

    //CHECK THAT SITEGROUP EVENT PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent') && $ismoduleenabled) {
      $this->view->iseventenabled = 1;
    } else {
      $this->view->iseventenabled = 0;
    }

    //CHECK THAT SITEGROUP DISCUSSION PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion') && $ismoduleenabled) {
      $this->view->isdiscussionenabled = 1;
    } else {
      $this->view->isdiscussionenabled = 0;
    }

    //CHECK THAT SITEGROUP POLL PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll') && $ismoduleenabled) {
      $this->view->ispollenabled = 1;
    } else {
      $this->view->ispollenabled = 0;
    }

    //CHECK THAT SITEGROUP REVIEW PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && $ismoduleenabled) {
      $this->view->isreviewenabled = 1;
    } else {
      $this->view->isreviewenabled = 0;
    }

    //CHECK THAT SITEGROUP OFFER PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer') && $ismoduleenabled) {
      $this->view->isofferenabled = 1;
    } else {
      $this->view->isofferenabled = 0;
    }

    //CHECK THAT SITEGROUP FORM PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupform') && $ismoduleenabled) {
      $this->view->isformenabled = 1;
    } else {
      $this->view->isformenabled = 0;
    }

    //CHECK THAT SITEGROUP INVITE PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupinvite') && $ismoduleenabled) {
      $this->view->isinviteenabled = 1;
    } else {
      $this->view->isinviteenabled = 0;
    }

    //CHECK THAT SITEGROUP BADGE PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge') && $ismoduleenabled) {
      $this->view->isbadgeenabled = 1;
    } else {
      $this->view->isbadgeenabled = 0;
    }

    //CHECK THAT SITEGROUP NOTE PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic') && $ismoduleenabled) {
      $this->view->ismusicenabled = 1;
    } else {
      $this->view->ismusicenabled = 0;
    }

    //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration') &&
            $ismoduleenabled) {
      $this->view->mixSettingsResults = Engine_Api::_()->getDbtable('mixsettings', 'sitegroupintegration')->getIntegrationItems();
      $this->view->issitegroupintegrationenabled = 1;
    } else {
      $this->view->issitegroupintegrationenabled = 0;
    }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

    //CHECK THAT SITEGROUP TWITTER PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter') && $ismoduleenabled) {
      $this->view->istwitterenabled = 1;
    } else {
      $this->view->istwitterenabled = 0;
    }

    //CHECK THAT SITEGROUP TWITTER PLUGIN IS ENABLED OR NOT
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember') && $ismoduleenabled) {
      $this->view->ismemberenabled = 1;
    } else {
      $this->view->ismemberenabled = 0;
    }

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET FORM VALUES
      $values = $form->getValues();
      include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
    }
  }

	//ACTION FOR SET THE DEFAULT MAP CENTER POINT
  public function setDefaultMapCenterPoint($oldLocation, $newLocation) {

    if ($oldLocation !== $newLocation && $newLocation !== "World" && $newLocation !== "world") {
        $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $newLocation, 'module' => 'Groups / Communities'));
        if(!empty($locationResults['latitude']) && !empty($locationResults['longitude'])) {
            $latitude = $locationResults['latitude'];
            $longitude = $locationResults['longitude'];
        }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.map.latitude', $latitude);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitegroup.map.longitude', $longitude);
    }
  }

  //ACTINO FOR SEARCH
  public function formSearchAction() {

    // GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_form_search');

    $table = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      $row = $table->getFieldsOptions('sitegroup', 'profile_type');
      $defaultAddition = 0;
      $rowCategory = $table->getFieldsOptions('sitegroup', 'category_id');
      $defaultCategory = 0;
      try {
        foreach ($values['order'] as $key => $value) {
          $table->update(array('order' => $defaultAddition + $defaultCategory + $key + 1), array('module = ?' => 'sitegroup', 'searchformsetting_id =?' => (int) $value));
          if (!empty($row) && $value == $row->searchformsetting_id)
            $defaultAddition = 40;

          if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id)
            $defaultCategory = 1;
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
    $this->view->enableBadgePlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupbadge');
    $this->view->enableReviewPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview');
    $this->view->enableGeoLocationPlugin = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupgeolocation');
    $this->view->searchForm = $table->fetchAll($table->select()->where('module = ?', 'sitegroup')->order('order'));
  }

  //ACTINO FOR ACTIVITY FEED
  public function activityFeedAction() {
    // GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_activity_feed');
 $aafmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
 if(!empty ($aafmodule))
   $this->view->isAAFModule=true;
    //FILTER FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_ActivityFeed();
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      $api = Engine_Api::_()->getApi("settings", "core");
      foreach ($values as $key => $value) {
        $api->setSetting($key, $value);
      }
      $enable = $form->sitegroup_feed_type->getValue();
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $activityfeed_array = array("sitegroupalbum_admin_photo_new", "sitegroupdocument_admin_new", "sitegroupevent_admin_new", "sitegroupmusic_admin_new", "sitegroupnote_admin_new", "sitegroupoffer_admin_new", "sitegrouppoll_admin_new", "sitegroupvideo_admin_new", "sitegroup_admin_topic_create", "sitegroup_admin_topic_reply");
      foreach ($activityfeed_array as $value) {
        $activit_type_sql = "UPDATE `engine4_activity_actiontypes` SET `enabled` = $enable WHERE `engine4_activity_actiontypes`.`type` = '$value' LIMIT 1";
        $db->query($activit_type_sql);
      }
    }
  }

  public function overwriteAction() {
    $type = $this->_getParam('type');
    $this->view->error = null;
    $this->view->status = false;
    if (empty($type))
      return;

    $moduleActivity = Engine_Api::_()->getDbtable('modules', 'core')->getModule('activity');
    $activityVersion = $moduleActivity->version;
    $dirName = 'activity-' . $activityVersion;
    $sourcePath = $dirName;
    $destinationPath = null;
    $api = Engine_Api::_()->getApi("settings", "core");

    $api->setSetting('sitegroup_feed_type', 1);

    $destinationPath = APPLICATION_PATH
            . '/application/modules/Activity/views/scripts/_activityText.tpl';
    $sourcePath .='/views/scripts/_activityText.tpl';

    $api->setSetting('sitegroupfeed_likegroup_dummy', 'b');


    if (is_file($destinationPath)) {
      @chmod($destinationPath, 0777);
    } else {
      $this->view->error = 'Target File does not exist.';
    }

    if (!is_writeable($destinationPath)) {
      $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
    }

    $serverPath = 'http://www.socialengineaddons.com/SocialEngine/SocialengineModules/index.php?path=';
    $sourcePath = $serverPath . @urlencode($sourcePath);
    $ch = curl_init();
    $timeout = 0;
    @curl_setopt($ch, CURLOPT_URL, $sourcePath);
    @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    @ob_start();
    @curl_exec($ch);
    @curl_close($ch);
    if (empty($exe_status)) {
      $get_value = @ob_get_contents();
    }
    if (empty($get_value)) {
      $get_value = @file_get_contents($oposit_url);
    }
    @ob_end_clean();

    if (!empty($get_value)) {
      if (!@file_put_contents($destinationPath, $get_value)) {
        $this->view->status = false;
        $this->view->error = 'Target file could not be overwritten. You do not have write permission chmod -R 777 recursively to the directory "/application/modules/Activity/". Please give the recursively write permission to this directory and try again.';
        return;
      }
    } else {
      $this->view->error = 'It seems that you do not have any internet connection that\'s why you are not able to overwrite this file.';
    }

    @chmod($activityTextPath_Original, 0755);
    if (empty($this->view->error))
      $this->view->status = true;
  }

	//ACTION FOR MAPPING OF LISTINGS
	Public function mappingCategoryAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->catid = $catid = $this->_getParam('catid');

		//GET CATEGORY TITLE
		$this->view->oldcat_title = $oldcat_title = $this->_getParam('oldcat_title');

		//GET CATEGORY DEPENDANCY
		$this->view->subcat_dependency = $subcat_dependency = $this->_getParam('subcat_dependency');

    //CREATE FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_Mapping();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		if( $this->getRequest()->isPost()){ 

			//GET FORM VALUES
			$values = $form->getValues();

			//GET GROUPES TABLE
			$tableSitegroup = Engine_Api::_()->getDbTable('groups', 'sitegroup');

			//GET CATEGORY TABLE
			$tableCategory = Engine_Api::_()->getDbtable('categories', 'sitegroup');

			//ON CATEGORY DELETE
			$rows = $tableCategory->getSubCategories($catid);
			foreach ($rows as $row) {
				$tableCategory->delete(array('subcat_dependency = ?' => $row->category_id, 'cat_dependency = ?' => $row->category_id));
				$tableCategory->delete(array('category_id = ?' => $row->category_id));
			}

			$previous_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitegroup')->getProfileType($catid);
			$new_cat_profile_type = Engine_Api::_()->getDbTable('profilemaps', 'sitegroup')->getProfileType($values['new_category_id']);

			//SELECT GROUPES WHICH HAVE THIS CATEGORY
			if($previous_cat_profile_type != $new_cat_profile_type) {
				$rows = $tableSitegroup->getCategorySitegroup($catid);
				if (!empty($rows)) {
					foreach ($rows as $key => $group_ids) {
						$group_id = $group_ids['group_id'];

						//DELETE ALL MAPPING VALUES FROM FIELD TABLES
						Engine_Api::_()->fields()->getTable('sitegroup_group', 'values')->delete(array('item_id = ?' => $group_id));
						Engine_Api::_()->fields()->getTable('sitegroup_group', 'search')->delete(array('item_id = ?' => $group_id));

						//UPDATE THE PROFILE TYPE OF ALREADY CREATED GROUPES
						$tableSitegroup->update(array('profile_type' => $new_cat_profile_type), array('group_id = ?' => $group_id));
					}
				}
			}

			//GROUP TABLE CATEGORY DELETE WORK
			if(isset($values['new_category_id']) && !empty($values['new_category_id']) ) {
				$tableSitegroup->update(array('category_id' => $values['new_category_id']), array('category_id = ?' => $catid));
			}
			else {
				$tableSitegroup->update(array('category_id' => 0), array('category_id = ?' => $catid));
			}

			$tableCategory->delete(array('category_id = ?' => $catid));
   	}

		$this->view->close_smoothbox = 1;
	}
	
	//ACTION FOR THE LANGUAGE FILE CHANGE DURING THE UPGRADE.
	public function languageAction() {
	
		//START LANGUAGE WORK
		Engine_Api::_()->getApi('language', 'sitegroup')->languageChanges();
		//END LANGUAGE WORK
		$redirect = $this->_getParam('redirect', false);
		if($redirect == 'install') {
			$this->_redirect('install/manage');
		} elseif($redirect == 'query') {
			$this->_redirect('install/manage/complete');
		}
	}
  
 //ACTION FOR ADD THE CATEGORY ICON
	Public function addIconAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');
		$category = Engine_Api::_()->getItem('sitegroup_category', $category_id);

    //CREATE FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_Addicon();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		//UPLOAD PHOTO
		if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
		{
			//UPLOAD PHOTO
			$photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
			if(!empty($photoFile->file_id)) {
				$category->file_id = $photoFile->file_id;
				$category->save();
			}
		}

		$this->view->close_smoothbox = 1;
	}

	//ACTION FOR EDIT THE CATEGORY ICON
	Public function editIconAction()
	{
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
		$category = Engine_Api::_()->getItem('sitegroup_category', $category_id);

    //CREATE FORM
    $this->view->form = $form = new Sitegroup_Form_Admin_Settings_Editicon();

		$this->view->close_smoothbox = 0;

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

		//UPLOAD PHOTO
		if( isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) )
		{
			//UPLOAD PHOTO
			$photoFile = $category->setPhoto($_FILES['photo']);

			//UPDATE FILE ID IN CATEGORY TABLE
			if(!empty($photoFile->file_id)) {
				$previous_file_id = $category->file_id;
				$category->file_id = $photoFile->file_id;
				$category->save();
			
				//DELETE PREVIOUS CATEGORY ICON
				$file = Engine_Api::_()->getItem('storage_file', $previous_file_id);
				$file->delete();
			}
		}

		$this->view->close_smoothbox = 1;
	}

  //ACTION FOR DELETE THE CATEGORY ICON
  public function deleteIconAction()
  {
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET CATEGORY ID
		$this->view->category_id = $category_id = $this->_getParam('category_id');

		//GET CATEGORY ITEM
		$category = Engine_Api::_()->getItem('sitegroup_category', $category_id);

		$this->view->close_smoothbox = 0;

		if( $this->getRequest()->isPost() && !empty($category->file_id)){

			//DELETE CATEGORY ICON
			$file = Engine_Api::_()->getItem('storage_file', $category->file_id);
			$file->delete();

			//UPDATE FILE ID IN CATEGORY TABLE
			$category->file_id = 0;
			$category->save();

			$this->view->close_smoothbox = 1;
   	}
		$this->renderScript('admin-settings/delete-icon.tpl');
	}
}
?>
