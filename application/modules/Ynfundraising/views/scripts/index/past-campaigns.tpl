
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="ynfundraising_list_campaigns thumbs" style="margin-bottom: 15px;">
	<?php foreach($this->paginator as $item):?>
		<li>
			<?php echo $this->partial('_campaign_item.tpl', array('campaign' => $item));?>
		</li>
	<?php endforeach;?>
</ul>
  <?php elseif(isset($this->formValues['search'])): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no campaigns that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no campaign.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  ));
  ?>
