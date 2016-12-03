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
        <h3> <?php echo $this->translate("Goal Templates") ?> </h3>
        <p class="description">
          <?php echo $this->translate("If you want to allow your users to choose templates for their goals, create the templates below. This feature is useful when users will create new goals.") ?>
        </p>
          <?php if(count($this->templates)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Picture") ?></th>  
                <th><?php echo $this->translate("Goal Templates Names") ?></th>
<?php //              <th># of Times Used</th>?>
             
              <th><?php echo $this->translate("Description") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->templates as $template): ?>
                    <tr>
                        <td>
                             <?php
          if( $template->photo_id ) {
          $photo = Engine_Api::_()->getItem('goal_photo',$template->photo_id);
           echo '<img src="'.$photo->getPhotoUrl().'" style="max-width: 200px; max-height: 400px;">';
            //echo $this->htmlLink($template->getHref(), $this->itemPhoto($template, 'thumb.icon'));
          }
        ?>
                        
                        
                        </td>  
                      <td><?php echo $template->title ?></td>
                          <td><?php echo  html_entity_decode($template->description) ?></td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'edit-template', 'id' =>$template->template_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'delete-template', 'id' =>$template->template_id), $this->translate("delete"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                      
                        <a class="menu_goal_admin_main goal_admin_main_templates" href="admin/goal/settings/tasks/id/<?php echo $template->template_id;?>">  tasks</a>
                        
                      </td>
                  
                    </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no goal templates.") ?></span>
      </div>
      <?php endif;?>
        <br/>

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'goal', 'controller' => 'settings', 'action' => 'add-template'), $this->translate('Add New Goal Template'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     