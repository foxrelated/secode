<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editicon.php 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteadvsearch_Form_Admin_Manage_Editicon extends Siteadvsearch_Form_Admin_Manage_Addicon {

  public function init() {
    parent::init();

    $this
            ->setTitle('Change Icon');
  }

}