<?php
?>
<div class='global_form_popup'>
 <?php echo $this->form->render($this); ?>    
</div>

<script type="text/javascript">
function updateTextFields() {
  if (document.getElementById('playlist_id').value == 0) {
    document.getElementById('title-wrapper').style.display = 'block';
  } else {
    document.getElementById('title-wrapper').style.display = 'none';
  }
  parent.Smoothbox.instance.doAutoResize();
}
en4.core.runonce.add(updateTextFields);
</script>