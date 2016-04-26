<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: manage.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<script type="text/javascript">

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected Listings ?")) ?>');
	}

	function selectAll()
	{
	  var i;
	  var multidelete_form = $('multidelete_form');
	  var inputs = multidelete_form.elements;
	  for (i = 1; i < inputs.length - 1; i++) {
	    if (!inputs[i].disabled) {
	      inputs[i].checked = inputs[0].checked;
    	}
  	}
	}
</script>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){  

    if( order == currentOrder ) { 
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else { 
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<h2><?php echo $this->translate("Listings / Catalog Showcase Plugin") ?></h2>

<?php if(count($this->navigation)): ?>
	<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<h3><?php echo $this->translate("Widget Settings") ?></h3>
<?php echo $this->translate("Configure the settings for the various widgets available with this plugin.") ?><br /><br />

<div class='tabs'>
	<ul class="navigation">
		<li>                               
			<?php  echo $this->htmlLink(array('module' => 'list','controller' => 'settings','action' => 'widget-settings'), $this->translate('General Settings'), array()); ?>
		</li>
		<li class="active">
			<?php   echo $this->htmlLink(array('module' => 'list','controller' => 'items','action' => 'manage'), $this->translate('Listing of the Day'), array());?>
		</li>
	</ul>
</div>

<div class='clear'>
	<div class='settings'>
		<form id='multidelete_form' method="post" action="<?php echo $this->url(array( 'module' => 'list', 'controller' => 'items', 'action' => 'multi-delete'),'admin_default');?>" onSubmit="return multiDelete()" class="global_form">
			<div>
				<h3><?php echo $this->translate("Listing of the Day widget") ?> </h3>
				<p class="description">
					<?php echo $this->translate("Add and Manage the listings on your site to be shown in the Listing of the Day widget. You can also mark these listings for future dates such that the marked listing automatically shows up as Listing of the Day on the desired date. Note that for this listing of the day to be shown, you must first place the Listing of the Day widget at the desired location.") ?>
				</p>
				<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'items', 'action' => 'add-item'), $this->translate('Add a Listing of the Day'), array(
					'class' => 'smoothbox buttonlink',
					'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>	<br/>	<br/>
				<?php if($this->paginator->getTotalItemCount()): ?>
					<table class='admin_table' style="width:700px;">
						<thead>
							<tr>
								<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
								<th width="550" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('engine4_list_listings.title', 'DESC');"><?php echo $this->translate("Listing Title") ?></a></th>
								<th width="70" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
								<th width="70" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>				
								<th width="70" align="left"><?php echo $this->translate("Option") ?></th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($this->paginator as $item): ?>
								<tr>
									<td><input name='delete_<?php echo $item->itemoftheday_id ;?>' type='checkbox' class='checkbox' value="<?php echo $item->itemoftheday_id  ?>"/></td>

									<td class='admin_table_bold' style="white-space:normal;" title='<?php echo $item->title ?>'>
										<a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'listing_id' => $item->listing_id, 'slug' => $item->getSlug()), 'list_entry_view') ?>"  target='_blank'>
											<?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 100) ?>
										</a>
									</td>
									<td align="left"><?php echo $item->date?></td>
									<td align="left"><?php echo $item->endtime?></td>
									<td align="left">
										<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'list', 'controller' => 'items', 'action' => 'delete-item', 'id' => $item->itemoftheday_id), $this->translate('delete'), array(
											'class' => 'smoothbox',)) ?>
									</td>
								</tr>
							<?php  endforeach; ?>
						</tbody>
					</table><br />
					<div class='buttons'>
						<button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
					</div>
				<?php else:?>
					<div class="tip"><span><?php  echo $this->translate("No listings have been marked as Listing of the Day."); ?></span> </div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>
<?php echo $this->paginationControl($this->paginator); ?>