<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if ($this->total_images > 0): ?>
  <script type="text/javascript">
    var previewFileForceOpen;
    var previewFile = function(event)
    {
      event = new Event(event);
      element = $(event.target).getParent('.admin_file').getElement('.admin_file_preview');
      			
      // Ignore ones with no preview
      if( !element || element.getChildren().length < 1 ) {
        return;
      }

      if( event.type == 'click' ) {
        if( previewFileForceOpen ) {
          previewFileForceOpen.setStyle('display', 'none');
          previewFileForceOpen = false;
        } else {
          previewFileForceOpen = element;
          previewFileForceOpen.setStyle('display', 'block');
        }
      }
      if( previewFileForceOpen ) {
        return;
      }

      var targetState = ( event.type == 'mouseover' ? true : false );
      element.setStyle('display', (targetState ? 'block' : 'none'));
    }

    window.addEvent('load', function() {
      $$('.slideshow-image-preview').addEvents({
        click : previewFile,
        mouseout : previewFile,
        mouseover : previewFile
      });
      $$('.admin_file_preview').addEvents({
        click : previewFile
      });
    });

    function changeSave(url){
      var finalOrder = [];
      var li = $('order-element').getElementsByTagName('li');
      for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      for (i = 0; i <= li.length; i++){
        if(finalOrder[i]!=origOrder[i])
        {
          changeOptionsFlag = true;
          break;
        }
      }
      					
      if(changeOptionsFlag == true && !saveFlag) { 
        var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the slides has been detected. If you click OK, all unsaved changes will be lost. Click Cancel to stay on this page and save your changes.")); ?>");
        if(!answer){
          Smoothbox.close();
        }
        else{
          setFlag(true);
          Smoothbox.open(url);
        }
        return answer;
      }
      else {
        Smoothbox.open(url);
      }
    }

    function uploadChangeSave(advancedslideshow_id, action, subject){
      var finalOrder = [];
      var li = $('order-element').getElementsByTagName('li');
      for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      for (i = 0; i <= li.length; i++){
        if(finalOrder[i]!=origOrder[i])
        {
          changeOptionsFlag = true;
          break;
        }
      }
      					
      if(changeOptionsFlag == true && !saveFlag) { 
        var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the slides has been detected. If you click OK, all unsaved changes will be lost. Click Cancel to stay on this page and save your changes.")); ?>");
        if(!answer){
          Smoothbox.close();
        }
        else{
          setFlag(true);
          //Smoothbox.open(url);
          window.location.href=en4.core.baseUrl +'admin/advancedslideshow/image/'+action+'/owner_id/1/advancedslideshow_id/'+advancedslideshow_id+'/subject/'+subject;
        }
        return answer;
      }
      else {
        //Smoothbox.open(url);
        window.location.href=en4.core.baseUrl +'admin/advancedslideshow/image/'+action+'/owner_id/1/advancedslideshow_id/'+advancedslideshow_id+'/subject/'+subject;
      }
    }

    function multiDelete()
    {
      var finalOrder = [];
      var li = $('order-element').getElementsByTagName('li');
      for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      for (i = 0; i <= li.length; i++){
        if(finalOrder[i]!=origOrder[i])
        {
          changeOptionsFlag = true;
          break;
        }
      }
      if(changeOptionsFlag == true) {
        var orderchange=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the slides has been detected. If you click OK, all unsaved changes will be lost. Click Cancel to stay on this page and save your changes.")); ?>");
      			
        if(orderchange){
          var doc= confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected slides ?")) ?>');
          if(doc == true)
            setFlag(true);
          return doc;
        }
        else {
          setFlag(false);
          return orderchange;
        }
      }
      else {
        return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected slides ?")) ?>');
      }
    }

    function selectAll()
    {
      var i;
      var multidelete_form = $('multidelete_form');
      var inputs = multidelete_form.elements;
      for (i = 1; i < inputs.length - 1; i++) {
        if (!inputs[i].disabled) {
          inputs[i].checked = inputs[0].checked;
        }
      }
    }
      		
    var saveFlag=false;
    var origOrder;
    var changeOptionsFlag = false;

    function setFlag(value){
      saveFlag=value;
    }
    window.addEvent('domready', function(){
      //         We autogenerate a list on the fly
      var initList = [];
      var li = $('order-element').getElementsByTagName('li');
      for (i = 1; i <= li.length; i++)
        initList.push(li[i]);
      origOrder = initList;
      var temp_array = $('order-element').getElementsByTagName('ul');
      temp_array.innerHTML = initList;
      new Sortables(temp_array);
    });

    window.onbeforeunload = function(event){
      var finalOrder = [];
      var li = $('order-element').getElementsByTagName('li');
      for (i = 1; i <= li.length; i++)
        finalOrder.push(li[i]);
      for (i = 0; i <= li.length; i++){
        if(finalOrder[i]!=origOrder[i])
        {
          changeOptionsFlag = true;
          break;
        }
      }
      						
      if(changeOptionsFlag == true && !saveFlag){ 
        var answer=confirm("<?php echo $this->string()->escapeJavascript($this->translate("A change in the order of the slides has been detected. If you click Cancel, all unsaved changes will be lost. Click OK to save change and proceed.")); ?>");
        if(answer) {
          document.multidelete_form.submit();
        }
      }
    }
  </script>
