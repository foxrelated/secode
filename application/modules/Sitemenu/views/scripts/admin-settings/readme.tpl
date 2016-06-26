<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: readme.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<h2><?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation' ?></h2>

<div class="tabs">
  <ul class="navigation">
    <li class="active">
      <a href="<?php echo $this->baseUrl() . '/admin/sitemenu/settings/readme' ?>" ><?php echo 'Please go through these important points and proceed by clicking the button at the bottom of this page.' ?></a>
    </li>
  </ul>
</div>		

<?php include_once APPLICATION_PATH . '/application/modules/Sitemenu/views/scripts/admin-settings/faq_help.tpl'; ?>
<br />
<button onclick="form_submit();"><?php echo 'Proceed to enter License Key' ?> </button>

<script type="text/javascript" >
  function form_submit() {
		
    var url='<?php echo $this->url(array('module' => 'sitemenu', 'controller' => 'settings'), 'admin_default', true) ?>';
    window.location.href=url;
  }
</script>
