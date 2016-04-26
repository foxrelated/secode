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

class Seo_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'seo_license', array(
      'label' => 'SEO Sitemap License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => true,

    ));
    
    $yesno = array(
      1 => 'Yes',
      0 => 'No'
    );    
    
    $this->addElement('Text', 'seo_sitemapfilename', array(
      'label' => 'XML Sitemap File Name',
      'description' => 'Enter filename of the xml sitemap file (ex: sitemap.xml). Make sure the sitemap file(s) are writable by applying CHMOD 755 or 777 to them or your SE_INSTALLATION_PATH/public/seo folder.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.sitemapfilename', 'sitemap.xml'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Radio', 'seo_gzipsitemap', array(
      'label' => 'Compress Sitemap',
      'description' => 'Would you like to build a compress version (gzip) of your xml sitemap file?',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.gzipsitemap', 1),
      'multiOptions' => $yesno,
    ));
    
    $this->addElement('MultiCheckbox', 'notifyservices', array(
      'label' => 'Notify Search Engines',
      'description' => 'Please choose search engines that you would like to be notified about sitemap updates.',
    	'multiOptions' => array(
        'google' => 'Google - Join the Google Webmaster Tools to check crawling statistics.',
        'bing' => 'Bing - Join the Bing Webmaster Tools to check crawling statistics.'
      ),
      'value' => explode(',',Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.notifyservices', 'google,bing')),
    ));
    

    $this->addElement('MultiCheckbox', 'enableheaders', array(
      'label' => 'SEO Page - Activation',
      'description' => 'You can enable supported features that you would like for SEO Page by checking boxes below. Note: If you disable a feature, it would be totally turned off regardless of what setting you may have for each individual page. This provides you a quick way to quickly disable (temporary/permanently) a feature you dont want without manually disable it on every pages.',
    	'multiOptions' => array(
        'title' => 'Page Title',
        'description' => 'Meta Description',
        'keywords' => 'Meta Keywords',
        'extra' => 'Extra Headers'
      ),
      'value' => explode(',',Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.enableheaders', 'title,description,keywords,extra')),
    ));
    
    
    $this->addElement('Radio', 'seo_titlemode', array(
      'label' => 'SEO Page - Title Tag',
      'description' => 'Please select the default output format setting for SEO Page Title Tag. Note: you will have option to customize it per indivual page later on.',
      'allowEmpty' => false,
      'required' => true,
    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.titlemode', Seo_Model_Page::MODE_PREPEND),
      'multiOptions' => array(
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Title]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Name - Page Title]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Title - Site Name]',
      ),
    ));
    
    $this->addElement('Radio', 'seo_descriptionmode', array(
      'label' => 'SEO Page - Meta Description',
      'description' => 'Please select the default output format setting for SEO Page Meta Description. Note: you will have option to customize it per indivual page later on.',
      'allowEmpty' => false,
      'required' => true,
    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.descriptionmode', Seo_Model_Page::MODE_OVERRIDE),
      'multiOptions' => array(
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Description]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Description - Page Description]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Description - Site Description]',
      ),
    ));
        
    $this->addElement('Radio', 'seo_keywordsmode', array(
      'label' => 'SEO Page - Meta Keywords',
      'description' => 'Please select the default output format setting for SEO Page Meta Keywords. Note: you will have option to customize it per indivual page later on.',
      'allowEmpty' => false,
      'required' => true,
    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.keywordsmode', Seo_Model_Page::MODE_APPEND),
      'multiOptions' => array(
        Seo_Model_Page::MODE_OVERRIDE => 'OVERRIDE Mode - [Page Keywords]',
        Seo_Model_Page::MODE_PREPEND => 'PREPEND Mode - [Site Keywords - Page Keywords]',
        Seo_Model_Page::MODE_APPEND => 'APPEND Mode - [Page Keywords - Site Keywords]',
      ),
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function checkChannelLogoUri($uri)
  {
    return Zend_Uri::check($uri);
  }
}