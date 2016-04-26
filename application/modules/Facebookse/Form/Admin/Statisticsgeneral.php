<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Statisticsgeneral.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Statisticsgeneral extends Engine_Form
{
  public function init()
  {
    $duration_array = array('6' => '6 hrs', '12' => '12 hrs' , '24' => '1 day' , '48' => '2 days' , '168' => '1 week', '360' => '15 days' , '720' => '1 month' , '1440' => '2 months' , '4320' => '6 months');
    $this->addElement('Select', 'duration_id', array(
      'label' => 'Select Duration:',
      'multiOptions' => $duration_array,
      'onchange' => 'javascript:fetchdurationSettings(this.value);',
      'ignore' => true,
      'value' => '24'
      
    ));
  }

}