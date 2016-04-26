
<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>


<h2><?php echo $this->translate("Store Plugin") ?></h2>
<!-- admin menu -->
<?php echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>



<p>
  <?php echo $this->translate('SOCIALSTORE_FIELDS_VIEWS_SCRIPTS_ADMINFIELDS_DESCRIPTION') ?>
</p>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add Question")?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading" style="display:none;"><?php echo $this->translate("Add Heading");?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate("Save Order");?></a>
</div>

<br />


<ul class="admin_fields">
  <?php foreach( $this->topLevelMaps as $field ): ?>
    <?php echo $this->adminFieldMeta($field) ?>
  <?php endforeach; ?>
</ul>

<br />
<br />

<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
  height: 65px;
}
</style>   
