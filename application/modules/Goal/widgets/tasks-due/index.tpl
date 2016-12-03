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

<h4 class="completed_tasks">
    <?php echo $this->translate("Tasks Due") ?>
</h4>

<?php if(count($this->completedTasks) > 0): ?>
<ul id="completed_tasks_list">
    <?php foreach($this->completedTasks as $task): ?>
    <li>
        <div>
       <?php if($this->viewer()->getIdentity() == $task->getOwner()->user_id): ?>      
            <span class="task_option">
            <?php echo $this->htmlLink(array('route' => 'task_specific', 'action' => 'complete', 'task_id' => $task->getIdentity()),
                  $this->translate('<button>Complete</button>'),
                  array('class' => 'smoothbox task_option_link')) ?>
            </span>
      <?php endif; ?> 
      <span class="task_title"><?php echo $task->title ?></span><br>
      <?php if($task->description) {  ?>
       <br><span class="task_title">Description: </span><br><?php echo html_entity_decode($task->description)
 ?>
      <?php }?>
      <?php if($task->notes) {  ?>
        <br> <span class="task_title">Notes: </span><br><?php echo html_entity_decode($task->notes) ?>
      <?php }?>
       
    
      
      
            <span class="task_date">
                <?php echo $this->translate('Due Date') ?>: <?php echo $this->locale()->toDate($task->duedate) ?>
            </span>
     <?php if($this->viewer()->getIdentity() == $task->getOwner()->user_id): ?>     
     <span class="task-edit-remove">
            <span class="task_edit">
                <?php echo $this->htmlLink(array('route' => 'task_specific', 'action' => 'edit', 'task_id' => $task->getIdentity()),
                  $this->translate('Edit'),
                  array('class' => 'smoothbox task_option_link', 'title' => 'Edit')) ?>
            </span>
                  
            <span class="task_delete">
                <?php echo $this->htmlLink(array('route' => 'task_specific', 'action' => 'delete', 'task_id' => $task->getIdentity()),
                  $this->translate('Delete'),
                  array('class' => 'smoothbox task_option_link', 'title' => 'Delete')) ?>
            </span>
      </span>
      <?php endif; ?>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
  <div class="tip">
    <span>
        <?php echo $this->translate('There are no tasks due.') ?>
    </span>
  </div>
<?php endif; ?>

