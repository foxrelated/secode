<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        include APPLICATION_PATH . '/application/modules/Nestedcomment/controllers/license/license.php';
    }

}