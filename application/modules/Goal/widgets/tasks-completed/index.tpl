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
    <?php echo $this->translate("Tasks Completed") ?>
</h4>

<?php if(count($this->completedTasks) > 0): ?>
<ul id="completed_tasks_list">
    <?php foreach($this->completedTasks as $task): ?>
    <li>
        <div>
            <span class="task_title"><?php echo $task->title ?></span>
            <span class="task_completed_date">
                Completion Date: <?php echo $this->locale()->toDate($task->completed_date) ?>
            </span>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
  <div class="tip">
    <span>
        <?php echo $this->translate('There are no completed tasks yet.') ?>
    </span>
  </div>
<?php endif; ?>

