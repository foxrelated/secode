<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<?php
	echo '<div class="tip"><span>You dont have "Birthday Email Plugin", Please install the "Birthday Email Plugin"</span></div>';
?>