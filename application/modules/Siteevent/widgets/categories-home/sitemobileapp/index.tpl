<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="categories-block">
    <ul class="ui-listview collapsible-listview" >
        <?php $k = 0; ?>
        <?php for ($i = 0; $i <= $this->totalCategories; $i++) : ?>
            <?php
            $category = "";
            if (isset($this->categories[$k]) && !empty($this->categories[$k])) {
                $category = $this->categories[$k];
            }

            $k++;

            if (empty($category)) {
                break;
            }
            ?>
            <li class="ui-btn ui-btn-icon-right ui-li-has-arrow ui-li ui-btn-up-c <?php if (isset($category['count'])): ?>ui-li-has-count<?php endif; ?>">
                <div class="collapsible_icon" >
                    <?php
                    $item = Engine_Api::_()->getItem('siteevent_category', $category['category_id']);
                    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($item['file_id']);
                    if ($file)
                        $src = $file->map();
                    if (!empty($item['file_id'])):?>
                    <a class="ui-link-inherit" href="<?php echo $item->getHref(); ?>" >
                            <img class="ui-icon ui-icon-shadow" alt="" height="25px" width="25px" src="<?php echo $src ?>" /> 
                    <?php endif; ?>
                    </a>
                </div>      
                <div class="ui-btn-inner ui-li" >
                    <div class="ui-btn-text">  
                        <a class="ui-link-inherit" href="<?php echo $item->getHref() ?>"  >
                            <?php echo $this->translate($item->getTitle(true)); ?>
    <?php if (isset($category['count'])): ?><span class="ui-li-count ui-btn-up-c ui-btn-corner-all"><?php echo $category['count'] ?></span><?php endif; ?></a>
                    </div>
                    <span class="ui-icon ui-icon-arrow-r">
                        &nbsp;
                    </span>
                </div>
            </li>
    <?php endfor; ?>
    </ul>
</div>