<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _facebookAccessPermission.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$isSelected = false;
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$dirName = 'android-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $dirName . '/settings.php';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['gmail_permission_done']) && !empty($appBuilderParams['gmail_permission_done']))
        $isSelected = true;
}
?>


<div class="form-wrapper" id="gmail_permission_done-wrapper" style="display: block;">
    <div id="gmail_permission_done-label" class="form-label">
        Give Permission to Access Google Play Developer Account
    </div>
    <div class="form-element" id="gmail_permission_done-element">
        <input type="hidden" value="" name="gmail_permission_done">
        <?php if (!empty($isSelected)): ?>
            <input type="checkbox" value="1" id="gmail_permission_done" name="gmail_permission_done" checked="checked">
        <?php else: ?>
            <input type="checkbox" value="1" id="gmail_permission_done" name="gmail_permission_done">
        <?php endif; ?>
            
        <label class="optional" for="gmail_permission_done">
            Please follow this <a target="_blank" href="https://youtu.be/U52t96CwisI">Video Tutorial</a> to give permission to our <span style="font-weight: bold;">socialengineaddons@gmail.com</span> email address, to access your Google Play Developer Console.
        </label>
    </div>
</div>