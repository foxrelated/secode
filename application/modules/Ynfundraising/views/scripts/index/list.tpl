<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynfundraising_filter_form').submit();
  }
</script>
<?php
 if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="ynfundraising_list_campaigns thumbs" style="margin-bottom: 15px;">
	<?php foreach($this->paginator as $item): ?>

		<li>
			<?php echo $this->partial('_campaign_item.tpl', array('campaign' => $item));?>
		</li>
	<?php endforeach;?>
</ul>
  <?php elseif($this->formValues['search']): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any campaigns that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any campaign.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  ));
  ?>
  <?php //echo $this->paginationControl($this->paginator,null, array("pagination/pagination.tpl","ynfundraising"));?>
