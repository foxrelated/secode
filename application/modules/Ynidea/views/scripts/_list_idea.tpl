<?php
/**
 * Show list idea
 */
?>
<ul class='ideas_browse ideas_list_tab'>
    <?php foreach( $this->arr_ideas as $idea ):?>
      <li>
        <div class="ideas_photo">
          <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.icon')) ?>
        </div> 
        
        <div class="ideas_options">
      		<span><?php //echo $this->translate('Modification date')?></span> <span style="font-weight: bold"><?php echo $this->timestamp($idea->modified_date); ?></span>
      		<br/>
      		<span><?php echo $this->translate('Voters')?></span>: <span style="font-weight: bold"><?php echO $idea->vote_count; ?></span>
      		<br/>
      		<span><?php echo $this->translate('Score')?></span>: <span style="font-weight: bold"><?php echo number_format($idea->ideal_score,1); ?></span>
    	</div>
        
        <div class="ideas_info">
          <div class="ideas_title">
            <div class="ideas_photo">
            <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),60);?>
            <?php echo $this->htmlLink($idea->getHref(), $idea_title);?>
            -
            <?php echo $idea->getOwner();?>
            </div>
          </div>              
          <div class="ideas_desc">
            <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),65); ?>
            <?php //echo $this->htmlLink($idea->getHref(), $this->translate("View more"));?>
          </div>
        </div>
        
      </li>
    <?php endforeach; ?>
</ul>
<div class="idea_view_all">
	<a href="<?php echo $this->url(array('orderby'=>$this->orderby),'ynidea_viewallideas')?>"><button><?php echo $this->translate("View all")?></button> </a>
</div>