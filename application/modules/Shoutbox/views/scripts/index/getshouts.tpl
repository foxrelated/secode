<?php if( $this->totalShouts ): ?>
      <?php // Iterate over the comments backwards (or forwards!)
      $shouts = $this->shouts->getIterator();
        $i = count($shouts) - 1;
        $d = -1;
        $e = -1;
      for( ; $i != $e; $i += $d ):
        $shout = $shouts[$i];
      ?>
    <li>
      <?php echo $this->htmlLink($shout->getOwner()->getHref(), $this->itemPhoto($shout->getOwner(), 'thumb.icon'), array('class' => 'shoutbox_thumb', 'title' => $shout->getOwner()->getTitle() )) ?>
      <div class='shoutbox_info'>
        <div class='shoutbox_body'>
          <?php echo $this->viewMore($shout->body) ?>
        </div>
        <div class='shoutbox_date'>
          <?php echo $this->timestamp($shout->creation_date) ?>
        </div>
      </div>
    </li>
  <?php endfor; ?>
<?php endif; ?>