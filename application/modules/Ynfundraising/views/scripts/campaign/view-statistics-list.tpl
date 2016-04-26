<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Yndonation
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
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

<?php echo $this->htmlLink($this->campaign->getHref(), $this->translate('Back to Campaign'),array('class'=>'buttonlink ynFRaising_icon_back')) ?>
<?php if (count($this->paginator) > 0):?>
  <table class="ynfundraising_transaction_table">
    <thead>
      <tr>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('donation_date', 'DESC')"><?php echo $this->translate("Date") ?></a></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('amount', 'DESC')"><?php echo $this->translate("Amount") ?></th>
        <th><a href="javascript:void(0);" onclick = "javascript:changeOrder('transaction_id', 'DESC')"><?php echo $this->translate("Transaction ID")?></th>
        <th><?php echo $this->translate("Donor") ?></th>
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
	        	<td><?php echo $this->currencyfund( $item->amount, $item->currency )?></td>
	        	<td><?php echo $item->transaction_id; ?></td>
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
							echo $this->translate('Guest');
						}
					}

					?>
			  	</td>
			  	<td>
			  	<?php
			  	/*
			  		if ($item->user_id > 0) {
			  			echo $user->email;
			  		}
			  		else {
						if ($item->guest_email) {
							echo $item->guest_email;
						}
					}
				*/
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
  <div class="ynfundraising_paginator" style="margin-top: 10px">
		  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
	</div>
<?php elseif(isset($this->formValues['search']) || isset($this->formValues['start_date']) || isset($this->formValues['end_date'])) :?>
	<div class="tip">
    	<span>
        	<?php echo $this->translate('There are no transactions that match your search query.'); ?>
        </span>
	</div>
  <?php else :?>
	<div class="tip">
    	<span>
        	<?php echo $this->translate('There is no transaction.'); ?>
        </span>
	</div>
<?php endif; ?>
