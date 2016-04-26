<ul class="ynfundraising_list_campaigns thumbs">
	<?php foreach($this->campaigns as $item):?>
	<li>
		<?php echo $this->partial(Engine_Api::_()->getApi('core', 'ynfundraising')->partialViewFullPath('_campaign_item.tpl'), array('campaign' => $item));?>
	</li>
	<?php endforeach;?>
	<div class="ynFRaising_viewAll">
		<?php echo $this->htmlLink(array('action'=>'list', 'route'=>'ynfundraising_general'), "<span>&rsaquo;</span>".$this->translate("View All"))?>
    </div>
</ul>