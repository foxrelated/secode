<div class="my-credit-data global_form_popup" style="width: 800px">
	<h3> <?php echo $this -> translate("%s's Transactions", $this -> user_name)?></h3>
    <div class="yncredit-table-title">
        <div><?php echo $this -> translate("Group Type")?></div>
        <div><?php echo $this -> translate("Action")?></div>
        <div><?php echo $this -> translate("Credits")?></div>
        <div><?php echo $this -> translate("Date")?></div>         
    </div>
    <?php if($this -> transactions -> getTotalItemCount() > 0):?>
	    <?php foreach($this -> transactions as $transaction): ?>
		    <div class="yncredit-table-content">
		        <div><?php echo $this -> translate('YNCREDIT_GROUP_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $transaction->group), '_')))?></div>
		        <div><?php $txtAction = $this -> translate('YNCREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $transaction->action_type), '_')));
		        	if(!empty($transaction -> object_type) && !empty($transaction -> object_id))
					{
						$obj = Engine_Api::_() -> getItem($transaction -> object_type, $transaction -> object_id);
						if($obj && $obj -> getType() == 'activity_action')
						{
							$obj = Engine_Api::_() -> getItem($obj -> object_type, $obj -> object_id);
						}
						if($obj)
						{
							if(substr_count($transaction -> content, "%") > 1)
							{
								echo $this -> translate($transaction -> content, $transaction -> item_count, $obj);
							}
							else
							{
								echo $this -> translate($transaction -> content, $this -> htmlLink($obj -> getHref(), $obj -> getTitle(), array('target' => '_blank')));
							}
						}
					}
					else 
					{
						echo $this -> translate($transaction -> content,"");
					}
		        	?>
		        </div>
		        <div class="yncredit-color-<?php echo ($transaction -> credit >= 0)?'up':'down' ?>">
		        	<?php echo $this->locale()->toNumber($transaction -> credit)?>
		        </div>       
		        <div><?php 
	                $start_time = strtotime($transaction -> creation_date);
					$oldTz = date_default_timezone_get();
					if($this->viewer() && $this->viewer()->getIdentity())
					{
						date_default_timezone_set($this -> viewer() -> timezone);
					}
					else 
					{
						date_default_timezone_set( $this->locale() -> getTimezone());
					}
					echo date("F j, Y", $start_time);
	                date_default_timezone_set($oldTz);?>
	            </div> 
		    </div>
	    <?php endforeach;?>
	<?php else: ?>
	<br>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no transactions.") ?>
        </span>
    </div>
<?php endif; ?>
<div class="pages">
   <?php echo $this->paginationControl($this->transactions, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
</div>
