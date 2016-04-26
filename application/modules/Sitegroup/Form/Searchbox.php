<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Searchbox.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitegroup_Form_Searchbox extends Engine_Form
{
  public function init()
  {
    $this->addElement('Text', 'title', array(
        'label' => '',
        'autocomplete' => 'off'));
    $this->addElement('Hidden', 'group_id', array());    
  }
}
?>