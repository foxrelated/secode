<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */

class Goal_Form_Createtemp extends Engine_Form
{
  

  public function init()
  {
      
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Create New Goal Using Template')
      ->setAttrib('id', 'goal_create_form')
      ->setMethod("POST")
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
     
   
    //custom code start 1/4/15
    
     // Category
    $this->addElement('Select', 'template_id', array(
      'label' => 'Template',
       'required' => true,
       'allowEmpty' => false,
       'validators' => array(
        array('NotEmpty', true),    
      ),
    ));
    $this->template_id->getValidator('NotEmpty')->setMessage('Please select valid template.', 'isEmpty');
    
    // Category
    $this->addElement('MultiCheckbox', 'task', array(
      'label' => 'Tasks',
        'value' => True,
        'multiOptions' => array(
        '0' => ' '
        )
    ));
//    $this->getElement('task')->clearValidators();
    
   
    
    // Title
//    $this->addElement('Text', 'title', array(
//      'label' => 'Title',
//      'allowEmpty' => false,
//      'required' => true,
//      'validators' => array(
//        array('NotEmpty', true),
//        array('StringLength', false, array(1, 64)),
//      ),
//      'filters' => array(
//        'StripTags',
//        new Engine_Filter_Censor(),
//      ),
//    ));
//
//    $title = $this->getElement('title');

    // Description
//    $this->addElement('Textarea', 'description', array(
//      'label' => 'Description',
//      'maxlength' => '10000',
//      'filters' => array(
//        'StripTags',
//        new Engine_Filter_Censor(),
//        new Engine_Filter_EnableLinks(),
//        new Engine_Filter_StringLength(array('max' => 10000)),
//      ),
//    ));
    //description
      $this->addElement('TinyMce', 'description', array(
      'label' => 'Description',
      'required' => true,
          
      'editorOptions' => array(
        'bbcode' => true,
        'html' => true,
      ),
      'allowEmpty' => false,        
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $this->addElement($end);

    //start time must be less than end time
     $specialValidator = new Engine_Validate_Callback(array($this, 'checkDate'));
     $specialValidator->setMessage('End Time must be greater than Start Time', 'invalid');
     $this->endtime->addValidator($specialValidator);
    //end date not in past
     $notpastValidator = new Engine_Validate_Callback(array($this, 'notPast'));
     $notpastValidator->setMessage('End Time must be greater than today', 'invalid');
     $this->endtime->addValidator($notpastValidator);    
    
    
    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));
    
    // Search
//    $this->addElement('Checkbox', 'search', array(
//      'label' => 'People can search for this goal',
//      'value' => True
//    ));

    // Privacy
   $availableLabels = array(
     
      'owner_member'          => 'Friends Only',
      'owner'                 => 'Just Me'
    );


    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('goal', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this goal?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('goal', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post on this goal?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Goal',
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

 public function checkDate($endtime)
  {
    $starttime = strtotime($this->getElement('starttime')->getValue());
    $endtime = strtotime($endtime);
    return ($starttime < $endtime );
  }  
  
 public function notPast($endtime)
  {
    $today = strtotime('today');
    $endtime = strtotime($endtime);
    return ($endtime > $today );   
  }   
}