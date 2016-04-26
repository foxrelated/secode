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

<div id="recommend">
<?php if (!empty($this->enable_fboldversion)) { ?>
 <iframe src="http://www.facebook.com/plugins/recommendations.php?width=<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>&amp;height=<?php echo $this->permissionTable_widgetsetting_array['fb_height'];?>&amp;header=<?php echo $this->permissionTable_widgetsetting_array['show_header'];?>&amp;colorscheme=<?php echo $this->permissionTable_widgetsetting_array['widget_color_scheme'];?>&amp;font=<?php echo $this->permissionTable_widgetsetting_array['widget_font'];?>&amp;border_color=<?php echo $this->permissionTable_widgetsetting_array['widget_border_color'];?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>px; height:<?php echo $this->permissionTable_widgetsetting_array['fb_height'];?>px;" allowTransparency="true"><?php if(empty($this->recomendations)){echo $this->translate($this->recomendations_path);} ?></iframe>

<?php } else { ?>

	<?php $widget_settings = Zend_Registry::get('facebookse_widgetsettings'); ?><?php if(empty($widget_settings)){ exit(); } ?><fb:recommendations site="<?php echo $this->siteurl;?>" width="<?php echo $this->permissionTable_widgetsetting_array['fb_width'];?>" height="<?php echo $this->permissionTable_widgetsetting_array['fb_height'];?>" colorscheme="<?php echo $this->permissionTable_widgetsetting_array['widget_color_scheme'];?>" font="<?php echo $this->permissionTable_widgetsetting_array['widget_font'];?>" border_color="<?php echo $this->permissionTable_widgetsetting_array['widget_border_color'];?>" header="<?php echo $this->permissionTable_widgetsetting_array['show_header'];?>"><?php if(empty($this->recomendations)){echo $this->translate($this->recomendations_path);} ?></fb:recommendations>
	<script type="text/javascript">
    var call_advfbjs = '1';
  </script>

<?php } ?>

</div>
