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
    $coreLikesTable    = Engine_Api::_()->getDbtable('likes','core');
    $coreCommentsTable = Engine_Api::_()->getDbtable('comments','core');
?>
<?php if( count($this->paginator) > 0 ): ?>

<ul class='goals_browse'>
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
      <div class="goals_info">
        <div class="goals_title">
          <h3><?php echo $this->htmlLink($goal->getHref(), $goal->getTitle()) ?> </h3>
          <?php if($goal->getCategory()): ?>
          <div class="goals_category">
              
            <h3 style="font-size:12px; margin-top: 3px;">
                <span style="font-weight: normal;">&nbsp;in </span>
              <a  href="<?php echo $this->base_url ?>/goals?category_id=<?php echo $goal->getCategory()->getIdentity()?>" >
                <?php echo $goal->getCategory() ?>
              </a>
            </h3>
          </div>
          <?php endif; ?>
            <?php if($this->viewer()->getIdentity() == $goal->getOwner()->getIdentity()): ?>      
            <div class="goal-options">
            <span class="goal_edit">
                <?php echo $this->htmlLink(array('route' => 'goal_specific', 'action' => 'edit', 'goal_id' => $goal->getIdentity()),
                  $this->translate('Edit'),
                  array('class' => 'goal_manage_edit', 'title' => 'Edit')) 
                ?>
            </span>
            <span class="goal_delete">
                <?php echo $this->htmlLink(array('route' => 'goal_specific', 'action' => 'delete', 'goal_id' => $goal->getIdentity()),
                  $this->translate('Delete'),
                  array('class' => 'smoothbox goal_manage_delete', 'title' => 'Delete')) 
                ?>
            </span>
            </div>    
          <?php endif; ?> 
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
    <div id="progressContainer">
         <div class="progress_bar">
            <div class="progress" style="width: <?php echo $roundedPercentage.'%'; ?>"></div>
         </div>
         <span class="percentage_title">
             <?php echo ' '.$roundedPercentage.'%'; ?>
         </span>
    </div>
  
  </div>    
        <div class="goals_desc">
          <?php echo $this->viewMore($goal->getDescription()) ?>
        </div>
          
     
    <?php
   
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
           
    <div class="like_view_comments">
            <span class="browse_view_count" >
                <?php echo $this->translate(array('%s view', '%s views', count($goal->view_count)), $this->locale()->toNumber(count( $goal->view_count ))) ?>
            </span>
        - 
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

<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] )): ?>
<div class="tip">
    <span>
    <?php echo $this->translate('Nobody has created a goal with that criteria.');?>
    <?php if( $this->canCreate ): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'goal_general').'">', '</a>') ?>
    <?php endif; ?>
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


<script type="text/javascript">
  $$('.core_main_goal').getParent().addClass('active');
</script>