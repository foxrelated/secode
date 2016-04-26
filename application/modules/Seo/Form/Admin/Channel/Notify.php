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

class Seo_Form_Admin_Channel_Notify extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Submit Sitemap to Search Engine')
      ->setDescription('Please check the box of services you would like to notify about your sitemap updates. It may take a while to submit to services, please be patient!. If it takes too long to submit to them all, try to do it once at a time. Do NOT abuse these services! You do not have to submit sitemap for each channel unless neccessary. Submitting the global sitemap "sitemap.xml", which includes all channels, should be good enough.');

    $sitemap = Engine_Api::_()->seo()->getSitemapInfo();  
    
    $urls = array();
    if ($sitemap['gzip']) {
      $urls[$sitemap['gzipfile']['url']] = $sitemap['gzipfile']['url'];
    }
    $urls[$sitemap['file']['url']] = $sitemap['file']['url'];
    
    $activeChannels = Engine_Api::_()->seo()->getChannelTable()->getActiveChannels();
    foreach ($activeChannels as $activeChannel)
    {
      if ($activeChannel->hasSitemapFile()) {
        $file = $activeChannel->getSitemapFileUrl();
        if ($sitemap['gzip']) {
          $urls[$file . '.gz'] = $file . '.gz';
        }
        $urls[$file] = $file;
      }
    }
    
    
    $this->addElement('Select', 'url', array(
      'label' => 'XML Sitemap URL',
     // 'value' => $url,
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => $urls,
    ));
   
    
    $this->addElement('MultiCheckbox', 'notifyservices', array(
      'label' => 'Notify Search Engines',
      'description' => 'Please choose search engines that you would like to be notified about sitemap updates.',
    	'multiOptions' => array(
        'google' => 'Google - Join the Google Webmaster Tools to check crawling statistics.',
        'bing' => 'Bing - Join the Bing Webmaster Tools to check crawling statistics.'
      ),
      'value' => explode(',',Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.notifyservices', 'google,bing')),
      'allowEmpty' => false,
      'required' => true,      
    ));
    
    // $this->addElement('Text', 'notifyyahooappid', array(
      //  'label' => '',
     //   'description' => 'Your Yahoo Application ID:',
     //   'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.notifyyahooappid'),
     //   'filters' => array(
     //     'StringTrim'
   //     ),
  //    ));    
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit Sitemap',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');    
    
  }

}