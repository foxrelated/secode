<!-- widget render -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.payment-menu') ?>
</div>
<div class="layout_middle">
<!-- form render-->
<?php echo $this->form->render($this);
?>
</div>
<div class = "ynstore_addresses">
<?php foreach ($this->addresses as $key => $address) :
?>
<div id = "ynstore_addresses_list_<?php echo $key; ?>" class = "ynstore_addresses_list">
	<div class = "ynstore_left_addbook">
		<span class = "ynstore_addbook_value">
			<?php 
			$count = count((array)$address);
			$i = 0;
			foreach ($address as $add) :?>
				<?php 
					$i++;
					if ($add != $title) {
						if ($i == $count) {
							echo $add;
						}
						else {
							if ($add != '') {
								echo  $add. ', ';
							}
						}
					}
				?>
				<?php endforeach;?>
		</span>
	</div>
	<div class = "ynstore_right_addbook">
		<?php echo $this->htmlLink(array(
            'id' => $key,  
			'action' => 'edit-shipping-address',
	        'controller' => 'payment',
	        'route' => 'socialstore_extended',
        ), 
        $this->translate('Edit'), array(
        	'class' => 'smoothbox',
        )) ?>
        |
        <?php echo $this->htmlLink(array(
        	'id' => $key,
        	'action' => 'delete-shipping-address',
        	'controller' => 'payment',
        	'route' => 'socialstore_extended',
        ),
        $this->translate('Delete'), array(
        	'class' => 'smoothbox',
        )) ?> 
	</div>
</div>
<?php endforeach;?>
</div>
