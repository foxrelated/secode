<?php
  $viewer = Engine_Api::_()->user()->getViewer();  
  $vote = Engine_Api::_()->ynidea()->getVote($this->idea->idea_id,$viewer->getIdentity());  
  
  $potential_plus = 0;
  $potential_minus = 0;
  $feasibility_plus = 0;
  $feasibility_minus = 0;
  $innovation_plus = 0;
  $innovation_minus = 0;
  
  if($vote){
      $potential_plus = $vote->potential_plus;
      $potential_minus = $vote->potential_minus;
      $feasibility_plus = $vote->feasibility_plus;
      $feasibility_minus = $vote->feasibility_minus;
      $innovation_plus = $vote->inovation_plus;
      $innovation_minus = $vote->inovation_minus;
  }    
  
  if($potential_plus == 1)
    $potential_plus_css = 'ynidea_box_plus_hover';
  else
    $potential_plus_css = 'ynidea_box_plus_acitve';
   
  if($potential_minus == 1)
    $potential_minus_css = 'ynidea_box_minus_hover';
  else
    $potential_minus_css = 'ynidea_box_minus_acitve';
    
  if($feasibility_plus == 1)
    $feasibility_plus_css = 'ynidea_box_plus_hover';
  else
    $feasibility_plus_css = 'ynidea_box_plus_acitve';
 
  if($feasibility_minus == 1)
    $feasibility_minus_css = 'ynidea_box_minus_hover';
  else
    $feasibility_minus_css = 'ynidea_box_minus_acitve';  
    
  if($innovation_plus == 1)
    $innovation_plus_css = 'ynidea_box_plus_hover';
  else
    $innovation_plus_css = 'ynidea_box_plus_acitve';   
    
   if($innovation_minus == 1)
    $innovation_minus_css = 'ynidea_box_minus_hover';
  else
    $innovation_minus_css = 'ynidea_box_minus_acitve'; 
?>
<form name="frm_vote" action="" method="post">
<div id="ynidea_box" class="ynideabox_votingbox">
    <div class="ynidea_box_row" style="padding: 5px">
        <div class="potential" style="width: 50%">
            <div><?php  echo $this->translate('Potential')?></div>
            <div><?php echo round($this->idea->potential_ave,2)*100; ?> %</div>
        </div>
        <div class="vote">
            <input type="button" id="_potential_plus" name="potential_plus" class="add <?php echo $potential_plus_css; ?>"/>
            <input type="button" id="_potential_minus" name="potential_minus" class="minus <?php echo $potential_minus_css; ?>"/>
        </div>
    </div>
    <div class="ynidea_box_row" style="padding: 5px">
        <div class="potential" style="width: 50%">
            <div><?php echo $this->translate('Feasibility')?></div>
            <div><?php echo round($this->idea->feasibility_ave,2)*100; ?> %</div>
        </div>
        <div class="vote">
            <input type="button" id="_feasibility_plus" name="feasibility_plus" class="add <?php echo $feasibility_plus_css; ?>"/>
            <input type="button" id="_feasibility_minus" name="feasibility_minus" class="minus <?php echo $feasibility_minus_css; ?>"/>
        </div>
    </div>
    <div class="ynidea_box_row" style="padding: 5px">
        <div class="potential" style="width: 50%">
            <div><?php echo $this->translate('Innovation') ?></div>
            <div><?php echo round($this->idea->innovation_ave,2)*100; ?> %</div>
        </div>
        <div class="vote">
            <input type="button" id="_innovation_plus" name="innovation_plus" class="add <?php echo $innovation_plus_css; ?>"/>
            <input type="button" id="_innovation_minus" name="innovation_minus" class="minus <?php echo $innovation_minus_css; ?>"/>
        </div>
    </div>
    <div class="ynidea_box_row">        
        <div class="submit">          
            <input id="btnSubmit" type="submit" name="submit" value="<?php echo $this->translate('Submit Vote')?>" 
            <?php if(!$this->permitvote): ?> disabled="true" style="cursor:default" <?php endif; ?> />  
            
            <input type="hidden" name="potential_plus" id="potential_plus" value="<?php echo $potential_plus;?>"/>
            <input type="hidden" name="potential_minus" id="potential_minus" value="<?php echo $potential_minus;?>"/>
            <input type="hidden" name="feasibility_plus" id="feasibility_plus" value="<?php echo $feasibility_plus; ?>"/>
            <input type="hidden" name="feasibility_minus" id="feasibility_minus" value="<?php echo $feasibility_minus; ?>"/>
            <input type="hidden" name="innovation_plus" id="innovation_plus" value="<?php echo $innovation_plus; ?>"/>
            <input type="hidden" name="innovation_minus" id="innovation_minus" value="<?php echo $innovation_minus;  ?>"/>         
        </div>
        <div style="padding: 3px; color: red">  
    	<span><?php echo $this->translate($this->message)?></span>
    	</div>
    </div>  
    
    <div class="ynidea_box_row total_score">
        <div class="potential">
            <div><?php echo $this->translate('Voters')?></div>
            <div><?php echo $this->idea->vote_count; ?></div>
        </div>
        <div class="vote">
            <div><?php echo $this->translate('Score')?></div>
            <div><?php echo $this->idea->ideal_score; ?></div>
        </div>
    </div>  
</div>
</form>

<script>

$('_potential_plus').addEvent('click', function(){
    $('potential_plus').value = 1;    
    $('potential_minus').value = 0;
    $('_potential_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active_hover.png)';
    $('_potential_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active.png)';
});
$('_potential_minus').addEvent('click', function(){
    $('potential_plus').value = 0;
    $('potential_minus').value = 1;
    $('_potential_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active.png)';
    $('_potential_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active_hover.png)';   
});
$('_feasibility_plus').addEvent('click', function(){
    $('feasibility_plus').value = 1;
    $('feasibility_minus').value = 0;       
    $('_feasibility_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active_hover.png)';
    $('_feasibility_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active.png)';
});
$('_feasibility_minus').addEvent('click', function(){
    $('feasibility_minus').value = 1;
    $('feasibility_plus').value = 0;
$('_feasibility_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active.png)';
    $('_feasibility_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active_hover.png)';
});
$('_innovation_plus').addEvent('click', function(){
    $('innovation_plus').value = 1;
    $('innovation_minus').value = 0;
    $('_innovation_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active_hover.png)';
    $('_innovation_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active.png)';
});
$('_innovation_minus').addEvent('click', function(){
    $('innovation_minus').value = 1;
    $('innovation_plus').value = 0;
$('_innovation_plus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/plus_active.png)';
    $('_innovation_minus').style.backgroundImage = 'url(application/modules/Ynidea/externals/images/minus_active_hover.png)';
});

$('btnSubmit').addEvent('click',function(){
    $('btnSubmit').readonly = 'true';
});

</script>
