<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 

<script type="text/javascript">

<?php if ($this->scrape_sitepageurl): ?>
        en4.core.runonce.add(function () {
            en4.facebookse.scrapeSiteUrl('<?php echo $this->FacebookseScrapeUrl; ?>');

        });

<?php endif; ?>
</script>
<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');

if (!empty($this->sitelike) && empty($this->isajax)) {
    $session = new Zend_Session_Namespace();
    if (isset($session->aaf_fbaccess_token))
        $accessToken = $session->aaf_fbaccess_token;
    ?>
    <ul>
        <li>
            <div id="contentlike-fb" class="fblikebutton clr">

            </div>		
        </li>
    </ul>

    <script type="text/javascript">
        var fblike_moduletype;
        var fblike_moduletype_id;
        var call_advfbjs = 0;
        //check if the child window is open

        window.addEvent('domready', function ()
        {
            en4.facebookse.loadFbLike(<?php echo json_encode($this->getallparams); ?>);

        });

    </script>
<?php } ?>

<?php
if (!empty($this->isajax)) {
    $fbLikeButton = Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default');
    if ($fbLikeButton == 'default')
        include_once(APPLICATION_PATH . "/application/modules/Facebookse/views/scripts/_fbdefaultlike.tpl");
    else
        include_once(APPLICATION_PATH . "/application/modules/Facebookse/views/scripts/_fbcustomlike.tpl");
}
?>
   