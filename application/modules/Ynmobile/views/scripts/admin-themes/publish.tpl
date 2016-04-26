<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: delete.tpl minhnc $
 * @author     MinhNC
 */
?>

<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php elseif( $this->status ): ?>

  <div><?php echo $this->translate("Published") ?></div>

  <script type="text/javascript">
    setTimeout(function() {
        parent.location.href = parent.location.href;
        parent.Smoothbox.close();

    }, 500);
  </script>

<?php endif; ?>