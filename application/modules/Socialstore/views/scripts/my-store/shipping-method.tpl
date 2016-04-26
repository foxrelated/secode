<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>
<div class='layout_middle'>
<h3><?php echo $this->translate('Shipping Methods')?></h3>
<br />
<p>
        <?php echo $this->htmlLink(array(
              'action' => 'add-shipping-method',
              'route' => 'socialstore_mystore_general'),
                                   $this->translate('Add Shipping Method'),
                                   array('class' => 'smoothbox',
                                   )) ?>
</p>
<br />
<?php if (count($this->methods) <= 0) : ?>
	<div class="tip">
		<span>
			<?php echo $this->translate("You have no shipping method yet!") ?>
	  </span>
	</div>
<?php else: ?>
<div class="ynstore_methodtb" style = "overflow: auto">
	<table class="admin_table">
	        <thead>
	            <tr>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Name") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Description") ?></th>
	    			  <th class = "ynstore_table_text"><?php echo $this->translate("Options") ?></th>
	            </tr>
	        </thead>
	        <!--  Table Contents  -->
		    <tbody>
				<?php foreach ($this->methods as $method) : ?>
				<tr>
			          <td class = "ynstore_table_text"><?php echo $method->name?></td>
			          <td class = "ynstore_table_text"><?php echo $method->description?></td>
			          <td class = "ynstore_table_text">
			          	<?php echo $this->htmlLink(
			          			array(
						            'shippingmethod_id' => $method->shippingmethod_id,  
									'action' => 'add-shippingrule',
							        'route' => 'socialstore_mystore_general',
						        ), 
						        $this->translate('Add Rule'), 
						        array(
						        	'class' => 'smoothbox',
				        		)) ?>
			          		|
			          	<?php echo $this->htmlLink(
			          			array(
						            'shippingmethod_id' => $method->shippingmethod_id,  
									'action' => 'edit-shipping-method',
							        'route' => 'socialstore_mystore_general',
						        ), 
						        $this->translate('Edit'), 
						        array(
						        	'class' => 'smoothbox',
				        		)) ?>
					        |
				        <?php echo $this->htmlLink(
				        		array(
						        	'shippingmethod_id' => $method->shippingmethod_id,  
						        	'action' => 'delete-shipping-method',
							        'route' => 'socialstore_mystore_general',
						        ),
						        $this->translate('Delete'), 
						        array(
						        	'class' => 'smoothbox',
						        )) ?> 
			          </td>
			    </tr>
		        <?php endforeach; ?>
		    </tbody>
    </table>
</div>
	<br />
	
<h3><?php echo $this->translate('Shipping Rules')?></h3>
<br />
	<?php if (count($this->rules) <= 0) : ?>
	<div class="tip">
		<span>
			<?php echo $this->translate("You have no shipping rule yet!") ?>
	  </span>
	</div>
<?php else: ?>
<div class="ynstore_methodtb" style = "overflow: auto">
		<table class="admin_table">
	        <thead>
	            <tr>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Name") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Description") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Applied Categories") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Applied Countries") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Shipment Fee") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Type") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Type Fee") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Handling Type") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Handling Fee/%") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Status") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Options") ?></th>
	            </tr>
	        </thead>
	        <!--  Table Contents  -->
		    <tbody>
		        <?php foreach ($this->rules as $rule) : ?>
		        <tr>
			          <td class = "ynstore_table_text"><?php echo $rule['name']?></td>
			          <td class = "ynstore_table_text"><?php echo $rule['description']?></td>
			          <td class = "ynstore_table_text">
			          	<?php 
			          		$cat = '';
			          		$i = 0;
			          		foreach ($rule['category_id'] as $category) {
			          			$i++;
			          			$cat .= Engine_Api::_()->getApi('shipping','socialstore')->getRuleCat($category);
			          			if ($i < count($rule['category_id'])) {
			          				$cat .= ', ';
			          			}
			          		}
			          	echo $cat;?>
		          	  </td>
			          <td class = "ynstore_table_text">
			          	<?php 
			          		$coun = '';
			          		$i = 0;
			          		foreach ($rule['country_id'] as $country) {
			          			$i++;
			          			$coun .= Engine_Api::_()->getApi('shipping','socialstore')->getRuleCountry($country);
			          			if ($i < count($rule['country_id'])) {
			          				$coun .= ', ';
			          			}
			          		}
			          	echo $coun;?>
			          </td>
			          <td class = "ynstore_table_number"><?php echo $this->currency($rule['order_cost'])?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate(ucfirst($rule['cal_type']))?></td>
			          <td class = "ynstore_table_number"><?php echo $this->currency($rule['type_amount'])?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate(ucfirst($rule['handling_type']))?></td>
			          <td class = "ynstore_table_number">
			          	<?php 
			          	if ($rule['handling_type'] == 'none') {
							echo $this->translate('None');
			          	}
			          	else {		          		
			          		if($rule['handling_fee_type'] == 'fixed') {
		          				echo $this->currency($rule['handling_fee']);
		          			}
		          			else {
		          				echo $rule['handling_fee'].'%';
		          			}
			          	}		
	          		  	?>
	          		  </td>
	          		  <td class = "ynstore_table_text">
	          		  	<?php 
	          		  			if ($rule['enabled'] == 1) {
	          		  				echo $this->translate('Active');
	          		  				$enabled = 'Disabled';
	          		  			}
	          		  			else {
	          		  				echo $this->translate('Inactive');
	          		  				$enabled = 'Enabled';
	          		  			}
	          		  	?>
	          		  </td>
			          <td class = "ynstore_table_text">
			            <?php echo $this->htmlLink(array(
			              'action' => 'change-shippingrule-status',
			              'route' => 'socialstore_mystore_general',
			              'shippingrule_id' => $rule['shippingrule_id'],
			              'status' => $enabled),
			              $this->translate($enabled),
			              array('class' => 'smoothbox'));
			            ?>
			             |
			            <?php echo $this->htmlLink(array(
			              'action' => 'edit-shippingrule',
			              'route' => 'socialstore_mystore_general',
			              'shippingrule_id' => $rule['shippingrule_id'],
							),
			            	$this->translate('Edit'),
			             array('class' => 'smoothbox',));
			            ?>
			             |
			            <?php echo $this->htmlLink(array(
			              'action' => 'delete-shippingrule',
			              'route' => 'socialstore_mystore_general',
			              'shippingrule_id' => $rule['shippingrule_id'],
			            	),
			            	$this->translate('Delete'),
			             array('class' => 'smoothbox',));
			            ?>
			          </td>
		        </tr>
		        <?php endforeach; ?>
		    </tbody>
    </table>
    </div>
<?php endif;?>
<?php endif;?>

<br />

</div>

 <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}

</style> 