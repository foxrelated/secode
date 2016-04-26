 <h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
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
</style>   
<h2><?php echo $this->translate("Statistics"); ?></h2>
<h3><?php echo $this->translate("Deals"); ?></h3>
<table class='admin_table'>
  <thead>
  <tr>
  <th><?php echo $this->translate('Deals'); ?></th>
  <th><?php echo $this->translate('Today'); ?></th>
  <th><?php echo $this->translate('This Week'); ?></th>
  <th><?php echo $this->translate('This Month'); ?></th>
  <th><?php echo $this->translate('This Year'); ?></th>
  <th><?php echo $this->translate('Total'); ?></th>
   </tr>
  </thead>
  <tbody>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Created');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->deals_0[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Pending');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->deals_10[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Upcoming');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo $this->deals_20[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Running');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo $this->deals_30[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Closed');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->deals_40[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Canceled');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo $this->deals_50[$i] ; ?></td>
  <?php endfor;?>
  </tr>
    </tbody>
</table>
<br/>
<h3><?php echo $this->translate("Transactions"); ?></h3>
<table class='admin_table'>
  <thead>
  <tr>
  <th><?php echo $this->translate('Transactions'); ?></th>
  <th><?php echo $this->translate('Today'); ?></th>
  <th><?php echo $this->translate('This Week'); ?></th>
  <th><?php echo $this->translate('This Month'); ?></th>
  <th><?php echo $this->translate('This Year'); ?></th>
  <th><?php echo $this->translate('Total'); ?></th>
   </tr>
  </thead>
  <tbody>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Paid Referral Amount');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->currencyadvgroup($this->amount_0[$i] ); ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Paid Amount To Company');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo  $this->currencyadvgroup($this->amount_1[$i]) ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Total Paid Amount');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->currencyadvgroup($this->amount_2[$i]) ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Collected Money From Deals');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->currencyadvgroup($this->amount_3[$i]) ; ?></td>
  <?php endfor;?>
  </tr>
  <!-- <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Publishing Fee');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo "$".$this->fee_0[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Feature Fee');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo "$".$this->fee_1[$i] ; ?></td>
  <?php endfor;?>
  </tr> -->
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Total Fee Collected From Deals');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo $this->currencyadvgroup($this->fee_0[$i]) ; ?></td>
  <?php endfor;?>
  </tr>
    </tbody>
</table>
<br/>
<h3><?php echo $this->translate("Requests"); ?></h3>
<table class='admin_table'>
  <thead>
  <tr>
  <th><?php echo $this->translate('Requests'); ?></th>
  <th><?php echo $this->translate('Today'); ?></th>
  <th><?php echo $this->translate('This Week'); ?></th>
  <th><?php echo $this->translate('This Month'); ?></th>
  <th><?php echo $this->translate('This Year'); ?></th>
  <th><?php echo $this->translate('Total'); ?></th>
   </tr>
  </thead>
  <tbody>
 <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Approved');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->requests_0[$i] ; ?></td>
  <?php endfor;?>
  </tr> 
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate("Request's Quantity from Seller");; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->requests_1[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
   <tr>
  <td style="font-weight: bold"><?php echo $this->translate("Request's Quantity from Buyer");; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
  <td><?php echo $this->requests_2[$i] ; ?></td>
  <?php endfor;?>
  </tr>
  <tr>
  <td style="font-weight: bold"><?php echo $this->translate('Total Requests');; ?></td>
  <?php for($i = 0; $i < 5; $i ++): ?>
   <td><?php echo $this->requests_3[$i]+ $this->requests_0[$i]; ?></td>
  <?php endfor;?>
  </tr>
    </tbody>
</table> 
