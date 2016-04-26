<?php
/*
 * Display campaign information
 */
?>
<script type="text/javascript">
window.addEvent('domready', function() {
	/*
     $("label", "#is_anonymous").addEvent(){
         var radioValue = $("#"+$(this).attr("for")).val();
      },
	*/
	if ($($("is_anonymous-element"))) {
		$("is_anonymous-element").addEvents({
		    mouseover: function(){
		    	$("is_anonymous-element").title = '<?php echo $this->translate('This will hide your name and donor information from all public activity feeds however the campaign will still receive your donor information') ?>';
		    }
		});
	}

});
</script>
<script type="text/javascript">
	var selectAmount = function(obj)
	{
		//$('amount-wrapper').hide();
		if (obj.get('for') != 'other') {
			$('amount').value = obj.get('for');
		}
		$$('a.ynfundraising_selectamount').each(function(el) {
			if (el.hasClass('ynfundraising_selectamount_select'))
					el.removeClass('ynfundraising_selectamount_select');
		});
		obj.addClass('ynfundraising_selectamount_select');
		if (obj.get('for') == 'other') {
			$('amount-wrapper').show();
			$('amount').focus();
			$('amount').value = "";
		}

	};
</script>

<?php
echo $this->partial ( '_menu.tpl', array () );
?>


<ul class="ynfundraising_campaigns_browse">
	<li>
		<div class='ynfundraising_campaigns_browse_photo'>
            <?php echo $this->htmlLink($this->campaign->getHref(), $this->itemPhoto($this->campaign, 'thumb.profile'))?>
     	</div>
		<div class='ynfundraising_campaigns_browse_info'>
			<div class='ynfundraising_campaigns_browse_info_title'>
				<b><?php echo $this->htmlLink($this->campaign->getHref(), $this->campaign->getTitle()) ?></b>
				- <font class="ynfundraising_campaigns_browse_info_status_">
                   <?php echo $this->translate(ucfirst($this->campaign->status));?>
                </font>
			</div>
			<div class=''>
				<?php echo $this->campaign->short_description?>
            </div>
		</div>
	</li>
</ul>

<?php echo $this->form->render($this)?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		if ($('list_other')) {
			//$('amount-wrapper').hide();
		}

		var flag = false;
		$$('a.ynfundraising_selectamount').each(function(el) {
			if ($('amount').value != '' && (el.get('for') == $('amount').value)) {
				el.addClass('ynfundraising_selectamount_select');
				flag = true;
			}
		});

		if ($('amount').value != '' && flag == false) {
			if ($('list_other')) {
				$('list_other').addClass('ynfundraising_selectamount_select');
			}
			$('amount-wrapper').show();
			$('amount').focus();
	}

});
</script>
