<ul class="ynidea_slideshow_container" id ="ynidea_slideshow_container">
 <li style="position:relative; overflow: hidden;">
 	<div id="slide-runner-widget" class='slideshow slideshow_ideas'>
	<?php
	 foreach($this->paginator as $idea):?>
	 <div class="slide featured_idea">
	 	<div style="clear: both">
		 	<div class="idea_photo">
		 		 <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.profile')) ?>
		 	</div>
		 	<div class="idea_info">
		 		<div class="title">
		 			<?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),20);
				     echo $this->htmlLink($idea->getHref(), $idea_title);
					 ?>
		 		</div>
		 		<div class="rate"></div>
		 			<span></span> <span></span>
		 		<div class="author">
		 			 <span><?php echo $this->translate('Author')?></span>: <span class=""><?php echo $idea->getOwner();?></span>
		 		</div>
		 		<div class="modification_date">
		 			 <span><?php echo $this->translate('Modification')?></span>: <span class=""><?php echo $this->timestamp($idea->modified_date);?></span>
		 		</div>
		 		<div class="feasibility">
		 			<span><?php echo $this->translate('Feasibility')?></span>: 
		 			<span class="idea_value">
		 			<?php	switch ($idea->feasibility) 
						  {
							  case 0:
								  echo $this->translate('Easy');
								  break;
							  case 1:
								  echo $this->translate('Slightly Complex');
								  break;
							  case 2:
								  echo $this->translate('Complex');
								  break;
							  case 3:
								  echo $this->translate('Very Complex');
								  break;
							  default:
								  echo $this->translate('Easy');
								  break;
						  }
				    ?>
		 			</span>
		 		</div>
		 		<div class="reproducible">
		 			<span><?php echo $this->translate('Reproducible')?></span>: <span class="idea_value"><?php echo $idea->reproducible?$this->translate('yes'):$this->translate('no');?></span>
		 		</div>
		 		<div class="ideal_score">
		 			<span><?php echo $this->translate('IdeaScore')?></span>: <span class="idea_value"><?php echo number_format($idea->ideal_score,2);?></span>
		 		</div>
		 		<div class="vote_ave">
		 			<span><?php echo $this->translate('AverageVote')?></span>: <span class="idea_value"><?php echo number_format($idea->vote_ave,2);?></span>
		 		</div>
		 	</div>
		</div>
		<br/>
		<div class="idea_description">
			<?php echo wordwrap(Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),250), 48, "\n", true); ?>
		</div>
		<div class="view_more"> <?php echo $this->htmlLink($idea->getHref(), $this->translate("View detail"));?></div>
	 </div>
	<?php endforeach;
	?>
	</div>
  </li>
</ul>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.slideshow_ideas').bxSlider({
            auto: true,
            touchEnabled: false,
            controls: false
        });
    });
</script>