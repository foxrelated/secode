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
        
        <?php foreach ($this->templates as $templates): ?>  
          <h3> <?php echo $templates->title; ?> Tasks</h3>
        <?php endforeach; ?> 
          
      
        <p class="description">
          <?php echo $this->translate("If you want to allow your users to choose templates for their goals, create the goal templates tasks below. This feature is useful when users will create new goals.") ?>
        </p>
          <?php if(count($this->tasks)>0):?>

         <table class='admin_table'>
          <thead>
            <tr>
              <th><?php echo $this->translate("Task Names") ?></th>
<?php //              <th># of Times Used</th>?>
                  <th><?php echo $this->translate("Description") ?></th>
               <th><?php echo $this->translate("Duration") ?></th>
              <th><?php echo $this->translate("Options") ?></th>
            </tr>

          </thead>
          <tbody>
            <?php foreach ($this->tasks as $tasks): ?>
                    <tr>
                      <td><?php echo $tasks->title ?></td>
                      <td><?php echo $tasks->description ?></td>
                       <td><?php echo $tasks->duration ?> days</td>
                      <td>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'edit-template-task', 'id' =>$tasks->temptask_id), $this->translate("edit"), array(
                          'class' => 'smoothbox',
                        )) ?>
                        |
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'goal', 'controller' => 'admin-settings', 'action' => 'delete-template-task', 'id' =>$tasks->temptask_id), $this->translate("delete"), array(
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
      <span><?php echo $this->translate("There are currently no goal tasks.") ?></span>
      </div>
      <?php endif;?>
        <br/>

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'goal', 'controller' => 'settings', 'action' => 'add-template-task' , 'id' =>$this->templateId), $this->translate('Add New Task'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
    </div>
    </form>
    </div>
  </div>
     