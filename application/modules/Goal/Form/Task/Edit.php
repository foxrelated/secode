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

class Goal_Form_Task_Edit extends Engine_Form
{
    protected $_goalid; 
  public function setGoalid($goalid) {
    $this->_goalid = (int) $goalid;
  } 
  public function init()
  {
    $this
      ->setTitle('Edit Task')
      ->setAttrib('id', 'goal_task_edit')
      ->setAction(
        Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array('action' => 'post', 'controller' => 'task','action'=>'edit'), 'task_specific', true)
      );

    $viewer = Engine_Api::_()->user()->getViewer();
    // Title
    $this->addElement('Text', 'title', array(
      'label' => 'Task Title',
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
    
    // Title
    $this->addElement('hidden', 'task_id', array(
      'label' => 'Task Title',
      'allowEmpty' => false,
      'required' => true,
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('duedate');
    $start->setLabel("Due Date");
    $start->setAllowEmpty(false);
    $this->addElement($start);  
    
    //due date must be in between goal's start date and end date
     $inGoalDateValidator = new Engine_Validate_Callback(array($this,'ingoalDate'));
     $inGoalDateValidator->setMessage('Due date must be in between goal\'s start date and end date', 'invalid');
     $this->duedate->addValidator($inGoalDateValidator);    
    
     //custom code
     //notes
      $this->addElement('TinyMce', 'notes', array(
      'label' => 'Notes',
      'required' => true,
          
      'editorOptions' => array(
        'bbcode' => true,
        'html' => true,
      ),
      'allowEmpty' => false,        
    ));
    
    
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
    
     
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Update Task',
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
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));  
    
  }
  
  public function ingoalDate($duedate)
  { 
   $goal = Engine_Api::_()->getItem('goal', $this->_goalid);
   $gstartTime = strtotime($goal->starttime);
   $gendTime = strtotime($goal->endtime) ;
   
   $tduedate = strtotime($duedate);
   
   if($tduedate > $gendTime){
       return false;
   }elseif($tduedate <= $gstartTime){
        return false;
   }else {
       return true;
   }
  }    
}