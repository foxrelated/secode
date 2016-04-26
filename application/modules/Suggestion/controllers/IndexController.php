<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Suggestion_IndexController extends Seaocore_Controller_Action_Standard {

    public function init() {
        $session = new Zend_Session_Namespace();

	if (empty($session->googleredirect) && empty($session->yahooredirect) && empty($session->windowlivemsnredirect) && empty($session->aolredirect) && empty($session->facebookredirect) && empty($session->linkedinredirect)&& empty($session->twitterredirect) && empty($_POST['task'])) {
            if (!$this->_helper->requireUser()->isValid())
                return;
        }

    }

    // Function for view all page which are linked by "suggestion home" widget.
    public function viewfriendsuggestionAction() {
        //SAVING THE INVITED USERS IN DATABASE INVITE TABLE.
        $this->view->success_fbinvite = false;
	 $fbinvited = $this->getRequest()->get('redirect_fbinvite',0);
        if ($fbinvited && $this->getRequest()->isPost()) {
            $facebookInvite = new Seaocore_Api_Facebook_Facebookinvite();
            $facebookInvite->seacoreInvite($this->getRequest()->get('ids'), 'facebook', 'suggestion');
            $this->view->success_fbinvite = true;
        }
        //Current user Id
        $this->view->user_id = $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->modArray = Engine_Api::_()->suggestion()->mix_suggestions(21, 'findFriend');

        //Sitemobile Code.
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            Zend_Registry::set('sitemobileNavigationName', 'suggestion_main_app') ;
            Zend_Registry::set('setFixedCreationForm', true);
						Zend_Registry::set('setFixedCreationFormBack', 'back');
						Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Friend Suggestions'));
            // Render
            $this->_helper->content
                    //->setNoRender()
                    ->setEnabled()
            ;
        }
    }

    // Function to handle the profile picture suggestion from one user to the other.
    public function profilePictureAction() {
        $id = $this->_getParam("id");
        $this->view->displayname = Engine_Api::_()->getItem('user', $id)->displayname;
        $this->view->form = $form = new Suggestion_Form_Photo();

        if ($this->getRequest()->isPost()) {

            // Condition for check email allow or not by user.
            $user = Engine_Api::_()->user()->getViewer();

            $values = $form->getValues();

            $is_error = 0;

            if (isset($values['Filedata'])) { // When select the image.
                $this->getSession()->data = $form->getValues();
                $file = APPLICATION_PATH . '/public/temporary/' . $values['Filedata'];
                $path = dirname($file);
                $name = basename($file);
                $this->_resizeImages($form->Filedata->getFileName());
                $this->view->image_name = $name;
                return;
            } else // If click on the submit button. {
            // Show error message if without select any image click on submit.
            if (empty($_POST['image'])) {
                $error = $this->view->translate('Please choose a photo to suggest.');
                $this->view->status = false;
                $error = Zend_Registry::get('Zend_Translate')->_($error);
                $form->getDecorator('errors')->setOption('escape', false);
                $form->addError($error);
                return;
            }

            $coordinates = $this->_getParam("coordinates");


            $suggestionTable = Engine_Api::_()->getItemTable('suggestion');
            $values = $form->getValues();
            $owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            // Begin database transaction
            $db = $suggestionTable->getAdapter();
            $db->beginTransaction();

            try {
                $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                // Insert value in "Received" data base.
                $suggestionRow = $suggestionTable->createRow();
                $suggestionRow->owner_id = $id;
                $suggestionRow->sender_id = $user_id;
                $suggestionRow->entity = 'photo';
                $suggestionRow->save();

                // Add in the notification table for show in the "update".
                $owner_obj = Engine_Api::_()->getItem('user', $id);
                $sender_obj = Engine_Api::_()->getItem('user', $user_id);
                // $owner_obj : Object which are geting suggestion.
                // $sender_obj : Object which are sending suggestion.
                // $list : Object from which table we'll link.
                // suggestion_picture :notification type.
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($owner_obj, $sender_obj, $suggestionRow, 'picture_suggestion');
                $getCorePluginVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version;
                if ($getCorePluginVersion < '4.1.8') {
                    Engine_Api::_()->authorization()->context->setAllowed($suggestionRow, 'everyone', 'view', 'everyone');
                }

                // Set photo
                if (!empty($_POST['image'])) {
                    $suggestionRow->setPhoto($_POST['image'], $coordinates);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            // After submit close the smoothbox.
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array($this->view->translate("Your suggestions have been sent."))
                    )
            );
        }
    }

    //}
    // This function is called in "profilePictureAction()" and creates images in "Temporary" folder in different sizes.
    protected function _resizeImages($file) {
        $name = basename($file);
        $path = dirname($file);

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $name)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $name)
                ->destroy();

        // Resize image (icon.normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(48, 120)
                ->write($path . '/in_' . $name)
                ->destroy();

        // Resize image (icon.square)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $name)
                ->destroy();
    }

    // This function is for the Viewall page of Suggestions.
    public function viewallAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $getViewAllSugg = array();

        if (isset($_REQUEST['type'])) {
            $getType = $_REQUEST['type'];
            $getArray = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginInfos($getType);
            $this->view->type = $getArray['displayName'];
        }

        // Sending base URL in view file.
        $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $sugg_display_object = array();
        // Get suggestion which return through array.
        $sugg_display = Engine_Api::_()->suggestion()->see_suggestion_display();

        // Call for number of user per widget.
        $this->view->numOfSuggestion = Engine_Api::_()->suggestion()->sugg_display();

        if (!empty($sugg_display)) {
            foreach ($sugg_display as $row_all_sugg_dis) {
                $getSuggestion = Engine_Api::_()->getDbTable('suggestions', 'suggestion')->getSuggestion($row_all_sugg_dis['suggestion_id'], $row_all_sugg_dis['sender_id']);

                if (!empty($getSuggestion)) {
                    $getViewAllSugg[$row_all_sugg_dis['entity']][] = $getSuggestion;
                }
            }
            $this->view->getModInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed();
            $this->view->getModName = Engine_Api::_()->getApi('modInfo', 'suggestion')->getModName();
            $this->view->getViewAllSugg = $getViewAllSugg;
        }
    }

    // This function is for the view page of a single suggestion
    public function viewAction() {
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Sending base URL in view file.
        $this->view->sugg_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        $this->view->getSuggestion = $getSuggestion = Engine_Api::_()->getDbTable('suggestions', 'suggestion')->getSuggestion($this->_getParam('sugg_id', null));
         
        //Sitemobile code.        
        if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') && $getSuggestion['modHref']) {
        return $this->_redirectCustom($getSuggestion['modHref'], array('prependBase' => false));
        }
    }

    // Ajax : call when confirm friend from notification page.
    protected $friend_detail;

    public function notificationacceptAction() {
        $friend_id = (int) $this->_getParam('friend_id');
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        // Remove suggestion if user have from "suggestion table" & "notification table".
        $received_table = Engine_Api::_()->getItemTable('suggestion');
        $received_name = $received_table->info('name');
        $received_select = $received_table->select()
                ->from($received_name, array('suggestion_id'))
                ->where('owner_id = ?', $user_id)
                ->where('entity = ?', 'friend')
                ->where('entity_id = ?', $friend_id);
        $fetch_array = $received_select->query()->fetchAll();

        if (!empty($fetch_array)) {
            foreach ($fetch_array as $row_friend_array)
            // Delete from "Notification table" from update tab.
                Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_id = ?' => $row_friend_array['suggestion_id'], 'type = ?' => 'friend_suggestion'));
            // Remove suggestion from "suggestion table".
            Engine_Api::_()->getItem('suggestion', $row_friend_array['suggestion_id'])->delete();
        }

        // After deleting work on suggestion which will display on request listing page.
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $add_friend_suggestion = Engine_Api::_()->suggestion()->add_friend_suggestion($friend_id, 4, 'accept_request', 'show_friend_suggestion');

        $friend_object = Engine_Api::_()->getItem('user', $friend_id);
        $isSitethemeModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetheme');
        // If there are any suggestion available then make suggestion and return this.
        if (!empty($add_friend_suggestion) && empty($isSitethemeModuleEnable)) {
            $suggestion_user = Engine_Api::_()->suggestion()->suggestion_users_information($add_friend_suggestion, '');
            $friend_link = '';
            foreach ($suggestion_user as $user_info) {
                $friend_link = $user_info->user_id;
                $photo = $this->view->htmlLink($user_info->getHref(), $this->view->itemPhoto($user_info, 'thumb.icon'), array('class' => 'thumb-img', 'target' => '_parent'));
                $title = $this->view->htmlLink($user_info->getHref(), $user_info->getTitle(), array('target' => '_parent'));
                $link = '<div id="userResponce_' . $user_info->user_id . '"><a onclick="friendSend(' . $user_info->user_id . ')" href="javascript:void(0);" class="buttonlink icon_friend_add" ><b>' . $this->view->translate("Add Friend") . '</b></a></div>';
                $friend_description = '<div id="user_' . $user_info->user_id . '" class="ajex-suggestion">' . $photo . '<div>' . $title . '</div>' . '' . $link . '</div>';
                $this->friend_detail .= $friend_description;
            }
            $friend_detail = '<b>' . $this->view->translate("You are now friends with %s. Next, %s", $this->view->htmlLink($friend_object->getHref(), $friend_object->getTitle()), $this->view->htmlLink($friend_object->getHref(), $this->view->translate("view %s's profile.", $friend_object->getTitle()))) . '</b>';
            $friend_sub_detail = $this->view->translate("You may also know some of %s's friends:", $friend_object->getTitle());
        }
        // If there are no suggestion available then only show message.
        else {
            $this->friend_detail = '';
            $friend_detail = '<b>' . $this->view->translate("You are now friends with %s. Next, %s", $this->view->htmlLink($friend_object->getHref(), $friend_object->getTitle()), $this->view->htmlLink($friend_object->getHref(), $this->view->translate("view %s's profile.", $friend_object->getTitle()))) . '</b>';
            $friend_sub_detail = '';
        }

        $this->view->status = true;
        $this->view->friend_link = $this->friend_detail;
        $this->view->friend_detail = $friend_detail;
        $this->view->friend_sub_detail = $friend_sub_detail;
    }

    // Ajax : Request send to suggested friend from "Notification page".
    public function sendfriendAction() {
        $friend_id = (int) $this->_getParam('friend_id');
        $this->addAction($friend_id);
        $this->view->status = true;
        $this->view->responce = $this->view->translate('Friend Request sent');
    }

    // This is widgetized page where - we are display the mixed suggestion in the center and people you may know in right.
    public function exploreAction() {
        $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if (empty($user_id)) {
            return $this->_forward('requireauth', 'error', 'core');
        }
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if ($coreversion < '4.1.0') {
            $this->_helper->content->render();
        } else {
            $this->_helper->content->setNoRender()->setEnabled();
        }
    }

    public function mutualfriendAction() {
      $friend_id = $this->_getParam('friend_id');
      $getMutualFriend = Engine_Api::_()->getApi('coreFun', 'suggestion')->getMutualFriend($friend_id);
      $headerTitle = Zend_Registry::get('Zend_Translate')->_('Mutual Friends');
      if (!empty($getMutualFriend)) {
        $this->view->friend_obj = Engine_Api::_()->suggestion()->suggestion_users_information($getMutualFriend, '');
        
        $headerTitle = $this->view->translate(array('%s Mutual Friend', '%s Mutual Friend', count($this->view->friend_obj)), $this->view->locale()->toNumber(count($this->view->friend_obj)));
       
      }
      
      Zend_Registry::set('setFixedCreationForm', true);
      Zend_Registry::set('setFixedCreationHeaderTitle', $headerTitle);
  }
  public function switchPopupAction() {
        $modName = $this->_getParam("modName", null);
        $modContentId = $this->_getParam("modContentId", null);
        $listingTypeId = $this->_getParam("listingId", null);
        $modError = $this->_getParam("modError", null);

        $modItemType = null;
        $findFriendFunName = null;
        $notificationType = null;

        if (strstr($modName, "sitereview")) {
            $getModId = Engine_Api::_()->suggestion()->getReviewModInfo($listingTypeId);
            $modArray = Engine_Api::_()->getItem('suggestion_modinfo', $getModId);
            $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed("sitereview_" . $getModId);
            $modInfo = $modInfo["sitereview_" . $getModId];
        } else {
            $modInfo = Engine_Api::_()->getApi('modInfo', 'suggestion')->getPluginDetailed($modName);
            $modInfo = $modInfo[$modName];
        }

        if (!empty($modInfo['notificationType'])) {
            $notificationType = $modInfo['notificationType'];
        }
        if (!empty($modInfo['findFriendFunName'])) {
            $findFriendFunName = $modInfo['findFriendFunName'];
        }
        if (!empty($modInfo['itemType'])) {
            $modItemType = $modInfo['itemType'];
        }
        if (!empty($modInfo['modName'])) {
            $modName = $modInfo['pluginName'];
        }

        $this->_forward('show-popup', 'index', 'suggestion', array('modName' => $modName, 'modContentId' => $modContentId, 'notificationType' => $notificationType, 'findFriendFunName' => $findFriendFunName, 'modError' => $modError, 'modItemType' => $modItemType, 'ReviewlistingTypeId' => $listingTypeId));
    }

    public function popupsAction() {
        $sugg_id = $this->_getParam('sugg_id');
        $sugg_type = $this->_getParam('sugg_type');
        switch ($sugg_type) {
            case 'page_document':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_document', 'notification_type' => 'page_document_suggestion', 'item_type' => 'sitepagedocument_document'));
                break;

            case 'page_poll':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_poll', 'notification_type' => 'page_poll_suggestion', 'item_type' => 'sitepagepoll_poll'));
                break;

            case 'page_music':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_music', 'notification_type' => 'page_music_suggestion', 'item_type' => 'sitepagemusic_playlist'));
                break;

            case 'page_offer':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_offer', 'notification_type' => 'page_offer_suggestion', 'item_type' => 'sitepageoffer_offer'));
                break;

            case 'page_video':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_video', 'notification_type' => 'page_video_suggestion', 'item_type' => 'sitepagevideo_video'));
                break;

            case 'page_event':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_event', 'notification_type' => 'page_event_suggestion', 'item_type' => 'sitepageevent_event'));
                break;

            case 'page_album':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_album', 'notification_type' => 'page_album_suggestion', 'item_type' => 'sitepage_album'));
                break;

            case 'page_report':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_report', 'notification_type' => 'page_report_suggestion', 'item_type' => 'sitepage_report'));
                break;

            case 'page_note':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_note', 'notification_type' => 'page_note_suggestion', 'item_type' => 'sitepagenote_note'));
                break;

            case 'page_review':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'page_review', 'notification_type' => 'page_review_suggestion', 'item_type' => 'sitepagereview_review'));
                break;

            case 'sitepage_page':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'sitepage', 'notification_type' => 'page_suggestion', 'item_type' => 'sitepage_page'));
                break;

            case 'business_document':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_document', 'notification_type' => 'business_document_suggestion', 'item_type' => 'sitebusinessdocument_document'));
                break;

            case 'business_poll':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_poll', 'notification_type' => 'business_poll_suggestion', 'item_type' => 'sitebusinesspoll_poll'));
                break;

            case 'business_music':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_music', 'notification_type' => 'business_music_suggestion', 'item_type' => 'sitebusinessmusic_playlist'));
                break;

            case 'business_offer':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_offer', 'notification_type' => 'business_offer_suggestion', 'item_type' => 'sitebusinessoffer_offer'));
                break;

            case 'business_video':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_video', 'notification_type' => 'business_video_suggestion', 'item_type' => 'sitebusinessvideo_video'));
                break;

            case 'business_event':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_event', 'notification_type' => 'business_event_suggestion', 'item_type' => 'sitebusinessevent_event'));
                break;

            case 'business_album':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_album', 'notification_type' => 'business_album_suggestion', 'item_type' => 'sitebusiness_album'));
                break;

            case 'business_note':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_note', 'notification_type' => 'business_note_suggestion', 'item_type' => 'sitebusinessnote_note'));
                break;

            case 'business_review':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'business_review', 'notification_type' => 'business_review_suggestion', 'item_type' => 'sitebusinessreview_review'));
                break;

            case 'sitebusiness_business':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'sitebusiness', 'notification_type' => 'business_suggestion', 'item_type' => 'sitebusiness_business'));
                break;

            case 'group_document':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_document', 'notification_type' => 'group_document_suggestion', 'item_type' => 'sitegroupdocument_document'));
                break;

            case 'group_poll':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_poll', 'notification_type' => 'group_poll_suggestion', 'item_type' => 'sitegrouppoll_poll'));
                break;

            case 'group_music':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_music', 'notification_type' => 'group_music_suggestion', 'item_type' => 'sitegroupmusic_playlist'));
                break;

            case 'group_offer':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_offer', 'notification_type' => 'group_offer_suggestion', 'item_type' => 'sitegroupoffer_offer'));
                break;

            case 'group_video':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_video', 'notification_type' => 'group_video_suggestion', 'item_type' => 'sitegroupvideo_video'));
                break;

            case 'group_event':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_event', 'notification_type' => 'group_event_suggestion', 'item_type' => 'sitegroupevent_event'));
                break;

            case 'group_album':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_album', 'notification_type' => 'group_album_suggestion', 'item_type' => 'sitegroup_album'));
                break;

            case 'group_note':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_note', 'notification_type' => 'group_note_suggestion', 'item_type' => 'sitegroupnote_note'));
                break;

            case 'group_review':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'group_review', 'notification_type' => 'group_review_suggestion', 'item_type' => 'sitegroupreview_review'));
                break;

            case 'sitegroup_group':
                $this->_forward('switch-popup', 'index', 'suggestion', array('modContentId' => $sugg_id, 'modError' => 1, 'modName' => 'sitegroup', 'notification_type' => 'group_suggestion', 'item_type' => 'sitegroup_group'));
                break;
        }
    }

    public function showPopupAction() {
        $modName = $this->_getParam("modName", 0);
        if (empty($modName)) {
            $modName = $_GET['modName'];
        }
        $this->view->modName = $modName;
        $modContentId = $this->_getParam("modContentId", null);

        $this->view->modError = $modSetError = $this->_getParam("modError", 0);
        $this->view->modItemType = $modItemType = $this->_getParam("modItemType", null);
        $this->view->notificationType = $notificationType = $this->_getParam("notificationType", 0);

        $findFriendFunName = $this->_getParam("findFriendFunName", null);
        if (empty($findFriendFunName)) {
            $findFriendFunName = $notificationType;
        }
        if (empty($findFriendFunName)) {
            $findFriendFunName = $_GET['findFriendFunName'];
        }
        $this->view->findFriendFunName = $findFriendFunName;
        $mod_notify_type = $this->_getParam("notification_type", null);
        $mod_entity = $this->_getParam("entity", null);
        $item_type = $this->_getParam("item_type", null);
        if (empty($mod_notify_type) && !empty($_GET['notification_type'])) {
            $mod_notify_type = $_GET['notification_type'];
        }
        if (empty($mod_entity) && !empty($_GET['entity'])) {
            $mod_entity = $_GET['entity'];
        }
        if (empty($item_type) && !empty($_GET['item_type'])) {
            $item_type = $_GET['item_type'];
        }
        $this->view->notification_type = $mod_notify_type;
        $this->view->entity = $mod_entity;
        $this->view->item_type = $item_type;
        $this->view->mod_set_error = $modSetError;
        $this->view->search_true = false;
        if ($this->getRequest()->isPost()) {
            // Send suggestion of the friends, which loggden user select in popup.
            $userFriendArray = $this->getRequest()->getPost();
            foreach ($userFriendArray as $flag => $ownerId) {
                if (strpos($flag, 'check_') !== FALSE) {
                    $emailParams = array();
                    $entity = $userFriendArray['entity'];
                    $entityId = $userFriendArray['entity_id'];                    
                    
                    if( !empty($userFriendArray['entity_title']) )
                        $emailParams['entity_title'] = $userFriendArray['entity_title'];
                    
                    if( !empty($userFriendArray['entity_link']) )
                        $emailParams['entity_link'] = $userFriendArray['entity_link'];
                    
                    $notificationType = $notificationType;
                    if (strpos($modName, 'accept_request') !== FALSE) {
                        $this->addAction($ownerId);
                    } else if ($entity == 'friendfewfriend') {
                        Engine_Api::_()->getDbTable('suggestions', 'suggestion')->setSuggestion($entityId, $entity, $ownerId, $notificationType, $emailParams);
                    } else {
                        Engine_Api::_()->getDbTable('suggestions', 'suggestion')->setSuggestion($ownerId, $entity, $entityId, $notificationType, $emailParams);
                    }
                }
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'messages' => array($this->view->translate("Your suggestions have been sent."))
                    )
            );
        } else {
            // Set variables for JS. when open popups.
            $this->view->modContentId = $modContentId;
            $modFunName = $findFriendFunName;
            $modParem = 'modContentId';
            if (!empty($_GET['task'])) {
                $getTask = $_GET['task'];
            }
            if (!empty($_GET['selected_checkbox'])) {
                $getSelectCheckbox = $_GET['selected_checkbox'];
            }
            if (!empty($_GET['page'])) {
                $getPage = $_GET['page'];
            }
            if (!empty($_GET['searchs'])) {
                $getSearch = $_GET['searchs'];
            }
            if (!empty($_GET['show_selected'])) {
                $getShowSelected = $_GET['show_selected'];
            }
            if (!empty($_GET['action_id'])) {
                $getActionId = $_GET['action_id'];
            }
            if (!empty($_GET['selected_friend_flag'])) {
                $this->view->selectedFriendFlag = $_GET['selected_friend_flag'];
            }
            // Assign variables for resolving log error.
            if (empty($getTask)) {
                $getTask = null;
            }
            if (empty($getSelectCheckbox)) {
                $getSelectCheckbox = null;
            }
            if (empty($getPage)) {
                $getPage = null;
            }
            if (empty($getSearch)) {
                $getSearch = null;
            }
            if (empty($getShowSelected)) {
                $getShowSelected = null;
            }
            if (empty($getActionId)) {
                $getActionId = null;
            }
            $veiw = $this->openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId, $modContentId, $modFunName, $modParem, $modItemType, $modName);

            foreach ($veiw as $key => $value) {
                $this->view->$key = $value;
            }
        }
    }

    public function openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId, $modId, $modFunName, $modParem, $modItemType, $modName) {

        //THIS IS WHEN DO SOME ACTIVITY ON THE SUGGESTION PAGE.
        if (!empty($getTask)) {
            $view['search_true'] = true;
        }

        if (!empty($getSelectCheckbox)) {
            $view['selected_checkbox'] = $getSelectCheckbox;
            $getSelectCheckbox = trim($getSelectCheckbox, ',');
            $modStrId_array = explode(",", $getSelectCheckbox);
            $view['friends_count'] = @COUNT($modStrId_array);
        } else {
            $view['selected_checkbox'] = '';
            $view['friends_count'] = $selected_friend_count = 0;
        }

        $view['page'] = $page = !empty($getPage) ? $getPage : 1;
        $view['search'] = $search = !empty($getSearch) ? $getSearch : '';


        //IF THE REQUEST IS FOR SHOWING ONLY SELECTED FRIENDS.
        if (!empty($getShowSelected)) {
            $search = '';
            $view['show_selected'] = $selected_friend_show = 1;
            $view[$modParem] = $modId = $getActionId;
            $modId_array = $modStrId_array;
        }
        //IF THE REQUEST IS FOR SHOWING ALL FRIENDS.
        else {
            if (empty($modId)) {
                $view[$modParem] = $modId = $getActionId;
            }
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $sugg_popups_limit = 0;



            // $modName would be 'friend' only when "Add a Friend popup" & "Few friend popup"
            if ((strpos($modName, 'friend') !== FALSE)) {
                if (strpos($modFunName, 'add_friend') !== FALSE) {
                    $view['members'] = $fetch_member_myfriend = Engine_Api::_()->suggestion()->$modFunName($modId, $sugg_popups_limit, 'add_friend', $search);
                } else if (strpos($modFunName, 'few_friend') !== FALSE) {
                    $view['members'] = $fetch_member_myfriend = Engine_Api::_()->suggestion()->$modFunName($modId, '', $sugg_popups_limit, $search);
                }
            } else if ((strpos($modName, 'accept_request') !== FALSE)) { // $modName would be "accept_request" only when accept the friend request.
                $view['members'] = $fetch_member_myfriend = Engine_Api::_()->suggestion()->$modFunName($modId, $sugg_popups_limit, 'accept_request', $search);
	  }else { // In all other the modules call thiese function.
                $view['members'] = $fetch_member_myfriend = Engine_Api::_()->suggestion()->getSuggestedFriend($modName, $modId, 0, $search);
            }

            if (!empty($page)) {
                $fetch_member_myfriend->setCurrentPageNumber($page);
            }
            $fetch_member_myfriend->setItemCountPerPage(40);

            $modId_array = array();

            foreach ($fetch_member_myfriend as $modRow) {
                $modId_array[] = $modRow['resource_id'];
            }
            $view['show_selected'] = $selected_friend_show = 0;
        }
        $view['mod_combind_path'] = $modId_array;

        //HERE WE ARE CHECKING IF THE REQUEST IS FOR ONLY SHOW SELECTED FRIENDS THEN WE WILL MAKE PAGINATION OF USER OBJECT OTHERWISE WE WILL SIMPLY USER FETCHALL QUERY.
        if (!empty($modId_array)) {

            $tempSelectedFriend = '';
            if (!empty($modId_array) && !empty($modStrId_array)) {
                foreach ($modId_array as $values) {
                    if (in_array($values, $modStrId_array)) {
                        $tempSelectedFriend .= ',' . $values;
                    }
                }
            }

            $view['tempSelectedFriend'] = $tempSelectedFriend;


            $view['suggest_user_id'] = $modId_array;
            if ($selected_friend_show) {
                $view['suggest_user'] = $view['members'] = $selected_friends = Engine_Api::_()->suggestion()->suggestion_users_information($modId_array, $selected_friend_show, '');
                $selected_friends->setCurrentPageNumber($page);
                $selected_friends->setItemCountPerPage(100);
            } else {
                $view['suggest_user'] = $memberArray = Engine_Api::_()->suggestion()->suggestion_users_information($modId_array, $selected_friend_show, '');
            }
        }
        if (!empty($_GET['getArray'])) {
            $view['getArray'] = $_GET['getArray'];
        } else {
            $view['getArray'] = array();
        }
        return $view;
    }

    public function addFriendAction() {
        $this->view->friend_id = $friend_id = (int) $this->_getParam('friendId');
        if (!empty($friend_id)) {
            $this->addAction($friend_id);
        }
    }

    //THIS FUNCTION IS USED TO SAVE THE FRIEND REQUEST, AND PERFORM ALLIED ACTIONS FOR NOTIFICATION UPDATES, ETC.
    public function addAction($id) {
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Disable Layout.
        $this->_helper->layout->disableLayout(true);
        Engine_Api::_()->getApi('Invite', 'Seaocore')->addAction($id);
    }

    public function sendInvites($recipients) {
        Engine_Api::_()->getApi('Invite', 'Seaocore')->sendInvites($recipients);
    }

    // end public function sendInvites()
    // This action renders the content for the site introduction popup for newly signed up users
    public function introductionAction() {
        $session = new Zend_Session_Namespace();
        unset($session->new_user_create);
        unset($session->new_user_verify);
        $this->view->content = Engine_Api::_()->getItem('suggestion_introduction', 1)->content;
    }

    //SHOWING THE INVITE STATISTICS OF THE INVITER:
    public function viewstatisticsAction() {
        
    }

}
