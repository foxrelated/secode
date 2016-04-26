<div class="widget-metro-blog yn-block">
	<div class="yn-title"><?php echo $this -> translate($this -> title);?><div class="btn_link"><a href="<?php echo $this -> url(array(),'blog_general', true);?>"></a></div></div>
	<div class="yn-content">
		<?php foreach( $this->paginator as $item ): ?>
		<div class="yn-item">
			<div class="item-title"><a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle()?></a></div>
			<div class="item-posted-by"><?php
			            $owner = $item->getOwner();
			            echo $this->translate('by %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle()));
			          ?></div>
			<div class="item-desc"><?php echo $this->string()->truncate($this->string()->stripTags($item->body), 100) ?></div>
		</div>
		<?php endforeach; ?>
	</div>
</div>