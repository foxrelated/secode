<?php

/**
 * @author    Nam Nguyen
 * @copyright YouNet Company
 * @since     4.11
 */

/**
 * Class Ynmobile_AdminThemeController
 */
class Ynmobile_AdminTestAccountController extends Core_Controller_Action_Admin
{
    public function init()
    {
        parent::init();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynmobile_admin_main', array(), 'ynmobile_admin_main_tests');
    }


    /**
     * List available themes
     */
    public function indexAction()
    {

    }
}