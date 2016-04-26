<div class="shoutbox_history">
    <a class="smoothbox" href="<?php echo $this->layout()->staticBaseUrl . 'shoutbox/index/index'; ?>/identity/<?php echo $this->identity; ?>"><?php echo $this->translate("View history") ?></a>
</div>
<?php if( $this->totalShouts ): ?>
<ul id="shoutbox_content">
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
</ul>
<?php else: ?>
    <div id="shoutbox_tip" class="tip">
      <span>
        <?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
      </span>
    </div>
    <ul id="shoutbox_content"></ul>
<?php endif; ?>
<?php if( $this->allowCreate ): ?>
<div class="shoutbox_input">
    <div id="shoutbox_loading" class="shoutbox_loading"></div>
    <div style="float: left;">
        <input id='shoutbox_msg' type='text' name='shoutbox_msg' class='shoutbox_msg' placeholder='<?php echo $this->translate("Shout!") ?>'/>
        <input id='shoutbox_identity' type='hidden' value='<?php echo $this->identity; ?>'/>
    </div>
</div>
<script type="text/javascript">
window.addEvent('domready', function(){	
    $('shoutbox_msg').addEvent('keydown', function(event){
        if (event.key == 'enter')
            {
                if(this.value){
                    addShout(this.value, $('shoutbox_identity').value);
                }
            }
    });
});
</script>
<?php else: ?>
<div class="shoutbox_input">
    <div id="shoutbox_loading" class="shoutbox_loading"></div>
    <div style="float: left;">
        <input id='shoutbox_identity' type='hidden' value='<?php echo $this->identity; ?>'/>
    </div>
</div>
<?php endif; ?>
<?php if( $this->autorefresh ): ?>
<script type="text/javascript">
window.addEvent('domready', function(){	
    getShouts.periodical(<?php echo $this->timer ?>);
});
</script>
<?php endif; ?>