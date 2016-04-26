
<ul class="ynidea_slideshow_container" id ="ynidea_slideshow_container">
 <li style="position:relative; overflow: hidden;">
 	<div id="slide-runner-widget" class="slideshow slideshow_trophies">
	<?php
	 foreach($this->paginator as $trophy):?>
	 <div class="slide featured_idea">
	 	<div style="clear: both">
		 	<div class="idea_photo">
		 		 <?php echo $this->htmlLink($trophy->getHref(), $this->itemPhoto($trophy, 'thumb.profile')) ?>
		 	</div>
		 	<div class="idea_info">
		 		<div class="title">
		 			<?php $trophy_title = Engine_Api::_()->ynidea()->subPhrase($trophy->getTitle(),40);
				     echo $this->htmlLink($trophy->getHref(), $trophy_title);
					 ?>
		 		</div>
		 		<div class="rate"></div>
		 			<span></span> <span></span>
		 		<div class="author">
		 			 <span><?php echo $this->translate('Creator')?></span>: <span class=""><?php echo $trophy->getOwner();?></span>
		 		</div>
		 		<div class="modification_date">
		 			 <span><?php echo $this->translate('Created date')?></span>: <span class=""><?php echo $this->timestamp($trophy->creation_date);?></span>
		 		</div>
		 		<div class="nominees">
		 			<span><?php echo $this->translate('Nominees')?></span>: <span class="idea_value"><?php echo $trophy->getNominees()?></span>
		 		</div>
		 		<div class="judges">
		 			<span><?php echo $this->translate('Judges')?></span>: <span class="idea_value"><?php echo $trophy->getJudges()?></span>
		 		</div>
		 		
		 	</div>
		</div>
		<br/>
		<div class="idea_description">
			<?php echo wordwrap(Engine_Api::_()->ynidea()->subPhrase(strip_tags($trophy->description),250), 48, "\n", true); ?>
		</div>
		<div class="view_more"> <?php echo $this->htmlLink($trophy->getHref(), $this->translate("View detail"));?></div>
	 </div>
	<?php endforeach;
	?>
	</div>
  </li>
</ul>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.slideshow_trophies').bxSlider({
            auto: true,
            touchEnabled: false,
            controls: false
        });
    });
</script>