<script type="text/javascript">
  function process(){
    var url = '<?php echo $this->transactionUrl ?>';
    var data = <?php echo Zend_Json::encode($this->transactionData) ?>;
    var request = new Request.Post({
      url : url,
      data : data
    });
    request.send();
  }
</script>
<!-- widget render -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ;?>
<div class="layout_right">
	<!-- render mini menu -->
	<?php echo $this->content()->renderWidget('socialstore.payment-menu') ?>
</div>
<div class="layout_middle">
<!-- form render-->
<div class="settings">
<?php echo $this->form->render($this) ?>
</div> 
</div>
<!--   <button onclick="process()">Process</button>

-->