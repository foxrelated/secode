
<?php if( $this->form ): ?>
<div style="padding:10px">
  <?php echo $this->form->render($this) ?>
<?php else: ?>
  <script type="text/javascript">
    parent.Smoothbox.close();
  </script>
<?php endif; ?>
</div>