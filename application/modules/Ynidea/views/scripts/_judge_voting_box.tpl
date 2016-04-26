<?php

$trophy_id = $this->trophy_id;  
$trophy = Engine_Api::_()->getItem('ynidea_trophy', $trophy_id);  
$viewer = Engine_Api::_()->user()->getViewer();  
$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$idea_id = $this->idea->idea_id;

$flag = true;     
// check enable checkTrophyVote($trophy_id) 
if(!Engine_Api::_()->ynidea()->checkTrophyVote($trophy_id))
    $flag = false;
    
// check voted checkTrophyExistedVote($trophy_id,$idea_id,$user_id)    
//if(Engine_Api::_()->ynidea()->checkTrophyExistedVote($trophy_id,$idea_id,$user_id))
//    $flag = false;
   
 //check is judge    checkIsJudge($trophy_id,$user_id)    
if(!Engine_Api::_()->ynidea()->checkIsJudge($trophy_id,$user_id))
    $flag = false;

if(!Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($trophy, $viewer, 'vote')->checkRequire())
	$flag = false;
?>
<form name="frm_vote" action="" method="post">
<div id="ynidea_box" class="ynideabox_judges">
	<?php if(Engine_Api::_()->ynidea()->checkIsJudge($trophy_id,$user_id)):?>
	<?php
            $judge_vote =  Engine_Api::_()->ynidea()->getTrophyVote($trophy_id,$idea_id,$user_id);
            $judge_vote = ($judge_vote == "")? 0 : $judge_vote;
            $judge_vote = number_format($judge_vote,0);                
    ?>
    <div class="ynidea_box_row">
        <div class="potential">
            <div>
            	<?php echo $this->translate("Your vote"); ?>:
            	<input class="ynidea_myjudgevote_<?php echo $idea_id;?>" <?php if(!$flag) echo 'readonly="true"'?> onkeyup="numberChange(this)" type="text" name="judge_point_<?php echo $idea_id;?>" id="judge_point_<?php echo $idea_id;?>" value="<?php echo $judge_vote; ?>" style="width:25px;"/>
            	/10
            </div>
        </div>
    </div>
    
    <div class="ynidea_box_row">        
        <div class="submit">            
            <?php if($flag):?>      
                <input type="button" onclick="judge_vote(<?php echo $trophy_id;  ?>,<?php echo $idea_id;  ?>,<?php echo $user_id;  ?>);" id="yn_judge_voting_<?php echo $idea_id;?>" name="yn_judge_voting" value="<?php echo $this->translate('Update');?>" />               
            <?php else:?>
                <input class="ynjudge_voting_disabled" type="button" id="yn_judge_voting_<?php echo $idea_id;?>" name="yn_judge_voting" value="<?php echo $this->translate('Update');?>" />
            <?php endif;?>
            <input type="hidden" name="idea_id" id="idea_id" value="<?php echo $this->idea->idea_id;  ?>"/>
                   
        </div>
    </div> 
    <div class="ynidea_box_row total_score">
    <?php else:?>
    <div class="ynidea_box_row">
    <?php endif; ?> 
        <div class="potential" style="width: 50%">
            <div><?php echo $this->translate("Voters"); ?></div>
            <?php
           
               $judges = Engine_Api::_()->ynidea()->getCountJudge($trophy_id);
               $judges = ($judges == "")? 0 : $judges;
               
               $voters = Engine_Api::_()->ynidea()->getCountJudgeVote($trophy_id,$idea_id);
               $voters = ($voters == "")? 0 : $voters;
               
               $ynvoters = $voters."/".$judges;     
               
               $score = Engine_Api::_()->ynidea()->getScoreJudge($trophy_id,$idea_id);
			   if($voters > 0)
               		$score = number_format($score/$voters,2)."/10";
			   else
			   		$score = number_format(0 ,2)."/10";
               
            ?>
            <div class="ynidea_voters_<?php echo $idea_id;?>"><?php echo $ynvoters; ?></div>
        </div>
        <div class="vote" style="width: 50%">
            <div><?php echo $this->translate("Score");?></div>
            <div class="ynidea_score_<?php echo $idea_id;?>"><?php echo $score; ?></div>
        </div>
    </div>  
</div>
</form>

<script>

function numberChange(e)
{
	function showMessage(text){
		alert(text);
	}
    var numbers = e.value;
    var flag = true;
    if(numbers != "")
    {
        if(isNumber(numbers))
        {
          numbers = parseFloat(numbers);      
		  e.value = numbers;
		    if(numbers < 0 || e.value == '' || numbers > 10)
            {               
               showMessage('<?php echo $this->translate("Number is invalid!") ?>');
               flag = false;
            }					  
		}
        else
        {   
        	showMessage('<?php echo $this->translate("Number is invalid!") ?>');
            flag = false;
        }
    }
    if(flag == false)
    {   
        e.value = '';
        e.focus();        
    }
    return flag;    
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
    
</script>

<script>


function judge_vote(trophy_id,idea_id,user_id){   
    var point = $('judge_point_'+idea_id).value;    
    var request = new Request.JSON({
			'url' : en4.core.baseUrl + 'trophies/judge-vote',
			'data' : {
				'format' : 'json',
                'trophy_id':trophy_id,
				'idea_id' : idea_id,
				'user_id' : user_id,
                'point' : point
			},
             //onSuccess
			'onComplete': function(response) {		  
			   	if (response.success == 1) {            	   
				   $$('.ynidea_myjudgevote_'+idea_id).set('value',response. myvote);		
                   $$('.ynidea_voters_'+idea_id).set('text',response.voters);
                   $$('.ynidea_score_'+idea_id).set('text',response.score);                   			
				}				
			}
		});
		request.send();
}

</script>
