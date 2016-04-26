<div class="generic_layout_container layout_top">
<?php echo $this->content()->renderWidget('ynaffiliate.main-menu') ?>
</div>
<div class="generic_layout_container layout_main">
   <div class="generic_layout_container layout_middle">
       <h3><?php echo $this->translate("List of subscribed affiliates") ?></h3>
		<?php echo $this->form->render($this); ?>
       </br>
       <div class="table_scroll">
        <table id="anyid" cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr style="background:#E9F4FA none repeat scroll 0 0;">
                   <th class="table_th"><?php echo $this->translate('Registration Date'); ?></th>
                   <th class="table_th" ><?php echo $this->translate('Affiliate Name'); ?></th>
                   <th class="table_th" ><?php echo $this->translate('Affiliate Email'); ?></th>
                   <th class="table_th" ><?php echo $this->translate('Referring URL'); ?></th>
                   
                </tr>
                <tr>
                   <td class="table_td"  >
                     <?php echo $this->translate('Registration Date'); ?>
                   </td>
                   <td class="table_td" >
                      <a href=""><?php echo $this->translate('Affiliate Name'); ?></a>
                   </td>
                    <td class="table_td" >
                     <?php echo $this->translate('Affiliate Email'); ?>
                   </td>
                   <td class="table_td" >
                      <?php echo $this->translate('Referring URL'); ?>
                   </td>
                </tr>    
                      
            </table>
          </div>
	</div>
</div>