<h2><?php echo $this->translate("Store Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>
<div class="profile_fields">
		<h4><span><?php echo $this->translate('Store Statistic');?></span></h4>
		<ul>
			<li>
				<span><?php echo $this->translate("Store") ?></span>
				<span>
					<?php echo $this->store; ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Summary") ?></span>
				<span>
					<?php echo $this->translate(array('%s follow', '%s follows', $this->store->follow_count), $this->locale()->toNumber($this->store->follow_count)) ?>
					-
					<?php echo $this->translate(array('%s view', '%s views', $this->store->view_count), $this->locale()->toNumber($this->store->view_count)) ?>
					-
					<?php echo $this->translate(array('%s comment', '%s comments', $this->store->comment_count), $this->locale()->toNumber($this->store->comment_count)) ?>					
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getTotalProduct()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Available Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getAvailableProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Store Rating") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->rate_ave)." ".$this->translate('Stars'); ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Featured Products") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getFeaturedProduct()) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Units Sold") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->sold_products) ?>
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Income") ?></span>
				<span>
					<?php echo $this->currency($this->store->getTotalAmount()) ?>
				</span>
			</li>
		
			<li>
				<span><?php echo $this->translate("Total Publish Fee") ?></span>
				<span>
					<?php echo $this->currency($this->store->getPublishedFee()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Total Feature Fee") ?></span>
				<span>
					<?php echo $this->currency($this->store->getFeaturedFee()) ?> 
				</span>
			</li>
			<li>
				<span><?php echo $this->translate("Commission Rate") ?></span>
				<span>
					<?php echo $this->locale()->toNumber($this->store->getCommissionRate()).'%' ?>
				</span>
			</li>
			<li>
			<a href="<?php echo $this->url(array('action'=>'product-statistic', 'store_id'=>$this->store->store_id)) ?>"><?php echo $this->translate('Products Statistic');?></a>
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
