<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Create.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Create extends Engine_Form {

  protected $_albums;
  protected $_roles = array(
      'everyone' => 'Everyone',
      'registered' => 'All Registered Members',
      'owner_network' => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member' => 'Friends Only',
      'owner' => 'Just Me'
  );

  public function init() {

    $auth = Engine_Api::_()->authorization()->context;
    $user = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $quota = Engine_Api::_()->authorization()->getPermission($user->level_id, 'sesmusic_album', 'addalbum_max');

    $albumsCount = Engine_Api::_()->getDbtable('albums', 'sesmusic')->getAlbumsCount(array('viewer_id' => $user->getIdentity(), 'column_name' => array('album_id', 'title')));

    $this->setTitle('Create New Music Album')
            ->setDescription('Create new music album and upload songs from your computer to add to this music album.')
            ->setAttrib('id', 'form-upload-music')
            ->setAttrib('name', 'album_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    if ($quota > count($albumsCount) || $quota == 0) {
      //Init name
      $this->addElement('Text', 'title', array(
          'label' => 'Music Album Name',
          'placeholder' => 'Enter Music Album Name',
          'maxlength' => '63',
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '63')),
          )
      ));

      //Init descriptions
      $this->addElement('Textarea', 'description', array(
          'label' => 'Music Album Description',
          'placeholder' => 'Enter Music Album Description',
          'maxlength' => '1000',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '1000')),
              new Engine_Filter_EnableLinks(),
          ),
      ));

      //Category Work
      $categories = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getCategory(array('column_name' => '*', 'param' => 'album'));
      $data[] = 'Select Category';
      foreach ($categories as $category) {
        $data[$category['category_id']] = $category['category_name'];
      }
      if (count($data) > 1) {
        //Add Element: Category
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $data,
            'onchange' => "ses_subcategory(this.value)",
        ));

        //Add Element: Sub Category
        $this->addElement('Select', 'subcat_id', array(
            'label' => "2nd-level Category",
            'allowEmpty' => true,
            'required' => false,
            'registerInArrayValidator' => false,
            'onchange' => "sessubsubcat_category(this.value)",
        ));

        //Add Element: Sub Sub Category
        $this->addElement('Select', 'subsubcat_id', array(
            'label' => "3rd-level Category",
            'allowEmpty' => true,
            'registerInArrayValidator' => false,
            'required' => false,
        ));
      }
      //End category work 
      //AUTHORIZATIONS
      $availableLabels = $this->_roles;

      //Element: auth_view
      $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesmusic_album', $user, 'auth_view');
      $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

      if (!empty($viewOptions) && count($viewOptions) >= 1) {
        //Make a hidden field
        if (count($viewOptions) == 1) {
          $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
          //Make select box
        } else {
          $this->addElement('Select', 'auth_view', array(
              'label' => 'View Privacy',
              'description' => 'Who may see this music album?',
              'multiOptions' => $viewOptions,
              'value' => key($viewOptions),
          ));
          $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
        }
      }

      //Element: auth_comment
      $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesmusic_album', $user, 'auth_comment');
      $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

      if (!empty($commentOptions) && count($commentOptions) >= 1) {
        //Make a hidden field
        if (count($commentOptions) == 1) {
          $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
          //Make select box
        } else {
          $this->addElement('Select', 'auth_comment', array(
              'label' => 'Comment Privacy',
              'description' => 'Who may post comments on this music album?',
              'multiOptions' => $commentOptions,
              'value' => key($commentOptions),
          ));
          $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
        }
      }


      $uploadoption = $settings->getSetting('sesmusic.uploadoption', 'myComputer');

      //Init file uploader
      $fancyUpload = new Engine_Form_Element_FancyUpload('file');
      $fancyUpload->clearDecorators()
              ->addDecorator('FormFancyUpload')
              ->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement' => '',
      ));
      Engine_Form::addDefaultDecorators($fancyUpload);
      $this->addElement($fancyUpload);

      $i = -5000;
      //Init hidden file IDs
      $this->addElement('Hidden', 'fancyuploadfileids', array(
          'order' => $i--,
      ));

      $uploadoption = $settings->getSetting('sesmusic.uploadoption', 'myComputer');
      if ($uploadoption == 'both' || $uploadoption == 'soundCloud') {
        //Init hidden file IDs
        $this->addElement('Hidden', 'soundcloudIds', array(
            'order' => $i--,
        ));
        
        $this->addElement('textarea', 'options', array(
            'label' => 'Upload from SoundCloud',
            'placeholder' => 'Enter the URL of song on SoundCloud',
            'description' => 'Enter the URL of song on SoundCloud.',
            'style' => 'display:none;',
        ));
      }

      //Init search checkbox
      $this->addElement('Checkbox', 'search', array(
          'label' => "Show this music album in search results",
          'value' => 1,
          'checked' => true,
      ));

      $this->addElement('File', 'album_cover', array(
          'label' => 'Music Album Cover Photo',
          'onchange' => 'showReadImage(this,"musicalbum_cover_preview")',
      ));
      $this->album_cover->addValidator('Extension', false, 'jpg,png,gif,jpeg');

      $album_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id');
      $album = Engine_Api::_()->getItem('sesmusic_album', $album_id);
      if ($album_id && $album && $album->album_cover) {
        $img_path = Engine_Api::_()->storage()->get($album->album_cover, '')->getPhotoUrl();
        $path = $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'musicalbum_cover_preview', array(
              'label' => 'Music Album Cover Photo Preview',
              'src' => $path,
              'width' => 100,
              'height' => 100,
          ));
        }
      } else {
        $this->addElement('Image', 'musicalbum_cover_preview', array(
            'label' => 'Music Album Cover Photo Preview',
            'width' => 100,
            'height' => 100,
            'disable' => true
        ));
      }

      if ($album_id) {
        if ($album->album_cover) {
          $this->addElement('Checkbox', 'remove_album_cover', array(
              'label' => 'Remove music album cover photo.'
          ));
        }
      }

      //Init album art
      $this->addElement('File', 'art', array(
          'label' => 'Music Album Main Photo',
          'onchange' => 'showReadImage(this,"musicalbum_main_preview")',
      ));
      $this->art->addValidator('Extension', false, 'jpg,png,gif,jpeg');

      if ($album_id && $album && $album->photo_id) {
        $img_path = Engine_Api::_()->storage()->get($album->photo_id, '')->getPhotoUrl();
        $path = $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'musicalbum_main_preview', array(
              'label' => 'Music Album Main Photo Preview',
              'src' => $path,
              'width' => 100,
              'height' => 100,
          ));
        }
      } else {
        $this->addElement('Image', 'musicalbum_main_preview', array(
            'label' => 'Music Album Main Photo Preview',
            'width' => 100,
            'height' => 100,
            'disable' => true
        ));
      }
      if ($album_id) {
        $album = Engine_Api::_()->getItem('sesmusic_album', $album_id);

        if ($album->photo_id) {
          $this->addElement('Checkbox', 'remove_album_main', array(
              'label' => 'Remove music album main photo.'
          ));
        }
      }


      //Init submit
      $this->addElement('Button', 'submit', array(
          'label' => 'Create Music Album',
          'type' => 'submit',
      ));
    } else {
      $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("You have already created the maximum number of music albums allowed. If you would like to create a music album, please delete an old one first.") . "</span></div>";
      $this->addElement('Dummy', 'dummy', array(
          'description' => $description,
      ));
      $this->dummy->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    }
  }

  public function clearUploads() {
    $this->getElement('fancyuploadfileids')->setValue('');
  }

  public function saveValues() {

    $albums = null;
    $values = $this->getValues();

    if (!empty($values['album_id'])) {
      $albums = Engine_Api::_()->getItem('sesmusic_albums', $values['album_id']);
      if (!empty($values['soundcloudIds'])) {
        foreach (explode(' ', $values['soundcloudIds']) as $file_id) {
          $albums->addSong($file_id, array('track_id' => 1));
        }
      }
    } else {
      $resource_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type');
      $resource_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_id');

      $albums = $this->_albums = Engine_Api::_()->getDbtable('albums', 'sesmusic')->createRow();
      $albums->title = htmlspecialchars(trim($values['title']), ENT_QUOTES, 'UTF-8');

      if (empty($albums->title))
        $albums->title = Zend_Registry::get('Zend_Translate')->_('_SESMUSIC_UNTITLED_PLAYLIST');

      $albums->owner_type = 'user';
      $albums->owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $albums->description = trim($values['description']);
      $albums->search = $values['search'];
      if (isset($values['category_id']))
        $albums->category_id = $values['category_id'];
      if (isset($values['subcat_id']))
        $albums->subcat_id = $values['subcat_id'];
      if (isset($values['subsubcat_id']))
        $albums->subsubcat_id = $values['subsubcat_id'];
      if (isset($values['label']))
        $albums->label = $values['label'];
      if ($resource_type)
        $albums->resource_type = $resource_type;
      if ($resource_id)
        $albums->resource_id = $resource_id;
      $albums->save();
      $values['album_id'] = $albums->album_id;

      //Get visitor IP Address
      $ip = $_SERVER['REMOTE_ADDR'];
      if ($ip)
        $albums->ip_address = $ip;
      $albums->save();

      //Assign $albums to a Core_Model_Item
      $albums = $this->_albums = Engine_Api::_()->getItem('sesmusic_albums', $values['album_id']);

      //Get file_id list
      $file_ids = array();
      foreach (explode(' ', $values['fancyuploadfileids']) as $file_id) {
        $file_id = trim($file_id);
        if (!empty($file_id))
          $file_ids[] = $file_id;
      }

      if (isset($values['soundcloudIds']) && !empty($values['soundcloudIds'])) {
        //Upload From Sound Cloud playlist, user and groups tracks
        if (strpos($values['soundcloudIds'], ',') > 0) {
          foreach (explode(',', $values['soundcloudIds']) as $file_id) {
            $albumSong = $albums->addSong($file_id, array('track_id' => 1, 'image' => 1));
            $image = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/' . $file_id . ".jpg";
            if (file_exists($image))
              $albumSong->setPhoto($image, 'mainPhoto', array('image' => 1, 'file_id' => $file_id));
          }
        } else {
          foreach (explode(' ', $values['soundcloudIds']) as $file_id) {
            $albumsong = $albums->addSong($file_id, array('track_id' => 1, 'image' => 1));
            $image = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/' . $file_id . ".jpg";
            if (file_exists($image))
              $albumsong->setPhoto($image, 'mainPhoto', array('image' => 1, 'file_id' => $file_id));
          }
        }
      }

      //Attach songs (file_ids) to playlist
      if (!empty($file_ids)) {
        foreach ($file_ids as $file_id) {
          $albums->addSong($file_id);
        }
      }

      //Only create activity feed item if "search" is checked
      if ($albums->search) {
        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activity->addActivity(Engine_Api::_()->user()->getViewer(), $albums, 'sesmusic_album_new', null, array('count' => count($file_ids)));
        if (null !== $action)
          $activity->attachActivity($action, $albums);
      }
    }

    //Authorizations
    $auth = Engine_Api::_()->authorization()->context;
    $prev_allow_comment = $prev_allow_view = false;
    foreach ($this->_roles as $role => $role_label) {
      //Allow viewers
      if ($values['auth_view'] == $role || $prev_allow_view) {
        $auth->setAllowed($albums, $role, 'view', true);
        $prev_allow_view = true;
      } else
        $auth->setAllowed($albums, $role, 'view', 0);

      //Allow comments
      if ($values['auth_comment'] == $role || $prev_allow_comment) {
        $auth->setAllowed($albums, $role, 'comment', true);
        $prev_allow_comment = true;
      } else
        $auth->setAllowed($albums, $role, 'comment', 0);
    }

    //Songs comment privacy
    $songs = Engine_Api::_()->getDbTable('albumsongs', 'sesmusic')->getAllSongs($albums->album_id);
    foreach ($songs as $song) {
      //Authorizations
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $prev_allow_comment = false;
      foreach ($this->_roles as $role => $role_label) {
        //Allow comments
        if ($values['auth_comment'] == $role || $prev_allow_comment) {
          $auth->setAllowed($song, $role, 'comment', true);
          $prev_allow_comment = true;
        } else
          $auth->setAllowed($song, $role, 'comment', 0);
      }
    }

    //Rebuild privacy
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    foreach ($actionTable->getActionsByObject($albums) as $action) {
      $actionTable->resetActivityBindings($action);
    }

    //Album Cover
    if (!empty($values['album_cover'])) {
      $previousPhoto = $albums->album_cover;
      if ($previousPhoto) {
        $albumCoverPhoto = Engine_Api::_()->getItem('storage_file', $previousPhoto);
        $albumCoverPhoto->delete();
      }
      $albums->setAlbumCover($this->album_cover, 'songCover');
    }

    if (isset($values['remove_album_cover']) && !empty($values['remove_album_cover'])) {
      $storage = Engine_Api::_()->getItem('storage_file', $albums->album_cover);
      $albums->album_cover = 0;
      $albums->save();
      if ($storage)
        $storage->delete();
    }

    if (isset($values['remove_album_main']) && !empty($values['remove_album_main'])) {
      $storage = Engine_Api::_()->getItem('storage_file', $albums->photo_id);
      $albums->photo_id = 0;
      $albums->save();
      if ($storage)
        $storage->delete();
    }

    if (!empty($values['art']))
      $albums->setPhoto($this->art);

    return $albums;
  }

}
