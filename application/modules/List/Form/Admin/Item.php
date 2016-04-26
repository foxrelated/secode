<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Item.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Admin_Item extends Engine_Form {

  protected $_field;

  public function init() {

    $this->setMethod('post');
    $this->setTitle('Add a Listing of the Day')
        ->setDescription('Select a start date and end date below and the corresponding listing from the auto-suggest Listing field. The selected listing will be displayed as "Listing of the Day" for this duration and if more than one listings are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');

    $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
    $starttime->setLabel("Start Date");
    $starttime->setAllowEmpty(false);
    $starttime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($starttime);

    $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
    $endtime->setLabel("End Date");
    $endtime->setAllowEmpty(false);
    $endtime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($endtime);

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Listing')
        ->addValidator('NotEmpty')
        ->setRequired(true)
        ->setAttrib('class', 'text')
        ->setAttrib('style', 'width:210px;');

    $this->addElement('Hidden', 'listing_id', array());

    $this->addElements(array($label));

    $this->addElement('Button', 'submit', array(
            'label' => 'Add Listing',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'javascript:parent.Smoothbox.close()',
            'decorators' => array(
                    'ViewHelper',
            ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}