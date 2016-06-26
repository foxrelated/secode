<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Create.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Form_Admin_Create extends Engine_Form
{
  protected $_parent_type;
  protected $_isComposer;
  protected $_parent_id;
  public function __construct($options = null, $_isComposer = false)
  {
    $this->_isComposer = $_isComposer;
    parent::__construct($options);
  }

  public function setParent_type($value)
  {
    $this->_parent_type = $value;
  }

  public function setParent_id($value)
  {
    $this->_parent_id = $value;
  }


  public function init()
  {
    $settingsTbl = Engine_Api::_()->getDbtable('settings', 'core');
    $bgPos = array('left', 'center', 'right');
    $bgPosSetting = $settingsTbl->getSetting('heevent.cover.position', 1);
    $bgRepeat = ((boolean) $settingsTbl->getSetting('heevent.cover.repeat', 1)) ? 'repeat' : 'no-repeat';
    $user = Engine_Api::_()->user()->getViewer();
    $classes = array(
      'heevent-create-form',
      'heevent-form',
      'heevent-admin-form',
      'heevent-block',
      'heevent-widget',
      'global_form'
    );
      $this->loadDefaultDecorators();
    if(!$this->_isComposer)
      $this
        ->setAttrib('id', 'event_create_form')
        ->setAttrib('class', implode(' ', $classes))
        ->setMethod("POST")
        ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $module_path = Engine_Api::_()->getModuleBootstrap('heevent')->getModulePath();
    $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');
    $view = new Zend_View();
$cover = <<<COVER
<div id="heevent_cover">
  <div class="cover-wrapper">
    <button id="heevent-create-delete-cover" class="heevent-hover-fadein heevent-abs-btn delete" onclick="return false;" style="display: none"><i class="hei hei-times"></i></button>
    <button id="heevent-create-upload-cover" class="heevent-abs-btn heevent-abs-btn-right" onclick="return false;"><i class="hei hei-upload"></i></button>
    <button name="prev" style="display: none;" onclick="return false;" class="heevent-create-cover-nav heevent-abs-btn-middle heevent-abs-btn" id="heevent-create-prev-cover"><i class="hei hei-angle-left"></i></button>
    <button name="next" style="display: none;" onclick="return false;" class="heevent-create-cover-nav heevent-abs-btn-middle heevent-abs-btn heevent-abs-btn-right" id="heevent-create-next-cover"><i class="hei hei-angle-right"></i></button>
    <img default-bg="url({$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/event-cover-nophoto.gif)" id="heevent-create-cover" class="fake-img" src="{$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/fake-29x8.gif" style="background-position:{$bgPos[$bgPosSetting]};background-repeat:{$bgRepeat};background-image:url({$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/event-cover-nophoto.gif)"/>
  </div>
</div>
COVER;
    $this->addElement('Hidden', 'photo_id', array());

    $this->addElement('Dummy', 'cover-photo', array(
      'content' => $cover
    ));
    $this->addElement('Radio', 'cover_position', array(
      'multiOptions' => array(
        0 => 'HEEVENT_Left',
        1 => 'HEEVENT_Center',
        2 => 'HEEVENT_Right',
      ),
      'value' => $bgPosSetting
    ));
    // Repeat Background
    $this->addElement('Checkbox', 'cover_repeat', array(
      'label' => 'HEEVENT_Repeat',
      'value' => (boolean) $settingsTbl->getSetting('heevent.cover.repeat', 1)
    ));

    $this->addDisplayGroup(array('cover_position', 'cover_repeat'), 'cover_params', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
      'label' => 'Cover Settings'
    ));


    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'event')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');

    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) > 1) {
      // Category
      $this->addElement('Select', 'category_id', array(
        'label' => 'Event Category',
        'multiOptions' => $categoryOptions,
      ));
    }


