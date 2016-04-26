<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: showfbsocialpluginpreview.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<div id="fb-root"></div>
		<!--INCLUDING FACEBOOK JAVASCRIPT SDK.-->
		<script>
			window.fbAsyncInit = function() {
				FB.init({appId: '<?php echo Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;?>', status: true, cookie: true,
								xfbml: true});
			};
			(function() {
				var e = document.createElement('script'); e.async = true;
				e.src = document.location.protocol +
					'//connect.facebook.net/en_US/all.js';
				document.getElementById('fb-root').appendChild(e);
			}());
		</script>
<?php $plugin_type = $this->fb_social_pluginpreview['plugin_type'];
 if ($plugin_type == 'activity_feed') { ?>
	<div style="display:block;float:left;height:300px;width:500px;overflow:auto;" class="facebookse_socialplugin_priview">

<?php } else if ($plugin_type == 'facepile') { ?>
	<div style="display:block;float:left;height:400px;width:500px;overflow:auto;" class="facebookse_socialplugin_priview">

<?php } else if ($plugin_type == 'likebox') { ?>
	<div style="display:block;float:left;height:400px;width:500px;overflow:auto;" class="facebookse_socialplugin_priview">

<?php } else if ($plugin_type == 'recommendation') { ?>
	<div style="display:block;float:left;height:300px;width:500px;overflow:auto;" class="facebookse_socialplugin_priview">

<?php }  ?>
<h3>
	<?php echo $this->translate('Your Facebook Social Plugin Preview') ?> 
</h3>
<?php
//GENERATING THE LIKE BUTTON CODE FOR LIKE BUTTON.

$curr_url = $this->fb_social_pluginpreview['site'];
$fb_width = $this->fb_social_pluginpreview['fb_width'];
$fb_height = $this->fb_social_pluginpreview['fb_height'];
$widget_color_scheme = $this->fb_social_pluginpreview['widget_color_scheme'];
$widget_font = $this->fb_social_pluginpreview['widget_font'];
$widget_border_color = $this->fb_social_pluginpreview['widget_border_color'];
$show_header = $this->fb_social_pluginpreview['show_header'];
$recommend = $this->fb_social_pluginpreview['recommend'];
$connection = $this->fb_social_pluginpreview['connection'];
$show_stream = $this->fb_social_pluginpreview['show_stream'];
$fbpageurl = $this->fb_social_pluginpreview['fbpageurl'];

if ($plugin_type == 'activity_feed') {
	echo '<div><fb:activity site="' . $curr_url . '" width="' . $fb_width .'" height="' . $fb_height . '" colorscheme="' . $widget_color_scheme . '" font="' . $widget_font . '" border_color="' . $widget_border_color . '" header="' . $show_header .  '" recommendations="' . $recommend .'"></fb:activity></div>';
} 
else if ($plugin_type == 'facepile') {
	echo '<div><fb:facepile max-rows="' . $connection . '" width="' . $fb_width .'"></fb:facepile></div>';
}
else if ($plugin_type == 'likebox') {

	echo '<div><fb:like-box href="' . $fbpageurl . '" width="' . $fb_width .'" colorscheme="' . $widget_color_scheme . '" connections="' . $connection . '" header="' . $show_header . '" stream="' . $show_stream .  '"></fb:like-box></div>';
}
else if ($plugin_type == 'recommendation') {
	echo '<div><fb:recommendations site="' . $curr_url . '" width="' . $fb_width .'" height="' . $fb_height . '" colorscheme="' . $widget_color_scheme . '" font="' . $widget_font . '" border_color="' . $widget_border_color . '" header="' . $show_header .  '"></fb:recommendations></div>';
}
?>
</div>