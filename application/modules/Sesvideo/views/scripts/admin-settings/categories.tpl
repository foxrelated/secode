<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: categories.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<h2><?php echo $this->translate("Videos Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
  <div class='clear'>
    <div class='settings'>
    <form class="global_form">
      
      	<?php if( count($this->subNavigation) ): ?>
        <div>
    		  <div class='tabs'>
            <?php
           		 echo $this->navigation()->menu()->setContainer($this->subNavigation)->render();
            ?>
      </div>
   		  <?php endif; ?>
        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'categories', 'action' => 'add-category'), 'Add New Category', array(
          'class' => 'smoothbox buttonlink',
          'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')); ?>
         <div style="margin-top:15px;"></div>
        <h3><?php echo $this->translate("Video Categories") ?></h3>
        <p class="description">
          <?php echo $this->translate("VIDEO_VIEWS_SCRIPTS_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
        </p>
          <?php if(count($this->categories)>0):?>
         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
              <th><?php echo $this->translate("Number of Times Used") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
                    <tr>
                      <td><?php echo $category->category_name?></td>
                      <td><?php echo $category->getUsedCount()?></td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate('edit'), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sesvideo', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate('delete'), array(
                          'class' => 'smoothbox',
                        )) ?>
                      </td>
                    </tr>
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