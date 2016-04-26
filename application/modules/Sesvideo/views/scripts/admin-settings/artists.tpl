<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: artists.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php $baseURL = $this->layout()->staticBaseUrl; ?>
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
     var ordering = { };
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
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var request = new Request.JSON({
      'url' : url,
      'method' : 'POST',
      'data' : ordering,
      onSuccess : function(responseJSON) {
      }
    });
    request.send();
  }
 
  function selectAll(){
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;

    for (i = 1; i < inputs.length - 1; i++) {
      if (!inputs[i].disabled) {
       inputs[i].checked = inputs[0].checked;
      }
    }
  }
  
  function multiDelete(){
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected artist?")) ?>');
  }
</script>

<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>

<div class='clear'>
  <div class="sesvideo_manage_artist_form settings">
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete-artists'));?>" onSubmit="return multiDelete()">
      <div>
        <h3><?php echo "Manage Artists"; ?></h3>
        <p><?php echo 'Here, you can add artists to your website by using the "Add New Artist" button below. You can also add about Artist and photo for any artists. <br /> Below, you can also choose any number of artists as Artist of the Day. These artists will be displayed randomly in the "Videos / Channels / Artist of the Day" widget.'; ?> </p>
        <br />
        <div>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'add-artist'), $this->translate('Add a New Artist'), array('class' => 'buttonlink smoothbox sesbasic_icon_add')); ?>
        </div><br />
        <?php if(count($this->paginator) > 0):?>
          <div class="sesvideo_manage_artist_form_head">
            <div style="width:5%">
              <input onclick="selectAll()" type='checkbox' class='checkbox'>
            </div>
            <div style="width:10%">
              <?php echo "Id";?>
            </div>
            <div style="width:30%">
              <?php echo "Name";?>
            </div>
            <div style="width:30%"  class="admin_table_centered">
              <?php echo "Of the Day";?>
            </div>
            <div style="width:25%">
              <?php echo "Options";?>
            </div>  
          </div>
          <ul class="sesvideo_manage_artist_form_list" id='menu_list'>
            <?php foreach ($this->paginator as $item) : ?>
              <li class="item_label" id="artists_<?php echo $item->artist_id ?>">
                <input type='hidden'  name='order[]' value='<?php echo $item->artist_id; ?>'>
                <div style="width:5%;">
                  <input name='delete_<?php echo $item->artist_id ?>_<?php echo $item->artist_id ?>' type='checkbox' class='checkbox' value="<?php echo $item->artist_id ?>_<?php echo $item->artist_id ?>"/>
                </div>
                <div style="width:10%;">
                  <?php echo $item->artist_id; ?>
                </div>
                <div style="width:30%;">
                  <?php echo $item->name ?>
                </div>
                <div style="width:30%;" class="admin_table_centered">
                  <?php if($item->offtheday == 1):?>  
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->artist_id, 'type' => 'sesvideo_artist', 'param' => 0), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/check.png', '', array('title'=> $this->translate('Edit Artist of the Day'))), array('class' => 'smoothbox')); ?>
                  <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-manage', 'action' => 'oftheday', 'id' => $item->artist_id, 'type' => 'sesvideo_artist', 'param' => 1), $this->htmlImage($baseURL . 'application/modules/Sesbasic/externals/images/icons/error.png', '', array('title'=> $this->translate('Make Artist of the Day'))), array('class' => 'smoothbox')) ?>
                  <?php endif; ?>
                </div>                
                <div style="width:25%;">          
                  <?php echo $this->htmlLink(
                    array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-settings', 'action' => 'edit-artist', 'artist_id' => $item->artist_id), $this->translate("Edit"), array('class' => 'smoothbox')) ?> |
                    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-settings', 'action' => 'delete-artist', 'artist_id' => $item->artist_id), $this->translate("Delete"), array('class' => 'smoothbox')) ?>
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
              <?php echo "You have not added any artists yet.";?>
            </span>
          </div>
        <?php endif;?>
      </div>
    </form>
  </div>
</div>