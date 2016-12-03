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

<ul id="goals_profile_tab" class='goals_browse'>
  <?php foreach( $this->paginator as $row ): ?>
  <?php //init
    $goal            =  $this->Item('goal',$row->goal_id);
    $gid             = $goal->getIdentity();
    $total_tasks     = $goal->totalTasks($gid);
    $completed_tasks = $goal->totalCompletedTasks($gid);

    //get tasks percentage
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
  
  <li>
      <div class="goals_photo">
        <?php echo $this->htmlLink($goal->getHref(), $this->itemPhoto($goal, 'thumb.icon')) ?>
      </div>
      <div class="goals_profile_tab_info">
        <div class="goals_profile_tab_title">
          <?php echo $this->htmlLink($goal->getHref(), $goal->getTitle()) ?>
        </div>
      </div>
        <div class="owner_title">
         <?php echo $this->translate('set by');?> <b><?php echo $goal->getOwner() ?></b>
         <?php if($goal->category_id > 0): ?>  
          in <a href="<?php echo $this->baseUrl() ?>/goals?category_id=<?php echo $goal->category_id?>" ><?php echo $goal->getCategory() ?></a>
        <?php endif; ?>
        </div> 

        <div class="goals_desc">
          <?php echo $this->string()->truncate($goal->getDescription(),80,'...') ?>
          
          
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
  <br/>
</ul>
<?php endif; ?>
