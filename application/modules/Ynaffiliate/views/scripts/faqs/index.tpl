<h3><?php echo $this->translate("FAQs") ?></h3>
<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynaffiliate-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynaffiliate-collapse"; ?>">
			<div class="ynaffiliate-faq-title">
				<span class="ynaffiliate-faq-icon"></span>
				<div class="ynaffiliate-faq-title-item ynaffiliate_question_preview"><?php echo $this->string()->truncate($item->question, 200);?></div>
				<div class="ynaffiliate-faq-title-item ynaffiliate_question_full"><?php echo $item->question?></div>
			</div>
			<div class="ynaffiliate-faq-content rich_content_body">
				<?php echo $item->answer?>
			</div>
		</div>
	<?php endforeach; ?>   
<?php else:?>
<div class="tip">
	<span>
		<?php echo $this->translate("No FAQs has been added.") ?>
	</span>
</div>
<?php endif; ?>

<!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>
<script type="text/javascript">
	$$('.ynaffiliate-faq-title').addEvent('click', function(){
		this.getParent('div.ynaffiliate-faq-item').toggleClass('ynaffiliate-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynaffiliate').getParent().addClass('active');
</script>