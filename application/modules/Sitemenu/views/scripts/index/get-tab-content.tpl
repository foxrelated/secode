<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-tab-content.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $tempCount = COUNT($this->obj); ?>
<div class="level1_hoverblock" style="height:<?php echo $this->content_height; ?>px">
    <?php if (!empty($tempCount)): ?>
        <?php foreach ($this->obj as $item) : ?>
            <div class="contentlist" title="<?php echo $item->getTitle(); ?>">
                <div>
                    <?php
                    if (empty($this->image_option)) :
                        echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.profile'));
                    else :

                        if ($this->module_name == 'video'):
                            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array('class' => 'video_title'));
                        elseif ($this->module_name == 'blog'):
                            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.profile')); //SHOW OWNER IMAGE WHEN MODULE IS BLOG
                        else:
                            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'));
                        endif;

                    endif;
                    ?>
                    <?php if (empty($this->is_title_inside)): ?>

                        <?php if ($this->module_name == 'video'): ?>
                            <span class="sitemenu_grid_title">
                                <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle(), 'class' => 'video_title')); ?>
                            </span>
                        <?php else: ?>
                            <span class="sitemenu_grid_title" onclick="location.href = '<?php echo $item->getHref() ?>';">
                                <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('title' => $item->getTitle())); ?>
                            </span>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
                <?php if (!empty($this->is_title_inside)): ?>
                    <span>
                        <?php if ($this->module_name == 'video'): ?>
                            <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->truncation_limit_content), array('title' => $item->getTitle(), 'class' => 'video_title')); ?>
                        <?php else: ?>
                            <?php echo $this->htmlLink($item->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($item->getTitle(), $this->truncation_limit_content), array('title' => $item->getTitle())); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="sitemenu_nocontent"><?php echo $this->translate("No content available."); ?> </div>
    <?php endif; ?>


    <?php $tempCategoryCount = COUNT($this->category_obj); ?>
    <?php if (!empty($this->showCategory)): ?>
        <?php if (!empty($tempCategoryCount)): ?>
            <div class="categories_section">
                <ul>
                    <?php foreach ($this->category_obj as $category): ?>
                        <?php if (!empty($category->category_id)): ?>
                            <li>
                                <?php
                                $tempHref = $category->getHref();
                                if (!empty($tempHref)) :
                                    echo $this->htmlLink($category->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($category->getTitle(), $this->truncation_limit_category), array('title' => $category->getTitle()));
                                elseif (!empty($this->is_category_name)) :
                                    $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_general') . "?category_id=" . $category->category_id;
                                    if (!empty($temp_url)):
                                        echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->getTitle(), $this->truncation_limit_category), array('title' => $category->getTitle()));
                                    else:
                                        echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->getTitle(), $this->truncation_limit_category);
                                    endif;

                                elseif (empty($this->is_category_name)):
                                    switch ($this->module_name) :
                                        case 'video':
                                            $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_general') . "?category=" . $category->category_id;
                                            if (!empty($temp_url)):
                                                echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->category_name, $this->truncation_limit_category), array('title' => $category->category_name));
                                            else:
                                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->category_name, $this->truncation_limit_category);
                                            endif;
                                            break;

                                        case 'classified':
                                            $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_general') . "?category_id=" . $category->category_id;
                                            if (!empty($temp_url)):
                                                echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->category_name, $this->truncation_limit_category), array('title' => $category->category_name));
                                            else:
                                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->category_name, $this->truncation_limit_category);
                                            endif;
                                            break;

                                        case 'sitepagedocument':
                                            $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_browse') . "?document_category_id=" . $category->category_id;
                                            if (!empty($temp_url)):
                                                echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->title, $this->truncation_limit_category), array('title' => $category->title));
                                            else:
                                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->category_name, $this->truncation_limit_category);
                                            endif;
                                            break;

                                        case 'sitepageevent':
                                            $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_browse') . "?event_category_id=" . $category->category_id;
                                            if (!empty($temp_url)):
                                                echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->title, $this->truncation_limit_category), array('title' => $category->title));
                                            else:
                                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->title, $this->truncation_limit_category);
                                            endif;
                                            break;

                                        case 'sitepagenote':
                                            $temp_url = $this->url(array('action' => 'browse'), $this->module_name . '_browse') . "?note_category_id=" . $category->category_id;
                                            if (!empty($temp_url)):
                                                echo $this->htmlLink($temp_url, Engine_Api::_()->seaocore()->seaocoreTruncateText($category->title, $this->truncation_limit_category), array('title' => $category->title));
                                            else:
                                                echo Engine_Api::_()->seaocore()->seaocoreTruncateText($category->title, $this->truncation_limit_category);
                                            endif;
                                            break;

                                        default:
                                            break;
                                    endswitch;

                                endif;
                                ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>      
        <?php else: ?>
            <div class="categories_section clr">
                <?php echo $this->translate("No category available."); ?>
            </div>  
        <?php endif; ?>
    <?php endif; ?>
</div>