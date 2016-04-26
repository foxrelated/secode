<?php if(count($this->arr_photos) > 0 ): ?>

  <ul class="thumbs">
    <?php foreach( $this->arr_photos as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach;?>
  </ul>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this user yet.');?>
    </span>
  </div>

<?php endif; ?>