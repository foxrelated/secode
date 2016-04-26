<h2>
    <?php echo $this->translate('Younet Mobile Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class="tabs">
    <?php
    	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class="settings">
    <?php if(!empty($this->errorMessage) || $this->shouldWritable): ?>
    <ul class="form-errors">
        <?php if($this->shouldWritable): ?>
        <li>
            Please set directory "<?php echo $this->shouldWritableDirectory; ?>" is writable.
        </li>
        <?php endif; ?>
        <?php if(!empty($this->errorMessage)): ?>
        <li>
            <?php echo $this->errorMessage; ?>
        </li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
    <?php echo $this->form->render($this) ?>
</div>
