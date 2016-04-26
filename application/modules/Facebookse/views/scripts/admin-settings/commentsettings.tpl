<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesetitngs.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class='seaocore_settings_form'>
	<?php $navigation_auth = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.navi.auth'); ?>
	<?php if (!empty($navigation_auth)) : ?>
	 <div class='settings'>
		<?php echo $this->form->render($this) ?>
	</div>
 <?php endif;?>
</div>
<div id="show_likepreview" style="display:none"></div>
<?php $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl();?>
<script type="text/javascript">
//<![CDATA[
var content_type = '<?php echo sprintf('%s', $this->content_type) ?>';
if (content_type != '') {
	window.addEvent('domready', function() {
		showcommentfields(<?php echo $this->commentsetting_showcomment; ?>);
		$('enable-1').addEvent('click', function () {

				showcommentfields (1);

		})

		$('enable-2').addEvent('click', function () {

				showcommentfields (1);

		})

	 $('enable-0').addEvent('click', function () {

				showcommentfields (0);

		})


	});

 var showcommentfields = function (show_commentfields) {
    if (show_commentfields == 1 || show_commentfields == 2) {
      $('commentbox_privacy-wrapper').style.display='block';
			$('commentbox_width-wrapper').style.display='block';
			$('commentbox_color-wrapper').style.display='block';
			$('preview').style.display='block';

    }
    else {
      $('commentbox_privacy-wrapper').style.display='none';
			$('commentbox_width-wrapper').style.display='none';
			$('commentbox_color-wrapper').style.display='none';
			$('preview').style.display='none';

   }
 }
}



var show_commentboxpreview = function () {
  var commentbox_maxcommentpost = '2';
	var commentbox_width = '500';
	var commentbox_color = 'light';

  if ($('commentbox_color')) {
    commentbox_color = $('commentbox_color').value;
   }

   if ($('commentbox_maxcommentpost')) {
    commentbox_maxcommentpost = $('commentbox_maxcommentpost').value;
   }

   if ($('commentbox_width')) {
    commentbox_width = $('commentbox_width').value;
   }



    var url =   en4.core.baseUrl+'admin/facebookse/settings/showcommentboxpreview';
		url += '?href="<?php echo $curr_url;?>"&commentbox_maxcommentpost=' + commentbox_maxcommentpost + '&commentbox_width=' + commentbox_width +'&commentbox_color=' + commentbox_color ;
		Smoothbox.open(url);


}

var fetchCommentSettings =function(pagelevel_id) {
	if (pagelevel_id != 0) {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/comments/'+pagelevel_id;
	}
	else {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/comments/';
	}

  }

//]]>
</script>