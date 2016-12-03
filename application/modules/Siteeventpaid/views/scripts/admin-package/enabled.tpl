<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: enabled.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo 'Disable Package ?'; ?></h3>
    <p>
      <?php echo 'Are you sure you want to disable this package? Disabling this package will make it unavailable to users while creating events, and while changing package of their events. Disabling will not affect the existing events of this package.'; ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->event_id ?>"/>
      <button type='submit'><?php echo 'Disable'; ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo 'cancel'; ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>