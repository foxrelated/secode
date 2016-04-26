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

<?php if (!empty($this->list->overview) && $this->list->owner_id == $this->viewer_id):?>
	<div class="seaocore_add">
		<a href='<?php echo $this->url(array('action' => 'overview', 'listing_id' => $this->list->listing_id), 'list_specific', true) ?>'  class="icon_lists_overview buttonlink"><?php echo $this->translate('Edit Overview'); ?></a>
	</div>
<?php endif;?>

<div>
	<?php if(!empty($this->list->overview)):?>
		<?php echo $this->translate($this->list->overview) ?>
	<?php else:?>
		<div class="tip">
			<span>
				<?php   echo $this->translate("You have not composed an overview for your listing. Click ").$this->htmlLink(
										array('route' => 'list_specific', 'action' => 'overview', 'listing_id' => $this->list->listing_id),
										$this->translate('here')
									).  $this->translate(" to compose it from the Dashboard of your listing.");?>
			</span>
		</div>
	<?php endif;?>
</div>