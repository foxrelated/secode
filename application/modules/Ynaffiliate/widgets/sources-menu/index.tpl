<ul class="ynaffiliate_quicklinks_menu">
	<li class="<?php echo $this->active_sources_menu=="static"?"active":""?>"" >
		<a href="<?php echo $this->url(array('controller'=>'sources','action'=>'index'),'ynaffiliate_extended',true) ?>" ><?php echo $this->translate('Suggest Links')?></a>
	</li>
	<li class="<?php echo $this->active_sources_menu=="dynamic"?"active":""?>"" >
		<a href="<?php echo $this->url(array('controller'=>'sources','action'=>'dynamic'),'ynaffiliate_extended',true) ?>" ><?php echo $this->translate('Dynamic Links')?></a>
	</li>	
  
</ul>
