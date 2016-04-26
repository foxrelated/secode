<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: delete.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>

<form method="post" class="global_form_popup">
  <div>    
    <?php  if($this->catparam == 'maincat') : ?>
    <h3><?php echo $this->translate("Delete Category?") ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to delete this category. It will not be recoverable after being deleted.") ?></p>
    <?php elseif($this->catparam == 'sub') : ?>
    <h3><?php echo $this->translate("Delete 2nd-level Category?") ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to delete this 2nd-level category. It will not be recoverable after being deleted.") ?></p>
    <?php elseif($this->catparam == 'subsub') : ?>
    <h3><?php echo $this->translate("Delete 3rd-level category?") ?></h3>
    <p><?php echo $this->translate("Are you sure that you want to delete this 3rd-level category. It will not be recoverable after being deleted.") ?></p>
    <?php endif; ?>    
    <br />
    <p>
      <?php  if(count($this->subcategory) > 0) : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("A category which has its 2nd-level categories can not be deleted. So, first delete its 2nd-level categories, then try to delete this category.") ?>
      </span>
    </div>
    <?php elseif(count($this->subsubcategory) > 0) : ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("A category which has its 3rd-level categories can not be deleted. So, first delete its 3rd-level categories, then try to delete this category.") ?>
      </span>
    </div>
    <?php else: ?>
    <input type="hidden" name="confirm" value="<?php echo $this->id ?>"/>
    <button type='submit'><?php echo $this->translate("Delete") ?></button>
    <?php echo $this->translate(" or ") ?> 
    <?php endif; ?>
    <button href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Ok") ?></button>
    </p>
  </div>
</form>