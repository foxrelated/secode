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

<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$module = $request->getModuleName();
$controller = $request->getControllerName();
$action = $request->getActionName();
?>

<?php if ($module == 'siteevent' && $controller == 'index' && $action == 'top-rated'): ?>
    <?php $url_action = 'top-rated'; ?>
<?php else: ?>
    <?php $url_action = 'index'; ?>
<?php endif; ?>

<?php if (!empty($this->category_id) || (isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id']))): ?>
    <div class="siteevent_event_breadcrumb">
        <?php if (!empty($this->category_id)): ?>

            <?php echo $this->htmlLink($this->url(array('action' => $url_action), "siteevent_general"), $this->translate("Browse Events")) ?>

            <?php if ($this->category_name != ''): ?>
                <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
            <?php endif; ?>

            <?php
            $this->category_name = $this->translate($this->category_name);
            $this->subcategory_name = $this->translate($this->subcategory_name);
            $this->subsubcategory_name = $this->translate($this->subsubcategory_name);
            ?>
            <?php if ($this->category_name != '') : ?>
                <?php if ($this->subcategory_name != ''): ?> 

                    <?php echo $this->htmlLink($this->url(array('action' => $url_action, 'category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->category_id)->getCategorySlug()), Engine_Api::_()->siteevent()->getCategoryHomeRoute()), $this->translate($this->category_name)) ?>
                <?php else: ?>
                    <?php echo $this->translate($this->category_name) ?>   
                <?php endif; ?>
                <?php if ($this->subcategory_name != ''): ?> 
                    <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                    <?php if (!empty($this->subsubcategory_name)): ?>
                        <?php echo $this->htmlLink($this->url(array('action' => $url_action, 'category_id' => $this->category_id, 'categoryname' => Engine_Api::_()->getItem('siteevent_category', $this->category_id)->getCategorySlug(), 'subcategory_id' => $this->subcategory_id, 'subcategoryname' => ucfirst(Engine_Api::_()->getItem('siteevent_category', $this->subcategory_id)->getCategorySlug())), "siteevent_general_subcategory"), $this->translate($this->subcategory_name)) ?>   
                    <?php else: ?>
                        <?php echo $this->translate($this->subcategory_name) ?>       
                    <?php endif; ?>
                    <?php if (!empty($this->subsubcategory_name)): ?>
                        <?php echo '<span class="brd-sep seaocore_txt_light">&raquo;</span>'; ?>
                        <?php echo $this->translate($this->subsubcategory_name); ?>    
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (((isset($this->formValues['tag']) && !empty($this->formValues['tag']) && isset($this->formValues['tag_id']) && !empty($this->formValues['tag_id'])))): ?>
            <?php
            $tag_value = $this->formValues['tag'];
            $tag_value_id = $this->formValues['tag_id'];
            $browse_url = $this->url(array('action' => $url_action), "siteevent_general", true) . "?tag=$tag_value&tag_id=$tag_value_id";
            ?>
                <?php if ($this->category_name): ?><br /><?php endif; ?>
                <?php echo $this->translate("Showing events tagged with: "); ?>
            <b><a href='<?php echo $browse_url; ?>'>#<?php echo $this->formValues['tag'] ?></a>
            <?php if ($this->current_url2): ?>  
                    <a href="<?php echo $this->url(array('action' => $url_action), "siteevent_general", true) . "?" . $this->current_url2; ?>"><?php echo $this->translate('(x)'); ?></a></b>
        <?php else: ?>
                <a href="<?php echo $this->url(array('action' => $url_action), "siteevent_general", true); ?>"><?php echo $this->translate('(x)'); ?></a></b>        
        <?php endif; ?>
    <?php endif; ?>
    </div>
<?php endif; ?>

