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

<?php if (!empty($this->list->sponsored) && $this->show_sponsered): ?>
	<div class="list_profile_sponsorfeatured" style='background: <?php echo $this->sponsored_color; ?>;'>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/star-img.png', '') ?>
		<?php echo $this->translate('SPONSORED'); ?>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/star-img.png', '') ?>
	</div>
<?php endif; ?>

<div class='list_photo <?php if ($this->can_edit):?>list_photo_edit_wrapper<?php endif;?>'>
	<?php if (!empty($this->can_edit)) : ?>
		<a class='list_photo_edit' href="<?php echo $this->url(array('action' => 'change-photo', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>">
			<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/edit_pencil.png', '') ?>
			<?php echo $this->translate('Change Picture'); ?>
		</a>
	<?php endif;?>
	<?php echo $this->itemPhoto($this->list, 'thumb.profile', '' , array('align' => 'center')); ?>
</div>

<?php if (!empty($this->list->featured) && $this->show_featured): ?> 
	<div class="list_profile_sponsorfeatured"  style='background: <?php echo $this->featured_color; ?>;'>
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/star-img.png', '') ?>
		<?php echo $this->translate('FEATURED');?>	
		<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/star-img.png', '') ?>
	</div>
<?php endif; ?>