<?php
/**
 * SocialEnginePro
 *
 * @category   Application_Extensions
 * @package    E-money
 * @author     Azim
 */

/**
 * @category   Application_Extensions
 * @package    E-money
 */
class Money_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Get navigation
        $this->view->navigation = Engine_Api::_()
            ->getApi('menus', 'core')
            ->getNavigation('money_main', array(), 'money_main_browse');
    }
}
