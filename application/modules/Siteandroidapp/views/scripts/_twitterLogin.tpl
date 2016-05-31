<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    _twitterLogin.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
    $isSelected = true;
    $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    $websiteStr = str_replace(".", "-", $getWebsiteName);
    $dirName = 'android-' . $websiteStr . '-app-builder';
    $appBuilderBaseFile = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . $dirName . '/settings.php';
    if (file_exists($appBuilderBaseFile)) {
        include $appBuilderBaseFile;
        if (isset($appBuilderParams['twitter_app_id']) && isset($appBuilderParams['twitter_app_secret']) && !empty($appBuilderParams['twitter_app_id']) && !empty($appBuilderParams['twitter_app_secret']))
            $isSelected = true;
        
        if (isset($appBuilderParams['twitter_app_id']) && isset($appBuilderParams['twitter_app_secret']) && empty($appBuilderParams['twitter_app_id']) && empty($appBuilderParams['twitter_app_secret']))
            $isSelected = false;
    }
?>

<div class="form-wrapper" id="twitter_app_id-wrapper">
    <div id="twitter_app_id-label" class="form-label">
        Twitter Login
    </div>
    <div class="form-element" id="twitter_app_id-element">
        <input type="hidden" value="" name="twitter_app_id">
        <?php if(!empty($isSelected)): ?>
            <input type="checkbox" value="1" id="twitter_app_id" name="twitter_app_id" checked="checked">
        <?php else: ?>
            <input type="checkbox" value="1" id="twitter_app_id" name="twitter_app_id">
        <?php endif; ?>  
        
        <label class="required" for="twitter_app_id">
            Enable login using Twitter. [You should have enabled Twitter Integration <a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'twitter'), 'admin_default', true); ?>">from here</a>. You can use the SocialEngineAddOns "<a href="http://www.socialengineaddons.com/services/configuring-3rd-party-invitation-applications" target="_blank">Configuring 3rd-party Invitation Applications Service</a>" if required.]            
        </label>
    </div>
</div>