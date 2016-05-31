<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    delete-existing-app.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate('Delete Previous Android App?'); ?></h3>
        <p>
            <?php echo $this->translate('As you have upgraded our App you can delete the older version of it. By this your current App will stop working till this new App gets publish on Google Play and your users will not be able to access your old App. To do so, please click on the ‘Delete’ button.
Let your current App work until this new App gets published on Google Play and delete it later on, as this procedure will take time. Click ‘Cancel’ to do so.'); ?>
        </p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
            <button type='submit'><?php echo $this->translate('Delete'); ?></button>
            or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
        </p>
    </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>