<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _initFrontController()
  {		
     $this->initActionHelperPath();
     // Initialize AddLike helper
     Zend_Controller_Action_HelperBroker::addHelper(new Facebookse_Controller_Action_Helper_AddLike()); 
     if (!empty($_SERVER['HTTP_USER_AGENT'])) {
         $isFacebook = strstr($_SERVER['HTTP_USER_AGENT'], "facebook") ? true : false;
      }
      else {

          $isFacebook = false;  
      }
      
       $front = Zend_Controller_Front::getInstance();       
       $module_id =  @$_GET['contentid'];
       $resourcetype = @$_GET['type'];      
       if ($isFacebook && !empty ($module_id) && !empty ($resourcetype)) { 
         
        $front->registerPlugin(new Facebookse_Plugin_Core);
       }
       else if ($isFacebook) { 
         try {
            @Zend_Controller_Action_HelperBroker::getHelper('layout')->setLayout('default-simple');
         }
         catch (Exception $e) {
           
         }
       }
       
  }
 
	public function __construct($application)
  {
    parent::__construct($application);
		include APPLICATION_PATH . '/application/modules/Facebookse/controllers/license/license.php';
		$coreversion = Engine_Api::_()->getDbtable( 'modules' , 'core' )->getModule( 'core' )->version;
    if($coreversion >= '4.8.0') {
      $doctypeHelper = new Zend_View_Helper_Doctype();
      $doctypes = $doctypeHelper->getDoctypes();
      if(isset($doctypes['XHTML1_RDFA']))
        $doctypeHelper->doctype('XHTML1_RDFA');
    }
    
  }
  
}