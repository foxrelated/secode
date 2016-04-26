<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
	<div class="profile_fields">
		<h4><span><?php echo $this->translate('Information');?></span></h4>
		<ul>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Title") ?></span>
				<span><?php echo $this->store ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Description") ?></span>
				<span><?php echo $this->store->getDescription() ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("View Status") ?></span>
				<span><?php echo $this->translate(ucfirst($this->store->view_status)) ?> | 
					<a class = "smoothbox" href="<?php echo $this->url(array("module"=>"socialstore","controller"=>"my-store","action"=>"show"), "default") ?>" class="<?php echo $this->active_menu=="my-products"?"active":"" ?> my-products"><?php if ($this->store->view_status == 'show') :
																																																						echo $this->translate("Hide Store"); 
																																																				else  : 
																																																						echo $this->translate("Show Store");
																																																				endif;?></a></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Created Date") ?></span>
				<span><?php echo $this->timestamp($this->store->creation_date) ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Approve Status") ?></span>
				<span><?php echo $this->translate(ucfirst($this->store->approve_status)) ?>
					<?php if($this->store->isApproved()) :?>
						- <?php echo  $this->timestamp($this->store->approved_date)?>
					<?php endif; ?>
					<?php if ($this->store->approve_status == 'new') :?>
					<br />
					<?php echo $this->translate('Your store has not been approved, started by %1$spublish%2$s your store!', '<a href="'.$this->url(array("module"=>"socialstore","controller"=>"my-store",'action' => 'publish-store',"store_id" => $this->store->getIdentity() ), "default",true).'">', '</a>'); ?>
					<?php endif;?>
				</span>
			</li>
		</ul>
	</div>
	<div class="profile_fields">
		<h4><span><?php echo $this->translate('Contact Information');?></span></h4>
		<ul>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Contact Person") ?></span>
				<span><?php echo $this->store->contact_name ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Email") ?></span>
				<span><a href="mailto:<?php echo $this->store->contact_email;?>"><?php echo $this->store->contact_email ?></a></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Address") ?></span>
				<span><?php echo $this->store->contact_address ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Location") ?></span>
				<span><?php echo $this->store->getLocation()->getFullString() ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Phone") ?></span>
				<span><?php echo $this->store->contact_phone ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Fax") ?></span>
				<span><?php echo $this->store->contact_fax ?></span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Website") ?></span>
				<span><a href= "<?php echo $this->store->contact_website;?>" target="_blank"><?php echo $this->store->contact_website ?></a></span>
			</li>
		</ul>
	</div>
	<div class="profile_fields">
		<h4><span><?php echo $this->translate('Quick Statistics');?></span></h4>
		<ul>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Summary") ?></span>
				<span>
					<?php echo $this->translate(array('%s follow', '%s follows', $this->store->follow_count), $this->locale()->toNumber($this->store->follow_count)) ?>
					-
					<?php echo $this->translate(array('%s view', '%s views', $this->store->view_count), $this->locale()->toNumber($this->store->view_count)) ?>
					-
					<?php echo $this->translate(array('%s comment', '%s comments', $this->store->comment_count), $this->locale()->toNumber($this->store->comment_count)) ?>					
				</span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Total Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getTotalProduct()) ?> 
				</span>
			</li>
			<li>
				<span class="store_span_title"><?php echo $this->translate("Available Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getAvailableProduct()) ?>
				</span>
			</li>
		</ul>
	</div>
</div>

