<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: detail.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<style type="text/css">
td{
	padding:3px;
}
td b{
	font-weight:bold;
}

/* small icons */
.rating_star_generic
{
  display: inline-block;
  width: 8px;
  height: 8px;
  background-repeat: no-repeat;
  font-size: 1px;
  cursor: default;
}
.rating_star,
.rating_star_half
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/star.png);
}

.rating_star_half
{
  background-image: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/star_half.png);
  width: 4px;
}
</style>

<div class="global_form_popup">
	<h3><?php echo $this->translate('Listing Details'); ?></h3><br />
	<table>
		<tr>
			<td width="120"><b><?php echo $this->translate('Title :'); ?></b></td>
			<td><?php echo $this->translate($this->listDetail->title); ?>&nbsp;&nbsp;</td>
			<tr >
				<td><b><?php echo $this->translate(' 	Owner :'); ?></b></td>
				<td><?php echo  $this->translate($this->listDetail->getOwner()->getTitle());?></td>
			</tr>
		
			<tr>
				<td><b><?php echo $this->translate('Featured :'); ?></b></td>
				<td>
					<?php if ($this->listDetail->featured)
						echo $this->translate('Yes');
						else
						echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Sponsored :'); ?></b></td>
				<td> <?php if ($this->listDetail->sponsored)
						echo $this->translate('Yes');
						else
						echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Creation Date :'); ?></b></td>
				<td>
				<?php echo $this->translate(gmdate('M d,Y, g:i A',strtotime($this->listDetail->creation_date))); ?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Approved :'); ?></b></td>
				<td>
					<?php  if ($this->listDetail->approved)
									echo $this->translate('Yes');
								else
									echo $this->translate("No") ;?>
				</td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Approved Date :'); ?></b></td>
				<td>
					<?php if(!empty($this->listDetail->approved_date)): ?>
					<?php echo $this->translate(date('M d,Y, g:i A',strtotime($this->listDetail->approved_date))); ?>
					<?php else:?>
					<?php echo $this->translate('-'); ?>
					<?php endif;?>
				</td>
			</tr>

			<?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1); ?>
			<?php if ($this->listDetail->location && $enableLocation): ?>
				<tr>
					<td><b><?php echo $this->translate('Location:'); ?></b></td>
					<td><?php echo $this->listDetail->location ?></td>
				</tr>
			<?php endif; ?>
		
			<tr>
				<td><b><?php echo $this->translate('Views :'); ?></b></td>
				<td><?php   echo $this->translate($this->listDetail->view_count ) ;?> </td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Comments :'); ?></b></td>
				<td><?php   echo $this->translate($this->listDetail->comment_count ) ;?> </td>
			</tr>

			<tr>
				<td><b><?php echo $this->translate('Likes :'); ?></b></td>
				<td><?php   echo $this->translate($this->listDetail->like_count ) ;?> </td>
			</tr>

			<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('list.rating', 1)): ?>
				<tr>           
					<td><b><?php echo $this->translate('Rating :'); ?></b></td>
					<td> 
						<?php for ($x = 1; $x <= $this->listDetail->rating; $x++): ?>
							<span class="rating_star_generic rating_star" title="<?php echo $list->rating.$this->translate(' rating'); ?>"></span>
						<?php endfor; ?>
						<?php if ((round($this->listDetail->rating) - $this->listDetail->rating) > 0): ?>
							<span class="rating_star_generic rating_star_half" title="<?php echo $list->rating.$this->translate(' rating'); ?>"></span>
						<?php endif; ?>
						<?php if (empty($this->listDetail->rating)) echo $this->translate("-");?>
					</td>
				</tr>
			<?php endif; ?>
		</table>
	<br />
	<button  onclick='javascript:parent.Smoothbox.close()' ><?php echo $this->translate('Close')  ?></button>
</div>

<?php if (@$this->closeSmoothbox): ?>
	<script type="text/javascript">
		TB_close();
	</script>
<?php endif; ?>