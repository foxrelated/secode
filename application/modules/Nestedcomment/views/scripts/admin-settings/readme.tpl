<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments'); ?></h2>
<div class="tabs">
    <ul class="navigation">
        <li class="active">
            <a href="<?php echo $this->baseUrl() . '/admin/nestedcomment/settings/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this page.') ?></a>
        </li>
    </ul>
</div>		

<?php include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>

<script type="text/javascript" >

    function form_submit() {
        window.location.href = "<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'settings'), 'admin_default', true) ?>";
    }

</script>