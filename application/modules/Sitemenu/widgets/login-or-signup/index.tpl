<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<?php
$tabIndex = 15;
if (!empty($this->form)) : 
    foreach ($this->form->getElements() as $el) :
        $el->setAttrib('tabindex', $tabIndex++);
    endforeach;
endif;
?>

<?php if (!$this->noForm): ?>
 <!-- <h3>
    <?php echo $this->translate('Member Sign In'); ?>

  </h3>-->
  <?php echo $this->form->render($this) ?>
  <?php if (!empty($this->fbUrl)): ?>
    <script type="text/javascript">
    var openFbLogin = function() {
    Smoothbox.open('<?php echo $this->fbUrl ?>');
    }
    var redirectPostFbLogin = function() {
    window.location.href = window.location;
    Smoothbox.close();
    }
    </script>
  <?php endif; ?>
<?php else: ?>
  <?php // echo $this->htmlLink(array('route' => 'user_login', 'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), $this->translate('Sign In')) ?>
  <?php // echo $this->translate('or') ?>
  <?php // echo $this->htmlLink(array('route' => 'user_signup'), $this->translate('Join')) ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>

<script type="text/javascript">
  advancedMenuUserLoginFormAction();
</script>