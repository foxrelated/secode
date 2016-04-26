<?php
$this->headScript()
	   ->appendFile($this->layout() -> staticBaseUrl . 'application/modules/Ynaffiliate/externals/scripts/moo.flot.js')
	   ->appendFile($this->layout() -> staticBaseUrl . 'application/modules/Ynaffiliate/externals/scripts/moo.flot.pie.js');
?>

<div class="ynaffiliate_stistic_count">
   <div class="ynaffiliate_stistic_count_header"><?php echo $this->translate('Affiliate Statistic'); ?></div>
   <div class="ynaffiliate_list_count_block">
      <ul class="ynaffiliate_list_count_items">
         <li>
            <span><?php echo $this->translate('Number of subscriptions'); ?></span>: 
            	<?php  echo $this->locale()->toNumber($this->subscriptions);?>
         </li>
          
         <li>
            <span><?php echo $this->translate('Number of Purchases'); ?></span>: 
            <?php  echo $this->locale()->toNumber($this->purchases)?>
         </li>
          
         <li>
            <span><?php echo $this->translate('Total Commission Points'); ?></span>:
            <?php  echo $this->locale()->toNumber($this->commissionPoints);?>
         </li>

         <br/>
         <br/>
         
         <li class="ynaffiliate_approved">
            <span><?php echo $this->translate('Approved'); ?></span>: 
            <?php  echo $this->locale()->toNumber($this->approvedCommissionPoints);?>
         </li>
         
         <li class="ynaffiliate_deplaying">
            <span><?php echo $this->translate('Delaying'); ?></span>: 
            <?php  echo $this->locale()->toNumber($this->delayingCommissionPoints);?>
         </li>
         
         <li class="ynaffiliate_waiting">
            <span><?php echo $this->translate('Waiting'); ?></span>: 
            <?php  echo $this->locale()->toNumber($this->waitingCommissionPoints);?>
         </li>
          
         <li class="ynaffiliate_requested">
            <span><?php echo $this->translate('Requested'); ?></span>: 
            <?php echo $this->locale()->toNumber($this->requestedPoints);?>
         </li>                            
      </ul>
   </div>
</div>


<div class="ynaffiliate_stistic_chart">
	<div class="ynaffiliate_stistic_chart_header">
		<?php echo $this -> translate('Total commission points group by')." ";?><span id="statistic_group_by"><?php echo $this -> translate('commission rules')?></span>
	</div>
	<?php echo $this->form->render($this); ?>
	<?php echo $this->partial('_chart.tpl', array('userId' => $this -> viewer() -> getIdentity()));?>
</div>