<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="siteevent_event_breadcrumb">
    <a href="<?php echo $this->url(array('action' => 'index'), 'siteevent_general', true); ?>"><?php echo $this->translate("Browse Events"); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <a href="<?php echo $this->url(array('action' => 'categories'), 'siteevent_general', true); ?>"><?php echo $this->translate("All Categories"); ?></a>  
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php echo $this->category->getTitle(true); ?>
</div>
