<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteeventpaid_package_page global_form_popup" style="margin:10px 0 0 10px;">
  <ul class="siteeventpaid_package_list">
    <li style="width:650px;">
      <div class="siteeventpaid_package_list_title">
        <div class="siteeventpaid_create_link">
          <a class="siteevent_buttonlink" href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("Create New Event"); ?> &raquo;</a>
        </div>
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
      </div>
      <?php $item = $this->package; ?>
      <?php $this->detailPackage = 1; ?>
      <?php include APPLICATION_PATH . '/application/modules/Siteeventpaid/views/scripts/package/_packageInfo.tpl'; ?>
      <button onclick='javascript:parent.Smoothbox.close()' class="fright"><?php echo $this->translate('Close'); ?></button>
    </li>
  </ul>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<script type="text/javascript">

  function createAD() {
    var url = '<?php echo $this->url(array("action" => "create", 'id' => $this->package->package_id), "siteevent_general", true) ?>';

    parent.window.location.href = url;
    parent.Smoothbox.close();
  }
</script>