<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-content.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
	<form method="post" class="global_form" action="<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'manage', 'action' => 'edit-content','content_id' => $this->content_id), 'admin_default', true);?>">
	   <?php echo $this->form->render($this) ?>
	 </form>
</div>
