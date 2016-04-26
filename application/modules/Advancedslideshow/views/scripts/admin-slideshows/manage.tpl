<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">

	function preview(advancedslideshow_id, height, width) {
		 var height_width = "width="+width+",height="+height;
		 var child_window = window.open (en4.core.baseUrl + 'admin/advancedslideshow/slideshows/previewmanage/advancedslideshow_id/'+ advancedslideshow_id,'mywindow',height_width);
	}

	function multiDelete()
	{
		return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete selected slideshows ?")) ?>');
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

<h2><?php echo $this->translate("Advanced Slideshow Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
    	<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>
<?php if(empty($this->getSlideManage)){ return; } ?>
<?php if($this->paginator->getTotalItemCount()): ?>	

	<h3><?php echo $this->translate("Manage Slideshows"); ?></h3>
	<p class="form-description"><?php echo $this->translate("Create slideshow by using 'Create New Slideshow' below. When you create a slideshow from here for a widgetized page of your site, it gets automatically placed on that page. <b style='font-weight:bold;'>It is recommended that you create all your slideshows from here, and not place them on the pages from the Layout Editor, as it would not work</b>. You can also create slideshows for non-widgetized pages of your site.<br />You can also edit, manage and delete slideshows by clicking on the links for each. You can make slideshow enabled/dis-abled and you can see preview by clicking on the preview links for each."); ?></p><br />
    
    <div class="tip">
      <span>
        <?php 
        echo $this->translate('We have released a new Slideshow Type: "HTML Slides with Bullet Navigation". For details, please <a href="http://www.socialengineaddons.com/content/enhancements-advanced-slideshow-plugin-multiple-slideshows" target="_blank">visit here</a>.');
        ?>
      </span>
    </div>

	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/slideshow_add.png" class="icon" />
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'create'), $this->translate('Create New Slideshow'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>

	<?php
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'settings', 'action' => 'guidelines'), $this->translate("Guidelines for placing Slideshows on Non-widgetized Pages"), array('class'=>'buttonlink advslideshow_icon_help'));
	?>
	<br /><br />

	<div>
		<div class="admin_files_pages">
     	<?php $pageInfo = $this->paginator->getPages(); ?>
			<?php echo $this->translate("Showing ");?><?php echo $pageInfo->firstItemNumber ?>-<?php echo $pageInfo->lastItemNumber ?><?php echo $this->translate(" of "); ?><?php echo $pageInfo->totalItemCount ?><?php echo $this->translate(" slideshow.")?>
    </div>
    <form id='multidelete_form' name='multidelete_form' method="post" action="<?php echo $this->url(array('action'=>'multi-delete'));?>" >
  			<table class='admin_table' width='100%'>
    			<thead>
      			<tr>
        			<th width="1%" align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
							<th width="1%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('advancedslideshow_id', 'ASC');"><?php echo $this->translate('Id'); ?></a></th>
        			<th width="10%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('widget_title', 'ASC');"><?php echo $this->translate('Name'); ?></a></th>
        			<th width="20%" align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate('Page'); ?></a></th>
         			<th align="left"><a href="javascript:void(0);" onclick="javascript:changeOrder('widget_position', 'ASC');"><?php echo $this->translate('Position'); ?></a></th>
							<th width="10%" align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('slideshow_type', 'ASC');"><?php echo $this->translate('Type'); ?></a></th>
							<th width="5%" align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('enabled', 'ASC');"><?php echo $this->translate('Status'); ?></a></th>
							<th width="5%" align="center"><?php echo $this->translate('Total Slides'); ?></th>
         			<th align="left"><?php echo $this->translate('Options'); ?></th>
      			</tr>
    			</thead>
					<tbody>
					<?php foreach( $this->paginator as $item ):?>
						<tr>
							<td>
								<input name='delete_<?php echo $item->advancedslideshow_id;?>' type='checkbox' class='checkbox' value="<?php echo $item->advancedslideshow_id ?>"/>
							</td>
							<td>
								<?php echo $item->advancedslideshow_id ?>
							</td>
					
							<td>
								<span title='<?php echo $item->widget_title; ?>'>
									<?php $widget_title = strip_tags($item->widget_title);
									$widget_title = Engine_String::strlen($widget_title) > 20 ? Engine_String::substr($widget_title, 0, 20) . '..' : $widget_title;?>
									<?php echo $widget_title ?>
								</span>
							</td>

							<td>
								<?php if(!empty($item->displayname)): ?>
									<?php echo $item->displayname ?>
								<?php else:?>
									<?php echo $this->translate("Non-Widgetized Page"); ?>
								<?php endif; ?>
							</td>

							<td>
								<?php 
									if($item->widget_position == 'full_width1' || $item->widget_position == 'full_width2' || $item->widget_position == 'full_width3' || $item->widget_position == 'full_width4' || $item->widget_position == 'full_width5')
										echo $this->translate("Full Width");
									elseif($item->widget_position == 'middle_column1' || $item->widget_position == 'middle_column2' || $item->widget_position == 'middle_column3')
										echo $this->translate("Middle Column");
									elseif($item->widget_position == 'right_column1' || $item->widget_position == 'right_column2' || $item->widget_position == 'right_column3')
										echo $this->translate("Right Column");
									elseif($item->widget_position == 'left_column1' || $item->widget_position == 'left_column2' || $item->widget_position == 'left_column3')
										echo $this->translate("Left Column");
									elseif($item->widget_position == 'extreme1' || $item->widget_position == 'extreme2' || $item->widget_position == 'extreme3')
										echo $this->translate("Extended Right/Left");
									else
										echo "----";
								?>
							</td>

							<td class="admin_table_centered">
								<?php 
									if($item->slideshow_type == 'flom')
										echo $this->translate("Curtain/Blind");
									elseif($item->slideshow_type == 'zndp')
										echo $this->translate("Zooming&Panning");
									elseif($item->slideshow_type == 'push')
										echo $this->translate("Push");
									elseif($item->slideshow_type == 'flas')
										echo $this->translate("Flash");
									elseif($item->slideshow_type == 'fold')
										echo $this->translate("Fold");
									elseif($item->slideshow_type == 'fadd')
										echo $this->translate("Fading");
									elseif($item->slideshow_type == 'noob')
										echo $this->translate("HTML Slides with Bullet Navigation");
                                                                       	else
										echo "----";
								?>
							</td>

							<?php if($item->enabled == 1):?>
								<td class="admin_table_centered">
									<?php echo $this->htmlLink(array('module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'enabled', 'advancedslideshow_id' => $item->advancedslideshow_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/caption_true.gif', '', array('title'=> $this->translate('Disable Slideshow')))) ?> 
								</td>
							<?php else: ?>
								<td class="admin_table_centered">
									<?php echo $this->htmlLink(array('module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'enabled', 'advancedslideshow_id' => $item->advancedslideshow_id), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Advancedslideshow/externals/images/caption_false.gif', '', array('title'=> $this->translate('Enable Slideshow')))) ?>
								</td>
							<?php endif; ?>

							<td class="admin_table_centered"><?php echo Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getTotalSlides($item->advancedslideshow_id); ?></td>

							<td>
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'edit', 'slideshowtype' => $item->slideshow_type, 'advancedslideshow_id' => $item->advancedslideshow_id), $this->translate('Edit')) ?>
								|
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $item->advancedslideshow_id), $this->translate('Manage')) ?>
								| 
								<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'delete', 'advancedslideshow_id' => $item->advancedslideshow_id), $this->translate('Delete'), array(
									'class' => 'smoothbox',
								)) ?>
								|
								<?php if($item->thumbnail): ?>
									<?php $height = $item->height+65;?>
									<a href="javascript:void(0);" onclick="javascript:preview('<?php echo $item->advancedslideshow_id ?>', '<?php echo $height ?>', '<?php echo $item->width ?>');"><?php echo $this->translate('Preview'); ?></a>
								<?php else: ?>
									<a href="javascript:void(0);" onclick="javascript:preview('<?php echo $item->advancedslideshow_id ?>', '<?php echo $item->height ?>', '<?php echo $item->width ?>');"><?php echo $this->translate('Preview'); ?></a>
								<?php endif; ?>

								<?php if($item->widget_page == -1): ?>
									|
									<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'code', 'advancedslideshow_id' => $item->advancedslideshow_id), $this->translate('Code'), array('class' => 'smoothbox',)) ?>
								<?php endif; ?>

							</td>
			         
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<br /><?php echo $this->paginationControl($this->paginator); ?>

				<div style="height:10px;"></div>
	      &nbsp;<button type='submit' name="delete" onclick="return multiDelete()" value="delete_image"><?php echo $this->translate('Delete Selected'); ?></button>	&nbsp;&nbsp;&nbsp;
			</form>	

<?php else:?>
	<div class="tip">
      <span>
        <?php echo $this->translate('You have not yet created any slideshow. Get started by ').$this->htmlLink(array(
			          'route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'create'
			        ), $this->translate('creating some.')); ?>
      </span>
    </div>	
<?php endif; ?>