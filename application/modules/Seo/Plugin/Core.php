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

class Seo_Plugin_Core
{
  
  protected $placeholders;
  
  public function onRenderLayoutDefaultSeo($event)
  {
		/**
     * @var Zend_View
     */
    $view = $event->getPayload();
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
     
    $page = Engine_Api::_()->seo()->getSeoPage($request->getModuleName(), $request->getControllerName(), $request->getActionName());
     
    if ($page && $page->enabled)
    {
      $this->loadPlaceholders($view->subject());
      
      $features = explode(',',Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.enableheaders', 'title,description,keywords,extra'));
      
      if (in_array('title', $features))
      {
        $this->addonRenderHeaderTitle($page, $view);
      }
      
      if (in_array('description', $features))
      {
        $this->addonRenderHeaderDescription($page, $view);
      }
      
      if (in_array('keywords', $features))
      {
        $this->addonRenderHeaderKeywords($page, $view);
      }

      if (in_array('extra', $features))
      {
        $this->addonRenderHeaderExtra($page, $event);
      }
      
    }

  }
  
  

  
  protected function addonRenderHeaderTitle(Seo_Model_Page $page, Zend_View $view)
  {
    $page_title = $page->getTitle();
    
    if ($page_title)
    {
      $page_title = $this->replacePlaceholders($page_title);
      
      $mode = $page->title_mode;
      if ($mode == Seo_Model_Page::MODE_DEFAULT)
      {
        $mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.titlemode', Seo_Model_Page::MODE_PREPEND);
      }

      $view->headTitle($page_title, Zend_View_Helper_Placeholder_Container_Abstract::SET);
      $view->headTitle()->setSeparator(' - ');
      
      if ($mode == Seo_Model_Page::MODE_PREPEND)
      {
        $view->headTitle($view->layout()->siteinfo['title'], Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
      }
      else if ($mode == Seo_Model_Page::MODE_APPEND)
      {
        $view->headTitle($view->layout()->siteinfo['title'], Zend_View_Helper_Placeholder_Container_Abstract::APPEND);
      }

    }
  }
  
  
  
  protected function addonRenderHeaderDescription(Seo_Model_Page $page, Zend_View $view)
  {
    $page_description = $page->getDescription();
    
    if ($page_description)
    {
      $page_description = $this->replacePlaceholders($page_description);
      
      $mode = $page->description_mode;
      if ($mode == Seo_Model_Page::MODE_DEFAULT)
      {
        $mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.descriptionmode', Seo_Model_Page::MODE_OVERRIDE);
      }

      if ($mode == Seo_Model_Page::MODE_PREPEND)
      {
        $page_description = $view->layout()->siteinfo['description'] . ' ' . $page_description;
      }
      else if ($mode == Seo_Model_Page::MODE_APPEND)
      {
        $page_description .= ' ' . $view->layout()->siteinfo['description'];
      }
      
      $view->headMeta()->setName('description', trim($page_description), array());
      
    }
  }
  
  
  protected function addonRenderHeaderKeywords(Seo_Model_Page $page, Zend_View $view)
  {
    $page_keywords = $page->getKeywords();
    
    if ($page_keywords)
    {
      $page_keywords = $this->replacePlaceholders($page_keywords);
      
      $mode = $page->keywords_mode;
      if ($mode == Seo_Model_Page::MODE_DEFAULT)
      {
        $mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('seo.keywordsmode', Seo_Model_Page::MODE_APPEND);
      }

      if ($mode == Seo_Model_Page::MODE_PREPEND)
      {
        $page_keywords = $view->layout()->siteinfo['keywords'] . ' ' . $page_keywords;
      }
      else if ($mode == Seo_Model_Page::MODE_APPEND)
      {
        $page_keywords .= ' ' . $view->layout()->siteinfo['keywords'];
      }
      
      $view->headMeta()->setName('keywords', trim($page_keywords), array());
      
    }
  }  
  
  protected function addonRenderHeaderExtra(Seo_Model_Page $page, $event)
  {
    if ($page->extra_headers)
    {
      $event->addResponse($page->extra_headers);
    } 
  }
  
  
  protected function loadPlaceholders($subject)
  {
    $vars = array(
      '%subject_title%' => '',
      '%subject_description%' => '',
      '%subject_keywords%' => '',
      '%subject_owner%' => '',
    );
    
    if ($subject instanceof Core_Model_Item_Abstract && $subject->getIdentity())
    {
      
      $vars['%subject_title%'] = $subject->getTitle();
      $vars['%subject_description%'] = $subject->getDescription();
      $vars['%subject_keywords%'] = $subject->getKeywords();

      if (($owner = $subject->getOwner('user')) instanceof Core_Model_Item_Abstract && $owner->getIdentity())
      {
        $vars['%subject_owner%'] = $owner->getTitle();
      }
    }
    
    $this->placeholders = $vars;
  }  
  
  protected function replacePlaceholders($text)
  {
    foreach ($this->placeholders as $key => $val)
    {
      $text = str_replace($key, $val, $text);
    }    
    
    return $text;
  }
  
}