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

class Seo_Plugin_Sitemap_Generator extends Zend_View_Helper_Navigation_Sitemap
{
  
  protected $processingInstructions = array();
  
  public function addProcessingInstruction($target, $content)
  {
    $this->processingInstructions[$target] = $content;
    return $this;
  }
  
  public function setProcessingInstructions($data)
  {
    foreach ($data as $target => $content)
    {
      $this->addProcessingInstruction($target, $content);
    }
    return $this;
  }
  
  public function clearProcessingInstructions()
  {
    $this->processingInstructions = array();
    return $this;
  }
  
  public function write($filename, Zend_Navigation_Container $container = null)
  {
    $data = $this->render($container);

    // Write data
    try
    {
      $this->_mkdir(dirname($filename));
      $this->_write($filename, $data);
      @chmod($filename, 0777);
    }

    catch( Exception $e )
    {
      @unlink($filename);
      throw $e;
    }

    return $filename;    
     
  }
  
  
  public function compressFile($source, $level=false)
  {
    $dest=$source.'.gz';
    $mode='wb'.$level;
    $error=false;
    $writesize = 1024*512;
    if ($fp_out=gzopen($dest,$mode))
    {
      if ($fp_in=fopen($source,'rb'))
      {
        while (!feof($fp_in))
        {
          gzwrite($fp_out,fread($fp_in,$writesize));
        }        
            
        fclose($fp_in);
      }
      else 
      {
        $error=true;
      }
      gzclose($fp_out);
    }
    else
    {
      $error=true;
    } 
      
    return ($error) ? false : $dest;
  }  
  
  protected function _mkdir($path, $mode = 0777)
  {
    // Change umask
    if( function_exists('umask') ) {
      $oldUmask = umask();
      umask(0);
    }

    // Change perms
    $code = 0;
    if( is_dir($path) ) {
      @chmod($path, $mode);
    } else if( !@mkdir($path, $mode, true) ) {
      $code = 1;
    }

    // Revert umask
    if( function_exists('umask') ) {
      umask($oldUmask);
    }

    // Respond
    if( 1 == $code ) {
      throw new Seo_Plugin_Sitemap_Exception(sprintf('Could not create folder: %s', $path));
    }
  }
  
  

  protected function _write($file, $data)
  {
    // Change umask
    if( function_exists('umask') ) {
      $oldUmask = umask();
      umask(0);
    }

    // Write
    $code = 0;
    if( !@file_put_contents($file, $data) ) {
      if( is_file($file) ) {
        @chmod($file, 0666);
      } else if( is_dir(dirname($file)) ) {
        @chmod(dirname($file), 0777);
      } else {
        @mkdir(dirname($file), 0777, true);
      }

      if( !@file_put_contents($file, $data) ) {
        $code = 1;
      }
    }

    // Revert umask
    if( function_exists('umask') ) {
      umask($oldUmask);
    }
    
    if( 1 == $code ) {
      throw new Seo_Plugin_Sitemap_Exception(sprintf('Unable to write to file: $s', $file));
    }
  }
  
    /**
     * Returns a DOMDocument containing the Sitemap XML for the given container
     *
     * @param  Zend_Navigation_Container $container  [optional] container to get
     *                                               breadcrumbs from, defaults
     *                                               to what is registered in the
     *                                               helper
     * @return DOMDocument                           DOM representation of the
     *                                               container
     * @throws Zend_View_Exception                   if schema validation is on
     *                                               and the sitemap is invalid
     *                                               according to the sitemap
     *                                               schema, or if sitemap
     *                                               validators are used and the
     *                                               loc element fails validation
     */
    public function getDomSitemap(Zend_Navigation_Container $container = null)
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        // check if we should validate using our own validators
        if ($this->getUseSitemapValidators()) {
            // require_once 'Zend/Validate/Sitemap/Changefreq.php';
            // require_once 'Zend/Validate/Sitemap/Lastmod.php';
            // require_once 'Zend/Validate/Sitemap/Loc.php';
            // require_once 'Zend/Validate/Sitemap/Priority.php';

            // create validators
            $locValidator        = new Zend_Validate_Sitemap_Loc();
            $lastmodValidator    = new Zend_Validate_Sitemap_Lastmod();
            $changefreqValidator = new Zend_Validate_Sitemap_Changefreq();
            $priorityValidator   = new Zend_Validate_Sitemap_Priority();
        }

        // create document
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = $this->getFormatOutput();

        if (is_array($this->processingInstructions))
        {
          foreach ($this->processingInstructions as $target => $content)
          {
            $pi = $dom->createProcessingInstruction($target, $content);
            $dom->appendChild($pi);   
          }
        }
        
        // ...and urlset (root) element
        $urlSet = $dom->createElementNS(self::SITEMAP_NS, 'urlset');
        $dom->appendChild($urlSet);

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
            RecursiveIteratorIterator::SELF_FIRST);

        $maxDepth = $this->getMaxDepth();
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }
        $minDepth = $this->getMinDepth();
        if (!is_int($minDepth) || $minDepth < 0) {
            $minDepth = 0;
        }

        // iterate container
        foreach ($iterator as $page) {
            if ($iterator->getDepth() < $minDepth || !$this->accept($page)) {
                // page should not be included
                continue;
            }

            // get absolute url from page
            if (!$url = $this->url($page)) {
                // skip page if it has no url (rare case)
                continue;
            }

            // create url node for this page
            $urlNode = $dom->createElementNS(self::SITEMAP_NS, 'url');
            $urlSet->appendChild($urlNode);

            if ($this->getUseSitemapValidators() &&
                !$locValidator->isValid($url)) {
                // require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf(
                        'Encountered an invalid URL for Sitemap XML: "%s"',
                        $url));
            }

            // put url in 'loc' element
            $urlNode->appendChild($dom->createElementNS(self::SITEMAP_NS,
                                                        'loc', $url));

            // add 'lastmod' element if a valid lastmod is set in page
            if (isset($page->lastmod)) {
                $lastmod = strtotime((string) $page->lastmod);

                // prevent 1970-01-01...
                if ($lastmod !== false) {
                    $lastmod = date('c', $lastmod);
                }

                if (!$this->getUseSitemapValidators() ||
                    $lastmodValidator->isValid($lastmod)) {
                    $urlNode->appendChild(
                        $dom->createElementNS(self::SITEMAP_NS, 'lastmod',
                                              $lastmod)
                    );
                }
            }

            // add 'changefreq' element if a valid changefreq is set in page
            if (isset($page->changefreq)) {
                $changefreq = $page->changefreq;
                if (!$this->getUseSitemapValidators() ||
                    $changefreqValidator->isValid($changefreq)) {
                    $urlNode->appendChild(
                        $dom->createElementNS(self::SITEMAP_NS, 'changefreq',
                                              $changefreq)
                    );
                }
            }

            // add 'priority' element if a valid priority is set in page
            if (isset($page->priority)) {
                $priority = $page->priority;
                if (!$this->getUseSitemapValidators() ||
                    $priorityValidator->isValid($priority)) {
                    $urlNode->appendChild(
                        $dom->createElementNS(self::SITEMAP_NS, 'priority',
                                              $priority)
                    );
                }
            }
        }

        // validate using schema if specified
        if ($this->getUseSchemaValidation()) {
            if (!@$dom->schemaValidate(self::SITEMAP_XSD)) {
                // require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf(
                        'Sitemap is invalid according to XML Schema at "%s"',
                        self::SITEMAP_XSD));
            }
        }

        return $dom;
    }
}