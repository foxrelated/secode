<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: mapping-category.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup">
    <?php echo $this->form->render($this) ?>
</div>

<?php if (@$this->closeSmoothbox || $this->close_smoothbox): ?>
    <?php $baseurl = Zend_Controller_Front::getInstance()->getBaseUrl(); ?>
    <script type="text/javascript">
        window.parent.location.href = '<?php echo $baseurl ?>' + '/admin/siteevent/settings/categories';
        window.parent.Smoothbox.close();
    </script>
<?php endif; ?>

<script type="text/javascript">
    function closeSmoothbox() {
        window.parent.Smoothbox.close();
    }
</script>

