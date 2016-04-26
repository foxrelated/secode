<?php
/**
 * Show list idea
 */
?>
<ul id="list_idea_<?php echo $this->tab;?>" class='ideas_browse ideas_list_tab'>
    <?php $viewer = Engine_Api::_()->user()->getViewer();
    foreach( $this->arr_ideas as $idea ):?>
      <li>
        <div class="trophy_ideas_photo">
          <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.normal')) ?>
        </div> 
        <div class="trophy_ideas_info">
          <div class="ideas_title">
            <div class="ideas_photo" style="width: 80%">
            <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),60);?>
            <?php echo $this->htmlLink($idea->getHref(), $idea_title);?>
             |
            <?php if($this->tab != '3'):?>
            	<span class="ynidea_score_<?php echo $idea->idea_id;?>">
					<?php echo $this->translate("Score: %s/10",number_format($idea->score,2)); ?>
				</span>
				<?php
				 else:
            		echo $this->translate("Public score: %s",$idea->ideal_score);
            	endif;?>
            </div>
            <div class="ideas_desc" style="width: 78%; float: left">
            <?php echo wordwrap(Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),200), 52, "\n", true); ?>
            <?php //echo $this->htmlLink($idea->getHref(), $this->translate("View more"));?>
          </div>
            <?php if(!$idea->checkAward($this->trophy_id)):?>  
            <div class="award_idea_<?php echo $idea->idea_id?>" class="" style="font-weight: bold; float: right">
            	<?php $trophy = engine_Api::_()->getItem('ynidea_trophy', $this->trophy_id);
            	if($trophy->status == 'voting' && $trophy->isOwner($viewer)):?>  
            	<div>  
	            	<?php echo $this->htmlLink(array(
	                  'action' => 'give-award',
	                  'id' => $idea->idea_id,
	                  'trophy_id' => $this->trophy_id,
	                  'route' => 'ynidea_specific',
	                  'reset' => true,
	                ), $this->translate("Give Award"), 
	                array('class'=>'buttonlink button_ynidea_give_award smoothbox')) ?>
	            </div>  
	            <?php endif;?>
	            <?php if($trophy->isOwner($viewer)):?>    
	            <div style="padding-top: 5px">   
	            	<?php echo $this->htmlLink(array(
	                  'action' => 'remove-nominee',
	                  'id' => $idea->idea_id,
	                  'trophy_id' => $this->trophy_id,
	                  'route' => 'ynidea_trophies',
	                  'reset' => true,
	                ), $this->translate("Remove"), 
	                array('class'=>'buttonlink menu_ynidea_delete smoothbox')) ?>
	            </div>  
	        	<?php endif;?>
	        </div>
        	<?php else:?>
        	<div class="award_idea_<?php echo $idea->idea_id?>" class="" style="float: left">	
        		<?php $award = $idea->checkAward($this->trophy_id)->award;
        		 if($award == 0):
				 ?>
				<span class="ynidea_glod_medal"></span>
        		<?php else: ?>
        		<span class="ynidea_silver_medal"></span>
        		<?php endif;?> 
        	</div>
        	<?php endif; ?>
          </div>              
        </div>
        <div class="voting_trophy">
        	<?php echo $this->partial('_judge_voting_box.tpl', 'ynidea', array(
                              'idea' => $idea,
                              'trophy_id' => $this->trophy_id,
                          ));?>
        </div>
        
      </li>
    <?php endforeach; ?>
</ul>
<div>
  <div id="idea_previous_<?php echo $this->tab;?>" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="idea_next_<?php echo $this->tab;?>" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function(){
    var anchor = $('list_idea_<?php echo $this->tab;?>').getParent();
    $('idea_previous_<?php echo $this->tab;?>').style.display = '<?php echo ( $this->arr_ideas->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('idea_next_<?php echo $this->tab;?>').style.display = '<?php echo ( $this->arr_ideas->count() <= $this->arr_ideas->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('idea_previous_<?php echo $this->tab;?>').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'ynidea/index/ajax-ideas/',
        data : {
          format : 'html',
          tab : <?php echo sprintf('%d', $this->tab);?>,
          trophy_id : <?php echo sprintf('%d', $this->trophy_id);?>,
          page : <?php echo sprintf('%d', $this->arr_ideas->getCurrentPageNumber() - 1) ?>
        },
        onComplete : function(response)
        {
        	$('tab_<?php echo $this->tab;?>').innerHTML = response;
        }
      }), {
        'element' : anchor
      })
    });

    $('idea_next_<?php echo $this->tab;?>').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'ynidea/index/ajax-ideas/',
        data : {
          format : 'html',
          trophy_id : <?php echo sprintf('%d', $this->trophy_id);?>,
          tab : <?php echo sprintf('%d', $this->tab);?>,
          page : <?php echo sprintf('%d', $this->arr_ideas->getCurrentPageNumber() + 1) ?>
        },
        onComplete : function(response)
        {
        	$('tab_<?php echo $this->tab;?>').innerHTML = response;
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>