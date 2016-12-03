<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate('Advanced Events - Inviter Extension') ?></h2>
<div class="tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/siteeventinvite/global/readme' ?>" ><?php echo $this->translate('Please go through these important points and proceed by clicking the button at the bottom of this event.') ?></a>

    </li>
  </ul></div>

<?php include_once APPLICATION_PATH . '/application/modules/Siteeventinvite/views/scripts/admin-global/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo $this->translate('Proceed to enter License Key') ?> </button>

<script type="text/javascript" >

  function form_submit() {
	
    var url='<?php echo $this->url(array('module' => 'siteeventinvite', 'controller' => 'global', "action" => "global"), 'admin_default', true) ?>';
    window.location.href=url;
  }

</script>