<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: change-photo.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="layout_middle">
  <div class="global_form_popup">
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
	function removePhotoListing(url) {
		window.location.href=url;
	}
</script>