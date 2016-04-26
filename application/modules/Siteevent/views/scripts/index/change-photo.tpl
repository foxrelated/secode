<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: change-photo.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="layout_middle">
    <div class="global_form_popup">
        <?php echo $this->form->render($this); ?>
    </div>
</div>
</div>
<script type="text/javascript">
    function removePhotoEvent(url) {
        window.location.href = url;
    }
</script>