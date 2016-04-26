<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesalbum
 * @package    Sesalbum
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: install.php 2015-06-16 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesalbum_Installer extends Engine_Package_Installer_Module {

  public function onPreinstall() {

    $db = $this->getDb();
    
    $sesbasic_currentversion = '4.8.9p10';
    $sesbasiccheckcurrentversion = '48910';
    
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sesbasic');
    $sesbasic_enabled = $select->query()->fetchObject();
    
    $sesbasic_siteversion = @explode('.', $sesbasic_enabled->version);
    if(strstr($sesbasic_siteversion[2], "p")) {
	    $sesbasic_site_last = str_replace('p','', $sesbasic_siteversion[2]);
    } else {
	    $sesbasic_site_last = $sesbasic_siteversion[2];
    }
    $sesbasic_finalsiteversion = $sesbasic_siteversion[0] . $sesbasic_siteversion[1] . $sesbasic_site_last ;

    if (empty($sesbasic_enabled)) {
      return $this->_error('<div class="global_form"><div><div><p style="color:red;">The required SocialEngineSolutions Basic Required Plugin is not installed on your website. Please download the latest version of this FREE plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
    } else {
      if (isset($sesbasic_enabled->enabled) && !empty($sesbasic_enabled->enabled)) {
        if ($sesbasic_finalsiteversion >= $sesbasiccheckcurrentversion) {
        } else {
          return $this->_error('<div class="global_form"><div><div><p style="color:red;">The latest version of the SocialEngineSolutions Basic Required Plugin installed on your website is less than the minimum required version: ' . $sesbasic_currentversion . '. Please upgrade this Free plugin to its latest version after downloading the latest version of this plugin from <a href="http://www.socialenginesolutions.com" target="_blank">SocialEngineSolutions.com</a> website.</p></div></div></div>');
        }
      } else {
        return $this->_error('<div class="global_form"><div><div><p style="color:red;">The SocialEngineSolutions Basic Required Plugin is installed but not enabled on your website. So, please first enable it from the "Manage" >> "Packages & Plugins" section.</p></div></div></div>');
      }
    }

    parent::onPreinstall();
  }

  public function onInstall() {
    
    $db = $this->getDb();
    
    parent::onInstall();
  }

}