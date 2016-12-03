<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Advanced Events Plugin');?>
</h2>
<?php if (count($this->navigationEvent)): ?>
	<div class='seaocore_admin_tabs'>
		<?php echo $this->navigation()->menu()->setContainer($this->navigationEvent)->render() ?>
	</div>
<?php endif; ?>

<h2><?php echo $this->translate('Advanced Events - Inviter Extension'); ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteeventinvite/views/scripts/admin-global/faq_help.tpl'; ?>