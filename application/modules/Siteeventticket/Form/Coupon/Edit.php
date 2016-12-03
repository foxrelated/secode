<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2014-05-19 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Coupon_Edit extends Siteeventticket_Form_Coupon_Create {

  public function init() {

    parent::init();

    $this->setTitle('Edit Coupon')
            ->setDescription("Edit your coupon's details below, then click 'Save Changes' to publish.");

    $this->submit->setLabel('Save Changes');
  }

}
