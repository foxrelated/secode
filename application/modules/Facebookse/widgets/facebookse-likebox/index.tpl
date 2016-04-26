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
<script type="text/javascript">
var call_advfbjs = '1';
</script>
<div id="like-box">
	<?php $facebook_likebox = Zend_Registry::get('facebookse_likebox'); ?>
    <?php if (!empty($this->enable_fboldversion) && !empty($this->permissionTable_widgetsetting_array['fbpageurl'])) { ?>
					<iframe src="http://www.facebook.com/plugins/likebox.php?href=<?php echo urlencode($this->permissionTable_widgetsetting_array['fbpageurl']);?>&amp;width=<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>&amp;colorscheme=<?php echo $this->permissionTable_widgetsetting_array['widget_color_scheme'];?>&amp;show_faces=true&amp;stream=<?php echo $this->permissionTable_widgetsetting_array['show_stream'];?>&amp;header=<?php echo $this->permissionTable_widgetsetting_array['show_header'];?>&amp;height=427&amp;connections=<?php echo $this->permissionTable_widgetsetting_array['connection'];?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>px; height:430px;" allowTransparency="true"><?php if ( empty($facebook_likebox)){ exit(); } ?><?php if(empty($this->facebook_profilepage_type)){ echo $this->translate($this->facebook_page_permition); } ?></iframe>

    <?php } else { ?> 
   <fb:like-box href="<?php echo $this->permissionTable_widgetsetting_array['fbpageurl'];?>" width="<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>" height="<?php echo $this->permissionTable_widgetsetting_array['fb_height'];?>"  colorscheme="<?php echo $this->permissionTable_widgetsetting_array['widget_color_scheme'];?>" connections="<?php echo $this->permissionTable_widgetsetting_array['connection'];?>" header="<?php echo $this->permissionTable_widgetsetting_array['show_header'];?>" stream="<?php echo $this->permissionTable_widgetsetting_array['show_stream'];?>"><?php if ( empty($facebook_likebox)){ exit(); } ?><?php if(empty($this->facebook_profilepage_type)){ echo $this->translate($this->facebook_page_permition); } ?></fb:like-box>
   <?php } ?>
   
</div>