<?php endif; ?>

<style type="text/css">
  .add-pictures-wrapper {padding:15px 0 10px;}
  .add-pictures{
    background:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/add.png) no-repeat;
    font-weight:bold;
    padding-left:20px;
    margin-right:15px;
  }
  .slidshow_images_wrapper{
    -moz-border-radius:5px 5px 5px 5px;
    background:none repeat scroll 0 0 #E9F4FA;
    padding:10px;
    width:98%;
  }
  .slidshow_images_wrapper ul.admin_files {
    -moz-border-radius:5px 5px 5px 5px;
    background:none repeat scroll 0 0 #FFFFFF;
    border:1px solid #CCCCCC;
    height:auto;
    max-height:100%;
    overflow-x:hidden;
    overflow-y:auto;
  }
  #order-element li.slidshow-list{
    cursor: move;
  }
  #order-element li.slidshow-list:hover{
    background:#fbfbfb;
  }
  .slideshow-image-preview img{
    width:60px;
  }
</style>

<h2><?php echo $this->translate("Advanced Slideshow Plugin") ?></h2>
<?php $advancedslideshow = $this->advancedslideshow; ?>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'manage'), $this->translate('Back to Manage Slideshows'), array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<br /><br />

<?php if (count($this->paginator)): ?>	
  <h3><?php echo $this->translate("Manage Slides"); ?></h3>
  <p class="form-description"><?php echo $this->translate('Add slides to your slideshow by using "Add More Slides" below. You can also add/edit the captions and URLs for the slides by clicking on the edit link for each. You can delete slide by clicking delete button. You can order the slides in the slideshow by dragging them to the position in sequence below and clicking on the "Save Order" button. You can also enable/disable the slides by clicking on the status for each.'); ?></p> 

  <div>
    <?php echo $this->translate('You are managing slides of the slideshow: %s', $this->advancedslideshow->widget_title) ?> </a>
  <?php if ($advancedslideshow['slideshow_type'] == 'noob')
    echo $this->translate('</br>You can create an HTML slide that can contain more than 1 image by clicking on “Create HTML Slide” link below.'); ?>
  </div>

  <br />

  <div class="tip">
    <span>
      <?php
      echo $this->translate(" If you are unable to upload slides using FancyUploader, then try uploading slides using ") . $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'advancedslideshow',
          'controller' => 'image',
          'action' => 'simple-upload',
          'owner_id' => 1,
          'advancedslideshow_id' => $this->advancedslideshow_id,
          'subject' => $this->advancedslideshow->getGuid(),
              ), $this->translate('basic uploader.'), array(
              //'class' => 'smoothbox'
      ));
      ?>
    </span>
  </div>

  <div class="add-pictures-wrapper">
    <a href="javascript:void(0);" onclick='uploadChangeSave("<?php echo $this->advancedslideshow_id ?>", "upload", "<?php echo $this->advancedslideshow->getGuid(); ?>")' class="add-pictures"><?php echo $this->translate('Add More Slides') ?></a>

    <a href="javascript:void(0);" onclick='uploadChangeSave("<?php echo $this->advancedslideshow_id ?>", "simple-upload", "<?php echo $this->advancedslideshow->getGuid(); ?>")' class="add-pictures"><?php echo $this->translate('Add More Slides Using Basic Uploader') ?></a>

    <?php
    if ($advancedslideshow['slideshow_type'] == 'noob')
      echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'image', 'action' => 'noob-slide', 'advancedslideshow_id' => $this->advancedslideshow_id), $this->translate("Create HTML Slide"), array('class' => 'add-pictures'));
    ?>
  </div>

  <div class="slidshow_images_wrapper">
    <div class="admin_files_pages">
      <?php $pageInfo = $this->paginator->getPages(); ?>
      <?php echo $this->translate("Showing "); ?><?php echo $pageInfo->firstItemNumber ?>-<?php echo $pageInfo->lastItemNumber ?><?php echo $this->translate(" of "); ?><?php echo $pageInfo->totalItemCount ?><?php echo $this->translate(" slides") ?>.
    </div>
    <form id='multidelete_form' name='multidelete_form' method="post" action="<?php echo $this->url(array('action' => 'multi-delete')); ?>" >
      <ul class="admin_files">
        <table class='admin_table' width='100%'>
          <thead>
            <tr>
              <th width="5%" align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
              <?php if ($advancedslideshow['slideshow_type'] == 'noob') : $noobtempflag = 1; ?>  <th width="5%" align="left"><?php echo $this->translate('Id'); ?></th> <?php endif; ?>
              <th width="10%" align="left"><?php echo $this->translate('Photo'); ?></th>
              <th width="<?php
            if ($advancedslideshow['slideshow_type'] == 'noob') : echo '15%';
            else: echo '20%';
            endif;
              ?>" align="center"><?php echo $this->translate('Caption'); ?></th>
              <th width="10%" align="left"><?php echo $this->translate('URL'); ?></th>
              <th width="%" align="center"><?php echo $this->translate('Status'); ?></th>
              <th width="20%" align="left"><?php echo $this->translate('Options'); ?></th>
            </tr>
          </thead>
          <tbody>
        </table>

        <div class="seaocore_admin_order_list" id='order-element'>
          <ul>
            <?php
            foreach ($this->paginator as $item): $i = 0;
              $i++;
              $id = 'admin_file_' . $i;
              $contentKey = $item->image_id;
              ?>
              <li class="slidshow-list"> 
                <input type='hidden' name='image_id[]' value='<?php echo $item->image_id; ?>'>
                <table class='admin_table' width='100%'>
                  <tr>
                    <td width="5%">
                      <input name='delete_<?php echo $item->image_id; ?>' type='checkbox' class='checkbox' value="<?php echo $item->image_id ?>"/>
                    </td>
                    <?php if ($advancedslideshow['slideshow_type'] == 'noob') : ?>
                      <td width="5%">
                        <?php echo $noobtempflag;
                        $noobtempflag++; ?>
                      </td>
                    <?php endif; ?>
                    <?php if (empty($item->slide_html)): ?>
                      <td width="10%">
                    <li class="admin_file admin_file_type_image" id="<?php echo $id ?>">
                      <div class="slideshow-image-preview">
                        <?php //$listing_image_id = $item->image_id + 1; ?>
                        <img class="thumb_normal item_photo_advancedslideshow_image  thumb_normal" alt="" src="<?php echo $item->getPhotoUrl('thumb.normal'); ?>">
                      </div>
                      <div class="admin_file_preview admin_file_preview_image" style="display:none">
                        <img class="thumb_icon item_photo_advancedslideshow_image  thumb_icon" alt="" src="<?php echo $item->getPhotoUrl(); ?>">
                      </div>
                    </li>
                    </td>
                    <?php
                  else:
                    $noobSlideArray = @unserialize($item->slide_html);
                    ?>

                    <td width="10%">
                    <li class="admin_file admin_file_type_image" id="<?php echo $id ?>">
                      <div class="slideshow-image-preview">
                        <?php
                        if (!empty($noobSlideArray['thumb_id'])):
                          $noobUrl = Engine_Api::_()->advancedslideshow()->displayPhoto($noobSlideArray['thumb_id'], 'thumb.normal');
                        else:
                          $noobUrl = null; // $this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/nonoob_slideshow_thumb_icon.png';                                            
                        endif;
                        if (!empty($noobUrl)):
                          ?>

                          <img class="thumb_normal item_photo_advancedslideshow_image  thumb_normal" alt="" src="<?php echo $noobUrl; ?>" />
                        <?php else: echo "---";
                        endif; ?>
                      </div>
                      <?php if (!empty($noobUrl)): ?>
                        <div class="admin_file_preview admin_file_preview_image" style="display:none">
                          <img class="thumb_icon item_photo_advancedslideshow_image  thumb_icon" alt="" src="<?php echo $noobUrl; ?>" />
                        <?php endif; ?>
                      </div>
                    </li>                                      
                    </td>
                  <?php
                  endif;
                  ?>
                  <td width="<?php
              if ($advancedslideshow['slideshow_type'] == 'noob') : echo '15%';
              else: echo '20%';
              endif;
                  ?>" style="text-align:center;">
                      <?php if (empty($item->caption)): ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/caption_false.gif" title="<?php echo $this->translate('Caption Not Set'); ?>">
                    <?php else: ?>
                      <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/caption_true.gif" title="<?php echo $this->translate('Caption Set'); ?>">
                    <?php endif; ?>
                  </td>

                  <td width="25%">
                    <?php if (!empty($item->url)): ?>
                      <a href = "<?php echo "http://" . $item->url ?>" target="_blank" title="<?php echo "http://" . $item->url ?>"><?php echo "http://" . $item->truncate60Url(); ?></a>

                    <?php elseif (!empty($item->params) && $this->advancedslideshow->resource_type == 'sitestoreproduct_category' && !empty($this->advancedslideshow->resource_id) && ($url = Engine_Api::_()->advancedslideshow()->getParamsUrl($item->params)) != ''): ?>

                      <?php echo $this->htmlLink($url, Engine_Api::_()->seaocore()->seaocoreTruncateText($url, 60), array('target' => '_blank', 'title' => $url)); ?>

                    <?php else: ?>		      					
                      ---	
                    <?php endif; ?>
                  </td>

                  <?php if ($item->enabled == 1): ?>
                    <td width="20%">
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'enabled', 'image_id' => $item->image_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/caption_true.gif', '', array('title' => $this->translate('Disable Slide')))) ?> 
                    </td>
                  <?php else: ?>
                    <td width="20%">
                      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'enabled', 'image_id' => $item->image_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/caption_false.gif', '', array('title' => $this->translate('Enable Slide')))) ?>
                    </td>
                  <?php endif; ?>

                  <td width="20%" class="admin_table_options">
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'visibility', 'slideshow_id' => $item->advancedslideshow_id, 'image_id' => $item->image_id), $this->translate('visibility'), array('class' => 'smoothbox')) ?>
                    | 
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'edit', 'image_id' => $item->image_id), $this->translate('edit'), array()) ?>
                    <?php if ($advancedslideshow['slideshow_type'] == 'noob' && !empty($item->slide_html))
                      echo " | " . $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'image', 'action' => 'edit-noob-slide', 'advancedslideshow_id' => $item->advancedslideshow_id, 'image_id' => $item->image_id), $this->translate('edit slide HTML'), array()) ?>
                    | 
                    <a href="javascript:void(0);" onclick='changeSave("<?php echo $this->url(array('module' => 'advancedslideshow', 'controller' => 'image', 'action' => 'remove', 'advancedslideshow_id' => $item->advancedslideshow_id, 'image_id' => $item->image_id), 'admin_default') ?>")'><?php echo $this->translate('delete') ?></a>

                  </td>
                  </tr>
                </table>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </ul>
      <div style="height:10px;"></div>
      &nbsp;<button type='submit' name="delete" onclick="return multiDelete()" value="delete_image"><?php echo $this->translate('Delete Selected'); ?></button>	&nbsp;&nbsp;&nbsp;
      <button name="order" id="order" type="submit" value="save_order" onClick="setFlag(true);"><?php echo $this->translate('Save Order'); ?></button>
    </form>	
  </div>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php
      echo $this->translate('You have not yet created a slide for your slideshow. Get started by ') . $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'advancedslideshow',
          'controller' => 'image',
          'action' => 'upload',
          'owner_id' => 1,
          'advancedslideshow_id' => $this->advancedslideshow_id,
          'subject' => $this->advancedslideshow->getGuid(),
              ), $this->translate('creating some.'), array(
              //'class' => 'smoothbox'
      )) . $this->translate(" If you are unable to upload slides using FancyUploader, then try uploading slides using ") . $this->htmlLink(array(
          'route' => 'admin_default',
          'module' => 'advancedslideshow',
          'controller' => 'image',
          'action' => 'simple-upload',
          'owner_id' => 1,
          'advancedslideshow_id' => $this->advancedslideshow_id,
          'subject' => $this->advancedslideshow->getGuid(),
              ), $this->translate('basic uploader.'), array(
              //'class' => 'smoothbox'
      ));

      if ($advancedslideshow['slideshow_type'] == 'noob')
        echo $this->translate(" If you want to create a custom slide containing more than 1 image, then ") . $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'image', 'action' => 'noob-slide', 'advancedslideshow_id' => $this->advancedslideshow_id), $this->translate("click here"), array()) . ".";
      ?>
    </span>
  </div>	
<?php endif; ?>