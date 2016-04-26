<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">

<?php //echo $this->form->render($this) ?>
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form store_browse_filters">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
      <div style = "margin-bottom:15px">
        <?php echo $this->htmlLink(array(
              'route' => 'socialstore_extended',
              'controller' => 'store-photo',
              'action' => 'upload',
              'store_id' => $this->store_id,
            ), $this->translate('Add More Photos'), array(
              'class' => 'buttonlink icon_store_photo_new'
          )) ?>
          </div>
      <div class="form-elements">
     <?php echo $this->form->getDecorator('FormErrors')->setElement($this->form)->render();?>
     <?php if(Count($this->paginator) > 0): ?>
      <?php echo $this->form->store_id; ?>
      <ul class='store_editphotos'>        
        <?php foreach( $this->paginator as $photo ): ?>
          <li>
            <div class="store_editphotos_photo">
              <?php echo $this->itemPhoto($photo, 'thumb.normal')  ?>
            </div>
            <div class="store_editphotos_info">
              <?php
                $key = $photo->getGuid();
                echo $this->form->getSubForm($key)->render($this);
              ?>
              <div class="store_editphotos_cover">
                <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->storephoto_id == $photo->file_id ): ?> checked="checked"<?php endif; ?> />
              </div>
              <div class="store_editphotos_label">
                <label><?php echo $this->translate('Main Photo');?></label>
              </div>
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