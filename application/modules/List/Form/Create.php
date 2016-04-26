<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Create.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Create extends Engine_Form {

  public $_error = array();

  protected $_defaultProfileId;

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function init() {

    //GET DECORATORS
    $this->loadDefaultDecorators();
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $expirySettings = Engine_Api::_()->list()->expirySettings();
    if ($expirySettings == 2) {
			$duration = $coreSettings->getSetting('list.expirydate.duration', array('1', 'week'));
			$interval_type = $duration[1];
			$interval_value = $duration[0];

			if($interval_type == 'week') {
        if($interval_value > 1)
				$validation = $interval_value.' weeks';
        else
        $validation = $interval_value.' Week';
			}
			elseif($interval_type == 'day') {
        if($interval_value > 1)
				$validation = $interval_value.' days';
        else 
        $validation = $interval_value.' day';
			}
			elseif($interval_type == 'month') {
        if($interval_value > 1)
				$validation = $interval_value.' months';
        else 
        $validation = $interval_value.' month';
			}
			else {
        if($interval_value > 1)
				$validation = $interval_value.' years';
        else 
        $validation = $interval_value.' year';
			}
      
      $description = Zend_Registry::get('Zend_Translate')->_('Compose your new listing below, then click "Post Listing" to publish the listing. <br /> Note: This listing will expire in %s after its approval.');
      $description = sprintf($description, $validation);
    }
    else {
      $description = 'Compose your new listing below, then click "Post Listing" to publish the listing.';
    }
    
    $this->getDecorator('Description')->setOption('escape', false);

    $this->setTitle('Post New Listing')
        ->setDescription($description)
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ->setAttrib('name', 'lists_create');

    $this->addElement('Text', 'title', array(
            'label' => 'Listing Title',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                    'StripTags',
                    new Engine_Filter_Censor(),
            //new Engine_Filter_StringLength(array('max' => '63')),
        )));

    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;

    $this->addElement('Text', 'tags', array(
            'label' => 'Tags (Keywords)',
            'autocomplete' => 'off',
            'description' => 'Separate tags with commas.',
            'filters' => array(
                    new Engine_Filter_Censor(),
            ),
    ));

    $this->tags->getDecorator("Description")->setOption("placement", "append");

    if($coreSettings->getSetting('list.description.allow', 1)) {
			if ($coreSettings->getSetting('list.requried.description', 1)) {
				$this->addElement('textarea', 'body', array(
								'label' => 'Description',
								'required' => true,
								'allowEmpty' => false,
								'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
								'filters' => array(
												'StripTags',
												new Engine_Filter_HtmlSpecialChars(),
												new Engine_Filter_EnableLinks(),
												new Engine_Filter_Censor(),
								),
				));
			} else {
				$this->addElement('textarea', 'body', array(
								'label' => 'Description',
								'attribs' => array('rows' => 24, 'cols' => 180, 'style' => 'width:300px; max-width:400px;height:120px;'),
								'filters' => array(
												'StripTags',
												new Engine_Filter_HtmlSpecialChars(),
												new Engine_Filter_EnableLinks(),
												new Engine_Filter_Censor(),
								),
				));
			}
    }

    $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'list_listing', 'photo');
    if ($allowed_upload) {
      $this->addElement('File', 'photo', array(
              'label' => 'Main Photo'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,jpeg,png,gif');
    }

    if ($coreSettings->getSetting('list.locationfield', 1)) {
      $this->addElement('Text', 'location', array(
          'label' => 'Location',
          'description' => 'Eg: Fairview Park, Berkeley, CA',
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              )));
      $this->location->getDecorator('Description')->setOption('placement', 'append');
    }

		$defaultProfileId = "0_0_".$this->getDefaultProfileId();

    $categories = Engine_Api::_()->getDbTable('categories', 'list')->getCategories(0, 0);
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

			if(!$this->_item) {
				$this->addElement('Select', 'category_id', array(
						'label' => 'Category',
						'allowEmpty' => false,
						'required' => true,
						'multiOptions' => $categories_prepared,
						'onchange' => " var profile_type = getProfileType($(this).value); 
														if(profile_type == 0) profile_type = '';
														$('$defaultProfileId').value = profile_type;
														changeFields($('$defaultProfileId'));
														subcategories(this.value, '', '');",
				));
			}
			else {
				$this->addElement('Select', 'category_id', array(
						'label' => 'Category',
						'allowEmpty' => false,
						'required' => true,
						'multiOptions' => $categories_prepared,
						'onchange' => " var profile_type = getProfileType($(this).value);
														if(profile_type == 0) profile_type = '';
														$('$defaultProfileId').value = profile_type;
														changeFields($('$defaultProfileId'));
														subcategories(this.value, '', ''); 
														prefieldForm();",
				));
			}
    
			$this->addElement('Select', 'subcategory_id', array(
					'RegisterInArrayValidator' => false,
					'allowEmpty' => true,
					'required' => false,
					'decorators' => array(array('ViewScript', array(
											'viewScript' => 'application/modules/List/views/scripts/_formSubcategory.tpl',
											'class' => 'form element')))
			));

			$this->addElement('Select', 'subsubcategory_id', array(
					'RegisterInArrayValidator' => false,
					'allowEmpty' => true,
					'required' => false,
					'decorators' => array(array('ViewScript', array(
																	'viewScript' => 'application/modules/List/views/scripts/_formSubcategory.tpl',
																	'class' => 'form element')))
			));
		}

    if( !$this->_item ) {
			$customFields = new List_Form_Custom_Standard(array(
										'item' => 'list_listing',
										'decorators' => array(
														'FormElements'
										)));

    } else {
      $customFields = new List_Form_Custom_Standard(array(
        'item' => $this->getItem(),
				'decorators' => array(
								'FormElements'
				)));
    }

		$customFields->removeElement('submit');
		if($customFields->getElement($defaultProfileId)){
			$customFields->getElement($defaultProfileId)           
							->clearValidators()
							->setRequired(false)
							->setAllowEmpty(true);
		}

    $this->addSubForms(array(
      'fields' => $customFields
    ));

    $this->addElement('Select', 'draft', array(
            'label' => 'Status',
            'multiOptions' => array("1" => "Published", "0" => "Saved As Draft"),
            'description' => 'If this entry is published, it cannot be switched back to draft mode.',
            'onchange'=>'checkDraft();'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Checkbox', 'search', array(
            'label' => "Show this listing in search results",
            'value' => 1,
    ));

    $this->addElement('Radio', 'end_date_enable', array(
        'label' => 'End Date',
        'multiOptions' => array("0" => "No end date.", "1" => "End listing on a specific date. (Please select date by clicking on the calendar icon below.)"),
        'description' => 'When should this listing end?',
        'value' => 0,
        'onclick' => "updateTextFields(this)",
    ));
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $end->setAllowEmpty(false);
    $date = (string) date('Y-m-d');
    $end->setValue($date . ' 00:00:00');
    $this->addElement($end);

    $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me',
    );

    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('list_listing', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    if (count($view_options) >= 1) {
      $this->addElement('Select', 'auth_view', array(
              'label' => 'Privacy',
              'description' => 'Who may see this listing?',
              'multiOptions' => $view_options,
              'value' => key($view_options),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }else{
       $this->addElement('Hidden', 'auth_view', array(
                'value' => "everyone"
            ));
    }

    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('list_listing', $user, 'auth_comment');
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));

    if (count($comment_options) >= 1) {
      $this->addElement('Select', 'auth_comment', array(
              'label' => 'Comment Privacy',
              'description' => 'Who may post comments on this listing?',
              'multiOptions' => $comment_options,
              'value' => key($comment_options),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }
		else {
			$this->addElement('Hidden', 'auth_comment', array('value' => "everyone"));
    }

    $availableLabels = array(
            'registered' => 'All Registered Members',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me',
    );
    $photo_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('list_listing', $user, 'auth_photo');
    $photo_options = array_intersect_key($availableLabels, array_flip($photo_options));

    if (count($photo_options) >= 1) {
      $this->addElement('Select', 'auth_photo', array(
              'label' => 'Photo Privacy',
              'description' => 'Who may post photos on this listing?',
              'multiOptions' => $photo_options,
              'value' => key($photo_options),
      ));
      $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
    }

    $videoEnable = Engine_Api::_()->list()->enableVideoPlugin();
    if ($videoEnable) {

      $video_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('list_listing', $user, 'auth_video');
      $video_options = array_intersect_key($availableLabels, array_flip($video_options));

      if (count($video_options) >= 1) {
        $this->addElement('Select', 'auth_video', array(
                'label' => 'Video Privacy',
                'description' => 'Who may post videos on this listing?',
                'multiOptions' => $video_options,
                'value' => key($video_options),
        ));
        $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    $this->addElement('Button', 'execute', array(
            'label' => 'Post Listing',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'list_general', true),
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

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