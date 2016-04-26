<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Photo_Album extends Engine_Form {

  public function init() {

    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id');
    $albumvalues['album_id'] = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($group_id)->album_id;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITEGROUP ITEM
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitegroup->owner_id == $viewer_id || $can_edit == 1) {
      $defaultalbum_id = 0;
    } else {
      $defaultalbum_id = $albumvalues['album_id'];
    }

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $user = Engine_Api::_()->user()->getViewer();

    $this
            ->setTitle('Add New Photos')
            ->setDescription('Browse and choose photos on your system to add to this album.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'albums_create')
            ->setAttrib('class', 'global_form sitegroup_form_upload')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Select', 'album', array(
        'label' => 'Choose Album',
        'multiOptions' => array('0' => 'Create A New Album'),
        'onchange' => "updateTextFields()",
    ));

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['group_id'] = $group_id;
    $paramsAlbum['getSpecialField'] = 1;

    $all_albums = Engine_Api::_()->getDbtable('albums', 'sitegroup')->getAlbums($paramsAlbum);
    $album_options = Array();
    foreach ($all_albums as $all_album) {
      $album_options[$all_album->album_id] = htmlspecialchars_decode($all_album->getTitle());
    }

    $this->album->addMultiOptions($album_options);

    $this->addElement('Text', 'title', array(
        'label' => 'Album Title',
        'maxlength' => '256',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '256')),
        )
    ));

    $this->addElement('Hidden', 'group_id', array(
        'value' => $group_id,
        'order' => 333
    ));

    $this->addElement('Hidden', 'default_album_id', array(
        'value' => $defaultalbum_id,
        'order' => 334
    ));

    // Privacy
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.manageadmin', 1))
      $ownerTitle = "Group Admins";
    else
      $ownerTitle = "Just Me";

    $user = Engine_Api::_()->user()->getViewer();
    
