<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: editphotos.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="list_edit_wrapper">
	<h3> <?php echo $this->translate('Edit Listing Photos'); ?></h3>
	<?php echo $this->translate('Edit and manage the photos of your listing below.'); ?><br /><br />
	<div class="layout_middle">
		<div>
			<?php echo $this->htmlLink(array('route' => 'list_photoalbumupload','album_id' => $this->album_id,'package_id' => $this->package_id, 'listing_id' => $this->listing_id), $this->translate('Add New Photos'), array('class' => 'buttonlink icon_photos_new')) ?>
	  </div>
	
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator); ?>
		<?php endif; ?>
	
		<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
			<?php echo $this->form->album_id; ?>
			<ul class='lists_editphotos' id="photo">
				<?php if(!empty($this->count)): ?>
					<?php foreach ($this->paginator as $photo):?>
						<li>
							<div class="lists_editphotos_photo"> <?php echo $this->itemPhoto($photo, 'thumb.normal') ?> </div>
	            <div class="lists_editphotos_info">
								<?php
									$key = $photo->getGuid();
									echo $this->form->getSubForm($key)->render($this);
								?>
	
	              <div class="lists_editphotos_cover">
	                <input type="radio" name="cover" value="<?php echo $photo->file_id ?>" <?php if ($this->list->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
	              </div>
	
	              <div class="lists_editphotos_label">
	                <label><?php echo $this->translate('Main Photo'); ?></label>
	              </div>
	            </div>
	          </li>
	        <?php endforeach; ?>
	      <?php else:?><br />
	
					<div class="tip">
						<span>
							<?php echo $this->translate('There are currently no photos in this album. Click'); ?>
							<a href='<?php echo $this->url(array('listing_id' => $this->listing_id), 'list_photoalbumupload', true) ?>'  class=''><?php echo $this->translate('here'); ?></a>
							<?php echo $this->translate(' to add photos now!'); ?>
						</span>
					</div>
				<?php endif;?>
			</ul>
			<?php if(!empty($this->count)): ?>
				<?php echo $this->form->submit->render(); ?>
			<?php endif;?>
		</form>
		<?php if( $this->paginator->count() > 0 ): ?>
			<br />
			<?php echo $this->paginationControl($this->paginator); ?>
		<?php endif; ?>
	</div>
</div>	