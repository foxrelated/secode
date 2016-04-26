<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Grouppoll_Form_Create extends Engine_Form
{
  public function init()
  {  
		$auth = Engine_Api::_()->authorization()->context;
		$user = Engine_Api::_()->user()->getViewer();
		$this->setTitle('Create New Poll')
				->setDescription("Create your poll below, then click 'Create Poll' to start your poll.")
				->setAttrib('id',      'grouppoll_create_form')
				->setAttrib('name',    'grouppoll_create')
				->setAttrib('enctype', 'multipart/form-data')
				->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('text', 'title', array(
      'label' => 'Poll Title',
      'required' => true,
      'maxlength' => 63,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '63'))
      ),
    ));

    $this->addElement('textarea', 'description', array(
      'label' => 'Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '400'))
      ),
    ));

    $this->addElement('textarea', 'options', array(
      'label' => 'Possible Answers',
      'style' => 'display:none;',
    ));

    $this->addElement('Radio', 'end_settings', array(
      'id'=>'end_settings',
      'label' => 'Voting End',
      'description' => 'When should voting end for this poll?',
      'onclick' => "updateTextFields(this)",
      'multiOptions' => array(
      "0" =>  "No end date.",
      "1" =>  "End this voting on a specific date. (Please select date by clicking on the calendar icon below.)",
      ),
      'value' => 0
    ));

    $this->addElement('CalendarDateTime', 'end_time', array(
      'value' => date('M d Y'),
      'ignoreValid' => true,
    ));
 
    $availableLabels = array(
      '1' => 'Registered Members',
      '2' => 'All Group Members',
      '3' => 'Officers and Owner Only',
    );

    $voteOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('grouppoll_poll', $user, 'gp_auth_vote');
    $voteOptions = array_intersect_key($availableLabels, array_flip($voteOptions));
    
    if ( !empty($voteOptions) && count($voteOptions) >= 1 ) {
      $this->addElement('Select', 'gp_auth_vote', array(
        'label' => 'Voting Privacy',
        'description'=> 'Who may vote on this poll?',
        'multiOptions' => $voteOptions,
        'value' => key($voteOptions),  
      ));
    $this->gp_auth_vote->getDecorator('Description')->setOption('placement', 'append');
    }

    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this poll in search results",
      'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Create Poll',
      'type' => 'submit',
      'decorators' => array(array('ViewScript', array(
      	'viewScript' => '_formButtonCancel.tpl',
        'class'      => 'form element')))
    ));
  }
}
?>