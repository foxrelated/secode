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
<?php if( count($this->paginator) > 0 ): ?>

<ul id="goals_profile_tab" class='generic_list_widget'>
  <?php foreach( $this->paginator as $goal ): ?>
  <?php //init
    $gid = $goal->getIdentity();
    $total_tasks = $goal->totalTasks($gid);
    $completed_tasks = $goal->totalCompletedTasks($gid);
  ?>
  <li>
       <div class="photo">
        <?php echo $this->htmlLink($goal->getHref(), $this->itemPhoto($goal, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="goals_profile_tab_title">
          <?php echo $this->htmlLink($goal->getHref(), $goal->getTitle()) ?>
        </div>
         <div class="stats">
        <?php echo $this->translate('Task ')?> 
        <?php echo $completed_tasks ?>/<?php echo $total_tasks ?>

        <?php if(!empty($goal->endtime) ): ?>
        <span class="stats"> 
          <?php echo $this->translate('Due Date ')?> 
          <?php echo $endTime = $this->locale()->toDate($goal->endtime); ?>
       </span>
        <?php endif ?>
    <?php
        //get tasks percentage
        $totalCompletedTasksInPercent = $completed_tasks / $total_tasks * 100;
        $roundedPercentage = round($totalCompletedTasksInPercent);   
     ?>
        <div class="stats">
          <?php echo $this->translate('Set by');?>: <?php echo $goal->getOwner() ?>
        </div>
  </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
