<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>

<div class="profile_fields">
		<h4><span><?php echo $this->translate('Store Statistic');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Total Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalStores); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->featuredStores); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Approved Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->approvedStores); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Available Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->showStores); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Users Follow Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->usersFollow); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Followed Stores") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->storesFollowed); ?>
				</span>
			</li>
		</ul>
	<h4><span><?php echo $this->translate('Product Statistic');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Total Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->totalProducts); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->featuredProducts); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Approved Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->approvedProducts); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Available Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->showProducts); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Users Favourite Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->usersFavourite); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Favourited Products") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->productsFavourited); ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Units Sold") ?></span>
				<span>
				<?php echo $this->locale()->toNumber($this->soldProducts); ?>
				</span>
			</li>
		</ul>
	<h4><span><?php echo $this->translate('Finance Statistic');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Total Publishing Fee from Stores") ?></span>
				<span>
				<?php echo $this->currency($this->storesPublishFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Feature Fee from Stores") ?></span>
				<span>
				<?php echo $this->currency($this->storesFeaturedFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Publishing and Feature Fee from Stores") ?></span>
				<span>
				<?php echo $this->currency($this->storesFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Publishing Fee from Products") ?></span>
				<span>
				<?php echo $this->currency($this->productsPublishFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Feature Fee from Products") ?></span>
				<span>
				<?php echo $this->currency($this->productsFeaturedFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Publishing and Feature Fee from Products") ?></span>
				<span>
				<?php echo $this->currency($this->productsFee) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Commission") ?></span>
				<span>
				<?php echo $this->currency($this->commission) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Income") ?></span>
				<span>
				<?php echo $this->currency($this->totalIncome) ?>
				</span>
			</li>
	</ul>	
	</div>


<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
.profile_fields {
    margin-top: 10px;
    overflow: hidden;
}
.profile_fields h4 {
    border-bottom: 1px solid #EAEAEA;
    font-weight: bold;
    margin-bottom: 10px;
    padding: 0.5em 0;
}
.profile_fields h4 > span {
    background-color: #FFFFFF;
    display: inline-block;
    margin-top: -1px;
    padding-right: 6px;
    position: absolute;
    color: #717171;
    font-weight: bold;
}
.profile_fields > ul {
    padding: 10px;
    list-style-type: none;
}
.profile_fields > ul > li {
    overflow: hidden;
    margin-top: 3px;
}

.profile_fields > ul > li > span {
    display: block;
    float: left;
    margin-right: 15px;
    overflow: hidden;
    width: 275px;
}

.profile_fields > ul > li > span + span {
    display: block;
    float: left;
    min-width: 0;
    overflow: hidden;
    width: auto;
}

</style>
