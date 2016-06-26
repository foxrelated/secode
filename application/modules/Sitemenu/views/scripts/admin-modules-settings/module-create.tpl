<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: module-create.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<h2>
    <?php echo 'Advanced Menus Plugin - Interactive and Attractive Navigation' ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render();
        ?>
    </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitemenu', 'controller' => 'modules-settings', 'action' => 'index'), "Back to Manage Modules", array('class' => 'sitemenu_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />


<div class='settings'>
    <?php
    if (($this->module_form_count > 1) || (!empty($this->modules_id))) :
        echo $this->form->render($this);
    else :
        echo '<div class="tip"><span>' . 'Content Display in main menu  is currently enabled for all content modules on your site.' . '</span></div>';
    endif;
    ?>
</div>

<script type="text/javascript">
showcategory();
function showcategory(){
  if($('category_option-1').checked){
      $('category_name-wrapper').style.display = 'block';
      $('category_name').required = true;
      $('category_title_field-wrapper').style.display = 'block';
      $('category_title_field').required = true;
  }else{
      $('category_name-wrapper').style.display = 'none';
      $('category_name').required = false;
      $('category_title_field-wrapper').style.display = 'none';
      $('category_title_field').required = false;
  }    
}
</script>

