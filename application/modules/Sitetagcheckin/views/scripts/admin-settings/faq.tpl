<?php
?>

<h2><?php echo $this->translate("Geo-Location, Geo-Tagging, Check-Ins & Proximity Search Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include_once APPLICATION_PATH .
'/application/modules/Sitetagcheckin/views/scripts/admin-settings/faq_help.tpl'; ?>
