<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editanno.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitegroupmember_Form_Announcement_Edit extends Sitegroupmember_Form_Announcement_Create	{

  public function init() {
  
    parent::init();
    $this->setTitle('Edit Announcement')
      ->setDescription('Edit the announcement for your group below.');

    // Change the submit label
    $this->getElement('submit')->setLabel('Edit Announcement');
  }
}