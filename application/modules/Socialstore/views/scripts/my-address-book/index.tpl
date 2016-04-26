<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<?php if (count($this->addressBook) <= 0) : ?>
	<div class="tip">
		<span>
			<?php echo $this->translate("You have no address yet!") ?>
	  </span>
	</div>
<?php else: ?>
<?php foreach ($this->addressBook as $id => $address) :?>	
<div class = "ynstore_addbook">
	<div class = "ynstore_left_addbook">
			<span class = "ynstore_addbook_title">
				<?php $title =  $address->title;
					  echo $title;
				?>
			</span>
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
            'addressbook_id' => $id,  
			'action' => 'edit',
	        'controller' => 'my-address-book',
	        'route' => 'socialstore_extended',
        ), 
        $this->translate('Edit'), array(
        	'class' => 'buttonlink smoothbox icon_store_edit',
        )) ?>
        |
        <?php echo $this->htmlLink(array(
        	'addressbook_id' => $id,
        	'action' => 'delete',
        	'controller' => 'my-address-book',
        	'route' => 'socialstore_extended',
        ),
        $this->translate('Delete'), array(
        	'class' => 'buttonlink smoothbox icon_product_delete',
        )) ?> 
	</div>
</div>
<?php endforeach;?>
<?php endif;?>
<div class="ynstore_addbookbutton">
<button type="button" onclick="javascript:addAddress();"><?php echo $this->translate('Add New Address'); ?></button>
</div>
<br />
<?php echo $this->form->render();?>

<script type = "text/javascript">
window.addEvent('domready',function(){
	var isposted = "<?php echo $this->isPosted;?>";
	if ($('ynstore_addbookform').getStyle('visibility') == 'visible' && isposted != 1) {
		$('ynstore_addbookform').setStyle('visibility','hidden');
	}
});
function addAddress() {
	if ($('ynstore_addbookform').getStyle('visibility') == 'hidden') {
		$('ynstore_addbookform').reset();
		$('ynstore_addbookform').setStyle('visibility','visible');
	}
}
function cancelAddress() {
	if ($('ynstore_addbookform').getStyle('visibility') == 'visible') {
		$('ynstore_addbookform').setStyle('visibility','hidden');
	}
}
</script>