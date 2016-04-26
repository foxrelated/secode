<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: claimgroup.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/style_sitegroup_profile.css');
?>
<?php if (!$this->status && $this->userclaim && $this->claimoption): ?>
  <div class="sitegroup_tellafriend_popup">
    <?php echo $this->form->render($this); ?>
  </div>
<?php endif; ?>