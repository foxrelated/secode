<?php
class Money_Model_Gateway extends Core_Model_Item_Abstract
{
    protected $_plugin;
    
    protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;
    
    public function getPlugin()
  {
    if( null === $this->_plugin ) {
      $class = $this->plugin;
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);
      if( !($plugin instanceof Money_Plugin_Gateway_Abstract) ) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
            'implement Engine_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }
    return $this->_plugin;
  }
  
  public function getGateway()
  {
    return $this->getPlugin()->getGateway();
  }
  
  public function getService()
  {
    return $this->getPlugin()->getService();
  }
}