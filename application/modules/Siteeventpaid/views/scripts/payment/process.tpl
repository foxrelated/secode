<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: process.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')): ?>
  <div>
    <h2> <?php echo $this->translate("Processing Payment") ?></h2>
    <div>
      <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loader.gif" /></center>
    </div>
    <div id="LoadingImage" class="siteeventpaid_payment_process">  
      <?php echo $this->translate("Processing Request. Please wait .....") ?>
    </div>
  </div>
  <script type="text/javascript">
    window.addEvent('load', function () {
      var url = '<?php echo $this->transactionUrl ?>';
      var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
      var request = new Request.Post({
        url: url,
        data: data
      });
      request.send();
    });
  </script>

<?php else: ?>

  <div>
    <div>
      <center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemobile/modules/Core/externals/images/loading.gif" /></center>
    </div>
    <div id="LoadingImage" style="text-align:center;margin-top:15px;font-size:17px;">  
      <?php echo $this->translate("Processing Request. Please wait .....") ?>
    </div>
  </div>
  <form method="post" action="<?php echo $this->transactionUrl ?>"id="siteevent_package_payment" style="display: none;">
    <?php foreach ($this->transactionData as $key => $value): ?>
      <input type="hidden" name="<?php echo $key ?>"  value="<?php echo $value; ?>"/>
    <?php endforeach; ?>
  </form>
  <script type="text/javascript">
    $('#siteevent_package_payment').submit();
  </script>

<?php endif; ?>