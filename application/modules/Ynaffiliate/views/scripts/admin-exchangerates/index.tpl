<h2>
   <?php echo $this->translate('Affiliate Plugin') ?>
</h2>
<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>
<h3>
   <?php echo $this->translate('Exchange Rates') ?>
</h3>
<p>
   <?php
   $base_currency = Engine_Api::_()->getApi('settings', 'core')->payment['currency'];
   $description = $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINEXCHANGERATE_INDEX_DESCRIPTION");
   $description = vsprintf($description, array($base_currency));
   echo $description;
   //echo $description.$base_currency;
   ?>
</p>
<br />

<?php if (count($this->paginator)): ?>
 <div class="admin_table_form">
   <table class='admin_table'>
      <thead>
         <tr>
            <th><?php echo $this->translate("Currency Code") ?></th>
            <th><?php echo $this->translate("Currency Name") ?></th>
            <th><?php echo $this->translate("Exchange Rate") ?></th>  
            <th><?php echo $this->translate("Options") ?></th>
         </tr>
      </thead>

      <tbody>
         <?php
         $rates = $this->paginator;

         foreach ($rates as $rate) {
            ?>
            <tr>   
               <td><?php echo $rate['exchangerate_id'] ?></td>
               <td><?php echo $rate['name'] ?></td>
               <?php if ($rate['exchange_rate'] == null) {
                  ?>
                  <td><?php echo $this->translate("N/A") ?></td> 
                  <td>
                     <a class="smoothbox" href='<?php echo $this->url(array('action' => 'addrate', 'exchangerate_id' => $rate['exchangerate_id'])); ?>'>
                        <?php echo $this->translate("Edit") ?>
                     </a>
                  </td>
                  <?php
               } else {
                  ?>
                  <td><?php echo $rate['exchange_rate'] ?></td>   
                  <td>  
                     <a class="smoothbox" href='<?php echo $this->url(array('action' => 'edit', 'exchangerate_id' => $rate['exchangerate_id'])); ?>'>
                        <?php echo $this->translate("Edit") ?>
                     </a>
                  </td>
               <?php } ?>


            </tr>
         <?php } ?>


      </tbody>
   </table>
 </div>
   </br>
   <div>
      <?php echo $this->paginationControl($this->paginator); ?>
   </div>

<?php else: ?>
   <div class="tip">
      <span>
         <?php echo $this->translate("There are no exchange rate yet.") ?>
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