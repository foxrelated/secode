<?php



/**
 * Radcodes - SocialEngine Channel
 *
 * @category   Application_Extensions
 * @package    Seo
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Seo_Model_DbTable_Channels extends Engine_Db_Table
{
  protected $_rowClass = 'Seo_Model_Channel';
  
  protected $_channels;

  protected $_channelsAssoc = array();

  protected $_activeChannels;
  
  public function getChannel($name)
  {
    if( null === $this->_channels ) {
      $this->getChannels();
    }

    if( !empty($this->_channelsAssoc[$name]) ) {
      return $this->_channelsAssoc[$name];
    }

    return null;
  }
  
  public function getChannels()
  {
    if( null === $this->_channels ) {
      $this->_channels = $this->fetchAll(null, 'order asc');
      foreach( $this->_channels as $channel ) {
        $this->_channelsAssoc[$channel->name] = $channel;
      }
    }

    return $this->_channels;
  }

  public function getChannelsAssoc()
  {
    if( null === $this->_channels ) {
      $this->getChannels();
    }
    
    return $this->_channelsAssoc;
  }

  public function hasChannel($name)
  {
    return !empty($this->_channelsAssoc[$name]);
  }

  
  public function getActiveChannels()
  {
    if( null == $this->_activeChannels ) {
      foreach ($this->getChannels() as $channel) {
        if ($channel->enabled && $channel->isSupported()) {
          $this->_activeChannels[$channel->name] = $channel;
        }
      }
    }
    return $this->_activeChannels;
  }
  
  public function isChannelActive($name)
  {
    $this->getChannelsAssoc();
    
    if (empty($this->_channelsAssoc[$name]) || empty($this->_channelsAssoc[$name]->enabled) || !$this->_channelsAssoc[$name]->isSupported()) {
      return false;
    }
    return true;
  }
  
  public function getMaxOrder()
  {
    $max = 0;
    foreach ($this->getChannels() as $channel) {
      if ($channel->order > $max) {
        $max = $channel->order;
      }
    }
    return $max;
  }
  
  
}