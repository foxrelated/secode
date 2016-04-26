
<div class="headline">
  <h2>
    <?php echo $this->translate('Auction');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
<div class='layout_middle'>
    
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="auctions_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='auctions_browse_info'>
            <p class='auctions_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
            </p>
            <p class='auctions_browse_info_date'>
              <?php echo $this->translate('Posted by');?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
              <?php echo $this->translate('about');?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
            </p>
            <p class='auctions_browse_info_blurb'>
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>349) echo "...";
              ?>
            </p>
             <div>
        <br/>
        <?php for($i = 1; $i <= 5; $i++): ?>
                <img border="0" src="application/modules/Auction/externals/images/<?php if ($i <= $item->rates): ?>star_full.png<?php elseif( $i > $item->rates &&  ($i-1) <  $item->rates): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" width="16px" />
         <?php endfor; ?>
        </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
   <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no auctions yet.');?>
      </span>
    </div>
   <?php endif; ?>
   <?php echo $this->paginationControl($this->paginator, null, array("pagination/auctionpagination.tpl","auction"), array("orderby"=>$this->orderby)); ?>

</div>

