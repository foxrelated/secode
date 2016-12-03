<?php
/**
 * iPragmatech Solution Pvt. Ltd.
 *
 * @category   Application_Core
 * @package    State Importer
 * @copyright  Copyright 2008-2013 iPragmatech Solution Pvt. Ltd.
 * @license    http://www.ipragmatech.com/license/
 * @version    $Id: Controller.php 9747 2013-07-06 02:08:08Z iPrgamtech $
 * @author     iPragmatech
 */

/**
 * @category   Application_Core
 * @package    State Importer
 * @copyright  Copyright 2008-2013 iPragmatech Solution Pvt. Ltd.
 * @license    http://www.ipragmaetch.com/license/
 */
class Qrcode_Widget_PluginUpdatesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Zend_Feed required DOMDocument
    if( !class_exists('DOMDocument', false) ) {
      $this->view->badPhpVersion = true;
      return;
    }

    // Get params
    $url = 'http://www.ipragmatech.com/feed?product_cat=SocialEngine';
    if( !$url ) {
      return $this->setNoRender();
    }
    $this->view->url = $url;
    $this->view->max = $max = $this->_getParam('max', 9);
    $this->view->strip = $strip = $this->_getParam('strip', false);
    $cacheTimeout = 1800;
    
    // Cacheing
    $cache = Zend_Registry::get('Zend_Cache');
    if( $cache instanceof Zend_Cache_Core &&
        $cacheTimeout > 0 ) {
      $cacheId = get_class($this) . md5($url . $max . $strip);
      $channel = $cache->load($cacheId);
      
      if( !is_array($channel) || empty($channel) ) {
        $channel = null;
      } else if( time() > $channel['fetched'] + $cacheTimeout ) {
        $channel = null;
      }
    } else {
      $cacheId = null;
      $channel = null;
    }

    if( !$channel ) {
      $rss = Zend_Feed::import($url);
      $channel = array(
      	'thumb'	      =>$rss->thumb(),
      	'price'       => $rss->price(),
        'title'       => $rss->title(),
        'link'        => $rss->link(),
        'description' => $rss->description(),
        'items'       => array(),
        'fetched'     => time(),
      );

      // Loop over each channel item and store relevant data
      $count = 0;
      foreach( $rss as $item ) {
        if( $count++ >= $max ) break;
        $channel['items'][] = array(

          'thumb'       => $item->thumb(),
          'price'       => $item->price(),
          'title'       => $item->title(),
          'link'        => $item->link(),
          'description' => $item->description(),
          'pubDate'     => $item->pubDate(),
          'guid'        => $item->guid(),
        );
      }

      $this->view->isCached = false;

      // Caching
      if( $cacheId && !empty($channel) ) {
        $cache->save($channel, $cacheId);
      }
    } else {
      $this->view->isCached = true;
    }

    $this->view->channel = $channel;
  }
}