<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
?>

<h2><?php echo $this->translate("Goals Plugin") ?></h2>

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
      <div>
        <h3> <?php echo $this->translate("Goal Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("If you want to allow your users to categorize their groups, create the categories below. This feature is useful if you want to list all your users' group that If you have no categories, your users will not be given the option of assigning a group category.") ?>
        </p>
          <?php if(count($this->categories)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Category Name") ?></th>
<?php //              <th># of Times Used</th>?>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
                    <tr>
                      <td><?php echo $category->title ?></td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate("delete"), array(
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
        <br/>

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'goal', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     