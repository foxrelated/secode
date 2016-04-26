<script type="text/javascript">

   window.addEvent('domready', function() {
      if( $('levelSelect') ) {
         $('levelSelect').removeEvents().addEvent('change', uiChangeMemberLevel);
      }
   });

   var uiChangeMemberLevel = function() {
      var level_id = $(event.target).value;
      var url = new URI(window.location);
      url.setData({level_id:level_id});
      window.location = url;
   };

</script>

<h2>
   <?php echo $this->translate('Affiliate Plugin') ?>
</h2>

<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>

<br />

<div class="admin_fields_type">
   <h3><?php echo $this->translate("Member Level"); ?>:</h3>
   <?php echo $this->formSelect('levelSelect', $this->level_id, array(), $this->levelOptions) ?>
</div>
<br/>
<?php if (count($this->paginator)): ?>
<div class="admin_table_form">
   <table class='admin_table'>
      <thead>
      <tr>
         <th><?php echo $this->translate("Payment Types") ?></th>

         <?php for ($l = 1; $l <= $this->MAX_COMMISSION_LEVEL; $l++) { ?>
            <th><?php echo $this->translate("Level %s", $l) ?></th>
         <?php } ?>

         <th><?php echo $this->translate("Options") ?></th>

      </tr>
      </thead>

      <tbody>
      <?php
            $rule = $this->paginator;

      foreach ($rule as $type) {
      ?>
      <tr>
         <td><?php echo $this->translate($type['rule_title']) ?></td>

         <?php for ($l = 1; $l <= $this->MAX_COMMISSION_LEVEL; $l++) { ?>
            <?php if (isset($type["level_$l"])) { ?>
               <td><?php echo $type["level_$l"] . " %" ?></td>
            <?php } else { ?>
               <td><?php echo $this->translate("N/A") ?></td>
            <?php } ?>
         <?php } ?>

         <td>
            <a class="smoothbox" href='<?php echo $this->url(array(
               'action' => 'edit',
               'rule_id' => $type['rule_id'],
               'level_id' => $this->level_id,
               'rulemap_id' => $type['rulemap_id'],
               'MAX_COMMISSION_LEVEL' => $this->MAX_COMMISSION_LEVEL
            ));
            ?>'>
            <?php echo $this->translate("Edit") ?>
            </a>
         </td>

      </tr>
      <?php } ?>

      </tbody>
   </table>
</div>
</br>
<div>
   <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
   )); ?>
</div>

<?php else: ?>
<div class="tip">
      <span>
         <?php echo $this->translate("There are no rules yet.") ?>
      </span>
</div>
<?php endif; ?>
<style type="text/css">
   .tabs > ul > li {
      display: block;
      float: left;
      margin: 2px;
      padding: 5px;
   }
   .tabs > ul {
      display: table;
      height: 65px;
   }

   .tabs > ul > li > a{
      white-space:nowrap!important;
   }
</style>