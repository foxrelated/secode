<?php echo $this->htmlLink($this->shout->getOwner()->getHref(), $this->itemPhoto($this->shout->getOwner(), 'thumb.icon'), array('class' => 'shoutbox_thumb', 'title' => $this->shout->getOwner()->getTitle() )) ?>
<div class='shoutbox_info'>
<div class='shoutbox_body'>
  <?php echo $this->shout->body ?>
</div>
<div class='shoutbox_date'>
  <?php echo $this->timestamp($this->shout->creation_date) ?>
</div>
</div>