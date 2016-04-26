<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Video.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Form_Video extends Engine_Form {
  protected $_defaultProfileId;
  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }
  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }
  public function init() {
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id');
    if ($video_id) {
      $video = Engine_Api::_()->getItem('video', $video_id);
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Init form
    $this
            ->setTitle('Add New Video')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'video_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;
		if(Zend_Controller_Front::getInstance()->getRequest()->getParam('type'))
			$valueUpload = Zend_Controller_Front::getInstance()->getRequest()->getParam('type');
		else
			$valueUpload = '';
    $user = Engine_Api::_()->user()->getViewer();
		// Init video
    $this->addElement('Select', 'type', array(
        'label' => 'Video Source',
        'multiOptions' => array('0' => ' '),
        'onchange' => "updateTextFields()",
				'value'=>$valueUpload,
    ));
    $video_options = Array();
    $myComputer = false;
    $setting = Engine_Api::_()->getApi('settings', 'core');
		$viewer = Engine_Api::_()->user()->getViewer();
		$allowedUploadOption = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'video_upload_option');
    foreach ($allowedUploadOption as $key => $valueUploadoption) {
      if ($valueUploadoption == 'youtube' && Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', false))
        $video_options[1] = "YouTube";
			if ($valueUploadoption == 'youtubePlaylist' && Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey', false))
        $video_options[5] = 'Youtube Playlist';
      if ($valueUploadoption == 'vimeo')
        $video_options[2] = "Vimeo";
      if ($valueUploadoption == 'dailymotion')
        $video_options[4] = 'Daily Motion';
			if ($valueUploadoption == 'url')
        $video_options[16] = 'From URL';
			if ($valueUploadoption == 'embedcode')
        $video_options[17] = 'From Embed Code';
      if ($valueUploadoption == 'myComputer')
        $myComputer = true;
    }
    //My Computer
    if ($myComputer) {
      $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
      $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
      if (!empty($ffmpeg_path) && $allowed_upload) {
        if (Engine_Api::_()->hasModuleBootstrap('mobi') && Engine_Api::_()->mobi()->isMobile()) {
          $video_options[3] = "My Device";
        } else {
          $video_options[3] = "My Computer";
        }
      }
    }
    // Init name
    $this->addElement('Text', 'title', array(
        'label' => 'Video Title',
        'maxlength' => '100',
        'allowEmpty' => true,
        'required' => false,
        'filters' => array(
            //new Engine_Filter_HtmlSpecialChars(),
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100')),
        )
    ));
		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo_enable_location', 1)){
    $this->addElement('Text', 'location', array(
        'label' => 'Location',
        'id' => 'locationSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
    $this->addElement('Text', 'lat', array(
        'label' => 'Lat',
        'id' => 'latSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
    $this->addElement('dummy', 'map-canvas', array());
    $this->addElement('dummy', 'ses_location', array('content'));
    $this->addElement('Text', 'lng', array(
        'label' => 'Lng',
        'id' => 'lngSes',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
		}
    // init tag
    $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        )
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
    // Init descriptions
    $this->addElement('Textarea', 'description', array(
        'label' => 'Video Description',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            //new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id');
    if ($video_id)
      $video = Engine_Api::_()->getItem('sesvideo_video', $video_id);
    //Artist Work
    $artistArray = array();
    $artistsTable = Engine_Api::_()->getDbTable('artists', 'sesvideo');
    $select = $artistsTable->select()->order('order ASC');
    $artists = $artistsTable->fetchAll($select);
    foreach ($artists as $artist) {
      $artistArray[$artist->artist_id] = $artist->name;
    }
    if (!empty($artistArray)) {
      $artistsValues = isset($video) ? json_decode($video->artists) : array();;
      $this->addElement('MultiCheckbox', 'artists', array(
          'label' => 'Video Artist',
          'description' => 'Choose from the below video artist.',
          'multiOptions' => $artistArray,
          'value' => $artistsValues,
      ));
    }
    // prepare categories
    $categories = Engine_Api::_()->sesvideo()->getCategories();
    if (count($categories) != 0) {
				$setting = Engine_Api::_()->getApi('settings', 'core');
				$categorieEnable = $setting->getSetting('video.category.enable','1');
				if($categorieEnable == 1){
					$required = true;	
					$allowEmpty = false;
				}else{
					$required = false;	
					$allowEmpty = true;	
				}		
      $categories_prepared[''] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }
      // category field
      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'allowEmpty' => $allowEmpty,
          'required' => $required,
          'onchange' => "showSubCategory(this.value);showFields(this.value,1);",
      ));
      //Add Element: Sub Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => "2nd-level Category",
          'allowEmpty' => true,
          'required' => false,
          'multiOptions' => array('0' => 'Please select sub category'),
          'registerInArrayValidator' => false,
          'onchange' => "showSubSubCategory(this.value);"
      ));
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => "3rd-level Category",
          'allowEmpty' => true,
          'registerInArrayValidator' => false,
          'required' => false,
          'multiOptions' => array('0' => 'Please select 3rd category'),
          'onchange' => 'showCustom(this.value);'
      ));
      $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
      $customFields = new Sesvideo_Form_Custom_Fields(array(
          'item' => 'video',
          'decorators' => array(
              'FormElements'
      )));
      $customFields->removeElement('submit');
      if ($customFields->getElement($defaultProfileId)) {
        $customFields->getElement($defaultProfileId)
                ->clearValidators()
                ->setRequired(false)
                ->setAllowEmpty(true);
      }
      $this->addSubForms(array(
          'fields' => $customFields
      ));
    }
    // Init search
    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this video in search results",
        'value' => 1,
    ));
    // View
    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
    );
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if (!empty($viewOptions) && count($viewOptions) >= 1) {
      // Make a hidden field
      if (count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this video?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    // Comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    if (!empty($commentOptions) && count($commentOptions) >= 1) {
      // Make a hidden field
      if (count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this video?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
		$viewer = Engine_Api::_()->user()->getViewer();
    //check dependent module sesprofile install or not
    if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable(array('seslock')) && Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'video_locked')) {
      // Video enable password
      $this->addElement('Select', 'is_locked', array(
          'label' => 'Enable Video Lock',
          'multiOptions' => array(
              0 => 'No',
              1 => 'Yes',
          ),
          'onclick' => 'enablePasswordFiled(this.value);',
          'value' => 0
      ));
      // Video lock password
      $this->addElement('Password', 'password', array(
          'label' => 'Set Lock Password',
          'value' => '',
      ));
    }
    // Video rotation
    $this->addElement('Select', 'rotation', array(
        'label' => 'Video Rotation',
        'multiOptions' => array(
            0 => '',
            90 => '90°',
            180 => '180°',
            270 => '270°'
        ),
    ));
    $this->type->addMultiOptions($video_options);
		//$this->addElement('FancyUpload', 'file');
    $uploadoption = $settings->getSetting('video.uploadphoto', '0');
    if ($uploadoption == 1) {
      if (isset($video) && $video->photo_id) {
        $img_path = Engine_Api::_()->storage()->get($video->photo_id, '')->getPhotoUrl();
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'cover_photo_preview sesbd', array(
              'src' => $path,
              'class' => 'sesvideo_channel_thumb_preview sesbd',
          ));
          $this->addElement('File', 'photo_id', array(
              'label' => 'Video Photo',
          ));
        }
        $this->photo_id->addValidator('Extension', false, 'jpg,png,gif,jpeg');
      } else {
        $this->addElement('File', 'photo_id', array(
            'label' => 'Video Photo',
        ));
        $this->photo_id->addValidator('Extension', false, 'jpg,png,gif,jpeg');
      }
    }
    // Init url
    $this->addElement('Text', 'url', array(
        'label' => 'Video Link (URL)',
        'description' => 'Paste the web address of the video here.',
        'maxlength' => '150'
    ));
    $this->url->getDecorator("Description")->setOption("placement", "append");
		$this->addElement('Textarea', 'embedUrl', array(
        'label' => 'Video Embed (URL)',
        'description' => 'Paste the Embed Url of the video here.',
    ));
    $this->addElement('Hidden', 'code', array(
        'order' => 1
    ));
    $this->addElement('Hidden', 'id', array(
        'order' => 2
    ));
    $this->addElement('Hidden', 'ignore', array(
        'order' => 3
    ));
    // Init file
    
    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
    $fancyUpload->clearDecorators()
            ->addDecorator('FormFancyUpload')
            ->addDecorator('viewScript', array(
                'viewScript' => '_FancyUpload.tpl',
                'placement' => '',
    ));
    Engine_Form::addDefaultDecorators($fancyUpload);
    $this->addElement($fancyUpload);
    // Init submit
    $this->addElement('Button', 'upload', array(
        'label' => 'Save Video',
        'type' => 'submit',
    ));
    //$this->addElements(Array($album, $name, $description, $search, $file, $submit));
  }
  public function clearAlbum() {
    $this->getElement('album')->setValue(0);
  }
  public function saveValues() {
    $set_cover = False;
    $values = $this->getValues();
    $params = Array();
    if ((empty($values['owner_type'])) || (empty($values['owner_id']))) {
      $params['owner_id'] = Engine_Api::_()->user()->getViewer()->user_id;
      $params['owner_type'] = 'user';
    } else {
      $params['owner_id'] = $values['owner_id'];
      $params['owner_type'] = $values['owner_type'];
      throw new Zend_Exception("Non-user album owners not yet implemented");
    }
    if (($values['album'] == 0)) {
      $params['name'] = $values['name'];
      if (empty($params['name'])) {
        $params['name'] = "Untitled Album";
      }
      $params['description'] = $values['description'];
      $params['search'] = $values['search'];
      $album = Engine_Api::_()->getDbtable('albums', 'album')->createRow();
      $set_cover = True;
      $album->setFromArray($params);
      $album->save();
      // CREATE AUTH STUFF HERE
      /*    $context = $this->api()->authorization()->context;
        foreach( array('everyone', 'registered', 'member') as $role )
        {
        $context->setAllowed($this, $role, 'view', true);
        }
        $context->setAllowed($this, 'member', 'comment', true);
       */
    } else {
      if (is_null($album)) {
        $album = Engine_Api::_()->getItem('album', $values['album']);
      }
    }
    // Add action and attachments
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $album, 'album_photo_new', null, array('count' => count($values['file'])));
    // Do other stuff
    $count = 0;
    foreach ($values['file'] as $photo_id) {
      $photo = Engine_Api::_()->getItem("album_photo", $photo_id);
      if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
        continue;
      if ($set_cover) {
        $album->photo_id = $photo_id;
        $album->save();
        $set_cover = false;
      }
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