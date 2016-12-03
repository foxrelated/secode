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

function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected goals?") ?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate("Manage Goals") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("This page lists all of the goals entries your users have posted.") ?>
</p>
	
<br />

<br />

<?php if( count($this->paginator) > 0 ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Owner") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Tasks") ?></th>
        <th><?php echo $this->translate("Completed Tasks") ?></th>
        <th><?php echo $this->translate("Goal Achieved") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->goal_id;?>' value='<?php echo $item->goal_id ?>' /></td>
          <td><?php echo $item->goal_id ?></td>
          <td><?php echo $item->title ?></td>
          <td><?php echo $this->user($item->user_id)->getTitle(); ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td><?php echo $item->totalTasks($item->getIdentity()); ?></td>
          <td><?php echo $item->totalCompletedTasks($item->getIdentity()) ;?></td>
          <td><?php echo ( $item->achieved ? $this->translate('Complete') : $this->translate('In Progress') ) ?></td>
          
          <td>
            <a href="<?php echo $this->url(array('id' => $item->goal_id), 'goal_profile') ?>">
              <?php echo $this->translate("view") ?>
            </a>
            |
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->goal_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <br />

  <div class='buttons'>
    <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  </form>

  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no goals created by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
