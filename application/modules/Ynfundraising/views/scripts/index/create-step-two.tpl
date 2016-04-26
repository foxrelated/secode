<?php 
$menu = $this->partial('_menu.tpl', array());  
echo $menu;
?>

<div class="layout_left ynfundraising_create_right_menu ">
<?php 
$menu_create = $this->partial('_menu_create.tpl', array('active_menu'=>'step02','campaign_id'=>$this->campaign_id));  
echo $menu_create;
?>
</div>

<div class="layout_middle">

<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form_popup ynfundraising_browse_filters">
  <div style="width: 100%">
    <div style="width: 100%">
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
       <?php echo $this->form->video_url->render(); ?>
       <div style="font-weight: bold">
        <?php echo $this->translate("Photos") ?>
      </div>
      <div style = "margin-bottom:15px; margin-top:15px">
        <?php echo $this->htmlLink(array(
              'route' => 'ynfundraising_extended',
              'controller' => 'photo',
              'action' => 'upload',
              'campaign_id' => $this->campaign_id,
            ), $this->translate('Add More Photos'), array(
              'class' => 'buttonlink icon_photos_new'
          )) ?>
          </div>
      <div class="form-elements">
     <?php echo @$this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
     <?php if(Count($this->paginator) > 0): ?>
      <?php echo $this->form->store_id; ?>
      <ul class='ynfundraising_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <div class="ynfundraising_editphotos_photo">
              <?php echo $this->itemPhoto($photo, 'thumb.profile')  ?>
            </div>
            <div class="ynfundraising_editphotos_info">
              <div class="ynfundraising_editphotos_cover">
               <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->photo_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
              </div>
              <div class="ynfundraising_editphotos_label">
                <label><?php echo $this->translate('Main Photo');?></label>
              </div>
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
            </div>
            <br/>
          </li>
        <?php endforeach; ?>
      </ul>
       <?php echo $this->form->submit->render(); ?>
       <?php echo $this->form->cancel->render(); ?>
       <?php endif;?>
               </div>
    </div>
  </div>
</form>
       <?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
</div>

<script type="text/javascript">

en4.core.runonce.add(function(){
    if($('video_url'))
    {
      new OverText($('video_url'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
 });

</script>