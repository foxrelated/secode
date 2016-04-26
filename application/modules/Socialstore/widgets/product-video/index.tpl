<?php 
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
?>
  

<h2>
  <?php echo $this->video->getTitle() ?>
</h2>

<div class="ynstore_video_view ynstore_video_view_container">
  <div class="ynstore_video_desc">
    <?php echo $this->translate('Posted by') ?>
    <?php echo $this->htmlLink($this->video->getParent(), $this->video->getParent()->getTitle()) ?>
  </div>
  <div class="ynstore_video_desc">
    <?php echo $this->video->description;?>
  </div>
  <?php if( $this->video->type == 3 ): ?>
  <div id="video_embed" class="video_embed">
  </div>
  <?php else: ?>
  <div class="ynstore_video_embed">
    <?php echo $this->videoEmbedded ?>
  </div>
  <?php endif; ?>
   <div class="ynstore_video_date">
    <?php echo $this->translate('Posted') ?>
    <?php echo $this->timestamp($this->video->creation_date) ?>
    </div>
 </div>