<?php
  // Render the admin js
  echo $this->render('_attributeField.tpl')
?>

<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.menu-mystore-mini') ?>
</div>

<div class="layout_middle">
<h3><?php echo $this->translate('Attribute Set')?></h3>
<br />
<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink socialstore_attributes_addattribute"><?php echo $this->translate("Add Attribute")?></a>
</div>

<br />


<ul class="ynstore_attributes">
  <?php foreach( $this->types as $field ): ?>
    <?php echo $this->ynStoreAttribute($field) ?>
  <?php endforeach; ?>
</ul>
</div>