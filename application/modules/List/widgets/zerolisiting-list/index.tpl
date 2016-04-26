<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div class="tip">
	<span> <?php echo $this->translate('No Listings have been posted yet.'); ?>
		<?php if ($this->can_create): ?>
    <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'list_general') . '">', '</a>'); ?>
    <?php endif; ?>
  </span>
</div>