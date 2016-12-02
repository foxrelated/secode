<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: print.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_print.css');
?>
<link href="<?php echo $this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/style_print.css'?>"  type="text/css" rel="stylesheet" media="print">
<?php
  $contactPrivacy = Engine_Api::_()->sitegroup()->isManageAdmin($this->sitegroup, 'contact');
?>
<div class="seaocore_print_group">
	<div class="seaocore_print_title">	
    <span class="left">
      <?php echo $this->sitegroup->getTitle(); ?>
    </span>
		<span class="right">
			<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'));?>
		</span>
	</div>
	<div class='seaocore_print_profile_fields'>
		<?php if ($this->sitegroup->closed == 1): ?>
			<div class="tip"> 
				<span> <?php echo $this->translate('This group has been closed by the poster.'); ?> </span>
			</div>
			<br/>
		<?php else: ?>
			<div class="seaocore_print_photo">
				<?php echo $this->itemPhoto($this->sitegroup, 'thumb.normal', '' , array('align'=>'left')); ?>
				<div id="printdiv" class="seaocore_print_button">
					<a href="javascript:void(0);" style="background-image: url('./application/modules/Sitegroup/externals/images/printer.png');" class="buttonlink" onclick="printData()" align="right"><?php echo $this->translate('Take Print') ?></a>
				</div>
			</div>
			<div class="seaocore_print_details">	      
				<h4>
					<?php echo $this->translate('Group Information') ?>
				</h4>
				<ul>
          <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1)):?>
            <li>
              <span><?php echo $this->translate('Created By:'); ?> </span>
              <span><?php echo $this->translate($this->sitegroup->getParent()->getTitle()) ?></span>
            </li>
          <?php endif;?>
					<li>
						<span><?php echo $this->translate('Posted On:'); ?></span>
						<span><?php echo $this->translate( gmdate('M d, Y', strtotime($this->sitegroup->creation_date))) ?></span>
					</li>
					<?php if(!empty($this->sitegroup->comment_count)): ?>
					<li>
						<span><?php echo $this->translate('Comments:'); ?></span>
						<span><?php echo $this->translate( $this->sitegroup->comment_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitegroup->view_count)): ?>
					<li>
						<span><?php echo $this->translate('Views:'); ?></span>
						<span><?php echo $this->translate( $this->sitegroup->view_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if(!empty($this->sitegroup->like_count)): ?>
					<li>
						<span><?php echo $this->translate('Likes:'); ?></span>
						<span><?php echo $this->translate( $this->sitegroup->like_count) ?></span>
					</li>
					<?php endif; ?>
					<?php if ($this->category): ?>
					<li>
						<span><?php echo $this->translate('Category:'); ?></span> 
						<span><?php echo $this->translate($this->category->category_name) ?>
						<?php if ($this->subcategory): ?> &raquo;
						<?php echo $this->translate($this->subcategory->category_name) ?>
            <?php endif; ?>
            <?php if ($this->subsubcategory): ?> &raquo;
            <?php echo $this->translate($this->subsubcategory->category_name); ?>
						<?php endif; ?>
						</span>
					</li>
					<?php endif; ?>
					<?php if ($this->sitegroupTags): $tagCount=0;?>
						<li>
							<span><?php echo $this->translate('Tag:'); ?></span>
								<span>
								<?php foreach ($this->sitegroupTags as $tag): ?>
								<?php if (!empty($tag->getTag()->text)):?>
								<?php if(empty($tagCount)):?>
								<?php echo "#". $tag->getTag()->text?>
								<?php "#".$tagCount++; else: ?>
								<?php echo $tag->getTag()->text?>
								<?php endif; ?>
								<?php endif; ?>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<li>
						<span><?php echo $this->translate('Description:'); ?></span>
						<span><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->body ?></span>
					</li>
           <?php  $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1); ?>
           <?php if($this->sitegroup->price && $enablePrice):?>
          <li>
            <span><?php echo $this->translate('Price:'); ?></span>
            <span><?php echo $this->locale()->toCurrency($this->sitegroup->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></span>
          </li>
          <?php endif; ?>
           <?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1); ?>
           <?php if($this->sitegroup->location && $enableLocation):?>
          <li>
            <span><?php echo $this->translate('Location:'); ?></span>
            <span><?php echo $this->sitegroup->location ?></span>
          </li>
          <?php endif; ?>
				</ul>
					<?php
						$user = Engine_Api::_()->user()->getUser($this->sitegroup->owner_id);
						$view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitegroup_group', $user, 'contact_detail');
						$availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');
						$options_create = array_intersect_key($availableLabels, array_flip($view_options));
					?>
          <?php if(!empty($contactPrivacy)): ?>
            <?php if(!empty($options_create) && (!empty($this->sitegroup->email) || !empty($this->sitegroup->website) || !empty($this->sitegroup->phone))):?>
							<h4>
								<?php echo $this->translate('Contact Details:');  ?>
							</h4>
            <?php endif; ?>
						<ul>               
							<?php if($options_create['phone'] == 'Phone'):?>
								<?php if(!empty($this->sitegroup->phone)):?>
								<li>
									<span><?php echo $this->translate('Phone:'); ?></span>
									<span><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->phone ?></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['email'] == 'Email'):?>
								<?php if(!empty($this->sitegroup->email)):?>
								<li>
									<span><?php echo $this->translate('Email:'); ?></span>
									<span><?php echo $this->translate(''); ?>
									<a href='mailto:<?php echo $this->sitegroup->email ?>'><?php echo $this->sitegroup->email ?></a></span>
								</li>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($options_create['website'] == 'Website'):?>
								<?php if(!empty($this->sitegroup->website)):?>
								<li>
									<span><?php echo $this->translate('Website:'); ?></span>
									<?php if(strstr($this->sitegroup->website, 'http://')):?>
									<span><a href='<?php echo $this->sitegroup->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></span>
									<?php else:?>
									<span><a href='http://<?php echo $this->sitegroup->website ?>'><?php echo $this->translate(''); ?> <?php echo $this->sitegroup->website ?></a></span>
									<?php endif;?>
								</li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
          <?php endif; ?>    
                            <?php if($this->sitegroup->profile_type): ?>
                <?php $str = $this->GroupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure); ?>
                <?php if(!empty($str) ): ?>
                  <h4>
                    <?php echo $this->translate('Profile Information') ?>
                  </h4>
                             <?php
                             $params = array('custom_field_heading' => 1, 'custom_field_title' => 1, 'customFieldCount' => 1000);
                             ?>
                  <?php echo $this->GroupProfileFieldValueLoop($this->sitegroup, $this->fieldStructure, $params) ?>	
                       
                <?php endif; ?>
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
