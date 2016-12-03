<?php

class Money_Model_Issue extends Core_Model_Item_Abstract {
    
  public function getHref($params = array()){    
    $params = array_merge(array(
      'route' => 'money_general',
        'reset' => true,
        'action' => 'transaction'
    ), $params);
    
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
    }
    public function getTitle()
    {
      return 'Transaction';

  }
}