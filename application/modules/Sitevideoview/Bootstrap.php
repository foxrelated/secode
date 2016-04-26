<?php

class Sitevideoview_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application) {
    parent::__construct($application);
    $this->initViewHelperPath();
    include APPLICATION_PATH . '/application/modules/Sitevideoview/controllers/license/license.php';
  }

  protected function _initFrontController() {

    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitevideoview_Plugin_Core);
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideoview.isActivate', 0)) {
      $headScript = new Zend_View_Helper_HeadScript();
      $StaticBaseUrl = '';
      if (Zend_Registry::isRegistered('StaticBaseUrl')) {
        $StaticBaseUrl = Zend_Registry::get('StaticBaseUrl');
      }
      $headScript->appendFile($StaticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
              ->appendFile($StaticBaseUrl
                      . 'application/modules/Sitevideoview/externals/scripts/core.js')
              ->appendFile($StaticBaseUrl
                      . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js');

      $headLink = new Zend_View_Helper_HeadLink();
      $headLink->appendStylesheet($StaticBaseUrl
              . 'application/modules/Seaocore/externals/styles/style_advanced_photolightbox.css');
    }
  }

}

?>