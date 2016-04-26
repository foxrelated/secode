<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: level.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php //ADDING SETTINGS IF SITEEVENTTICKET MODULE IS ENABLED.
if (Engine_Api::_()->siteevent()->hasTicketEnable()):?>
<script type="text/javascript">
  window.addEvent('domready', function () {
    showcommissionType();
  });
  
  function showcommissionType(){
    if(document.getElementById('commission_handling')){
          if(document.getElementById('commission_handling').value == 1) {
            document.getElementById('commission_fee-wrapper').style.display = 'none';
            document.getElementById('commission_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('commission_fee-wrapper').style.display = 'block';
            document.getElementById('commission_rate-wrapper').style.display = 'none';
          }
        }
  }
</script>
<?php endif;?>
<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<script type="text/javascript">
    var fetchLevelSettings = function(level_id) {
        window.location.href = en4.core.baseUrl + 'admin/siteevent/settings/level/id/' + level_id;
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>