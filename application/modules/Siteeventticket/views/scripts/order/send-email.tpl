<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: send-tickets.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <?php if($this->adminCall): ?>  
        <h3><?php echo 'Send Tickets?'; ?></h3>
        <p>
          <?php echo 'Do you want to send tickets to buyer?'; ?>
        </p>
    <?php else: ?>
        <h3><?php echo $this->translate('Send Email?'); ?></h3>
        <p>
          <?php echo $this->translate('Do you want to get this order by email?'); ?>
        </p>    
    <?php endif; ?>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->order_id ?>"/>
      <button type='submit'><?php echo 'Send'; ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo 'cancel'; ?></a>
    </p>
  </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
