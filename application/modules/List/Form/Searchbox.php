<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Searchbox.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Searchbox extends Engine_Form
{
  public function init()
  {
    $this->addElement('Text', 'title', array(
        'label' => '',
        'autocomplete' => 'off'));
    $this->addElement('Hidden', 'listing_id', array());   
  }
}