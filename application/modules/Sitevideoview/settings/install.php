<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideoview
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2012-06-028 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideoview_Installer extends Engine_Package_Installer_Module {

  function onPreinstall() {
    $db = $this->getDb();
    $PRODUCT_TYPE = 'sitevideoview';
    $PLUGIN_TITLE = 'Sitevideoview';
    $PLUGIN_VERSION = '4.6.0p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Video Lightbox Viewer Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.7.1p4';
    $PRODUCT_TITLE = 'Video Lightbox Viewer Plugin';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license2.php";
    } else {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }
    parent::onPreinstall();
  }

  function onInstall() {

		$db = $this->getDb();

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_modules')
			->where('name = ?', 'video');
		$check_socialengineaddons = $select->query()->fetchAll();
		
		if ( !empty($check_socialengineaddons) ) {
      //Replace the Like Button widget to new widget.
      $this->updateWidgteName("video.list-recent-videos", "sitevideoview.list-popular-videos");
      $this->updateWidgteName("video.profile-videos", "sitevideoview.profile-videos");
      $this->updateWidgteName("ynvideo.profile-videos", "sitevideoview.profile-videos");
      $this->updateWidgteName("video.list-popular-videos", "sitevideoview.list-recent-videos");
		}

		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='sitevideoview';");

    parent::onInstall();
  }
  
	//FUNCTION FOR THE lIKE BUTTON WIDGET.
	public function updateWidgteName($oldwidgteName, $newWidgetName) {

		$db = $this->getDb() ;
		$select = new Zend_Db_Select( $db ) ;
		$select->from( 'engine4_core_content' )->where( 'name = ?' , $oldwidgteName );
		$results = $select->query()->fetchAll();

		if (!empty($results)) {
				$db->query('UPDATE  `engine4_core_content` SET  `name` =  \''.$newWidgetName.'\' WHERE  `engine4_core_content`.`name` =\''.$oldwidgteName.'\';');
		}
	}
}
?>