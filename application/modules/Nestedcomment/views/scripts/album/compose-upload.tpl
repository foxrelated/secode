<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: compose-upload.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    $try(function() {
        parent.en4.album.getComposer().processResponse(<?php echo $this->jsonInline($this->getVars())?> );
    });
    $try(function() {
        parent._composePhotoResponse = <?php echo $this->jsonInline($this->getVars()) ?>;
    });
</script>