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

<div id='goal_status'>
  <h2 class="goal_title">
    <?php echo $this->goal->getTitle() ?>
  </h2>
  <div class="goal_tasks_status">
      <span class="taskBy">
      <?php echo $this->translate('Task ')?> 
      <?php echo count($this->tasks_completed) ?>/<?php echo count($this->total_tasks) ?>
      </span>
      
     <?php if(!empty($this->subject()->endtime) ): ?>
        <span class="goal_dueDate"> 
        <?php echo $this->translate('Due Date ') ?>
        <?php echo $endTime = $this->locale()->toDate($this->subject()->endtime); ?>
        </span>         
               <? //days will show if goal is not completed// ?>
        <span class="days_left">    
               <?php if($this->goal->achieved == 0): ?>
               <?php  
                $end = strtotime($this->subject()->endtime);
                $dif = $end - strtotime('today');
                if($dif > 0){ //must not in negitive
                    $mins = $dif/60;
                    $hours = $mins/60;
                    $days = $hours/24;
                    echo ' ( '.round($days).' '.$this->translate(' days away ').' ) ';
                }
               ?>
        </span>       
              <?php else: ?>
        <span class="goal_achieved_date">       
                <?php echo ' Completed On: '.$this->locale()->toDate($this->subject()->ach_date); ?>
              <?php endif;?>
        </span>
     <?php endif ?>

     
     <?php
        //get tasks percentage
        $total_tasks = count($this->total_tasks);
        $completed_tasks = count($this->tasks_completed);
        $totalCompletedTasksInPercent = $completed_tasks / $total_tasks * 100;
        
        $roundedPercentage = round($totalCompletedTasksInPercent);
         
     ?>
     
     
     <div id="progressContainer">
            <div class="progress_bar">
                <div class="progress" style="width: <?php echo $roundedPercentage.'%'; ?>"></div>
            </div>
         <span class="percentage_title">
             <?php echo ' '.$roundedPercentage.'% Complete'; ?>
         </span>
    </div>
    <!-- <?php if($this->goal->achieved == 1): ?>
        <div class="goal_ach_status"><strong>Complete</strong></div>
     <?php else: ?>
        <div class="goal_ach_status"><strong>Incomplete</strong></div>
     <?php endif; ?> -->   
  </div>
    <?php if($this->subject()->description): ?>
    <div class="goal_desc">
        <h4>More About Goal</h4>
        <?php echo html_entity_decode($this->subject()->description) ?>
    </div>
    <?php endif; ?>
</div>