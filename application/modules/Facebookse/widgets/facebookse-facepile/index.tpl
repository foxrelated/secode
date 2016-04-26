<?php
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php $this->headScript()
				  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');?>
<div id="facefile">
<?php if (empty($this->facepile)) { ?>
<?php 	echo $this->translate($this->facebook_facepile); ?>
<?php } ?>
<?php $facepile_status = Zend_Registry::get('facebookse_status'); 
      $site_URL = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $this->baseUrl();    

?>
	<?php if(empty($facepile_status)){ exit(); } ?>
			<?php if (!empty($this->enable_fboldversion) || !empty($this->fblogin) ) { 
				$fbaapid =  Engine_Api::_()->getApi("settings", "core")->core_facebook_appid;
			?>
				<iframe src="http://www.facebook.com/plugins/facepile.php?href=<?php echo $site_URL;?>&amp;app_id=<?php echo $fbaapid;?>&amp;width=<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>&amp;max_rows=<?php echo $this->permissionTable_widgetsetting_array['connection'];?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>px;" allowTransparency="true"></iframe>
  
    <?php } else { ?>
			<fb:facepile href="<?php echo $site_URL;?>" max-rows="<?php echo $this->permissionTable_widgetsetting_array['connection'];?>" width="<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>"></fb:facepile>
			
			<script type="text/javascript">
        var call_advfbjs = '1';
      </script>
     <?php } ?>
	</div>
