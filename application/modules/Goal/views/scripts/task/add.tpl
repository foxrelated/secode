<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
?>
<?php if($this->goal->achieved == 1): ?>
<p class="gaol_completed">Goal is already completed you could not add more tasks.</p>
<a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
      <?php echo $this->translate("Close") ?></a>
<?php else: ?>
    <?php echo $this->form->render($this) ?>
<?php endif; ?>

