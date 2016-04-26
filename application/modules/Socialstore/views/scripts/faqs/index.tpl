<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<!--  do not remove this line  -->
<a id="faq-0" name="faq-0"></a>
<h2><?php echo $this->translate("FAQs") ?></h2>
<div class="layout_middle">
<?php if (count($this->items) > 0): ?>
<ul class="faqs-lists">
	<?php foreach($this->items as $item): ?>
	<li><a href="<?php echo $this->url(array('controller'=>'faqs','action'=>'index')) ?>#faq-<?php echo $item->getIdentity() ?>"><?php echo $item->question ?></a></li>
	<?php endforeach; ?>
</ul>

<ul class="faq-details-list">
<?php 
$i=0;
foreach($this->items as $item): ?>
	<!--  do not remove this line  -->
	<li>
		<a id="faq-<?php echo $item->getIdentity() ?>" name="faq-<?php echo $item->getIdentity() ?>"></a>
		<div class="faq-question"><?php echo ++$i?>. <?php echo $item->question ?></div>
		<div class="faq-answer">
			<?php echo $item->answer ?>
		</div>
		<div class="faq-gotop"><a class="go-top" href="<?php echo $this->url(array('controller'=>'faqs','action'=>'index')) ?>#faq-0"><?php echo $this->translate("Go Top")?></a></div>
		
	</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
	<div class="tip"><span><?php echo $this->translate('There are no FAQs yet.')?></span></div>
<?php endif; ?>
</div>