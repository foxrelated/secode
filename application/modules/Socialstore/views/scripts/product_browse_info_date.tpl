<div class="store_browse_info_date">
	<?php if(@$this->show_options['author']): ?>
	<?php echo $this->translate('Posted by %s', $this->item->getOwner()) ?>
	<?php endif; ?>
	<?php if(@$this->show_options['creation']): ?>
	<?php echo $this->timestamp($this->item->creation_date) ?>
	<?php endif; ?>
	<?php if(@$this->show_options['socialstore']): ?>
	<?php echo $this->translate('Store %s', $this->item->getStore()) ?>
	<?php endif; ?>
	<?php if(@$this->show_options['indexing'] == 'favourite'): ?>
	-
	<?php echo $this->translate(array('%s favourite', '%s favourites', $this->item->favourite_count), $this->locale()->toNumber($this->item->favourite_count)) ?>
	<?php endif; ?> 
	<?php if(@$this->show_options['indexing'] == 'view'): ?>
	-
	<?php echo $this->translate(array('%s view', '%s views', $this->item->view_count), $this->locale()->toNumber($this->item->view_count)) ?>
	<?php endif; ?>
	<?php if(@$this->show_options['indexing'] == 'sold'): ?>
	-
	<?php echo $this->translate(array('%s bought', '%s boughts', $this->item->sold_qty), $this->locale()->toNumber($this->item->sold_qty)) ?>
	<?php endif; ?>
</div>