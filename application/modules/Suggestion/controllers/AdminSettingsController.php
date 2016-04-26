<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_AdminSettingsController extends Core_Controller_Action_Admin {
 
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );
  public function indexAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_admin_settings');
  }

  public function manageModuleAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_modInfo');

    $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $page = $this->_getParam('page', 1);
    $pagesettingsTable = Engine_Api::_()->getItemTable('suggestion_modinfo');
    $pagesettingsTableName = $pagesettingsTable->info('name');
    $pagesettingsSelect = $pagesettingsTable->select();
    $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator->setCurrentPageNumber($page);
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $obj = Engine_Api::_()->getItem('suggestion_modinfo', $value);
          if (empty($obj->is_delete)) {
            $obj->delete();
          }
        }
      }
    }
  }

  public function moduleCreateAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_modInfo');
    $mod_name = $this->_getParam('module_name', null);
    $module_table = Engine_Api::_()->getDbTable('modinfos', 'suggestion');
    $module_name = $module_table->info('name');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->form = $form = new Suggestion_Form_Admin_Module(array('edit' => 0, 'defaultMod' => 0));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      if (strstr($values['module'], 'sitereview')) {
        $explodeModules = @explode("_", $values['module']);
        $tempListingModule = 'sitereview';
        $tempListingId = $explodeModules[1];
      }
      $resource_type = $values['item_type'];
      $title = $values['title_items'];
      $module = !empty($tempListingModule) ? $tempListingModule : $values['module'];
      $values['enabled'] = empty($values['enabled']) ? FALSE : TRUE;
      $values['settings'] = 'a:1:{s:7:"default";i:1;}';

      switch ($module) {
        case 'sitepage':
          $values['notification_type'] = 'page_suggestion';
          break;
        case 'sitebusiness':
          $values['notification_type'] = 'business_suggestion';
          break;
        case 'sitegroup':
          $values['notification_type'] = 'group_suggestion';
          break;
        case 'sitereview':
          $values['notification_type'] = 'sitereview_' . $tempListingId . '_suggestion';
          $values['settings'] = @serialize(array("default" => 1, "listing_id" => $tempListingId));
          $values['module'] = 'sitereview';
          $values['default'] = 1;
          $values['owner_field'] = 'owner_id';
          break;
        default:
          $values['notification_type'] = $module . '_suggestion';
          break;
      }

      if (strstr($module, 'sitereview')) {
        $notificationType = 'sitereview_' . $tempListingId . '_suggestion';
      } else {
        $notificationType = $module . '_suggestion';
      }
      $notificationBody = '{item:$subject} has suggested to you a {item:$object:' . strtolower($values['item_title']) . '}.';

      // Check Owner field value is exist or not in table.
      if (!empty($values['owner_field'])) {
        $table_name = Engine_Api::_()->getItemTable($values['item_type'])->info('name');
        $is_owner = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['owner_field'] . "'")->fetch();
        if (empty($is_owner)) {
          $error_owner = $this->view->translate('Please check the Content Owner Field. A field matching the one specified by you could not be found in the database table.');

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error_owner);
          return;
        }
      }



      $customCheck = $module_table->fetchRow(array('item_type = ?' => $resource_type));
      if (!empty($customCheck) && !strstr($module, 'sitereview')) {
        $itemError = Zend_Registry::get('Zend_Translate')->_("This ‘Content Module’ already exist.");
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($itemError);
        return;
      }

      $resourceTypeTable = Engine_Api::_()->getItemTable($resource_type);
      $primaryId = current($resourceTypeTable->info("primary"));
      if (!empty($primaryId))
        $values['field_name'] = $primaryId;



      //BEGIN TRANSACTION
      $queryObj = Zend_Db_Table_Abstract::getDefaultAdapter();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $table = Engine_Api::_()->getDbtable('modinfos', 'suggestion');
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();


        if (!empty($values['enabled'])) {
          // Insert in notification_type Table.
          $queryObj->query("INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type` , `module` , `body` , `is_request` ,`handler`) VALUES ('$notificationType', 'suggestion', '$notificationBody', 1, 'suggestion.widget.get-notify')");
        }

        // Insert in language_template Table.
        $emailtemType = 'notify_' . $notificationType;
        $queryObj->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('$emailtemType', 'suggestion', '[suggestion_sender], [suggestion_entity], [email], [link]'
);");

        if (!empty($values['item_title'])) {
          $itemTitle = $values['item_title'];
          $itemTitleLower = strtolower($itemTitle);
          // Insert in Language Files.
          $language1 = array('You have a ' . $itemTitle . ' suggestion' => 'You have a ' . $itemTitle . ' suggestion');
          $language2 = array('View all ' . $itemTitle . ' suggestions' => 'View all ' . $itemTitle . ' suggestions');
          $language3 = array('This ' . $itemTitleLower . ' was suggested by' => 'This ' . $itemTitleLower . ' was suggested by');

          $this->addPhraseAction($language1);
          $this->addPhraseAction($language2);
          $this->addPhraseAction($language3);
        }

        // Insert in Language Files.
        // EMAIL-TEMPLATE
        $languageModTitle = strtoupper($module);
        $makeEmailArray = array(
            "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_TITLE" => $values['item_title'] . " Suggestion",
            "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_DESCRIPTION" => "This email is sent to the member when someone suggest a " . $values['item_title'] . '.',
            "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_SUBJECT" => $values['item_title'] . " Suggestion",
            "_EMAIL_NOTIFY_" . $languageModTitle . "_SUGGESTION_BODY" => "[header]

[sender_title] has suggested to you a " . $values['item_title'] . ". To view this suggestion please click on: <a href='http://[host][object_link]'>http://[host][object_link]</a>.

[footer]"
        );
        $userSettingsNotfication = array("ACTIVITY_TYPE_" . $languageModTitle . "_SUGGESTION" => "When I receive a " . strtolower($values['item_title']) . " suggestion.");
        $userNotification = array($notificationLanguage => $notificationLanguage);

        $this->addPhraseAction($makeEmailArray);
        $this->addPhraseAction($userSettingsNotfication);
        $this->addPhraseAction($userNotification);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage-module'));
    }
  }

  public function moduleEditAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_modInfo');
    $manageModules = Engine_Api::_()->getItem('suggestion_modinfo', $this->_getParam('modinfo_id'));
    if (empty($manageModules))
      return;

    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

    $this->view->form = $form = new Suggestion_Form_Admin_Module(array('edit' => 1, 'defaultMod' => $manageModules->default));

    $getEditElement = $this->getEditElement($manageModules, $form);
    $formExtraValue = @unserialize($manageModules->settings);

    if (strstr($manageModules->module, 'magentoint')) {
      $form->removeElement('owner_field');
    }

    $modTitle = Engine_Api::_()->getDbTable('modules', 'core')->getModule($manageModules->module)->title;
    $form->module->setmultiOptions(array($manageModules->module => $modTitle));
    $form->item_type->setmultiOptions(array($manageModules->item_type => $manageModules->item_type));

    //SHOW PRE-FIELD FORM
    $form->populate($manageModules->toArray());

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($manageModules->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID THAN RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    unset($values['module']);
    unset($values['item_type']);

    if (strstr($manageModules->module, "sitereview")) {
      $removeField = array("sitereview_video_title", "sitereview_album_title", "sitereview_discussion_title");
      foreach ($removeField as $field) {
        unset($values[$field]);
      }
    }

    // Check Owner field value is exist or not in table.
    if (!empty($manageModules) && !empty($values['owner_field'])) {
      $table_name = Engine_Api::_()->getItemTable($manageModules->item_type)->info('name');
      $is_owner = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $values['owner_field'] . "'")->fetch();
      if (empty($is_owner)) {
        $error_owner = $this->view->translate('Please check the Content Owner Field. A field matching the one specified by you could not be found in the database table.');

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error_owner);
        return;
      }
    }

    $getFieldArray = array('item_title', 'button_title', 'quality', 'link', 'popup', 'enabled', 'owner_field');
    foreach ($values as $key => $value) {
      if (in_array($key, $getFieldArray)) {
        $formValue[$key] = $value;
      } else {
        $formExtraValue[$key] = $value;
      }
    }

    $formValue['settings'] = serialize($formExtraValue);
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $modInfo['enabled'] = !empty($modInfo['enabled']) ? $modInfo['enabled'] : 0;
      $mod_enabled = !empty($formValue['enabled']) ? 0 : 1;
      Engine_Api::_()->getDbtable('modinfos', 'suggestion')->setNotificationType(array('module' => $manageModules->module, 'item_title' => $formValue['item_title'], 'enabled' => $mod_enabled));
      $manageModules->setFromArray($formValue);
      $manageModules->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('controller' => 'settings', 'action' => 'manage-module'));
  }

  public function enabledContentTabAction() {
    $value = $this->_getParam('modinfo_id');
    $type = $this->_getParam('type');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    $content = Engine_Api::_()->getItemTable('suggestion_modinfo')->fetchRow(array('modinfo_id = ?' => $value));

    if (strstr($type, 'enabled')) {
      // Update the notification type table, when enabled or disabled the plugin.
      Engine_Api::_()->getDbtable('modinfos', 'suggestion')->setNotificationType(array('module' => $content->module, 'item_title' => $content->item_title, 'enabled' => $content->enabled));
    }

    try {
      $content->$type = !$content->$type;
      $content->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-module'));
  }

  public function deleteModuleAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->resource_type = $resource_type = $this->_getParam('item_type');

    $mixsettingstable = Engine_Api::_()->getDbtable('modinfos', 'suggestion');
    $sub_status_select = $mixsettingstable->fetchRow(array('item_type = ?' => $resource_type));
    $this->view->module = $sub_status_select->module;

    if ($this->getRequest()->isPost()) {
      $custom = Engine_Api::_()->getItemTable('suggestion_modinfo')->fetchRow(array('item_type = ?' => $resource_type));

      if (!empty($settingTable))
        $settingTable->delete();
      $custom->delete();

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }
  }

  public function getEditElement($modInfo, $form) {
    $getElement = array();
    switch ($modInfo->module) {
      case 'list':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Listings ?");

        $form->popup->setLabel("Suggestions popup after Creating a Listing");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Listing ? [This popup enables the user to suggest the newly created poll to his/her friends, so that they may vote on it.]");
        break;

      case 'recipe':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Recipes ?");

        $form->popup->setLabel("Suggestions popup after Creating a Recipe");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Recipe ? [This popup enables the user to suggest the newly created poll to his/her friends, so that they may vote on it.]");
        break;

      case 'album':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Albums ?");

        $form->popup->setLabel("Suggestions popup after Creating an Album");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating an Album ? [This popup enables the user to suggest the newly created album to his/her friends, so that they may view it, tag on it or comment on it.]");
        break;

      case 'poll':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Polls ?");

        $form->popup->setLabel("Suggestions popup after Creating a Poll");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Poll ? [This popup enables the user to suggest the newly created poll to his/her friends, so that they may vote on it.]");
        break;

      case 'classified':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Classifieds ?");

        $form->popup->setLabel("Suggestions popup after Creating a Classified");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Classified ? [This popup enables the user to suggest the newly created classified to his/her friends, so that they may view it.]");
        break;

      case 'blog':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Blogs ?");

        $form->popup->setLabel("Suggestions popup after Creating a Blog");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Blog ? [This popup enables the user to suggest the newly created blog to his/her friends, so that they may read it or comment on it.]");
        break;

      case 'music':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Musics ?");

        $form->popup->setLabel("Suggestions popup after Uploading Music");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Uploading Music ? [This popup enables the user to suggest the newly uploaded music to his/her friends, so that they may listen to it or comment on it.]");
        break;

      case 'video':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Videos ?");

        $form->popup->setLabel("Suggestions popup after Creating a Video");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Video ? [This popup enables the user to suggest the newly created video to his/her friends, so that they may view it.]");
        break;

      case 'user':

        $form->removeElement('quality');
        $form->removeElement('popup');

        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on their friends' profiles ?");

        $form->addElement('Select', 'friend_sugg_level', array(
            'label' => 'Max Connection Level for Friend Suggestions',
            'description' => 'Select the maximum connection level till which friend suggestions should be shown to users. (Note: Increasing the connection level also increases resource utilization for computing friend suggestions.)',
            'multiOptions' => array(
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('user', 'friend_sugg_level')
        ));

        $form->addElement('Radio', 'quality', array(
            'label' => 'Quality of Suggestions',
            'description' => 'Select the quality of suggestions of this suggestion type that should be shown to users. (Note: A higher quality of suggestion uses a better algorithm for computing suggestions and therefore accordingly, also uses more computation resources.)',
            'multiOptions' => array(2 => 'High', 0 => 'Good'),
            'value' => 0
        ));
        
        $form->addElement('Radio', 'show_default_image_member', array(
            'label' => 'Show members without profile pictures',
            'description' => 'Do you want to show those members in this widget who have not uploaded their profile pictures?',
            'multiOptions' => array(
                1 => 'Yes, show members without profile pictures',
                0 => 'No, do not show membres without profile pictures'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('user', 'show_default_image_member')
        ));

        $form->addElement('Radio', 'popup', array(
            'label' => 'Suggestions popup after Add as Friend',
            'description' => 'Do you want the suggestions popup to be shown to a user after Adding of a Friend ? [This popup enables the user to suggest the newly added friend to his/her other friends, so that they may also add him/her.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('user', 'popup')
        ));

        $form->addElement('Radio', 'accept_friend_popup', array(
            'label' => 'Suggestions popup after Accepting a Friend Request',
            'description' => 'Do you want the suggestions popup to be shown to a user after Accepting a Friend Request ? [This popup suggests to a user other friends of the added friend, and allows the user to add them as a friend.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('user', 'accept_friend_popup')
        ));
        break;

      case 'forum':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Forum Topics ?");

        $form->popup->setLabel("Suggestions popup after Creating a Forum Topic");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Forum Topic ? [This popup enables the user to suggest the newly created forum topic to his/her friends, so that they may view it / comment on it.]");

        $form->addElement('Radio', 'after_forum_join', array(
            'label' => 'Suggestions popup after Replying to a Forum Topic',
            'description' => 'Do you want the suggestions popup to be shown to a user after Replying to a Forum Topic ? [This popup enables the user to suggest the forum topic to his/her friends, so that they may view it / take part in the discussion too.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('forum', 'after_forum_join')
        ));
        break;

      case 'group':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Groups ?");

        $form->popup->setLabel("Suggestions popup after Creating a Group");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Group ? [This popup enables the user to suggest the newly created group to his/her friends, so that they may join it.]");

        $form->addElement('Radio', 'after_group_join', array(
            'label' => 'Suggestions popup after Joining a Group',
            'description' => 'Do you want the suggestions popup to be shown to a user after Joining a Group ? [This popup enables the user to suggest the just joined group to his/her friends, so that they may join it too.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('group', 'after_group_join')
        ));
        break;

      case 'event':
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Events ?");

        $form->popup->setLabel("Suggestions popup after Creating an Event");
        $form->popup->setDescription("Do you want the suggestions popup to be shown to a user after Creating a Event ? [This popup enables the user to suggest the newly created event to his/her friends, so that they may attend / join it.]");

        $form->addElement('Radio', 'after_event_join', array(
            'label' => 'Suggestions popup after Joining an Event',
            'description' => 'Do you want the suggestions popup to be shown to a user after Joining an Event ? [This popup enables the user to suggest the just joined event to his/her friends, so that they may attend / join it too.]',
            'multiOptions' => array(
                1 => 'Yes, show this suggestions popup.',
                0 => 'No, do not show this suggestions popup.'
            ),
            'value' => Engine_Api::_()->suggestion()->getModSettings('event', 'after_event_join')
        ));
        break;

      case 'sitepage':
        $form->removeElement('popup');
        $form->link->setLabel("Suggest to Friends link for Page");
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Pages ?");

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument')) {
          $form->addElement('Radio', 'document_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Document',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Documents ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'document_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll')) {
          $form->addElement('Radio', 'poll_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Poll',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Polls ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'poll_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo')) {
          $form->addElement('Radio', 'video_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Video',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Video ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'video_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent')) {
          $form->addElement('Radio', 'event_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Event',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Event ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'event_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
          $form->addElement('Radio', 'review_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Review',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Reviews ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'review_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereport')) {
          $form->addElement('Radio', 'report_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Report',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Reports ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'report_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
          $form->addElement('Radio', 'album_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Album',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Albums ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'album_sugg_link')
          ));
        }



        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) {
          $form->addElement('Radio', 'note_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Note',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Notes ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'note_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic')) {
          $form->addElement('Radio', 'music_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Music',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Music ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'music_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer')) {
          $form->addElement('Radio', 'offer_sugg_link', array(
              'label' => 'Suggest to Friends link for Page Offer',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Page Offer ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitepage', 'offer_sugg_link')
          ));
        }
        break;

      case 'sitebusiness':

        $form->removeElement('popup');
        $form->link->setLabel("Suggest to Friends link for Business");
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Businesses ?");

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessdocument')) {
          $form->addElement('Radio', 'document_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Document',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Documents ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'document_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinesspoll')) {
          $form->addElement('Radio', 'poll_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Poll',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Polls ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'poll_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessvideo')) {
          $form->addElement('Radio', 'video_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Video',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Video ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'video_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessevent')) {
          $form->addElement('Radio', 'event_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Event',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Event ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'event_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessreview')) {
          $form->addElement('Radio', 'review_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Review',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Reviews ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'review_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessalbum')) {
          $form->addElement('Radio', 'album_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Album',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Albums ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'album_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessnote')) {
          $form->addElement('Radio', 'note_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Note',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Notes ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'note_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessmusic')) {
          $form->addElement('Radio', 'music_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Music',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Music ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'music_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessoffer')) {
          $form->addElement('Radio', 'offer_sugg_link', array(
              'label' => 'Suggest to Friends link for Business Offer',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Business Offer ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitebusiness', 'offer_sugg_link')
          ));
        }
        break;
        
      case 'sitegroup':

        $form->removeElement('popup');
        $form->link->setLabel("Suggest to Friends link for Group");
        $form->link->setDescription("Do you want to show the 'Suggest to Friends' link to users on the main pages of Groups ?");

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdocument')) {
          $form->addElement('Radio', 'document_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Document',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Documents ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'document_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouppoll')) {
          $form->addElement('Radio', 'poll_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Poll',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Polls ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'poll_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupvideo')) {
          $form->addElement('Radio', 'video_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Video',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Video ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'video_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupevent')) {
          $form->addElement('Radio', 'event_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Event',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Event ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'event_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
          $form->addElement('Radio', 'review_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Review',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Reviews ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'review_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum')) {
          $form->addElement('Radio', 'album_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Album',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Albums ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'album_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupnote')) {
          $form->addElement('Radio', 'note_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Note',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Notes ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'note_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmusic')) {
          $form->addElement('Radio', 'music_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Music',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Music ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'music_sugg_link')
          ));
        }

        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupoffer')) {
          $form->addElement('Radio', 'offer_sugg_link', array(
              'label' => 'Suggest to Friends link for Group Offer',
              'description' => "Do you want to show the 'Suggest to Friends' link to users on the main pages of Group Offer ?",
              'multiOptions' => array(
                  1 => 'Yes, show this link.',
                  0 => 'No, do not show this link.'
              ),
              'value' => Engine_Api::_()->suggestion()->getModSettings('sitegroup', 'offer_sugg_link')
          ));
        }
        break;
    }
  }

  public Function guidelinesAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_modInfo');
  }

  // Added phrase in language file.
  public function addPhraseAction($phrase) {
    if ($phrase) {
      //file path name
      $targetFile = APPLICATION_PATH . '/application/languages/en/custom.csv';
      if (!file_exists($targetFile)) {
        //Sets access of file
        touch($targetFile);
        //changes permissions of the specified file.
        chmod($targetFile, 0777);
      }
      if (file_exists($targetFile)) {
        $writer = new Engine_Translate_Writer_Csv($targetFile);
        $writer->setTranslations($phrase);
        $writer->write();
        //clean the entire cached data manually
        @Zend_Registry::get('Zend_Cache')->clean();
      }
    }
  }

  public function iconAction() {
    $module = $this->_getParam('getModule');
    if (empty($module)) {
      return;
    }
    $this->view->className = 'notification_type_' . $module . '_suggestion';
  }

  //SHOW INVITE STATISTICS

  public function inviteStatisticsAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sugg_admin_main', array(), 'suggestion_invitestatistics');
    $this->view->category = $category = $this->_getParam('category', 'listview');
    
    $this->view->searchAjax =  $this->_getParam('searchAjax', false);
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $inviteTableName = $inviteTable->info('name');
    $select = $inviteTable->select();
    if ($category == 'listview') { 
      $this->view->formFilter = $formFilter = new Seaocore_Form_Admin_Invite_Filter();
      
      $page = $this->_getParam('page', 1);     
      //GET USER TABLE
      $userTable = Engine_Api::_()->getDbtable('users', 'user');
      $userTableName = $userTable->info('name');
      // Process form
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
          'order' => 'totalInvites',
          'order_direction' => 'DESC',
              ), $values);

      $this->view->assign($values);
      
      
    $select->setIntegrityCheck(false);
      // Set up select info
      $select->from($inviteTableName , array('COUNT(new_user_id) as totalInvites'));
