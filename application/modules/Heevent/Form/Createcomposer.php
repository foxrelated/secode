<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Createcomposer.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Form_Createcomposer extends Engine_Form
{
  protected $_parent_type;

  protected $_parent_id;
  
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
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('')
      ->setAttrib('id', 'event_create_form')
      ->setMethod("POST")
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    $module_path = Engine_Api::_()->getModuleBootstrap('heevent')->getModulePath();
    $this->addPrefixPath('Engine_Form_Element_', $module_path . '/Form/Element/', 'element');
    // Title






    $this->addElement('Datepicker', 'starttime', array(
      'allowEmpty' => false,
      'allowEmpty' => false,
      'placeholder' => 'Start time',
    ));


    $this->addElement('Datepicker', 'endtime', array(
      'allowEmpty' => false,
      'required' => false,
      'placeholder' => 'End time',
    ));

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
    // Location
    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'class' => 'heeventinput',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Event Category',
      'class' => 'heeventinput',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));
    
    // Search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'People can search for this event',
      'value' => True
    ));

    // Approval
    $this->addElement('Checkbox', 'approval', array(
      'label' => 'People must be invited to RSVP for this event',
    ));

    // Invite
    $this->addElement('Checkbox', 'auth_invite', array(
      'label' => 'Invited guests can invite other people as well',
      'value' => True
    ));

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
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
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
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}