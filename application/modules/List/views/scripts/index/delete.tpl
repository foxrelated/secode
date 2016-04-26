<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: delete.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

 <?php include_once APPLICATION_PATH . '/application/modules/List/views/scripts/navigation_views.tpl'; ?>

<div class='global_form'>
  <form method="post" class="global_form">
    <div>
      <div>
        <h3><?php echo $this->translate('Delete Listing?'); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the Listing with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->list->title, $this->timestamp($this->list->modified_date)); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete'); ?></button>
          <?php echo $this->translate('or'); ?> <a href='<?php echo $this->url(array('action' => 'manage'),  'list_general', true) ?>'><?php echo $this->translate('cancel'); ?></a>
        </p>
      </div>
    </div>
  </form>
</div>