<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _facebookLogin.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
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
        if (isset($appBuilderParams['facebook_app_id']) && isset($appBuilderParams['facebook_app_secret']) && !empty($appBuilderParams['facebook_app_id']) && !empty($appBuilderParams['facebook_app_secret']))
            $isSelected = true;
        
        if (isset($appBuilderParams['facebook_app_id']) && isset($appBuilderParams['facebook_app_secret']) && empty($appBuilderParams['facebook_app_id']) && empty($appBuilderParams['facebook_app_secret']))
            $isSelected = false;
    }
?>

<div class="form-wrapper" id="facebook_app_id-wrapper">
    <div id="facebook_app_id-label" class="form-label">
        Facebook Login
    </div>
    <div class="form-element" id="facebook_app_id-element">
        <input type="hidden" value="" name="facebook_app_id">
        <?php if(!empty($isSelected)): ?>
            <input type="checkbox" value="1" id="facebook_app_id" name="facebook_app_id" checked="checked" onchange="showHideFacebookDependentFields();">
        <?php else: ?>
            <input type="checkbox" value="1" id="facebook_app_id" name="facebook_app_id" onchange="showHideFacebookDependentFields();">
        <?php endif; ?>        
        
        <label class="required" for="facebook_app_id">
            Enable login using Facebook [You should have enabled Facebook Integration <a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'facebook'), 'admin_default', true); ?>">from here</a>. You can use the SocialEngineAddOns "<a href="http://www.socialengineaddons.com/services/facebook-application-configuration-and-submission-service" target="_blank">Facebook Application Configuration and Submission Service</a>" if required.]
        </label>
    </div>
</div>

<script type="text/javascript">
     window.addEvent('domready', function () {
         showHideFacebookDependentFields();
     });
     
    function showHideFacebookDependentFields() {
        if(!$('facebook_app_id').checked){
            $('facebook_access_permission-wrapper').style.display = 'none';
            $('facebook_app_display_name-wrapper').style.display = 'none';            
        }else {
            $('facebook_access_permission-wrapper').style.display = 'block';
            $('facebook_app_display_name-wrapper').style.display = 'block';
//            showHideFacebookDisplayNameTextBox();
        }
    }
</script>