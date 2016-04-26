<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>
<script type="text/javascript">
function multiDelete()
{
  return confirm("<?php echo $this->translate("Are you sure you want to delete the selected slides ?") ?>");
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
<h3><?php echo "Manage HTML5 Video Background"; ?></h3>
<p>
	<?php echo $this->translate("This page lists all the HTML5 Video Backgrounds by you. Here, you can also add and manage any number of HTML5 Videos Backgrounds for your website. You can place these background videos anywhere on your website including the Landing Page and any other widgetized page of your choice.<br /><br />You can add and manage any number of Videos or Photos in each HTML5 Video Background. Each video or photo is highly configurable and you can add title, description, extra button to each slide. You can also choose to show Sign In and Sign Up buttons on each slide with signup form to non-logged in users.<br /><br />You can configure background video in the widget “HTML5 Video Background” from the Layout Editor.") ?>	
</p>
<br class="clear" />
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'create-gallery'), $this->translate("Create New HTML5 Video Background"), array('class'=>'smoothbox sesbasic_icon_add buttonlink')) ?>
</div>
<?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s HTML5 video background found.', '%s HTML5 video backgrounds found', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th align="center"><?php echo $this->translate("Total Video Backgrounds") ?></th>
        <th><?php echo $this->translate("Creation Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->gallery_id;?>' value='<?php echo $item->gallery_id ?>' /></td>
          <td><?php echo $item->gallery_id ?></td>
          <td><?php echo $item->gallery_name; ?></td>
           <td class="admin_table_centered"><?php echo $item->countSlide(); ?></td>
          <td><?php echo $item->creation_date; ?></td>  
          <td>
          	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'manage', 'id' => $item->gallery_id), $this->translate("Manage Videos"), array()) ?>
            |
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'create-gallery', 'id' => $item->gallery_id), $this->translate("Edit"), array('class' => 'smoothbox')) ?>
            |
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'delete-gallery', 'id' => $item->gallery_id),
                $this->translate("Delete"),
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
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no gallery by you.") ?>
    </span>
  </div>
<?php endif; ?>