<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
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


  
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()"> 
  <?php endif; ?>
  <div>
        <h3><?php echo "Manage Videos and Photos"; ?></h3>
        <p><?php echo $this->translate("This page lists all the videos and photos added by you for the HTML5 Video Background. Below, you can also add and manage any number of videos or photos. Each video or photo is highly configurable and you can add title, description, extra button to each slide. You can also choose to show Sign In and Sign Up buttons on each slide with signup form to non-logged in users.") ?>	 </p>
        <br />
        <div>
         <div class="sesbasic_search_reasult"><?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'create-slide','id'=>$this->gallery_id), $this->translate("Upload New Video or Photo"), array('class'=>'buttonlink sesbasic_icon_add')); ?>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'index'), $this->translate("Back to Manage HTML5 Video Background"), array('class'=>'sesbasic_icon_back buttonlink')); ?>
</div>
        </div>
        <?php if( count($this->paginator) ): ?>
  <div class="sesbasic_search_reasult">
    <?php echo $this->translate(array('%s video / photo found.', '%s videos / photos found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div><?php endif; ?>
        <?php if(count($this->paginator) > 0):?>
          <div class="sesvideo_manage_artist_form_head" style="width:100%;">
            <div style="width:5%">
              <input onclick='selectAll();' type='checkbox' class='checkbox' />
            </div>
            <div style="width:10%">
              <?php echo "Id";?>
            </div>
            <div style="width:50%">
             <?php echo $this->translate("Title") ?>
            </div>
            <div style="width:15%"  class="admin_table_centered">
             <?php echo $this->translate("Type") ?>
            </div>
            <div style="width:20%">
             <?php echo $this->translate("Options"); ?>
            </div>  
          </div>
          <ul class="sesvideo_manage_artist_form_list" id='menu_list' style="width:100%;">
            <?php foreach ($this->paginator as $item) : ?>
              <li class="item_label" id="slide_<?php echo $item->slide_id ?>">
                <input type='hidden'  name='order[]' value='<?php echo $item->slide_id; ?>'>
                <div style="width:5%;">
                  <input type='checkbox' class='checkbox' name='delete_<?php echo $item->slide_id;?>' value='<?php echo $item->slide_id ?>' />
                </div>
                <div style="width:10%;">
                  <?php echo $item->slide_id; ?>
                </div>
                <div style="width:50%;">
                  <?php echo $item->title ?>
                </div>
                <div style="width:15%;" class="admin_table_centered">
                  <?php echo strtolower($item->file_type) == 'mp4' ? 'Video' : 'Photo'; ?>
                </div>                
                <div style="width:20%;">          
                  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'create-slide', 'slide_id' => $item->slide_id,'id'=>$this->gallery_id), $this->translate("Edit"), array()) ?>
            |
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'delete-slide', 'id' => $item->slide_id, 'type' => strtolower($item->file_type) == 'mp4' ? 'Video' : 'Photo'),
                $this->translate("Delete"),
                array('class' => 'smoothbox')) ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class='buttons'>
            <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
          </div>
        <?php else:?>
          <div class="tip">
            <span>
              <?php echo "There are no slides created by you.";?>
            </span>
          </div>
        <?php endif;?>
      </div>
  <br />
  </form>
  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<script type="text/javascript"> 
  
  var SortablesInstance;

  window.addEvent('load', function() {
    SortablesInstance = new Sortables('menu_list', {
      clone: true,
      constrain: false,
      handle: '.item_label',
      onComplete: function(e) {
        reorder(e);
      }
    });
  });

 var reorder = function(e) {
     var menuitems = e.parentNode.childNodes;
     var ordering = {};
     var i = 1;
     for (var menuitem in menuitems)
     {
       var child_id = menuitems[menuitem].id;

       if ((child_id != undefined))
       {
         ordering[child_id] = i;
         i++;
       }
     }
 
    ordering['format'] = 'json';

    //Send request
    var url = '<?php echo $this->url(array("action" => "order")) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });
    request.send();
  }
</script>
<style type="text/css">
.sesvideo_manage_artist_form_head > div,
.sesvideo_manage_artist_form_list li > div{
	box-sizing:border-box;
}
</style>