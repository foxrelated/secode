<?php

class Ynaffiliate_Model_Commission extends Core_Model_Item_Abstract
{
    protected $_type = 'ynaffiliate_commission';
    protected $_parent_type = 'user';

    public function getOwner() {
        return Engine_Api::_()->getItem('user', $this->user_id);
    }

    public function getClient() {
        return Engine_Api::_()->getItem('user', $this->from_user_id);
    }

    public function getHref() {
        $params = array(
            'route' => 'ynaffiliate_extended',
            'controller' => 'tracking',
            'action' => 'purchase',
            'reset' => true,
        );

        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
    }

    public function getTitle() {
        return '';
    }
}