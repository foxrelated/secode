<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<h2>
  <?php echo $this->translate(''); ?>
</h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>
<?php endif; ?>
<div class='clear'>
  <div>
<?php if( count($this->subNavigation) ): ?>
<div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
  ?>
</div>
<?php endif; ?>
<div class="catagories-form-cont">
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category - Video Form Questions Mapping") ?> </h3>
        <p class="description">
          <?php echo $this->translate("Here, you can map Categories with the Profile Types, so that questions belonging to the mapped Profile Type will appear to users in the Contact Us Form when they choose the associated Category.") ?>
        </p>
        <?php if (count($this->sescategories) > 0): ?>
        <table class='admin_table' style="width: 60%;">
          <thead>
            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Profile Type") ?></th>
              <th><?php echo $this->translate("Map") ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->sescategories as $category):  ?>
            <?php if($category['category_id'] == 0) : ?>
              <?php continue; ?>
            <?php endif; ?>
            <tr>
              <td><?php echo $category['category_name'] ?></td>
                <td><?php echo $category['profile_type_label']; ?></td>
              <td>
                <?php if (empty($category['profile_type_id'])): ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'catmapping', 'category_id' => $category['category_id']), $this->translate('Add'), array('class' => 'smoothbox'))
                ?>
                <?php else: ?>
                <?php  ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'removemapping', 'category_id' => $category['category_id']), $this->translate('Remove'), array('class' => 'smoothbox'))
                ?>
                <?php endif; ?>
              </td>
            </tr>
            <?php //Subcategory Work
            $subcategory = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $category['category_id']));
            
            $options_table = Engine_Api::_()->getDbtable('options', 'sesvideo');
            foreach ($subcategory as $sub_category): 
            $profileTypeLabel = '-----';
                if (!empty($sub_category['profile_type'])) {
                  $profileTypeLabel = $options_table->getOptionsLabel($sub_category['profile_type']);
                }
            ?>
            <tr>
              <td>&rarr; <?php echo $sub_category['category_name']; ?></td>
               <td><?php echo $profileTypeLabel; ?></td>
              <td>
                <?php if (empty($sub_category['profile_type'])): ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'catmapping', 'category_id' => $sub_category['category_id']), $this->translate('Add'), array('class' => 'smoothbox'))
                ?>
                <?php else: ?>
                <?php  ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'removemapping', 'category_id' => $sub_category['category_id']), $this->translate('Remove'), array('class' => 'smoothbox'))
                ?>
                <?php endif; ?>
              </td>
            </tr>
            <?php //SubSubcategory Work
            $subsubcategory = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $sub_category['category_id']));
            foreach ($subsubcategory as $subsub_category):           
              $profileTypeLabel = '-----';
                if (!empty($subsub_category['profile_type'])) {
                  $profileTypeLabel = $options_table->getOptionsLabel($subsub_category['profile_type']);
                }
            ?>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&rarr; <?php echo $subsub_category['category_name']; ?></td>
              <td><?php echo $profileTypeLabel; ?></td>
              <td>
               <?php if (empty($subsub_category['profile_type'])): ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'catmapping', 'category_id' => $subsub_category['category_id']), $this->translate('Add'), array('class' => 'smoothbox'))
                ?>
                <?php else: ?>
                <?php  ?>
                <?php
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'settings', 'action' => 'removemapping', 'category_id' => $subsub_category['category_id']), $this->translate('Remove'), array('class' => 'smoothbox'))
                ?>
                <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>            
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <br/>
        <?php  ?>
        <div class="tip">
          <span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
        </div>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>
  </div>
</div>