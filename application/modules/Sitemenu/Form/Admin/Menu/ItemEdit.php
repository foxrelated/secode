<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemEdit.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Form_Admin_Menu_ItemEdit extends Sitemenu_Form_Admin_Menu_ItemCreate {

    public function init() {
        parent::init();
        $this->setTitle('Edit Menu Item');
        $this->submit->setLabel('Edit Menu Item');
    }
}