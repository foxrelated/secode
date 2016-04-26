<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php
$current_date = date("Y-m-d i:s:m", time());
$listApi = Engine_Api::_()->list();
$expirySettings = $listApi->expirySettings();
$approveDate=null;
if ($expirySettings == 2):
  $approveDate = $listApi->adminExpiryDuration();
endif;
?>

<?php if($approveDate && $this->list->approved_date && $approveDate > $this->list->approved_date): ?>
<div class="tip">
  <span>
  <?php echo $this->translate('This listing has been expired.'); ?>
  </span>
</div>
<?php elseif($expirySettings == 1 && $this->list->end_date && $this->list->end_date !='0000-00-00 00:00:00' && $current_date > $this->list->end_date):?>
	<div class="tip">
		<span>
		<?php echo $this->translate('This listing has been expired.'); ?>
		</span>
	</div>
<?php endif;?>

<div id='profile_status'>
  <h2>
		<?php echo $this->list->getTitle(); ?>
	</h2>
</div>