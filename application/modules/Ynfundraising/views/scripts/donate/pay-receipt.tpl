<?php
echo $this->partial ( '_menu.tpl', array () );
?>
<?php if (isset($this->message)): ?>
<div class="tip">
    <span><?php echo $this->message; ?></span>
</div>
<?php endif; ?>