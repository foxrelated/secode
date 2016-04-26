<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>

<!-- admin menu -->
<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>

<p>
  <?php echo $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINMANAGE_REQUEST_DESCRIPTION") ?>
</p>
<br />
<?php 
$baseCurrency = Engine_Api::_()->getApi('settings', 'core')->payment['currency'];

echo $this->translate('Current Currency: ');?>
<?php echo $baseCurrency; ?>
<br />
<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>

 <br/>
 <br/>
<?php if( count($this->paginator) > 0 ): ?>
 <div class="admin_table_form">
 <table class='admin_table'>
      <thead class="table_thead">
         <tr>
            <th><?php echo $this->translate("Affiliate Name") ?></th>
            <th><?php echo $this->translate("Request Points") ?></th>
            <th><?php echo $this->translate("Request Amount") ?></th>
             <th><?php echo $this->translate("Request Currency") ?></th>
            <th><?php echo $this->translate("Request Status") ?></th>
            <th><?php echo $this->translate("Message") ?></th>   
            <th><?php echo $this->translate("Date") ?></th> 
            <th>
				<?php echo $this->translate("Options") ?>
			</th>
           
         </tr>
      </thead>
      <tbody>
	<?php foreach($this->paginator as $item): ?>
         <tr>
            <td><?php echo Engine_Api::_()->getItem('user',$item->user_id);	 ?></td> 
            <td><?php echo $this->locale()->toNumber(round($item->request_points, 2)); ?></td> 
            <td><?php echo $this->locale()->toNumber(round($item->request_amount, 2))?></td>
             <td><?php echo $item->currency; ?></td>
            <td><?php echo $this->translate(ucfirst($item->request_status)) ?></td> 
            <td><?php echo $item->request_message; ?></td> 
            <td><?php echo $this->locale()->toDateTime($item->request_date) ?></td>
          	<td>
			<?php if($item->isWaitingToProcess()): ?>
				<a href="<?php echo $this->url(array('action'=>'accept','id'=>$item->request_id)) ?>"><?php echo $this->translate('Accept ')?></a>|
				<a href="<?php echo $this->url(array('action'=>'deny','id'=>$item->request_id)) ?>" class="smoothbox"><?php echo $this->translate(' Deny')?></a>
			<?php else: ?>
				<?php echo $this->translate('N/A');?>
			<?php endif; ?>
			</td>
         </tr>

	<?php endforeach; ?>
      </tbody>
   </table>
 </div>
<br />
 <!-- Page Changes  -->
    <div>
       <?php  echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => false,
          'query' => $this->formValues,
        ));     ?>
    </div>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no requests yet.") ?>
    </span>
  </div>
<?php endif; ?>
	
<style type="text/css">

.admin_search {
    max-width: 650px !important;
}
</style>
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