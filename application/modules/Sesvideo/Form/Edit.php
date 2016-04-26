<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Edit.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Edit extends Engine_Form {

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
    $this->setTitle('Edit Video')
            ->setAttrib('name', 'video_edit');
    $user = Engine_Api::_()->user()->getViewer();

    $this->addElement('Text', 'title', array(
        'label' => 'Video Title',
        'required' => true,
        'notEmpty' => true,
        'validators' => array(
            'NotEmpty',
        ),
        'filters' => array(
            //new Engine_Filter_HtmlSpecialChars(),
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '100')),
            new Engine_Filter_HtmlSpecialChars(),
        )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an video title");
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
        'description' => 'Separate tags with commas.'
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Textarea', 'description', array(
        'label' => 'Video Description',
        'rows' => 2,
        'maxlength' => '512',
        'filters' => array(
            'StripTags',
            new Engine_Filter_HtmlSpecialChars(),
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        )
    ));

    //Artist Work
    $artistArray = array();
    $artistsTable = Engine_Api::_()->getDbTable('artists', 'sesvideo');
    $select = $artistsTable->select()->order('order ASC');
    $artists = $artistsTable->fetchAll($select);

    foreach ($artists as $artist) {
      $artistArray[$artist->artist_id] = $artist->name;
    }

    if (!empty($artistArray)) {
      $artistsValues = json_decode($albumsong->artists);
      $this->addElement('MultiCheckbox', 'artists', array(
          'label' => 'Video Artist',
          'description' => 'Choose from the below video artist.',
          'multiOptions' => $artistArray,
          'value' => $artistsValues,
      ));
    }

    // prepare categories
    $categories = Engine_Api::_()->sesvideo()->getCategories();
    $categories_prepared[0] = "";
    foreach ($categories as $category) {
      $categories_prepared[$category->category_id] = $category->category_name;
    }

    // category field
    $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories_prepared,
        'onchange' => 'showSubCategory(this.value);showFields(this.value,1);'
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
    $video = Engine_Api::_()->core()->getSubject();
    // General form w/o profile type
    $aliasedFields = $video->fields()->getFieldsObjectsByAlias();
    $this->view->topLevelId = $topLevelId = 0;
    $this->view->topLevelValue = $topLevelValue = null;

    if (isset($aliasedFields['profile_type'])) {
      $aliasedFieldValue = $aliasedFields['profile_type']->getValue($video);
      $topLevelId = $aliasedFields['profile_type']->field_id;
      $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
      if (!$topLevelId || !$topLevelValue) {
        $topLevelId = null;
        $topLevelValue = null;
      }
      $this->view->topLevelId = $topLevelId;
      $this->view->topLevelValue = $topLevelValue;
    }
    // Get category map form data
    $defaultProfileId = "0_0_" . $this->getDefaultProfileId();
    $customFields = new Sesvideo_Form_Custom_Fields(array(
        'item' => Engine_Api::_()->core()->getSubject(),
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
    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this video in search results",
    ));


    // Privacy
    $availableLabels = array(
        'everyone' => 'Everyone',
        'registered' => 'All Registered Members',
        'owner_network' => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member' => 'Friends Only',
        'owner' => 'Just Me'
    );


    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if (empty($viewOptions)) {
      $viewOptions = $availableLabels;
    }

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
    if (empty($commentOptions)) {
      $commentOptions = $availableLabels;
    }

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
    if (Engine_Api::_()->getApi('core', 'sesbasic')->isModuleEnable('seslock') && Engine_Api::_()->authorization()->getPermission($viewer, 'video', 'video_locked')) {
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
      $this->addElement('password', 'password', array(
          'label' => 'Set Lock Password',
          'value' => '',
      ));
    }
    $uploadoption = $settings->getSetting('video.uploadphoto', '0');
    if (isset($video) && $uploadoption == 1) {
      if (isset($video) && $video->photo_id) {
        $img_path = Engine_Api::_()->storage()->get($video->photo_id, '');
			if($img_path){
       if(strpos($img_path,'http') === FALSE)
				$path = 'http://' . $_SERVER['HTTP_HOST'] . $img_path->getPhotoUrl();
			 else
				$path =$img_path->getPhotoUrl();
        if (isset($path) && !empty($path)) {
          $this->addElement('Image', 'cover_photo_preview sesbd', array(
              'src' => $path,
              'class' => 'sesvideo_channel_thumb_preview sesbd',
							'onClick'=>'return false;',
          ));
          $this->addElement('File', 'photo_id', array(
              'label' => 'Video Photo',
          ));
        }
			}else{
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
    // Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Video',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $video->getHref(),
        'onclick' => '',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}
