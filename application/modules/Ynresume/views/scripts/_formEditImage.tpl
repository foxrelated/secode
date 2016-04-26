<?php if( $this->subject()->photo_id !== null ): ?>
  <div>
    <?php echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')) ?>
  </div>
  <br />
 

<?php endif; ?>