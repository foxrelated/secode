
<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Clone Slider") ?></h3>
        <p>
            <?php echo $this->translate("Enter a new name for the cloned slider, leave blank to use old title") ?>
        </p>
        <div id="title-element" class="form-element">
            <input type="text" name="title" id="title" value="<?php echo $this->slider->getTitle() . ' (' . $this->translate('Cloned') . ')' ?>" maxlength="255"></div>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->title?>"/>
            <button type='submit'><?php echo $this->translate("Clone") ?></button>
            <?php echo $this->translate(" or ") ?>
            <a href='javascript:void(0);' onclick='parent.Smoothbox.close()'>
                <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
    TB_close();
</script>
<?php endif; ?>
