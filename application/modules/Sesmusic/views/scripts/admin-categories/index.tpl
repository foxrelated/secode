<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesmusic/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic-form">
  <div>
    <?php if( count($this->subNavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
        ?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
      <div class='settings sesbasic_admin_form'>
        <form class="global_form">
          <div>
            <h3><?php echo $this->translate("Manage Music Album Categories") ?> </h3>
            <p class="description">
              <?php echo $this->translate('Music album categories can be managed here. To create new categories, use "Add New Category" link. Below, you can also create 2nd-level categories and 3rd-level categories.') ?>
            </p>        
            <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'add-category'), $this->translate('Add New Category'), array('class' => 'buttonlink smoothbox', 'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?><br /><br />        
            <?php if(count($this->categories)>0):?>
            <table class='admin_table' style="width: 100%;">
              <thead>
                <tr>
                  <th><?php echo $this->translate("Name") ?></th>
                  <th><?php echo $this->translate("Icon") ?></th>
                  <th><?php echo $this->translate("Options") ?></th>
                </tr>
              </thead>
              <tbody>
                <?php //Category Work ?>
                <?php foreach ($this->categories as $category): ?>
                <?php if($category->category_id == 0 && $this->resource_type == 'album') : ?>
                  <?php continue; ?>
                <?php endif; ?>
                <tr>
                  <td><b class="bold"><?php echo $category->category_name ?></b></td>
                  <td>
                    <?php if($category->cat_icon): ?>
                    <img src="<?php echo Engine_Api::_()->storage()->get($category->cat_icon, '')->getPhotoUrl(); ?>" alt="" style="height: 16px;" />
                    <?php else: ?>
                    <?php echo "---"; ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $category->category_id,'catparam' => 'maincat'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-category', 'id' => $category->category_id, 'catparam' => 'maincat'), $this->translate('Delete'), array('class' => 'smoothbox')); ?>               
                    <?php if(!$category->subcat_id): ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'add-category', 'category_id' => $category->category_id), $this->translate('Add 2nd-level Category'), array('class' => 'smoothbox')) ?>
                    <?php if($category->cat_icon): ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-icon', 'category_id' => $category->category_id, 'file_id' => $category->cat_icon, 'catparam' => 'maincat'), $this->translate("Remove Icon"), array('class' => 'smoothbox')) ?>
                    <?php endif; ?>

                    <?php endif; ?>
                  </td>
                </tr>
                <?php //Subcategory Work
                $subcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $category->category_id));           
                foreach ($subcategory as $sub_category):  ?>
                <tr>
                  <td>&rarr; <?php echo $sub_category->category_name ?></td>
                  <td>
                    <?php if($sub_category->cat_icon): ?>
                    <img src="<?php echo Engine_Api::_()->storage()->get($sub_category->cat_icon, '')->getPhotoUrl(); ?>" alt="" style="height:16px;" />
                    <?php else: ?>
                    <?php echo "---"; ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $sub_category->category_id,'catparam' => 'sub'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-category', 'id' => $sub_category->category_id,'catparam' => 'sub'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'add-category', 'category_id' => $sub_category->category_id, 'subcat_id' => $sub_category->subcat_id), $this->translate('Add 3rd-level Category'), array('class' => 'smoothbox')) ?>
                    <?php if($sub_category->cat_icon): ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-icon', 'category_id' => $sub_category->category_id, 'file_id' => $sub_category->cat_icon, 'catparam' => 'sub'), $this->translate("Remove Icon"), array('class' => 'smoothbox')) ?>
                    <?php endif; ?>
                    <?php //endif; ?>
                  </td>
                </tr>

                <?php //SubSubcategory Work
                $subsubcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $sub_category->category_id));
                foreach ($subsubcategory as $subsub_category): ?>
                <tr>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&rarr; <?php echo $subsub_category->category_name ?></td>
                  <td><?php if($subsub_category->cat_icon): ?>
                    <img src="<?php echo Engine_Api::_()->storage()->get($subsub_category->cat_icon, '')->getPhotoUrl(); ?>" alt="" style="height:16px;"  />
                    <?php else: ?>
                    <?php echo "---"; ?>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'edit-category', 'id' => $subsub_category->category_id,'catparam' => 'subsub'), $this->translate('Edit'), array('class' => 'smoothbox')) ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-category', 'id' => $subsub_category->category_id, 'catparam' => 'subsub'), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
                    <?php if($subsub_category->cat_icon): ?>
                    |
                    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesmusic', 'controller' => 'categories', 'action' => 'delete-icon', 'category_id' => $subsub_category->category_id, 'file_id' => $subsub_category->cat_icon, 'catparam' => 'sub'), $this->translate("Remove Icon"), array('class' => 'smoothbox')) ?>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php endforeach; ?>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php else:?>
            <br/>
            <div class="tip">
              <span><?php echo $this->translate("There are currently no categories.") ?></span>
            </div>
            <?php endif;?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>