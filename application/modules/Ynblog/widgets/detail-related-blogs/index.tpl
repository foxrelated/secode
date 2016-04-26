<ul class="ynblog-mode-views ynblog-related-blogs clearfix">
  <?php 
  	foreach( $this->blogs as $item )
    {
  		if ($item->checkPermission())
		{
			echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'comment'));
		}
	}
  	?>
</ul>
