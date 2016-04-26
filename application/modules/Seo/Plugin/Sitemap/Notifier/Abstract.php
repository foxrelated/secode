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

class Seo_Plugin_Sitemap_Notifier_Abstract
{
  
  protected $SERVICE_URL;
  protected $URL_KEY;
  
  protected $params = array();
  
  protected $sucessToken;
  
  public function __construct($params = array())
  {
    if (is_array($params) && count($params))
    {
      $this->setParams($params);
    }
  }
  
  public function ping($url)
  {
    
    $client = new Zend_Http_Client();
    $client->setUri($this->SERVICE_URL);
    $client->setParameterGet($this->URL_KEY, $url);
    
    foreach ($this->params as $key => $value)
    {
      $client->setParameterGet($key, $value);
    }
    
    $client->setMethod(Zend_Http_Client::GET);
    
    $response = $client->request();
    
    if ($response->isSuccessful())
    {
      return $this->validate($response);
    }
    else 
    {
      return false;
    }
  }
  
  public function validate($response)
  {
    if ($this->sucessToken)
    {
      return (strpos(strtolower($response->getBody()),strtolower($this->sucessToken))!==false);
    }
    return true;
  }
  
  
  public function setParam($key, $value)
  {
    $this->params[$key] = $value;
    return $this;
  }
  
  public function setParams($params)
  {
    foreach ($params as $key => $value)
    {
      $this->setParam($key, $value);
    }
    return $this;
  }
}