<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Bootstrap extends Engine_Application_Bootstrap_Abstract {

    public function __construct($application) {
        parent::__construct($application);
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license.php';
    }

    protected function _initFrontController() {

        $this->initActionHelperPath();
        $this->initViewHelperPath();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Siteevent_Plugin_Core);
        $headScript = new Zend_View_Helper_HeadScript();
    }

}