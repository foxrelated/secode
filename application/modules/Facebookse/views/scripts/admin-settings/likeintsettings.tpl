<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likeintsettings.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">
function show_fbinstruct() {
	if ($('fb_site_likeint-1').checked) {
		Smoothbox.open('<div style="margin:10px;"><?php echo $this->string()->escapeJavascript($this->translate('With this integration, if a user clicks on the Facebook Like Button for an item, the item will also be liked on your site. The vice-versa will not happen, i.e., if the user clicks the Like button of the site, then the item will not get liked on Facebook. If the user clicks the Facebook Like Button to unlike an item, then that item will not get unliked on site.'));?></div><br /><center><button onclick="javascript:Smoothbox.close();">Ok</button></center>')
  }

 }
</script>

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
<div class='clear'>
  <div class='settings facebookse_likesetting_formL'>
		<?php echo $this->form->render($this) ?>
	</div>
</div>