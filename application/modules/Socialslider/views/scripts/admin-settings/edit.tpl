<?php

/**
 * 
 * 
 */
?>

<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Socialslider/externals/scripts/jscolor/jscolor.js')
?>

<?php echo $this->form->render($this); ?>