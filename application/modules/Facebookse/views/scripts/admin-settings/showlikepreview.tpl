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
<div style="display:block;float:left;height:150px;width:500px;overflow:auto;" class="facebookse_socialplugin_priview">
<h3>
	<?php echo $this->translate('Your Facebook Like Button Preview') ?> 
</h3>
<?php
//GENERATING THE LIKE BUTTON CODE FOR LIKE BUTTON.

$like = $this->like_button['action'];
$showface = $this->like_button['show_faces'];
$send_button = $this->like_button['send_button'];
$font = $this->like_button['font'];
$color = $this->like_button['colorscheme'];
$layout = $this->like_button['layout'];
$curr_url = $this->like_button['href'];

?>
  

<?php echo '<div><fb:like href="' . $curr_url . '" layout="' . $layout .'" show_faces="' . $showface . '" width="320" action="' . $like . '" font="' . $font . '" colorscheme="' . $color . '" send="' . $send_button . '"></fb:like></div>';?>
</div>