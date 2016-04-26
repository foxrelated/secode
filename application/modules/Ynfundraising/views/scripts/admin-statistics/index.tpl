<link type="text/css" href="application/modules/Ynfundraising/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynfundraising/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynfundraising/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>

<script type="text/javascript">
    jQuery.noConflict();
    jQuery(document).ready(function(){
        // Datepicker
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#end_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynfundraising/externals/images/calendar.png',
            buttonImageOnly: true
        });

    });

 function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['orderby'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('orderby').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }

</script>

<?php
echo $this->partial('_menu_admin.tpl', array('tab_select' => 'ynfundraising_admin_main_statistics'));
?>
<?php echo $this->translate('YNFUNDRAISING_VIEWS_SCRIPTS_ADMINSTATISTICS_DESCRIPTION')?>
<?php echo $this->form->render($this)?>
<?php if (count($this->paginator) > 0):?>
<div>
  <table class="admin_table" style="padding-top: 15px; clear: both">
    <thead>
      <tr>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('donation_date', 'DESC')"><?php echo $this->translate("Date") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('campaign_title', 'DESC')"><?php echo $this->translate("Campaign") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('owner_title', 'DESC')"><?php echo $this->translate("Owner") ?></a></th>
        <th><?php echo $this->translate("Donor") ?></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('transaction_id', 'DESC')"><?php echo $this->translate("Transaction ID")?></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('amount', 'DESC')"><?php echo $this->translate("Amount") ?></th>
		<th><a href="javascript:void(0);" onclick = "javascript:changeOrder('payer_email', 'DESC')"><?php echo $this->translate("Email Address") ?></th>
		<th><?php echo $this->translate("Option") ?></th>
      </tr>
    </thead>
    <tbody>
      	<?php
      	foreach ($this->paginator as $item):
      	?>
	        <tr>
	        	<td>
					<?php
					// requested date
					echo $this->locale()->toDateTime($item->donation_date)
					//echo $this->timestamp($item->transaction_date);
				  	?>
	        	</td>
	        	<td>
	        	<?php
	        	$campaign = Engine_Api::_()->getItem('ynfundraising_campaign', $item->campaign_id);
	        	echo $campaign->getTitle();
	        	?>
	        	</td>
	        	<td>
	        	<?php
	        	echo $this->htmlLink($campaign->getOwner()->getHref(), $campaign->getOwner()->getTitle());
	        	?>
	        	</td>
	        	<td>
	        	<?php
					// donor
					if ($item->user_id > 0) {
						$user = Engine_Api::_ ()->getItem ( 'user', $item->user_id);
						echo $this->htmlLink($user->getHref(), $user->getTitle());
					}
					else {
						if ($item->guest_name) {
							echo $item->guest_name;
						}
						else {
							echo $this->translate('Anonymous');
						}
					}

					?>
	        	</td>
			  	<td><?php echo $item->transaction_id;?></td>
	        	<td><?php echo $this->currencyfund( $item->amount, $item->currency )?></td>
			  	<td>
			  	<?php
			  		echo $item->payer_email;
			  		?>
			  	</td>
			  	<td>
			  		<?php
			  		echo $this->htmlLink(
								array('route' => 'ynfundraising_extended',
										'controller' => 'campaign',
										'action' => 'view-statistics-detail',
										'donation_id' => $item->donation_id,
								),
								$this->translate('View Details'),
								array('class' => 'smoothbox')
					);
					?>
			  	</td>
	        </tr>

      	<?php endforeach; ?>

    </tbody>
  </table>
  <div class="ynfundraising_paginator" style="padding-top: 10px">
		  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
	</div>
</div>
  <?php else :?>
	<div class="tip">
    	<span>
        	<?php echo $this->translate('There is no transaction that match your search query.'); ?>
        </span>
	</div>
<?php endif; ?>
