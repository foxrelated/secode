<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesettings.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="headline">
	<h2><?php echo $this->translate('Likes');?></h2>
	<div class='tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
	</div>
</div>
<div class='layout_right'></div>
<div class='layout_middle'>
	<div class="usersetting">
		<?php  echo $this->form->render($this) ?>
	</div>
</div>
<style type="text/css">
.global_form div.form-label{display:none;}
</style>