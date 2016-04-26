<script type="text/javascript">
  en4.core.runonce.add(function(){

    var anchor = $('shoutbox_smoothbox').getParent();

    $('shoutbox_next').style.display = '<?php echo ( $this->shouts->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('shoutbox_previous').style.display = '<?php echo ( $this->shouts->count() == $this->shouts->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('shoutbox_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'shoutbox/index/index',
        data : {
          format : 'html',
          identity : '<?php echo $this->identity ?>',
          page : <?php echo sprintf('%d', $this->shouts->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('shoutbox_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'shoutbox/index/index',
        data : {
          format : 'html',
          identity : '<?php echo $this->identity ?>',
          page : <?php echo sprintf('%d', $this->shouts->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });
</script>



<div id="shoutbox_smoothbox" class="shoutbox_smoothbox layout_shoutbox_shoutbox">
<?php if( $this->shouts->count() ): ?>
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
      <?php echo $this->htmlLink('javascript:void(0);', $this->itemPhoto($shout->getOwner(), 'thumb.icon'), array('class' => 'shoutbox_thumb', 'title' => $shout->getOwner()->getTitle(), 'onclick' => 'parent.gotoProfile("'.$shout->getOwner()->getHref().'")' )) ?>
      <div class='shoutbox_info'>
        <div class='shoutbox_body'>
          <?php echo $shout->body ?>
        </div>
        <div class='shoutbox_date'>
          <?php echo $this->timestamp($shout->creation_date) ?>
        </div>
      </div>
    </li>
  <?php endfor; ?>
</ul>
<div>
  <div id="shoutbox_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="shoutbox_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
<?php else: ?>
    <div id="shoutbox_tip" class="tip">
      <span>
        <?php echo $this->translate("Nothing has been posted here yet") ?>
      </span>
    </div>
    <ul id="shoutbox_content"></ul>
<?php endif; ?>
</div>