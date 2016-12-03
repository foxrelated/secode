<span style="float: right; margin-top: -42px;">
	<a href="/stay-on-top" class="buttonlink"><?php echo $this->translate('My Items want to be here') ?></a>
</span>

<?php

$this->headScript() ->appendFile('application/modules/Hecore/externals/scripts/featured_carousel.js');
$container = 'he_carousel_'.rand(11111, 99999);

?>

<div style="clear:both;"></div>

<script type="text/javascript">
  en4.core.runonce.add( function(){
    new he_featured_carousel('<?php echo $container;?>');
  });
</script>
<div id="<?php echo $container;?>" class="he_carousel_list">
  <div class="content">
    <div class="prev"></div>
    <div class="listing">
      <div class="list">
                <?php foreach ($this->paginator as $featured):             $owner = $featured->getOwner();             if(!$this->viewer()->hasPermission($owner)) {                continue;            }            ?>
        <div class="item">
          <div class="thumbnail">
            <?php echo $this->htmlLink($featured->getHref(), $this->itemPhoto($featured, 'thumb.normal')); ?>
          </div>
          <div class="tip_title"></div>
          <div class="tip_text"><?php echo $featured->getTitle() ?></div>
        </div>
        <?php endforeach; ?>
        <div class="clr"></div>
      </div>
    </div>
    <div class="next"></div>
    <div class="clr"></div>
  </div>



</div>

<div class="clr"></div>
