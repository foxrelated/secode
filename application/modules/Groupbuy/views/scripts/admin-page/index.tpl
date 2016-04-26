<!-- Style For Admin Navigation Bar-->
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

<h2><?php echo $this->translate("Group Buy Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render The Menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Help") ?></h3>

<div>
    <table class="admin_table">
            <!-- Instruction Page Table Label-->
	<thead>
               <tr>
                  <th><?php echo $this -> translate("Title")   ?></th>
                  <th><?php echo $this -> translate("Created_date") ?></th>
                  <th><?php echo $this -> translate("Modified_date") ?></th>
                  <th><?php echo $this -> translate("Options")?></th>
               </tr>
	</thead>
            <!-- Instruction Page Table Rows -->
        <tbody>
                <?php foreach($this->paginator as $item): ?>
		<tr>
      <td><?php if(strlen($item->title)>35){
                                  echo $this->htmlLink($item->getHref(),substr($item->title,0,35)." ...");
                              }
                              else echo $this->htmlLink($item->getHref(),$item->title);
                                      ?>
      </td>
			<td><?php echo $item->created_date?></td>
			<td><?php echo $item->modified_date?></td>
			<td>
        <?php echo $this->htmlLink(array('route'       => 'groupbuy_page',
                                         'page_name'   => $item -> name),
                                   $this->translate('View')
                                   )?>
        |
        <?php echo $this->htmlLink(array('route'       => 'admin_default',
                                         'module'      => 'groupbuy',
                                         'controller'  => 'page',
                                         'action'      => 'edit-page',
                                         'page_id'        => $item -> page_id),
                                   $this->translate('Edit')
                                   )
        ?>
			</td>
		</tr>
		<?php endforeach; ?>
        </tbody>
    </table>
</div>
<br/>
            <!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
</div>