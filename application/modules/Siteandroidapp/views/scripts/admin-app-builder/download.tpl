<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    download.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->redirectToCreateForm): ?>
    <script type="text/javascript">
        parent.window.location.href = '<?php echo $this->url(array('module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'create', 'package' => $this->package, 'downloadTar' => 1), 'admin_default', false); ?>'
    </script>
<?php endif; ?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate('Download tar file to email it to SocialEngineAddOns'); ?></h3>
        <p>
            Before downloading this file, please ensure that you have filled all the App Submission Details. This tar file contains all the details required for building your Android App. After downloading this file, please do the below:
        </p><br />
        <p>
            <span style="font-weight: bold;">1)</span> Send this .tar file to SocialEngineAddOns as an attachment via e-mail to <a href="mailto: apps@socialengineaddons.com">apps@socialengineaddons.com</a>, with the subject: <span style="font-weight: bold;">[Android][<?php echo $this->clientId; ?>][<?php echo str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])); ?>] Details for building App</span>
        </p>
        <br />
        <p>
            <span style="font-weight: bold;">2)</span> File a Support Ticket from the "Support" section of your SocialEngineAddOns Client Area by choosing the Product as: "Android Mobile Application", with the subject as: "Android App Build and Setup: <?php echo str_replace('www.', '', strtolower($_SERVER['HTTP_HOST'])); ?>" and notify us to proceed with app building. Please also mention the FTP and Admin details of your website in that too.
        </p>      
        <br />
        <p>
            <input type="hidden" name="id" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo $this->translate('Download File'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>