//      $select->from($inviteTableName, array("COUNT(`{$inviteTableName}`.`new_user_id`) as abc")); 
      $select->join($userTableName, "`{$userTableName}`.`user_id`=`{$inviteTableName}`.`user_id`", array("displayname", 'username', 'email', 'user_id')); 
//      $select->where("(`{$inviteTableName}`.`new_user_id` <> ?)", 0);
//      $select->where("(`{$inviteTableName}_2`.`new_user_id` = ?)", 0);
      
      $select->order((!empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
       $select->group(($inviteTableName.'.user_id' ));

      if (!empty($values['displayname'])) {
        $select->where($userTableName . '.displayname LIKE ?', '%' . $values['displayname'] . '%');
      }
     
      if (!empty($values['email'])) {
        $select->where($userTableName. '.email LIKE ?', '%' . $values['email'] . '%');
      }
      if (!empty($values['username'])) {
        $select->where($userTableName. '.username LIKE ?', '%' . $values['username'] . '%');
      }

      // Filter out junk
      $valuesCopy = array_filter($values);
   
      // Make paginator
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $this->view->paginator->setItemCountPerPage(50);
      $this->view->paginator = $paginator->setCurrentPageNumber($page);
      $this->view->formValues = $valuesCopy;

     /// $this->view->superAdminCount = count(Engine_Api::_()->user()->getSuperAdmins());
     // $this->view->hideEmails = _ENGINE_ADMIN_NEUTER;
      //$this->view->formDelete = new User_Form_Admin_Manage_Delete();

      //$this->view->openUser = (bool) ( $this->_getParam('open') && $paginator->getTotalItemCount() == 1 );
    }
    
    else if ($category == 'graphview') { 
      
      $chunk = Zend_Date::DAY;
      $period = Zend_Date::WEEK;
      $start = time();

      // Make start fit to period?
      $startObject = new Zend_Date($start);

      $partMaps = $this->_periodMap[$period];
      foreach ($partMaps as $partType => $partValue) {
        $startObject->set($partValue, $partType);
      }
      $startObject->add(1, $chunk); 
       $this->view->is_ajax = $this->_getParam('is_ajax', 0);
       $this->view->formFilterGraph = $formFilterGraph = new Seaocore_Form_Admin_Invite_FilterGraph();
       $date_select = $select->from($inviteTable, array('MIN(timestamp) as earliest_invite_date'));       
       $earliest_invite_date = $select->query()
                          ->fetchColumn();
       $this->view->prev_link = 1;
      $this->view->startObject = $startObject = strtotime($startObject);
      $this->view->earliest_ad_date = $earliest_invite_date = strtotime($earliest_invite_date);
      if ($earliest_invite_date > $startObject) {
        $this->view->prev_link = 0;
      }
       
      
    }
  }

}

?>
