<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
?>
<?php echo $this->form->render($this);?>
<br/>
<?php
	   echo $this->htmlLink(array(
	         'route' => 'ynfundraising_general',
	          'action' => 'create-step-one',
	          'reset' => true,
	      ), $this->translate('Create Stand-alone Campaign'), array(
	           'class' => 'buttonlink ynfundraising_quick_create',
	    ));
?>
<div class="profile_fields">
	<h4>
		<span class='ynfundraising_module_title'><?php echo $this->translate("Create Campaign From Idea/Trophy")?></span>
		<img alt=""  style="float:right;" rel="group_desc" id="desc_more_icon_id" src="./application/modules/Ynfundraising/externals/images/up.jpg" onmousedown="toggleDesc('ynFRaising_createSearchUL_ideabox','desc_more_icon_id'); return false;" />
	</h4>
</div>
<ul class="ynFRaising_viewGeneralUL ynFRaising_createSearchUL" id="ynFRaising_createSearchUL_ideabox">
<?php if (count($this->paginator) > 0): ?>
<?php foreach($this->paginator as $item):?>
	<?php
		$object_item = Engine_Api::_()->getApi('core', 'ynfundraising')->getItemFromType($item);
	?>
	<li>
		<?php echo $this->htmlLink($object_item->getHref(), $this->itemPhoto($object_item, 'thumb.profile'), array('class' => 'ynFRaising_createSearch_thumb')) ?>
		<div class='ynFRaising_LRH3ULLi_info'>
			<div class='ynFRaising_LRH3ULLi_name'>
				<a href="<?php echo $object_item->getHref();?>" title='<?php echo $this->string()->escapeJavascript($object_item->title)?>'>
					<?php echo $this->string()->truncate($object_item->title,70);?>
				</a>
			</div>
			<div class='ynFRaising_LRH3ULLi_date'>
				<?php echo $this->translate("Created by %s",$object_item->getOwner());?>
			</div>
			<p class="ynFRaising_Detaildesc">
				<?php echo $this->string()->truncate($this->string()->striptags($object_item->getDescription()), 100);?>
			</p>
			<div class='ynFRaising_createSearchBtn'>
				<?php if($object_item->user_id == $user_id):?>
					<a class="smoothbox" href="<?php echo $this->url(array('action'=>'confirm-create','parent_id'=>$item['parent_id'],'parent_type'=>$item['parent_type']),'ynfundraising_general')?>"><button><?php echo $this->translate("Create Campaign")?></button></a>
					<?php else:?>
					<a class="smoothbox" href="<?php echo $this->url(array('action'=>'request-create','parent_id'=>$item['parent_id'],'parent_type'=>$item['parent_type']),'ynfundraising_general')?>"><button><?php echo $this->translate("Create Campaign")?></button></a>
				<?php endif;?>
			</div>
		</div>
	</li>
<?php endforeach;?>
<div style="padding-top: 10px;">
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
</div>
<?php else :?>
<div class="tip">
      <span>
        <?php echo $this->translate('There is no any idea/trophy.');?>
      </span>
</div>
<?php endif; ?>
</ul>

<script type="text/javascript">
function toggleDesc(block_id,img_id){
    if(document.getElementById(block_id).style.display == 'none'){
      document.getElementById(block_id).style.display = 'block';
      document.getElementById(img_id).src = './application/modules/Ynfundraising/externals/images/up.jpg';
    }else{
      document.getElementById(block_id).style.display = 'none';
      document.getElementById(img_id).src = './application/modules/Ynfundraising/externals/images/down.jpg';
    }
}

en4.core.runonce.add(function(){
    if($('search')){
      new OverText($('search'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
 });

</script>