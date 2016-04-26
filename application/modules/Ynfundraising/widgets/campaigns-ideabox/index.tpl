<ul class="ynfundraising_list_campaigns thumbs">
	<?php foreach($this->campaigns as $item):?>
		<li>
			<?php echo $this->partial('_campaign_item.tpl', 'ynfundraising', array('campaign' => $item));?>
		</li>
	<?php endforeach;?>
</ul>
<div class="ynFRaising_viewAll">
	<?php echo $this->htmlLink(array('action'=>'list', 'route'=>'ynfundraising_general', 'status' => 'ongoing'), "<span>&rsaquo;</span>".$this->translate("View All"))?>
</div>