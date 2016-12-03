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
<?php 
    $taskTable         = Engine_Api::_()->getDbtable('tasks','goal'); 
    $coreLikesTable    = Engine_Api::_()->getDbtable('likes','core');
    $coreCommentsTable = Engine_Api::_()->getDbtable('comments','core'); ?>
<?php if( count($this->paginator) > 0 ): ?>

<ul class='goals_browse'>
  <?php foreach( $this->paginator as $goal ): ?>
  
  <?php
        //get total tasks
        $tSel = $taskTable->select()
            ->where('goal_id = ?', $goal->getIdentity())
            ->where('user_id = ?', $goal->getOwner()->getIdentity())
            ;
    
        $total_tasks = $taskTable->fetchAll($tSel);
      
        //get completed tasks
        $tCompleteSel = $taskTable->select()
            ->where('goal_id = ?', $goal->getIdentity())
            ->where('user_id = ?', $goal->getOwner()->getIdentity())
            ->where('complete = ?', 1)
            ;
        $tasks_completed = $taskTable->fetchAll($tCompleteSel);
   
        //get tasks percentage
        $total_tasks = count($total_tasks);
        $completed_tasks = count($tasks_completed);
        $totalCompletedTasksInPercent = $completed_tasks / $total_tasks * 100;
        
        $roundedPercentage = round($totalCompletedTasksInPercent);
  
  ?>
  
    <li>
      <div class="goals_photo">
        <?php echo $this->htmlLink($goal->getHref(), $this->itemPhoto($goal, 'thumb.normal')) ?>
      </div>
      <div class="goals_info">
        <div class="goals_title">
          <h3><?php echo $this->htmlLink($goal->getHref(), $goal->getTitle()) ?></h3>
          <span class="goals_time"><?php echo 'Due Date: '.$this->locale()->toDate($goal->endtime) ?></span>
          <span class="goals_time"><?php echo 'Created: '.$this->locale()->toDate($goal->creation_date) ?></span>
        </div>
          
        <div class="owner_title">
         <?php echo $this->translate('set by');?> <b><?php echo $goal->getOwner() ?></b>
         <?php if($goal->category_id > 0): ?>  
          in <a href="<?php echo $this->baseUrl() ?>/goals?category_id=<?php echo $goal->category_id?>" ><?php echo $goal->getCategory() ?></a>
       <?php endif; ?>
        </div>   
        <div class="goals_status">
         <?php if($goal->achieved == 1): ?>   
          <?php echo $this->translate('status'); ?>: <?php echo $this->translate('Completed'); ?>
         <?php else: ?>
          <?php echo $this->translate('status'); ?>: <?php echo $this->translate('In progress'); ?> <?php echo '('.$roundedPercentage.'% '.$this->translate('Complete').')'; ?>
         <?php endif; ?>
        </div>
          
       <?php if($goal->getDescription()): ?>
        <div class="goals_desc">
          <?php echo $this->viewMore($goal->getDescription()) ?>
        </div>
       <?php endif; ?>
       
       
      <?php     
      
        //get total tasks
        $tSel = $taskTable->select()
            ->where('goal_id = ?', $goal->getIdentity())
            ->where('user_id = ?', $goal->getOwner()->getIdentity())
            ;
    
        $total_tasks = $taskTable->fetchAll($tSel);

      
      
        //get completed tasks
        $tCompleteSel = $taskTable->select()
            ->where('goal_id = ?', $goal->getIdentity())
            ->where('user_id = ?', $goal->getOwner()->getIdentity())
            ->where('complete = ?', 1)
            ;
        $tasks_completed = $taskTable->fetchAll($tCompleteSel);
 
        //get tasks percentage
        $total_tasks = count($total_tasks);
        $completed_tasks = count($tasks_completed);
        $totalCompletedTasksInPercent = $completed_tasks / $total_tasks * 100;
        
        $roundedPercentage = round($totalCompletedTasksInPercent);
        
        //get likes
        $likesSel = $coreLikesTable->select()
            ->where('resource_type = ?', 'goal')
            ->where('resource_id = ?', $goal->getIdentity())
            ;
        $goal_likes = $coreLikesTable->fetchAll($likesSel);
     
     
        //get likes
        $commentsSel = $coreCommentsTable->select()
            ->where('resource_type = ?', 'goal')
            ->where('resource_id = ?', $goal->getIdentity())
            ;
        $goal_comments = $coreCommentsTable->fetchAll($commentsSel);   
     ?>
       
     <div id="progressContainer" class="browse_progressbar">
            <span class="browse_likes_count" >
                <?php echo $this->translate(array('%s like', '%s likes', count($goal_likes)), $this->locale()->toNumber(count( $goal_likes))) ?>
            </span>
         -
            <span class="browse_likes_count" >
                <?php echo $this->translate(array('%s comment', '%s comments', count($goal_comments)), $this->locale()->toNumber(count( $goal_comments))) ?>
            </span>
    </div>
    </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] ) && $this->usersArray[0] != 0): ?>
<div class="tip">
    <span>
    <?php echo $this->translate('Nobody has created a goal with that criteria.');?>
    <?php if( $this->canCreate ): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'goal_general').'">', '</a>') ?>
    <?php endif; ?>
    </span>
</div> 
<?php elseif( $this->usersArray[0] == 0 ): ?>
<div class="tip">
    <span>
    <?php echo $this->translate('No goal found with that criteria.');?>
    </span>
</div> 
<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('There are no goals yet.') ?>
    <?php if( $this->canCreate): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'goal_general').'">', '</a>') ?>
    <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'query' => $this->formValues
)); ?>


<script type="text/javascript">
  $$('.core_main_goal').getParent().addClass('active');
</script>


