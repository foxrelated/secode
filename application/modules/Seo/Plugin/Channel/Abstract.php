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

abstract class Seo_Plugin_Channel_Abstract
{

  protected $_channel;
  
  public function __construct(Seo_Model_Channel $channel)
  {
    $this->_channel = $channel;
  }

  
  public function addUrls(Zend_Navigation_Container $container, $options = array())
  {
    $paginator = $this->getPaginator($options);
    $paginator->setItemCountPerPage(50);
    
    $totalPages = $paginator->count();
    
    //Zend_Debug::dump($totalPages, 'totalPages');
    
    $counter = 0;
    
    for ($page = 1; $page <= $totalPages; $page++)
    {
      $paginator->setCurrentPageNumber($page);
      
      //Zend_Debug::dump($page, 'current page=');
      
      foreach ($paginator as $item)
      {
        //Zend_Debug::dump($item->getTitle(), "item id=".$item->getIdentity()." title=");
        
        if ($this->_channel->maxitems > 0 && $counter++ >= $this->_channel->maxitems)
        {
          return;
        }
        
        $container->addPage($this->itemToPage($item));
      }
    }
  }
  
  /**
	 * @return Zend_Paginator
   */
  public function getPaginator($options = array())
  {
    return Zend_Paginator::factory(array());
  }
  
  /**
   * @return Seo_Plugin_Sitemap_Page
   */
  public function itemToPage($item)
  {
    $page = new Seo_Plugin_Sitemap_Page();
    $page->setUri($item->getHref());
    if ($this->_channel->changefreq)
    {
      $page->set('changefreq', $this->_channel->changefreq);
    }
    if ($this->_channel->priority)
    {
      $page->set('priority', $this->_channel->priority);
    }    
    if (!empty($item->modified_date))
    {
      $page->set('lastmod', $item->modified_date);
    }
    return $page;
  }
  
  public function isSupported()
  {
    $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    return in_array($this->_channel->name, $enabledModuleNames);
  }
  
}