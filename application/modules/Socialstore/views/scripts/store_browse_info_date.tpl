<div class="store_browse_info_date">
	<?php if(@$this->show_options['author']): ?>
	<?php echo $this->translate('Posted by %s', $this->item->getOwner()) ?>
	<?php endif;?>
	<?php if(@$this->show_options['creation']): ?>
    <?php echo $this->timestamp($this->item->creation_date) ?>
    <?php endif; ?>
   	<?php if(@$this->show_options['indexing']=='follow'): ?>
    -
    <?php echo $this->translate(array('%s follow', '%s follows', $this->item->follow_count), $this->locale()->toNumber($this->item->follow_count)) ?>
    <?php endif; ?>
    <?php if(@$this->show_options['indexing']=='view'): ?>
    -
    <?php echo $this->translate(array('%s view', '%s views', $this->item->view_count), $this->locale()->toNumber($this->item->view_count)) ?>
    <?php endif; ?>
    <?php if(@$this->show_options['indexing']=='rated'): ?>
    -
    <?php echo $this->translate(array('%s star', '%s stars', $this->item->rate_ave), $this->locale()->toNumber($this->item->rate_ave)) ?>
    <?php endif; ?>
    <?php if(@$this->show_options['indexing']=='comment'): ?>
    -
    <?php echo $this->translate(array('%s comment', '%s comment', $this->item->comment_count), $this->locale()->toNumber($this->comment_count)) ?>
    <?php endif; ?>
</div>