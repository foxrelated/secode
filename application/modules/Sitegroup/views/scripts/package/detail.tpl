<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: package-detail.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class="sitegroup_package_group global_form_popup" style="margin:10px 0 0 10px;">
  <ul class="sitegroup_package_list">
    <li style="width:650px;">
      <div class="sitegroup_package_list_title">
        <div class="sitegroup_create_link">
          <a href="javascript:void(0);" onclick="createAD()"><?php echo $this->translate("Create a Group"); ?> &raquo;</a>
        </div>
        <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->package->title; ?></h3>
      </div>
       <?php $item=$this->package;?>
       <?php $this->detailPackage=1; ?>
       <?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/package/_packageInfo.tpl'; ?>
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
		var url='<?php echo $this->url(array("action"=>"create" ,'id' => $this->package->package_id), 'sitegroup_general', true) ?>';

		parent.window.location.href=url;
		parent.Smoothbox.close();
  }
</script>