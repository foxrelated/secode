<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: admin-cheque-detail.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>

<div class="global_form_popup">
  <h3><?php echo $this->translate("%s's Bank Account Details", $this->site_title) ?></h3>
  <div class="fleft p10"><pre><?php echo $this->admin_cheque_detail ?></pre></div>

  <div class='buttons clr'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div>
</div>