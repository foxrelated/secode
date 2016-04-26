<?php $viewer = Engine_Api::_()->user()->getViewer();
if($this->trophy->user_id == $viewer->getIdentity()):?>
<div style="margin-bottom: 10px; float: left; padding-right: 10px">
<?php echo $this->htmlLink(array(
              'action' => 'manage-nominees',
              'id' => $this->trophy->getIdentity(),
              'route' => 'ynidea_trophies',
              'reset' => true,
            ), $this->translate('Manage Nominees'), array('class'=>'buttonlink menu_ynidea_manageidea smoothbox'
            )) ?>
</div>
<div style="margin-bottom: 10px; ">
<?php echo $this->htmlLink(array(
              'action' => 'add-nominees',
              'id' => $this->trophy->getIdentity(),
              'route' => 'ynidea_trophies',
              'reset' => true,
            ), $this->translate('Add Nominees'), array('class'=>'buttonlink menu_ynidea_addidea smoothbox'
            )) ?>
</div>
 <?php endif;?> 
 <?php if(count($this->public_ranking) > 0): ?>
 <div id="yn_idea_tabs" class="tabs_alt tabs_parent">
          <!--  Tab bar -->
          <ul id="yn_idea_tab_list" class = "main_tabs">
          	    
                  <!-- Alphabetic -->
                  <li class="active">
                      <a href="javascript:;" rel="tab_1" class="selected">
                            <?php echo $this->translate('Alphabetic');?>
                      </a>
                  </li>
                  <!-- Ranking -->
                  <li>
                      <a href="javascript:;" rel="tab_2">
                            <?php echo $this->translate('Ranking');?>
                      </a>
                  </li>
                  <!-- Public ranking -->
                  <li>
                      <a href="javascript:;" rel="tab_3">
                            <?php echo $this->translate('Public Ranking');?>
                      </a>
                  </li>
                  <!-- I need to vote 
                  <li>
                      <a href="javascript:;" rel="tab_4">
                            <?php echo $this->translate('I need to vote');?>
                      </a>
                  </li>
                  -->
                  
          </ul>
    </div>
    <!-- Alphabetic Tab Content-->
    <div id="tab_1" class="tabcontent">
         <?php
                  echo $this->partial('_list_nominees.tpl', 'ynidea', array(
                              'arr_ideas' => $this->alphabetic,
                              'trophy_id' => $this->trophy->trophy_id,
                              'tab' => '1',
                          ));
        ?>
    </div>
    <!-- Ranking Tab Content -->
    <div id="tab_2" class="tabcontent">
        <?php
                echo $this->partial('_list_nominees.tpl', 'ynidea', array(
                              'arr_ideas' => $this->ranking,
                              'trophy_id' => $this->trophy->trophy_id,
                              'tab' => '2',
                          ));
        ?>
    </div>
    <!-- Public Ranking Tab Content -->
    <div id="tab_3" class="tabcontent">
        <?php
                echo $this->partial('_list_nominees.tpl', 'ynidea', array(
                              'arr_ideas' => $this->public_ranking,
                              'trophy_id' => $this->trophy->trophy_id,
                              'tab' => '3',
                          ));
        ?>
    </div>
    
    
 <script type="text/javascript">
       var yn_idea_tabs =new ddtabcontent("yn_idea_tabs");
       yn_idea_tabs.setpersist(false);
       yn_idea_tabs.setselectedClassTarget("link");
       yn_idea_tabs.init(900000);

</script>
<?php else: ?>
<div class="tip">
    <span>
        <?php echo $this->translate('There are no nominees.') ?>
    </span>
</div>
<?php endif; ?>

