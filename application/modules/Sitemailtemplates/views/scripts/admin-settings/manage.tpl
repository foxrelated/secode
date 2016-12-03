<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
 
  function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected templates ?")) ?>');
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

<h2><?php echo $this->translate("Email Templates Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<h3><?php echo $this->translate("Manage Templates"); ?></h3>
<p class="form-description"><?php echo $this->translate('The ability to maintain your brand identity in your email communication with your users is an important aspect for the growth of your website / community. This page contains settings to customize the template of emails going out from your site to match your site\'s identity and look elegant. Such emails have more impact than plain, simple emails.<br />Below, you can create template by using "Create New Template" link. The templates that you create here will be available to you in the “Mail Templates” section of this plugin to enable them for chosen messages. You can also edit and delete templates created by you by clicking on the links for each. You can also activate any template by clicking on the ‘Activate’ links for each.<br />Note : When you have finalized the email template designs, be sure to activate them for outgoing emails using the “Activate Rich Emails” field from “Global Settings” section. You can not delete the default templates available to you with this plugin and the ‘Activated’ template.');?></p><br />

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitemailtemplates/externals/images/template_add.png" class="icon" />
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'create'), $this->translate('Create New Template'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br /><br />

<div class="admin_files_pages">
	<?php $pageInfo = $this->paginator->getPages(); ?>
  <?php $totalItemCount = $pageInfo->totalItemCount;?>
	<?php echo $this->translate("Showing ");?><?php echo $pageInfo->firstItemNumber ?>-<?php echo $pageInfo->lastItemNumber ?><?php echo $this->translate(" of "); ?><?php echo $totalItemCount ?>
   <?php if($totalItemCount >1):?><?php echo $this->translate(" templates.")?><?php else:?><?php echo $this->translate(" template.")?><?php endif;?>
</div>

<form id='multidelete_form' name='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" >
	<table class='admin_table'>
		<thead>
			<tr>
				<th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
				<th style='width: 10%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('template_id', 'DESC');"><?php echo $this->translate('ID'); ?></a></th>
				<th style='width: 20%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('template_title', 'ASC');"><?php echo $this->translate('Name'); ?></a></th>
				<th style='width: 20%;' class='admin_table_options'><?php echo $this->translate('Options'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $this->paginator as $item ):?>
				<tr>
					<td>
						<input name='delete_<?php echo $item->template_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->template_id ?>"<?php if($item->active_delete == 1 || !empty($item->active_template)):?> disabled="true" <?php endif;?>/>
					</td>
					<td>
						<?php echo $item->template_id ?>
					</td>
					<td>
						<?php echo $item->template_title ?>
					</td>
					<td>
						<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'edit', 'template_id' => $item->template_id), $this->translate('Edit')) ?>
						|

            <?php if($item->active_delete == 0 && empty($item->active_template)):?>
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'delete', 'template_id' => $item->template_id), $this->translate('Delete'), array(
								'class' => 'smoothbox',
							)) ?>
						<?php else:?>
              <?php echo $this->translate('Delete');?>
            <?php endif;?>
            |
						<?php if($item->active_template == 0):?>
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'activate-template','template_id' => $item->template_id), $this->translate('Activate as Default'),array(
								'class' => 'smoothbox',
							)) ?>
						<?php else :?>
							<?php echo $this->translate('Activated');?>
						<?php endif;?>
            <?php if($item->active_delete == 1):?>|
							<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemailtemplates', 'controller' => 'settings', 'action' => 'show-template', 'template_id' => $item->template_id), $this->translate('Template Design'),array(
								'class' => 'smoothbox',
							)) ?>
							
            <?php endif;?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
  <div style="height:10px;"></div>
	&nbsp;<button type='submit' name="delete" onclick="return multiDelete()" value="delete_image"><?php echo $this->translate('Delete Selected'); ?></button>	&nbsp;&nbsp;&nbsp;
  <br /><?php echo $this->paginationControl($this->paginator); ?>
<form>
