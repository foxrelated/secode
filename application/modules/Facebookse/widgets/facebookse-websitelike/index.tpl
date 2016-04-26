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
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Facebookse/externals/scripts/core.js');
if (!empty($this->sitelike)) :
    ?>
    <div id="websitelike">
        <?php
        if (empty($this->websitelike_type)) :
            echo $this->translate($this->facebookse_like_type);
        else :
            echo $this->like_button;
        endif;
        ?>
    </div>
    <script type="text/javascript">
        var call_advfbjs = '1';
        $('websitelike').innerHTML = '<?php echo $this->like_button; ?>';
        window.addEvent('domready', function ()
        {
            en4.facebookse.loadFbLike(<?php echo $this->LikeSettings; ?>);
        });
    </script>

    <?php
 endif;