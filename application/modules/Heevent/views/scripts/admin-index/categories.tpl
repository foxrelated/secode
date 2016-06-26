<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: categories.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>

<h2><?php echo $this->translate("HEEVENT_Advanced Events Plugin") ?></h2>

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
        <h3> <?php echo $this->translate("Event Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("EVENT_VIEWS_SCRIPTS_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
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
                      <td><?php echo $category->title?></td>
                      <td>
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'event', 'controller' => 'admin-settings', 'action' => 'edit-category', 'id' =>$category->category_id),
																$this->translate('edit'),
																array('class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'event', 'controller' => 'admin-settings', 'action' => 'delete-category', 'id' =>$category->category_id),
																$this->translate('delete'),
																array('class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(
																array('route' => 'default', 'module' => 'heevent', 'controller' => 'admin-index', 'action' => 'themes', 'id' =>$category->category_id),
																$this->translate('HEEVENT_add theme photo')
                      ) ?>

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

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'event', 'controller' => 'settings', 'action' => 'add-category'), $this->translate('Add New Category'), array(
        'class' => 'smoothbox buttonlink',
        'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>

    </div>
    </form>
    </div>
  </div>
     