//     $availableLabels = array(
//         'registered' => 'All Registered Members',
//         'owner_network' => 'Friends and Networks',
//         'owner_member_member' => 'Friends of Friends',
//         'owner_member' => 'Friends Only',
//         'owner' => $ownerTitle
//     );

    $allowMemberInLevel = Engine_Api::_()->authorization()->getPermission($user_level, 'sitegroup_group', 'smecreate');
    $allowMemberInthisPackage = false;
    $allowMemberInthisPackage = Engine_Api::_()->sitegroup()->allowPackageContent($sitegroup->package_id, "modules", "sitegroupmember");
    $sitegroupMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
    
		$availableLabels = array(
			'registered' => 'All Registered Members',
			'owner_network' => 'Friends and Networks',
			'owner_member_member' => 'Friends of Friends',
			'owner_member' => 'Friends Only',
			'like_member' => 'Who Liked This Group',
		);
		if (!empty($sitegroupMemberEnabled) && $allowMemberInthisPackage) {
			$availableLabels['member'] = 'Group Members Only';
		} elseif( !empty($sitegroupMemberEnabled) && $allowMemberInLevel) {
			$availableLabels['member'] = 'Group Members Only';
		}
		$availableLabels['owner'] = $ownerTitle;
    
    
    
    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_album', $user, 'auth_tag');

    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));
    if (!empty($tagOptions) && count($tagOptions) >= 1) {
      if (count($tagOptions) == 1) {
        $this->addElement('hidden', 'auth_tag', array('value' => key($tagOptions)));
      } else {
        $this->addElement('Select', 'auth_tag', array(
            'label' => 'Tag Post Privacy',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $tagOptions,
            'value' => key($tagOptions),
        ));
        $this->auth_tag->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Init search
    $this->addElement('Checkbox', 'search', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Show this album in search results"),
        'value' => 1,
        'disableTranslator' => true
    ));

    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
            ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);

    $this->addElement('Hidden', 'fancyuploadfileids');
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Photos',
        'type' => 'submit',
    ));
  }

  public function clearAlbum() {

    $this->getElement('album')->setValue(0);
  }

  public function saveValues() {

    $set_cover = false;
    $values = $this->getValues();
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = Array();
    
    if ((empty($values['owner_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
    } else {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
    if ((empty($values['album'])) && (empty($values['default_album_id']))) {
      $params['title'] = $values['title'];
      if (empty($params['title'])) {
        $params['title'] = "Untitled Album";
      }
      $params['search'] = $values['search'];

      $default_album_id = Engine_Api::_()->getItemTable('sitegroup_album')->getDefaultAlbum($values['group_id'])->album_id;

      if (empty($default_album_id)) {
        $params['default_value'] = 1;
      } else {
        $params['default_value'] = 0;
      }

      $params['group_id'] = $values['group_id'];
      $album = Engine_Api::_()->getDbtable('albums', 'sitegroup')->createRow();
      $album->setFromArray($params);
      $album->view_count = 1;
      $album->save();
      $set_cover = true;

      $auth = Engine_Api::_()->authorization()->context;
      //$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered');

			$sitegroupmemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember');
			if (!empty($sitegroupmemberEnabled)) {
				$roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
			} else {
				$roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 	'registered', 'everyone');
			}

      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'registered';
        }
      }

      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }

      //COMMENT PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
      }
    } else {
      if (!empty($values['album'])) {
        $album = Engine_Api::_()->getItem('sitegroup_album', $values['album']);
      } else {
        $album = Engine_Api::_()->getItem('sitegroup_album', $values['default_album_id']);
      }
    }
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0);
    $content_id = Engine_Api::_()->sitegroup()->GetTabIdinfo('sitegroup.photos-sitegroup', $album->group_id, $layout);
    $sitegroup = Engine_Api::_()->core()->getSubject('sitegroup_group');
   
    if (count($values['file'] > 0)) {
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $sendFB_Activity = 0;
      $activityFeedType = null;
      if (Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) {
        $activityFeedType = 'sitegroupalbum_admin_photo_new';
        $sendFB_Activity = 1;
      } elseif ($sitegroup->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($sitegroup)) {
        $activityFeedType = 'sitegroupalbum_photo_new';
        $sendFB_Activity = 1;
      }

      if ($activityFeedType) {
        $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $sitegroup, $activityFeedType, null, array('child_id' => $album->getIdentity(),'count' => count($values['file'])));
        Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
      }

      //GROUP ALBUMS CREATE NOTIFICATION AND EMAIL WORK
      if(!empty($action)) {
				$sitegroupVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitegroup')->version;
				if ($sitegroupVersion >= '4.2.9p3') {
					Engine_Api::_()->sitegroup()->sendNotificationEmail($album, $action, 'sitegroupalbum_create', 'SITEGROUPALBUM_CREATENOTIFICATION_EMAIL', 'Groupevent Invite', count($values['file']));
					
					$isGroupAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitegroup')->isGroupAdmins($viewer->getIdentity(), $album->group_id);
					if (!empty($isGroupAdmins)) {
						//NOTIFICATION FOR ALL FOLLWERS.
						Engine_Api::_()->sitegroup()->sendNotificationToFollowers($album, $action, 'sitegroupalbum_create', count($values['file']));
					}
				}
      }

      if ($sendFB_Activity == 1) {
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $facebooksefeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebooksefeed');
          $facebooksefeedmoduleversion = $facebooksefeedmodule->version;
          if ($facebooksefeedmoduleversion > '4.1.6p2') {
            $album_array = array();
            $album_array['type'] = 'sitegroupalbum_photo_new';
            $album_array['object'] = $album;
            $album_array['file_ids'] = $this->getValues();
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($album_array);
          }
        }
      }
      
    }

    $count = 0;
    foreach ($values['file'] as $photo_id) {
      $photo = Engine_Api::_()->getItem("sitegroup_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
        continue;
      if ($set_cover) {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
      $photo->album_id = $album->album_id;
      $photo->collection_id = $album->album_id;
      $photo->save();

      if ($action instanceof Activity_Model_Action && $count < 8) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $count++;
    }
    return $album;
  }

}