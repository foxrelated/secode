<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
	$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');
?>
<?php if($this->list->owner_id == $this->viewer_id): ?>
	<?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/_DashboardNavigation.tpl'; ?>
<?php else:?>
	<h2>
		<?php echo $this->list->__toString() ?>
		<?php echo $this->translate('&#187; Photos');?>
	</h2>
<?php endif;?>

<div>
	<?php echo $this->form->render($this) ?>
</div>