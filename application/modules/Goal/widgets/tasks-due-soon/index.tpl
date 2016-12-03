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

<h3 class="completed_tasks">
    <?php echo $this->translate("Tasks Due Soon") ?>
</h3>

<?php if(count($this->duetasks) > 0): ?>
<ul id="completed_tasks_list">
    <?php foreach($this->duetasks as $task): ?>
    <li>
        <div> 
            <span class="task_title">
              <?php echo $this->htmlLink($task->getOwner()->getHref(), $task->getTitle()) ?>
              <span style="font-weight: normal;">&nbsp;for </span> 
              <?php echo $task->getOwner()->getTitle() ?>
            </span>
            <?php if($this->viewer()->getIdentity() == $task->getOwner()->user_id): ?>      
            <span class="task_option">
            <?php echo $this->htmlLink(array('route' => 'task_specific', 'action' => 'complete', 'task_id' => $task->getIdentity()),
                  $this->translate('<button>Complete</button>'),
                  array('class' => 'smoothbox task_option_link')) ?>
            </span>
      <?php endif; ?>
            <span class="task_date">
                <?php echo $this->translate('Due Date') ?>: <?php echo $this->locale()->toDate($task->duedate) ?>
            </span>
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