//    $allPhotos = <<<ALLCATPHOTOS
// &mdash; <a href="javascript://" onclick="">{$view->translate('HEEVENT_View All Themes')}</a>
//ALLCATPHOTOS;
//
//    $this->addElement('Dummy', 'all_photos', array(
//      'content' => $allPhotos
//    ));

    // Title
    $this->addElement('Text', 'title', array(
      'label' => 'Event Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

//    $title = $this->getElement('title');

    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Event Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));
    //tickets params
      $this->addElement('Text', 'ticket_count', array(
      'label' => 'Ticket count',
      'allowEmpty' => true,
      'validators' => array(
          array('NotEmpty', false),
          array('StringLength', false, array(1, 20)),
      ),
      'filters' => array(
          'StripTags',
          new Engine_Filter_Censor(),
      ),
  ));
  $this->addElement('Text', 'ticket_price', array(
      'label' => 'Ticket price',
      'allowEmpty' => true,
      'validators' => array(
          array('NotEmpty', false),
          array('StringLength', false, array(1, 20)),
      ),
      'filters' => array(
          'StripTags',
          new Engine_Filter_Censor(),
      ),
  ));
    // Start time
    $this->addElement('Datepicker', 'starttime', array(
      'allowEmpty' => false,
      'required' => true,
    ));
    $this->starttime->setLabel('Start Time');
    $addEndTime = <<<ADDENDTIME
