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

<?php echo $this->like_button; ?>

<script type="text/javascript">
    var fblike_moduletype = '<?php echo $this->resource_type; ?>';
    var fblike_moduletype_id = '<?php echo $this->resource_identity; ?>';
    var call_advfbjs = 1;
    en4.core.runonce.add(function () {
        setTimeout('callFBParse(\'contentlike-fb\');', 50);
    });
</script>

<?php if (!$this->fbbutton_commentbox) : ?>
    <style type="text/css">
        .fb_iframe_widget span{
            overflow:hidden;
        }
    </style>

<?php endif; ?>