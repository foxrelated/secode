<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: tax.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  window.addEvent('domready', function () {
    showOtherElements();
  });

  function showOtherElements()
  {
    if ($('siteeventticket_tax_mandatory-wrapper')) {
      if ($('siteeventticket_tax_enabled-1').checked) {
        $('siteeventticket_tax_mandatory-wrapper').style.display = 'block';
      }
      else {
        $('siteeventticket_tax_mandatory-wrapper').style.display = 'none';
      }
    }
  }
</script>
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

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>
