<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _rejectionConfirmation.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$isSelected = true;
$getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
$websiteStr = str_replace(".", "-", $getWebsiteName);
$dirName = 'ios-' . $websiteStr . '-app-builder';
$appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $dirName . '/settings.php';
if (file_exists($appBuilderBaseFile)) {
    include $appBuilderBaseFile;
    if (isset($appBuilderParams['ios_app_rejection_confirmation']) && !empty($appBuilderParams['ios_app_rejection_confirmation']))
        $isSelected = true;

    if (isset($appBuilderParams['ios_app_rejection_confirmation']) && empty($appBuilderParams['ios_app_rejection_confirmation']))
        $isSelected = false;
}
?>

<div class="form-wrapper" id="ios_app_rejection_confirmation-wrapper">
    <div id="ios_app_rejection_confirmation-label" class="form-label">
        Common App Rejections <span style="color: RED">*</span>
    </div>
    <div class="form-element" id="ios_app_rejection_confirmation-element">
        <input type="hidden" value="" name="ios_app_rejection_confirmation">
        <?php if (!empty($isSelected)): ?>
            <input type="checkbox" value="1" id="ios_app_rejection_confirmation" name="ios_app_rejection_confirmation" onchange="showDownload();">
    <!--            <input type="checkbox" value="1" id="ios_app_rejection_confirmation" name="ios_app_rejection_confirmation" checked="checked">-->
        <?php else: ?>
            <input type="checkbox" value="1" id="ios_app_rejection_confirmation" name="ios_app_rejection_confirmation" onchange="showDownload();">
        <?php endif; ?>        

        <label class="required" for="ios_app_rejection_confirmation">
            I have read, understood and done all required changes on my site. [Please <a href="<?php echo $this->url(array('module' => 'siteiosapp', 'controller' => 'app-builder', 'action' => 'app-rejection-faq'), 'admin_default', true); ?>" class="smoothbox">click here</a> to read common app rejection reasons, and the things you should do for quick app approval.]
        </label>
    </div>
</div>
<script type="text/javascript">
    window.addEvent('domready', function () {
        showDownload();
    });

    function showDownload() {
        if (document.getElementById('ios_app_rejection_confirmation').checked) {
            document.getElementById('download_tar').style.display = 'block';
        } else {
            document.getElementById('download_tar').style.display = 'none';
        }
    }
</script>