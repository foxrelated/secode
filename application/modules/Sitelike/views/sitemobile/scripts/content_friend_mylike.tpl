<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_friend_mylike.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (empty($this->isajaxrequest))	{ ?>
	<script type="text/javascript">
		var active_tab = '<?php echo $this->activetab;?>';
	</script>
<?php } ?>

<script type="text/javascript">
	var url = '<?php echo $this->url(array('module' => 'sitelike', 'controller' => 'index', 'action' => $this->urlAction), 'default', true) ?>';
</script>

<?php ?>

<?php 
$show_like_button = 0;
include_once APPLICATION_PATH . '/application/modules/Sitelike/views/sitemobile/scripts/_my-friends-likes.tpl';
?>