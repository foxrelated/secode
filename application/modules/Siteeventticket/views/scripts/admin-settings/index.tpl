<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function () {
    showOtherElements();
    showCheckboxElements();
  });

  function showOtherElements()
  {
      if ($('siteeventticket_ticket_enabled-1').checked) {
        $('siteeventticket_buylimitmax-wrapper').style.display = 'block';
        $('siteeventticket_buyer_details-wrapper').style.display = 'block';
        $('siteeventticket_detail_step-wrapper').style.display = 'block';
      }
      else {
        $('siteeventticket_buylimitmax-wrapper').style.display = 'none';
        $('siteeventticket_buyer_details-wrapper').style.display = 'none';
        $('siteeventticket_detail_step-wrapper').style.display = 'none';
      }
  }
  
  function showCheckboxElements()
  {
      if ($('siteeventticket_detail_step-1').checked && $('siteeventticket_ticket_enabled-1').checked) {
        $('siteeventticket_buyer_details-wrapper').style.display = 'block';
      }
      else {
        $('siteeventticket_buyer_details-wrapper').style.display = 'none';
      }
  }
</script>

<!--<script type="text/javascript">
    function dismiss(modName) {
      var d = new Date();
      // Expire after 1 Year.
      d.setTime(d.getTime()+(365*24*60*60*1000));
      var expires = "expires="+d.toGMTString();
      document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
        $('dismiss_modules').style.display = 'none';
    }
</script>-->

<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<?php if (!Engine_Api::_()->hasModuleBootstrap('sitemailtemplates')): ?>
    <div id="dismiss_modules">
        <div class="seaocore-notice">
            <div class="seaocore-notice-icon">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
            </div>
            <div class=" fleft seaocore-notice-text ">
                <?php echo sprintf("Note: Please install '%s' plugin for emailing the ticket pdf to buyer.", '<a href="http://www.socialengineaddons.com/socialengine-email-templates-plugin" target="_blank">Email Templates</a>'); ?>
            </div>	
<!--            <div class="fright">
                <button onclick="dismiss('sitemailtemplates_dompdf');"><?php echo 'Dismiss'; ?></button>
            </div>            -->
        </div>
    </div>
<?php endif; ?>

<?php if (!file_exists('application/libraries/dompdf/dompdf_config.inc.php')): ?>
    <div id="dismiss_modules">
        <div class="seaocore-notice">
            <div class="seaocore-notice-icon">
                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
            </div>
            <div class=" fleft seaocore-notice-text ">
                <?php echo sprintf("Note: Please %s to install the dompdf library for emailing the ticket pdf to buyer.", '<a href="'.$this->url(array('module' => 'siteeventticket', 'controller' => 'dompdf', 'action' => 'index'), "admin_default", 'click here').'" target="">click here</a>'); ?>                 
            </div>	
<!--            <div class="fright">
                <button onclick="dismiss('sitemailtemplates_dompdf');"><?php echo 'Dismiss'; ?></button>
            </div>            -->
        </div>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>
