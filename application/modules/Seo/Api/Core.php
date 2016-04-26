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

class Seo_Api_Core extends Core_Api_Abstract
{
  
  public function getSeoPage($module, $controller, $action)
  {
    $table = Engine_Api::_()->getDbtable('pages', 'seo');
    $select = $table->select()
      ->where('page_module = ?', $module)
      ->where('page_controller = ?', $controller)
      ->where('page_action = ?', $action);
      
    $row = $table->fetchRow($select);
    
    return $row;
  }
  
  
  // Select
  /**
   * Gets a paginator for seos
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getSeoPagesPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getSeoPagesSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Gets a select object for the user's seo entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getSeoPagesSelect($params = array(), $options = null)
  {
    $table = Engine_Api::_()->getDbtable('pages', 'seo');
    $rName = $table->info('name');

    
    $order = $rName.'.'.( !empty($params['order']) ? $params['order'] : 'creation_date' );
    $order_direction = !empty($params['order_direction']) ? $params['order_direction'] : 'DESC';
    $order_expr = "$order $order_direction";
    
    $select = $table->select()
      ->order( $order_expr );
   
    
    if( !empty($params['page_module']))
    {
      $select->where($rName.'.page_module = ?', $params['page_module']);
    }

    if( !empty($params['page_controller']))
    {
      $select->where($rName.'.page_controller = ?', $params['page_controller']);
    }    
    
    if( !empty($params['page_action']))
    {
      $select->where($rName.'.page_action = ?', $params['page_action']);
    }    
    
    return $select;
  }  
  
  
  /**
	 * @return Seo_Model_DbTable_Channels
   */
  public function getChannelTable()
  {
    return Engine_Api::_()->getDbtable('channels', 'seo');
  }  
  
  public function buildSitemap($channel = null)
  {
    //ini_set('memory_limit', '64M');
    
    $container = new Seo_Plugin_Sitemap_Container();
    
    if ($channel instanceof Seo_Model_Channel)
    {
      $channel->addUrls($container);
    }
    else
    {
      $activeChannels = $this->getChannelTable()->getActiveChannels();
      foreach ($activeChannels as $activeChannel)
      {
        $activeChannel->addUrls($container);
      }
    }

    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    
    $serverUrl = $request->getScheme() . '://' . $request->getHttpHost();
        
    $generator = new Seo_Plugin_Sitemap_Generator();
    $generator->setContainer($container)
      ->setServerUrl($serverUrl)
      ->setFormatOutput(true);
      
    $stylesheet = $serverUrl . $request->getBaseUrl() . '/application/modules/Seo/externals/styles/sitemap.xsl';
    $generator->addProcessingInstruction("xml-stylesheet", "type=\"text/xsl\" href=\"$stylesheet\"");
      
      
    $file = $generator->write($this->getSitemapFilePath($channel));  

    if ($file)
    {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('seo.sitemaplastupdate', time());
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.gzipsitemap', 1))
      {
        $generator->compressFile($file);
      }      
    }
       
    return $file;

  }

  
  public function submitSitemap($url, $services)
  {
    $results = array();
    
    foreach ($services as $service => $params)
    {
      $notifier = Seo_Plugin_Sitemap_Notifier::factory($service, $params);
      try
      {
        $results[$service] = $notifier->ping($url);
      }
      catch (Exception $e)
      {
        $results[$service] = $e;
      }
    }
    
    return $results;
  }
  
  public function getSitemapFilename($channel = null)
  {
    if ($channel instanceof Seo_Model_Channel) {
      $file = "sitemap_" . $channel->name . ".xml";
    }
    else {
      $file = Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemapfilename', 'sitemap.xml');
    }
    
    return $file;
  }
  
  /**
   * Get full path to where sitemap file is stored locally
   * @return string
   */
  public function getSitemapFilePath($channel = null)
  {
    return APPLICATION_PATH_PUB . '/seo/' . $this->getSitemapFilename($channel);
  }
  
  public function getSitemapCompressedFilePath()
  {
    return $this->getSitemapFilePath() . '.gz';
  }
  
  public function getSitemapFileUrl($channel = null)
  {
    $path = Zend_Controller_Front::getInstance()->getRequest()->getScheme() 
            . '://' .Zend_Controller_Front::getInstance()->getRequest()->getHttpHost()
            . Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl()
            . '/public/seo/'
            . $this->getSitemapFilename($channel);
    return $path;
  }
  
  public function getSitemapCompressedFileUrl()
  {
    return $this->getSitemapFileUrl() . '.gz';
  }
  
  public function getSitemapInfo()
  {
    $sitemap = array(
      'last_update' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemaplastupdate'),
      'last_submit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemaplastsubmit'),
    
      'file' => array(
        'name' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemapfilename', 'sitemap.xml'),
        'path' => $this->getSitemapFilePath(),
        'url' => $this->getSitemapFileUrl(),
      ),
      'gzip' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.gzipsitemap', 1),
      
      'gzipfile' => array(
        'name' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemapfilename', 'sitemap.xml') . '.gz',
        'path' => $this->getSitemapCompressedFilePath(),
        'url' => $this->getSitemapCompressedFileUrl(),
      ),
      

    );   

    return $sitemap;
  }
  
  public function isLayoutHookInstalled()
  {
    $layout_script = APPLICATION_PATH . DS . 'application/modules/Core/layouts/scripts/default.tpl';
    $content = file_get_contents($layout_script);
   // echo $layout_script;
    return (strpos($content, 'onRenderLayoutDefaultSeo') !== false);
  }
  
}