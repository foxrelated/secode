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
    <a href="<?php echo $this->url(array('action' => 'home'), "siteevent_general"); ?>"><?php echo $this->translate("Events Home"); ?></a>
    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
    <?php if ($this->category_name): ?>
        <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug()), Engine_Api::_()->siteevent()->getCategoryHomeRoute()); ?>"><?php echo $this->translate($this->category_name); ?></a>
        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
        <?php if (!empty($this->subcategory_name)): ?>
            <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug()), "siteevent_general_subcategory") ?>"><?php echo $this->translate($this->subcategory_name); ?></a>
            <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <?php if (!empty($this->subsubcategory_name)): ?>
                <a href="<?php echo $this->url(array('category_id' => $this->siteevent->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->category_id)->getCategorySlug(), 'subcategory_id' => $this->siteevent->subcategory_id, 'subcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subcategory_id)->getCategorySlug(), 'subsubcategory_id' => $this->siteevent->subsubcategory_id, 'subsubcategoryname' => Engine_Api::_()->getItem('siteevent_category', $this->siteevent->subsubcategory_id)->getCategorySlug()), "siteevent_general_subsubcategory") ?>"><?php echo $this->translate($this->subsubcategory_name); ?></a>
                <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($this->render): ?> 
        <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
    <?php else: ?>            
        <?php echo $this->siteevent->getTitle(); ?>
    <?php endif; ?>
</div>
