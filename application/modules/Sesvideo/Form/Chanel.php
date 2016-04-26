<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Chanel.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Form_Chanel extends Engine_Form {
  public function init() {
		$is_chanel = false;
    $setting = Engine_Api::_()->getApi('settings', 'core');
    $chanel_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('chanel_id');
		$titleText = 'Add New Video Chanel';
    if ($chanel_id) {
      $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
      $is_chanel = true;
			$titleText = 'Edit Video Chanel';
    }
		
    // Init form
    $this
            ->setTitle($titleText)
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'video_chanel_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAttrib('onsubmit', 'return checkValidation();')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;
		$viewChanel = $deleteChanel = '';
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $user = Engine_Api::_()->user()->getViewer();
		if(isset($chanel)){
		$canDelete = Engine_Api::_()->core()->getSubject('sesvideo_chanel')->authorization()->isAllowed($user, 'delete');
		 if($canDelete){
			$deleteChanel = '<a href="' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'chanel', 'action' => 'delete', 'chanel_id' => $chanel_id), 'sesvideo_chanel', true) . '" class="sesbasic_button" onclick="opensmoothboxurl(this.href);return false;" id="deleteChanel"><i class="fa fa-trash sesbasic_text_light"></i>'.Zend_Registry::get('Zend_Translate')->_('Delete Channel').'</a>';
		 }
			$viewChanel 	= ' <a href="'.$chanel->getHref().'" class="sesbasic_button"><i class="fa fa-eye sesbasic_text_light"></i>'.Zend_Registry::get('Zend_Translate')->_('View Channel').'</a></li>';
		}
    $this->addElement('Dummy', 'tabs_form_chanel', array(
        'content' => '<div class="sesvideo_create_channel_tabs sesbasic_clearfix sesbm"><ul id="chanel_create_form_tabs" class="sesbasic_clearfix"><li data-url = "first_step" class="active first_step sesbm"><a id="save-first-click" href="javascript:;">'.Zend_Registry::get('Zend_Translate')->_('Basic').'</a></li><li data-url="first_second" class="first_second sesbm"><a id="save_second-click" href="javascript:;">'.Zend_Registry::get('Zend_Translate')->_('Appearance').'</a></li><li class="first_third sesbm" data-url = "first_third"><a id="save_third-click" href="javascript:;">'.Zend_Registry::get('Zend_Translate')->_('Videos').'</a></li><li data-url="last_elem" class="last sesbm"><a href="javascript:;" id="last-click">'.Zend_Registry::get('Zend_Translate')->_('Membership').'</a></li><li class="sesvideo_create_channel_tabs_btns">'.$deleteChanel.$viewChanel.'</ul></div>'
    ));
    // Init name
    $this->addElement('Text', 'title', array(
        'label' => 'Chanel Title',
        'maxlength' => '100',
				'autocomplete' => 'off',
        'allowEmpty' => false,
        'required' => true,
        'filters' => array(
            //new Engine_Filter_HtmlSpecialChars(),
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100')),
        )
    ));
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
        'label' => 'Channel Description',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            //new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_EnableLinks(),
        ),
    ));
    // prepare categories
    $categories = Engine_Api::_()->sesvideo()->getCategories();
    if (count($categories) != 0) {
			$setting = Engine_Api::_()->getApi('settings', 'core');
				$categorieEnable = $setting->getSetting('videochanel.category.enable','1');
				if($categorieEnable == 1){
					$required = true;	
					$allowEmpty = false;
				}else{
					$required = false;	
					$allowEmpty = true;	
				}		
      $categories_prepared[''] = "Please select category";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }
      // category field
      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'allowEmpty' => $allowEmpty,
          'required' => $required,
          'onchange' => "showSubCategory(this.value);",
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
          'multiOptions' => array('0' => 'Please select 3rd category')
      ));
    }
    $httpConfig = (!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://";
    if ($chanel_id) {
      if ($chanel->custom_url && $chanel->custom_url != '')
        $custom_url = $chanel->custom_url;
      else
        $custom_url = $chanel->chanel_id;
    } else
      $custom_url = '';
    $custom_url = isset($_POST['custom_url']) ? $_POST['custom_url'] : $custom_url;
    $this->addElement('Dummy', 'shortURL', array(
        'content' => '<div>
												<input type="text" name="custom_url" id="custom_url" value="' . $custom_url . '"  maxlength="24" autocomplete="off">
												<span class="msg"></span>
                        <span id="url_prefix"><span class="sesbasic_text_light">' . $httpConfig . $_SERVER['HTTP_HOST'] . '/' . $setting->getSetting('video.video.manifest', 'videos') . '/' . $setting->getSetting('video.chanel.manifest', 'channel') . '/</span><b id="channelurl"></b></span>
                    </div>',
    ));
		// Init submit
    $this->addElement('Button', 'save_second', array(
        'label' => 'Next',
				'class' => 'next_elm',
        'type' => 'button',
    ));
		 $this->addDisplayGroup(array(
		 		'title',
		 		'tags',
		 		'description',
		 		'category_id',
		 		'subcat_id',
        'subsubcat_id',
        'shortURL',
				'save_second',
            ), 'first_step', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    // Init search
    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this video channel in search results",
        'value' => 1,
    ));
		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('video.enable.subscription',1)){
			$this->addElement('Checkbox', 'follow', array(
					'label' => "Someone follows this Channel",
					'value' => 1,
			));
		}
    // View
    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
    );
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesvideo_chanel', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if (!empty($viewOptions) && count($viewOptions) >= 1) {
      // Make a hidden field
      if (count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this video channel?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    // Element: auth_comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sesvideo_chanel', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));		
    if (!empty($commentOptions) && count($commentOptions) >= 1) {
      // Make a hidden field
      if (count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
        // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this chanel?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }
		
		 // Init submit
    $this->addElement('Button', 'upload', array(
        'label' => 'Save Video',
        'type' => 'submit',
    ));
		$this->addDisplayGroup(array(
		 		'search',
		 		'follow',
		 		'auth_view',
				'auth_comment',
				'upload',
            ), 'last_elem', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
		
    $this->addElement('Dummy', 'added_videos_create', array(
		 'label' => 'Manage Videos',
        'content' => '<div class="added_manage_videos sesbasic_custom_scroll sesbasic_clearfix">
													<ul id="added_manage_videos" class="sesbasic_clearfix"></ul>
										  </div>',
    ));
    $enableOptioninChanel = $setting->getSetting('video.enable.chaneloption',array('my_created','liked_videos','rated_videos','watch_later'));
    $arrayOptions = array();
    foreach ($enableOptioninChanel as $key => $valueoptions) {
      $arrayOptions[$valueoptions] = ucwords(str_replace('_', ' ', $valueoptions));
    }
    $this->addElement('Select', 'manage_videos', array(
       
        'description' => 'Add videos to channel?',
        'multiOptions' => $arrayOptions,
        'value' => count($arrayOptions) > 0 ? key($arrayOptions) : '',
        'onChange' => 'getVideos(this.value);'
    ));
    $this->addElement('Dummy', 'added_videos', array(
        'content' => '<div class="sesbasic_clearfix"><ul id="manage_videos_data" class="sesbasic_clearfix"></ul></div>',
    ));
    $this->addElement('Button', 'last', array(
        'label' => 'Next',
				'class' => 'next_elm',
        'type' => 'button',
    ));
		$this->addDisplayGroup(array(
		 		'added_videos_create',
		 		'manage_videos',
		 		'added_videos',
				'last',
        ), 'first_third', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
    $this->addElement('File', 'chanel_cover', array(
        'label' => 'Channel Cover',
        'onchange' => 'readImageUrl(this,"cover_photo_preview")',
				'description'=>'recommended size is 1000*300'
    ));
		$this->chanel_cover->getDecorator("Description")->setOption("placement", "append");
    $this->chanel_cover->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		$chanelCoverPreview = '';
    if ($chanel_id) {
      if (!$is_chanel)
        $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
      if ($chanel->cover_id) {
        $this->addElement('Checkbox', 'remove_chanel_cover', array(
            'label' => 'Yes, remove channel cover.'
        ));
				$chanelCoverPreview = 'remove_chanel_cover';
      }
    }
    if (isset($chanel) && $chanel->cover_id) {
       $img_path = Engine_Api::_()->storage()->get($chanel->cover_id, '')->getPhotoUrl();
		if(strpos($img_path,'http') === FALSE)
      $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
		 else
		 	$path = $img_path;
      if (isset($path) && !empty($path)) {
        $this->addElement('Image', 'cover_photo_preview', array(
            'src' => $path,
            'class' => 'sesvideo_channel_thumb_preview sesbd',
        ));
      }
    } else {
      $this->addElement('Image', 'cover_photo_preview', array(
          'src' => '',
          'class' => 'sesvideo_channel_thumb_preview',
      ));
    }

    $this->addElement('File', 'chanel_thumbnail', array(
        'label' => 'Channel Thumbnail',
				'description'=>'recommended size is 400*400',
        'onchange' => 'readImageUrl(this,"thumbnail_photo_preview")',
    ));
		$this->chanel_thumbnail->getDecorator("Description")->setOption("placement", "append");
    $this->chanel_thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    if (isset($chanel) && $chanel->thumbnail_id) {
      $img_path = Engine_Api::_()->storage()->get($chanel->thumbnail_id, '')->getPhotoUrl();
		if(strpos($img_path,'http') === FALSE)
      $path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path;
		 else
		 	$path = $img_path;
      if (isset($path) && !empty($path)) {
        $this->addElement('Image', 'thumbnail_photo_preview', array(
            'src' => $path,
            'class' => 'sesvideo_channel_thumb_preview sesbd',
        ));
      }
    } else {
      $this->addElement('Image', 'thumbnail_photo_preview', array(
          'src' => '',
          'class' => 'sesvideo_channel_thumb_preview sesbd',
      ));
    }
		// Init submit
    $this->addElement('Button', 'save_third', array(
        'label' => 'Next',
				'class' => 'next_elm',
        'type' => 'button',
    ));
		$chanelThumbnail = '';
    if ($chanel_id) {
      if (!$is_chanel)
        $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $chanel_id);
      if ($chanel->thumbnail_id) {
        $this->addElement('Checkbox', 'remove_chanel_thumbnail', array(
            'label' => 'Yes, remove channel thumbnail.'
        ));
				$chanelThumbnail = 'remove_chanel_thumbnail';
      }
    }
		$this->addDisplayGroup(array(
		 		'chanel_cover',
		 		'cover_photo_preview',
				$chanelCoverPreview,
		 		'chanel_thumbnail',
		 		'thumbnail_photo_preview',
				$chanelThumbnail,
				'save_third',
            ), 'first_second', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
		
    $this->addElement('Hidden', 'code', array(
        'order' => 1
    ));
    $this->addElement('Hidden', 'video_ids', array(
        'order' => 4
    ));
    $this->addElement('Hidden', 'delete_video_ids', array(
        'order' => 5
    ));
    $this->addElement('Hidden', 'id', array(
        'order' => 2
    ));
    $this->addElement('Hidden', 'ignore', array(
        'order' => 3
    ));
   
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
