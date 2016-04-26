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

class Seo_Installer extends Engine_Package_Installer_Module
{

  public function installLayoutHeaderHook()
  {
    $layout_script = APPLICATION_PATH . DS . 'application/modules/Core/layouts/scripts/default.tpl';
    $content = file_get_contents($layout_script);
    
    if (strpos($content, 'onRenderLayoutDefaultSeo') === false)
    {
      $content = file_get_contents($layout_script);
      
      $search = '<?php echo $this->headTitle()->toString()';
      $replace = '<?php echo $this->hooks("onRenderLayoutDefaultSeo", $this) ?>' . "\n  $search";
      
      $content = str_replace($search, $replace, $content);
      file_put_contents($layout_script, $content);
    }
    
    
  }
  
  function onInstall()
  {
    $this->installLayoutHeaderHook();
    
    parent::onInstall();
  }
}
