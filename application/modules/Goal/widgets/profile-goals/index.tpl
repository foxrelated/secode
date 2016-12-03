<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
 ?>
<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('goals_profile_tab').getParent();
    $('profile_goals_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_goals_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_goals_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_goals_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>


<?php if( count($this->paginator) > 0 ): ?>

<ul id="goals_profile_tab" class='goals_browse'>
  <?php foreach( $this->paginator as $goal ): ?>
  <?php //init
    $gid = $goal->getIdentity();
    $total_tasks = $goal->totalTasks($gid);
    $completed_tasks = $goal->totalCompletedTasks($gid);
  ?>
  <li>
      <div class="goals_photo">
        <?php echo $this->htmlLink($goal->getHref(), $this->itemPhoto($goal, 'thumb.normal')) ?>
      </div>
      <div class="goals_profile_tab_info">
        <div class="goals_profile_tab_title">
          <?php echo $this->htmlLink($goal->getHref(), $goal->getTitle()) ?>
        </div>
      </div>
       <div class="goal_tasks_status">
        <?php echo $this->translate('Task ')?> 
        <?php echo $completed_tasks ?>/<?php echo $total_tasks ?>

        <?php if(!empty($goal->endtime) ): ?>
        <span class="goal_endTime"> 
          <?php echo $this->translate('Due Date ')?> 
          <?php echo $endTime = $this->locale()->toDate($goal->endtime); 
          
            $end = strtotime($goal->endtime);
            $dif = $end - strtotime('now');
            if($dif > 0){ //must not in negitive
                $mins = $dif/60;
                $hours = $mins/60;
                $days = $hours/24;
                echo ' ( '.round($days).' '.$this->translate(' days away ').' ) ';
            }
          ?>
       </span>
        <?php endif ?>
    <?php
        //get tasks percentage
        $totalCompletedTasksInPercent = $completed_tasks / $total_tasks * 100;
        $roundedPercentage = round($totalCompletedTasksInPercent);   
     ?>
  
  </div>
          <?php if($goal->getCategory()): ?>
        <div class="goals_category">
          Category: <?php echo $goal->getCategory() ?>
        </div>
          <?php endif; ?>
          
       <?php if($goal->getDescription()): ?>
        <div class="goals_desc">
          <?php echo $this->viewMore($goal->getDescription()) ?>
        </div>
       <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>


<div>
  <div id="profile_goals_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_goals_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>