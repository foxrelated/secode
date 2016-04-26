
<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>

<?php echo $this->content()->renderWidget('ynaffiliate.admin-main-menu') ?>

<p>
   <?php echo $this->translate("YNAFFILIATE_VIEWS_SCRIPTS_ADMINMANAGE_CLIENT_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
   <?php echo $this->form->render($this); ?>
</div>
<br/>
<?php if (count($this->paginator) > 0): ?>

   <table class='admin_table'>
      <thead>
         <tr>

            <th><?php echo $this->translate("Affiliate Name") ?></th>
            <th><?php echo $this->translate("Client Name") ?></th> 
            <th><?php echo $this->translate("Registered Date") ?></th>
            <th><?php echo $this->translate("Referring URL") ?></th> 

         </tr>
      </thead>
      <tbody>
		<?php foreach ($this->paginator as $comm) : ?>
         <tr>
            <td><?php echo Engine_Api::_()->getItem('user',$comm['affiliate_id']);?></td>
            <td><?php echo Engine_Api::_()->getItem('user',$comm['new_user_id']); ?></td> 
            <td><?php echo $this->locale()->toDateTime(Engine_Api::_()->getItem('user',$comm['new_user_id'])->creation_date); ?></td>
            <td>
            	<?php 
					$assoc = Engine_Api::_()->ynaffiliate()->getAssocId($comm['new_user_id']);
					$link_id = $assoc->link_id;
                     if ($link_id != 0) {
                     	$Links = new Ynaffiliate_Model_DbTable_Links;
	                    $target_url = $Links->getTargetLink($link_id);
    	                echo $target_url;
                     }
                     else {
                     	echo $this->translate('N/A');
                     }
            	?>
            </td> 

         </tr>



	<?php endforeach;?>
      </tbody>
   </table>

   <br />

   
      <div>
         <?php echo $this->paginationControl($this->paginator, null, null, array(
          'pageAsQuery' => false,
          'query' => $this->formValues,
        )); ?>
      </div>
   <br/>


<?php else: ?>
   <div class="tip">
      <span>
         <?php echo $this->translate("There are no clients yet.") ?>
      </span>
   </div>
<?php endif; ?>

<style type="text/css">

   .admin_search {
      max-width: 100% !important;
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
.search div label{
    margin-bottom: 5px;
}
.search form > div{
    margin-left: 0px;
    margin-right: 10px;
    margin-bottom: 10px;
}
.f1 button[type="submit"]{
    margin-top: 17px;
}
</style>