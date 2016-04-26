<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: print.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/style_print.css');
?>

<link href="<?php echo $this->layout()->staticBaseUrl.'/application/modules/Seaocore/externals/styles/style_print.css'?>" type="text/css" rel="stylesheet" media="print">

<div class="seaocore_print_page">
	<div class="seaocore_print_title">
		<span class="right">
			<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
		</span>
		<?php if ($this->list->closed != 1): ?>
			<span class="left">
				<?php echo $this->translate($this->list->getTitle()) ?>
			</span>
		<?php endif; ?>	
	</div>
	<div class='seaocore_print_profile_fields'>
		<?php if ($this->list->closed == 1): ?>
			<div class="tip"> 
				<span> <?php echo $this->translate('This listing has been closed by the poster.'); ?> </span>
			</div>
			<br/>
		<?php else: ?>
      <div class="seaocore_print_photo">
      	<?php echo $this->itemPhoto($this->list, 'thumb.profile', '' , array('align' => 'left')); ?>
      	<div id="printdiv" class="seaocore_print_button">
					<a href="javascript:void(0);" style="background-image: url('<?php echo $this->layout()->staticBaseUrl?>application/modules/List/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
				</div>
      </div>
      <div class="seaocore_print_details">	      
				<h4>
					<?php echo$this->translate('Listing Information') ?>
				</h4>

				<ul>
					<li>
						<span><?php echo $this->translate('Posted By :'); ?> </span>
						<span><?php echo $this->translate($this->list->getParent()->getTitle()) ?></span>
					</li>
					<li>
						<span><?php echo $this->translate('Posted On :'); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->list->creation_date))) ?></span>
					</li>
					<?php if(!empty($this->list->comment_count)): ?>
						<li>
							<span><?php echo $this->translate('Comments :'); ?></span>
							<span><?php echo $this->translate( $this->list->comment_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->list->view_count)): ?>
						<li>
							<span><?php echo $this->translate('Views :'); ?></span>
							<span><?php echo $this->translate( $this->list->view_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if(!empty($this->list->like_count)): ?>
						<li>
							<span><?php echo $this->translate('Likes :'); ?></span>
							<span><?php echo $this->translate( $this->list->like_count) ?></span>
						</li>
					<?php endif; ?>
					<?php if ($this->category_name): ?>
						<li>
							<span><?php echo $this->translate('Category :'); ?></span> 
							<span><?php echo $this->translate($this->category_name) ?>
								<?php if ($this->subcategory_name): ?> &raquo;
									<?php echo $this->translate($this->subcategory_name) ?>
								<?php endif; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ($this->listTags): $tagCount=0;?>
					 <li>
						<span><?php echo $this->translate('Tag :'); ?></span>
							<span>
								<?php foreach ($this->listTags as $tag): ?>
									<?php if (!empty($tag->getTag()->text)):?>
										<?php if(empty($tagCount)):?>
											<?php echo "#". $tag->getTag()->text?>
											<?php "#".$tagCount++; ?>
										<?php else: ?>
											<?php echo $tag->getTag()->text?>
										<?php endif; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<li>
						<span><?php echo $this->translate('Description :'); ?></span>
						<span><?php echo $this->translate(''); ?> <?php echo $this->list->body ?></span>
					</li>
					<?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('list.locationfield', 1); ?>
          <?php if($this->list->location && $enableLocation):?>
						<li>
							<span><?php echo $this->translate('Location:'); ?></span>
							<span><?php echo $this->list->location ?></span>
						</li>
          <?php endif; ?>
				</ul>
				<?php $str = $this->fieldValueLoop($this->list, $this->fieldStructure); if(!empty($str) ): ?>
					<h4>
						<?php echo$this->translate('Profile Information') ?>
					</h4>
          <?php echo Engine_Api::_()->list()->removeMapLink($this->fieldValueLoop($this->list, $this->fieldStructure)) ?>					
				<?php endif; ?>
			</div>	
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
 function printData() {
		document.getElementById('printdiv').style.display = "none";
		window.print();
		setTimeout(function() {
					document.getElementById('printdiv').style.display = "block";
		}, 500);
	}
</script>