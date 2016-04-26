<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Directory / Pages - Short Page URL Extension') ?></h2>
<?php  $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageurl.is.enable', 0);?>
<?php if(empty($is_element)):?>
	<div class="tip">
		<span>
			<?php echo $this->translate('This plugin enables you to set a limit for the number of Likes for a Page before the simplified short URL is assigned to it. This solves 2 purposes: One, more Likes of a Page would be indicative of its genuineness, and thus validity of its short URL. Second, a limit on Likes for these URLs to be valid for the respective Pages will motivate the Page Owners to gather more Likes for their Pages on your site.
If the short URL of any Page on your site is similar to the URL of a standard plugin page, then that URL will open that Page profile and not the standard plugin page. To avoid such a situation, edit the URL of such a Page using the “Manage Banned Page URLs” section.');?>
		</span>
	</div>
<?php endif;?>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php if(!empty($is_element)):?>
	<script type="text/javascript">
	window.addEvent('domready', function() {
			showurl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1) ?>");
			showediturl("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1) ?>");
		});	

		function showurl(option) {
			if(option == 1) {
				$('sitepage_likelimit_forurlblock-wrapper').style.display = 'block';
			}
			else {
				$('sitepage_likelimit_forurlblock-wrapper').style.display = 'none';
			}
		}

		function showediturl(option) {
			if(option == 1) {
				$('sitepage_edit_url-wrapper').style.display = 'block';
			}
			else {
				$('sitepage_edit_url-wrapper').style.display = 'none';
			}
		}

	</script>
<?php endif;?>