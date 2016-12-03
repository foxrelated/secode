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
class Money_Widget_UserBalanceController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$viewer || !$viewer->getIdentity()) {
            return $this->setNoRender();
        }

        $this->view->balans = $money = Engine_Api::_()->money()->getUserBalance($viewer);
        $this->view->currency = Engine_Api::_()->getApi('settings',
            'core')->getSetting('money.site.currency', 'USD');
    }
}
