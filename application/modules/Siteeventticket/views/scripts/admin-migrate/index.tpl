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

<br/>

<div id="success_message" class='success-message'></div>
<div id="unsuccess_message" class="error-message"></div>

<?php if($this->lastEventId): ?>
    <div class='seaocore_settings_form'>
      <div class='settings'>
        <?php
        echo $this->form->render($this);
        ?>
      </div>
    </div>
<?php else: ?>
    <div class="tip">
        <span><?php echo "You have already migrated all the rsvp events." ?></span>
    </div>
<?php endif; ?>

<script type="text/javascript">

    function startMigration()
    {
        var migration_confirmation = confirm('<?php echo $this->string()->escapeJavascript("Are you sure you want to start migration ?") ?>');

        if (migration_confirmation) {

            Smoothbox.open("<div><center><b>" + '<?php echo $this->string()->escapeJavascript("Migrating Events...") ?>' + "</b><br /><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loader.gif' alt='' /></center></div>");
            en4.core.request.send(new Request.JSON({
                url: en4.core.baseUrl + 'admin/siteeventticket/migrate/migrate',
                method: 'get',
                data: {
                    'format': 'json'
                },
                onSuccess: function(responseJSON) {
                    
                    if(responseJSON.lasteventid != 0) {
                       $('unsuccess_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/cross.png); padding-left: 18px; color: red; background-repeat: no-repeat; background-position: left center;'>Sorry for this inconvenience !! Migration is interrupted due to some reason. Please click on 'Start Migration' button to start the migration from the same point again.</span><br /><br />";           
                    }
                    else {
                        $('migrate').style.display = 'none';
                        $('unsuccess_message').style.display = 'none';
                        $('success_message').innerHTML = "<span style='background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/notice.png);  padding-left: 18px; color: green; background-repeat: no-repeat; background-position: left center;'>Migration is done succesfully.</span><br /><br />";                       
                    }

                    Smoothbox.close();
                }
            }))
        }
    }
</script>