<a href="javascript://" onclick="$('addendtime-wrapper').hide(); $('endtime-wrapper').setStyle('display', 'block');">{$view->translate('HEEVENT_Add End Time')}</a>
ADDENDTIME;

    $this->addElement('Dummy', 'add-endtime', array(
      'content' => $addEndTime
    ));
    $this->addElement('Datepicker', 'endtime', array(
      'allowEmpty' => true,
      'required' => false,
    ));
    $this->endtime->setLabel('End Time');

    // Location
    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'onchange' => 'drawMap();',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->addElement('Dummy', 'use_my_location', array(
      'content' => '<a onclick="getLocation();" href="javascript://">' . $view->translate('HEEVENT_Try My Location') . '</a>',
      'label' => null,
    ));

    $this->addDisplayGroup(array('location', 'use_my_location'), 'locations', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
    $this->addElement('Text', 'map_zoom', array(
      'label' => 'HEEVENT_Zoom of map',
      'value' => $settingsTbl->getSetting('heevent.map.zoom', 10),
      'onchange' => 'drawMap();'
    ));

    if(!$this->_isComposer){
      $map = <<<MAP
  <div id="heevent-create-map">
  <button class="heevent-hover-fadein heevent-abs-btn" onclick="zoomIn(); return false;"><i class="hei hei-plus"></i></button>
  <button class="heevent-hover-fadein heevent-abs-btn" onclick="zoomOut(); return false;" style="bottom:0"><i class="hei hei-minus"></i></button>
  <img id="heevent-create-map-img" src="{$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/fake-4x3.gif" /></div>
MAP;

      $this->addElement('Dummy', 'map', array(
        'content' => $map
      ));
    }

    // Host
    if ($this->_parent_type == 'user')
    {
      $this->addElement('Text', 'host', array(
        'label' => 'Host',
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
      ));
    }

    // Search
    $this->addElement('Checkbox', 'search', array(
      'label' => $this->_isComposer ? 'HEEVENT_Searchable' : 'People can search for this event',
      'value' => True
    ));

    // Approval
    $this->addElement('Checkbox', 'approval', array(
      'label' => $this->_isComposer ? 'HEEVENT_By Invitation Only' : 'People must be invited to RSVP for this event',
    ));

    // Invite
    $this->addElement('Checkbox', 'auth_invite', array(
      'label' => $this->_isComposer ? 'HEEVENT_Guests Can Invite' : 'Invited guests can invite other people as well',
      'value' => True
    ));
    if($this->_isComposer){
      $this->addElement('Text', 'edit_options', array(
        'value' => $view->translate('HEEVENT_Event Options'),
        'disabled' => 'disabled',
        'style' => 'width:auto;border-radius:0'
      ));

      $this->addDisplayGroup(array('edit_options', 'search', 'approval', 'auth_invite'), 'options', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
          ),
        'order' => 4,
        'style' => 'display: inline-block;'
        ));
    }
    // Privacy
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_view');
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_comment');
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_photo');
    
    if( $this->_parent_type == 'user' ) {
      $availableLabels = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member'        => 'Friends Only',
        'member'              => 'Event Guests Only',
        'owner'               => 'Just Me'
      );
      $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
      $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
      $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

    } else if( $this->_parent_type == 'group' ) {

      $availableLabels = array(
        'everyone'      => 'Everyone',
        'registered'    => 'All Registered Members',
        'parent_member' => 'Group Members',
        'member'        => 'Event Guests Only',
        'owner'         => 'Just Me',
      );
      $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
      $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
      $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
    }

    // View
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this event?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Comment
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this event?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Photo
    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'auth_photo', array('value' => key($photoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_photo', array(
            'label' => 'Photo Uploads',
            'description' => 'Who may upload photos to this event?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions)
        ));
        $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Buttons
    if(!$this->_isComposer)
      $this->addElement('Dummy', 'edit-privacy', array(
        'label' => 'HEEVENT_Edit Privacy',
        'content' => '<a href="javascript://"  onclick="$(this).hide(); $$(\'#auth_invite-wrapper, #auth_view-wrapper, #auth_comment-wrapper, #auth_photo-wrapper\').show()">'. $view->translate('HEEVENT_Edit Privacy') .' <i class="hei hei-caret-down"></i></a>',
      ));
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this->_isComposer ? 'Create' : 'Save Changes',
      'type' => $this->_isComposer ? 'button' : 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    // Photo
    if(!$this->_isComposer){
      $this->addElement('File', 'photo', array(
        'label' => 'Main Photo',
        'accept' => 'image/*'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    }

    if($this->_isComposer)
      $dgBtns = array('submit', 'cancel');
    else
      $dgBtns = array('edit-privacy', 'submit', 'cancel');

    $this->addDisplayGroup($dgBtns, 'buttons', array(
        'decorators' => array(
          'FormElements',
          'DivDivDivWrapper',
        ),
      ));

  }

  public function populate($values)
  {
    if($values instanceof Heevent_Model_Event){
      $pos = array(
        'left' => 0,
        'center' => 1,
        'right' => 2,
      );
      $event = $values;
      $values = $event->toArray();
      $params = $event->getParams();
      if($params){

        $params = $params->toArray();
        Zend_Json::decode($params['location_params']);
        $values = array_merge($values, Zend_Json::decode($params['location_params']));

        $posJson = Zend_Json::decode($params['cover_params']);
        $posVals = array(
          'cover_position' => $pos[$posJson['position']],
          'cover_repeat' => $posJson['repeat'] == 'repeat' ? 1 : 0,

        );
        $values = array_merge($values, $posVals);
      }
      $this->getElement('photo_id')->setAttrib('src', $event->getPhotoUrl());
      $this->getElement('photo_id')->setAttrib('file-id', $event->photo_id);
    }
    parent::populate($values);
  }

  public function getValues($suppressArrayNotation = false)
  {
    $values = parent::getValues($suppressArrayNotation);
    unset($values['cover-photo']);
    unset($values['all_photos']);
    unset($values['add-endtime']);
    unset($values['use_my_location']);
    unset($values['map']);
    unset($values['edit-privacy']);

    $pos = array('left', 'center', 'right');
    $params = array();
    $params['cover_params'] = Zend_Json::encode(array(
      'position' => $pos[$values['cover_position']],
      'repeat' => $values['cover_repeat'] ? 'repeat' : 'no-repeat'
    ));
    unset($values['cover_position']);
    unset($values['cover_repeat']);

    $params['location_params'] = Zend_Json::encode(array(
      'map_zoom' => $values['map_zoom'],
    ));
    unset($values['map_zoom']);
    $values['heevent_params'] = $params;
    if(isset($values['endtime']) && !$values['endtime']){
      $values['endtime'] = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($values['starttime'])));
    }
    return $values;
  }
}