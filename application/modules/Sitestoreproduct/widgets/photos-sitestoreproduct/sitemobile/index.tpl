<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
 ?>
<?php if ($this->total_images): ?>
		<ul class="thumbs thumbs_nocaptions" id="profile_sitestorealbums">
			<?php foreach ($this->paginator as $album): ?>
				<li>
					<a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
					</a> 
				</li>
			<?php endforeach; ?>   
		</ul>
		<?php if ($this->paginator->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginator, $this->identity, 'profile_sitestorealbums', array('itemCount' => $this->itemCount));
			?>
		<?php endif; ?>
<?php else: ?>
	<div class="tip">
		<span>
				<?php echo $this->translate('You have not added any photo.');?>
		</span>
	</div>
<?php endif; ?>

<style type="text/css">

.layout_sitereview_photos_sitereview > h3 {
	display:none;
}

</style>