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
#global_page_groupbuy-admin-page-edit-page div.edit-form div.form-label > label{
  font-weight: bold!important;
}
</style>

<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<h3><?php echo $this->translate("Instruction Pages") ?></h3>
<div class='edit-form'>
<?php
    //Render Form
    echo $this->form->render($this);
?>
</div>