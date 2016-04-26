<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  function setModuleName(module_name){
    window.location.href="<?php echo $this->url(array('module' => 'nestedcomment', 'controller' => 'module', 'action' => 'add'), 'admin_default', true) ?>/module_name/"+module_name;
  }
</script>

<h2><?php echo $this->translate('Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'nestedcomment', 'controller' => 'module', 'action' => 'index'), $this->translate("Back to Manage Modules for Nested Comments"), array('class' => 'seaocore_icon_back buttonlink')) ?>

<br style="clear:both;" /><br />

<div class="seaocore_settings_form">
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
    
    en4.core.runonce.add(function() {
      hideOptions(1);
   })
    
    function hideOptions(option) {
    
      if(option == 1) {
        $('showDislikeUsers-wrapper').style.display = 'none';
        $('showLikeWithoutIcon-wrapper').style.display = 'none';
        $('showLikeWithoutIconInReplies-wrapper').style.display = 'none';
      } else {
        $('showDislikeUsers-wrapper').style.display = 'block';
        $('showLikeWithoutIcon-wrapper').style.display = 'block';
        $('showLikeWithoutIconInReplies-wrapper').style.display = 'block';
      }
    }
    
</script>   