<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Seo_Model_Channel extends Core_Model_Item_Abstract
{
  // Properties
  protected $_searchTriggers = false;
  
  protected $_plugin;
  
  // Interfaces
  /**
   * Gets an absolute URL to the page to default feed
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'default',
      'reset' => true,
      'module' => 'core',
      'controller' => 'sitemap',
      'action' => 'index',
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

    
  /*
   * @return Seo_Plugin_Channel_Abstract
   */
  public function getPlugin()
  {
    if (!$this->_plugin)
    {
      if ($this->plugin)
      {
        $classname = $this->plugin;
      }
      else if ($this->custom)
      {
        $classname = "Seo_Plugin_Channel_Custom";
      }
      else
      {
        $classname = "Seo_Plugin_Channel_".ucfirst(strtolower($this->name));
      }

      $this->_plugin = new $classname($this);
    }
    return $this->_plugin;
  }
  
  
  public function isActive()
  {
    return $this->enabled && $this->isSupported();
  }
  
  public function __call($name, $arguments)
  {
    return call_user_func_array(array($this->getPlugin(), $name), $arguments);
  }
  
  protected function _postInsert()
  {
    
  }
  
  public function getSitemapFilename()
  {
    return Engine_Api::_()->seo()->getSitemapFilename($this);
  }
  
  public function getSitemapFileUrl()
  {
    return Engine_Api::_()->seo()->getSitemapFileUrl($this);
  }
  
  
  public function hasSitemapFile()
  {
    $file = Engine_Api::_()->seo()->getSitemapFilePath($this);
    return file_exists($file);
  }
}