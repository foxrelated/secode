<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: showlikepreview.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
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
<div class="global_form_popup">
	<div class="facebookse_socialplugin_priview" style="width:600px;height:400px;overflow:auto;">
		<h3><?php echo $this->translate('Your Facebook Comment Box Preview') ?></h3>
		<?php
		//GENERATING THE LIKE BUTTON CODE FOR LIKE BUTTON.
		$commentbox_maxcommentpost = $this->comment_box['commentbox_maxcommentpost'];
		$commentbox_width = $this->comment_box['commentbox_width'];
		$commentbox_color = $this->comment_box['commentbox_color'];
		?>
		<?php echo '<div><fb:comments href="http://developers.facebook.com/docs/reference/plugins/comments/ " num_posts="' . $commentbox_maxcommentpost .'" width="' . $commentbox_width . '" colorscheme="' . $commentbox_color . '" ></fb:comments></div>';?>
	</div>
</div>

