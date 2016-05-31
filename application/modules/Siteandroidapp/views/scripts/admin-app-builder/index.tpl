<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    index.tpl 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
    <?php echo $this->translate('Android Mobile Application') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<br />
<h3>
    Please read & follow the below important instructions for building your Android App
</h3>
<p>
    Below are the steps to help you with providing your Android App's details for building and submitting the app to app store. Please read them carefully and follow them.
</p>
<br />
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">
        <li>
            <?php 
                echo '<p>Not purchased our Mobile Apps Subscription Plans yet? Please do so from here: <br /><a href="http://www.socialengineaddons.com/socialengine-ios-iphone-android-mobile-apps-subscriptions" target="_blank">http://www.socialengineaddons.com/socialengine-ios-iphone-android-mobile-apps-subscriptions</a>.</p>'
            . '<br /><p>The only difference between our "Mobile Starter Plan" and "Mobile Pro Plan" is that the Mobile Pro plan enables you to submit the app to app store with your own app store developer account, whereas with the Mobile Starter Plan, it gets submitted with our SocialEngineAddOns developer account.</p>';
            ?>
        </li>

        <li>
            <?php echo '<p>Fill the "App Submission Details" form with information specific to your app, like App Title, Description, branding information, etc, by clicking on the "Proceed to Android App Setup" button at the bottom of this page.</p><br /><p><span style="font-weight: bold;">Note:</span> If you have subscribed to the "Mobile Pro Plan", then you will also need to provide us your Google Play Developer Account Details. If you have not yet enrolled into the Android Developer Program, then please enroll from here: <a href="https://play.google.com/apps/publish/signup/" target="_blank">https://play.google.com/apps/publish/signup/</a> (also see: <a href="https://support.google.com/googleplay/android-developer/answer/6112435?hl=en&rd=1" target="_blank">https://support.google.com/googleplay/android-developer/answer/6112435?hl=en&rd=1</a>).</p>'; ?>
        </li>
        
        <li>
            <?php 
                echo '<p>After filling ALL the App Submission Details, please save the form. Then, you will see a "Download tar" link at the bottom of the page. Please click on it to download the compressed tar file with your app\'s details, and email that file to us as an attachment at: <a href="mailto: apps@socialengineaddons.com">apps@socialengineaddons.com</a>, with email subject as the one that will be shown to you in the download popup.</p>';
            ?>
        </li>
        
        <li>
            <?php echo '<p>Then, please initiate a Support Ticket from your SocialEngineAddOns Client Area by choosing the Product as: "Android Mobile Application", subject as: "Android App Build and Setup", and send us the FTP and Admin details of your website from it. This will enable our support team to start work on your App\'s creation.</p>'; ?>
        </li>

        <br />
        <span style="font-weight: bold;">Note:</span> We will be submitting your App to the Google Play Store within 12 to 48 hours of receiving ALL the details from you. <br /><br />
        While submitting your App to the App Store, we will take and post good screenshots of your App for the App listing. If you have the "Mobile Pro Plan", then:
        <br />
        - You will be able to change them easily as per your choice from your Google Play Developer Console.<br />
        - After your App is submitted, you will be also be able to add additional graphics for it from your Google Play developer account like: Feature Graphic, Promo Graphic, Promo Video, etc (see details here: <a href="https://support.google.com/googleplay/android-developer/answer/1078870" target="_blank">https://support.google.com/googleplay/android-developer/answer/1078870</a>).
        <br /><br />
        We also recommend you to go through Google Play's App Content Policies: <a href="https://play.google.com/about/developer-content-policy.html" target="_blank">https://play.google.com/about/developer-content-policy.html</a>.
        <br /><br />
        <center>
            <button onclick="form_submit();"><?php echo 'Proceed to Android App Setup'; ?> </button>
        </center>
        <br />
        </li>
    </ul>
</div>

<script type="text/javascript" >
    function form_submit() {
        var url = '<?php echo $this->url(array('module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'create'), 'admin_default', true) ?>';
        window.location.href = url;
    }
</script>