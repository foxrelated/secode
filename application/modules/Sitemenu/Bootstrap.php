<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    protected function _initFrontController() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sitemenu_Plugin_Core);
        include APPLICATION_PATH . '/application/modules/Sitemenu/controllers/license/license.php';
    }

}