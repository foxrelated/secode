 <?php
$menu = $this->partial('_menu.tpl', array());  
echo $menu;?>
<h3>
	<?php echo $this->idea->getTitle() ?>
</h3>
<div>
	<?php echo $this->translate("Tags: ");
	if(count($this->tags) > 0):
		foreach($this->tags as $tag): ?>
			<a  href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' ><?php echo $tag->text?></a> 
		<?php endforeach;
	else:
	echo $this->translate("None");
		endif; ?> 
</div>
<div style="padding-top: 5px;">
	<div>
		<?php echo $this->translate("Created by")." ";
			  echo $this->idea->getOwner()." | ";
			  echo $this->timestamp($this->idea->creation_date);
		?>
	</div>
	<div>
		<?php echo $this->translate("Version")." ";
			  echo $this->idea->version." | ";
			  echo $this->timestamp($this->idea->version_date);?>
	</div>
</div>
<div style="padding-top: 5px; margin-bottom: 15px">
	<?php echo $this->translate("Cost").": ";
		  echo $this->idea->cost." | ";
		  echo $this->translate("Feasibility").": ";
		  switch ($this->idea->feasibility) 
		  {
			  case 0:
				  echo $this->translate('Easy');
				  break;
			  case 1:
				  echo $this->translate('Slightly Complex');
				  break;
			  case 2:
				  echo $this->translate('Complex');
				  break;
			  case 3:
				  echo $this->translate('Very Complex');
				  break;
			  default:
				  echo $this->translate('Easy');
				  break;
		  }
		  echo " | ";
		  echo $this->translate("Reproducible").": ";
		  echo $this->idea->reproducible?$this->translate("Yes"):$this->translate("No"); 
	?>
</div>
<div class="ynidea_content">  
<?php echo $this->idea->body;?>
</div>

<div class="layout_middle" style="float: left; width: 100%; "> 

<form>
 
 <div style="padding-bottom: 10px; padding-top: 15px;">
 <h3><?php echo $this->translate("History")?></h3>
 </div> 
<table class="ynidea_table ynidea_table_index_history">
<tr class="ynidea_header">
<th><?php echo $this->translate("Version"); ?></th>
<th><?php echo $this->translate("Voters"); ?></th>
<th><?php echo $this->translate("Average Vote"); ?></th>
<th><?php echo $this->translate("Potential"); ?></th>
<th><?php echo $this->translate("Feasibility"); ?></th>
<th><?php echo $this->translate("Innovation"); ?></th>
<th><?php echo $this->translate("Changed By"); ?></th>
<th><?php echo $this->translate("Publish"); ?></th>
<th><?php echo $this->translate("Action"); ?></th>
</tr>
<?php
foreach($this->paginator as $revision): 
$idea = Engine_Api::_()->getItem('ynidea_idea', $revision->idea_id);
 ?>
   <tr class="ynidea_table_body">
   <td>
   <?php
   $str = $this->translate("CURRENT");  
   if($this->idea->version == $revision->idea_version):
   $str = $this->translate("CURRENT");
   else:
   $str = $this->translate("v. %s",$revision->idea_version);
   endif;
   ?>
   <?php echo $this->htmlLink(array(
          'action' => 'preview-revision',
          'id' => $revision->version_id,
          'route' => 'ynidea_specific',
          'reset' => true,
        ), $str, array(
        )) ?> 
  <?php if($this->idea->version == $revision->idea_version):  
   echo $this->translate("(v. %s)",$revision->idea_version);
   endif;
   ?>
   </td>
  <td><?php if($this->idea->version == $revision->idea_version): echo $this->idea->vote_count; else: echo $revision->vote_count; endif;?> </td>
  <td><?php if($this->idea->version == $revision->idea_version): echo $this->idea->vote_ave; else: echo $revision->vote_ave; endif;?> </td>
  <td><?php if($this->idea->version == $revision->idea_version): echo $this->idea->potential_ave; else: echo $revision->potential_ave; endif;?> </td>
  <td><?php if($this->idea->version == $revision->idea_version): echo $this->idea->feasibility_ave; else: echo $revision->feasibility_ave; endif;?> </td>
  <td><?php if($this->idea->version == $revision->idea_version): echo $this->idea->innovation_ave; else: echo $revision->innovation_ave; endif;?> </td>
   <td><?php 
   $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
   $viewer = Engine_Api::_()->user()->getViewer();
   echo $this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle());
   ?> </td>
    <td>
        <?php 
        if(Engine_Api::_()->ynidea()->checkPublishIdea($revision->idea_id,$revision->version_id))
            echo $this->translate('Published');
        elseif($this->idea->authorization()->isAllowed($viewer,'edit'))
		{
	        echo $this->htmlLink(array(          
	          'action' => 'publish-history',
	          'id' => $revision->idea_id,
	          'version_id' => $revision->version_id,
	          'route' => 'ynidea_specific',
	          'reset' => true,
	        ), $this->translate('Publish'), 
	        array('class'=>'smoothbox'
	        ));
		}
         ?> 
    </td>
    <td>
        <?php
        if($this->idea->isOwner($viewer) || $revision->isOwner($viewer))
		{
	        if(!Engine_Api::_()->ynidea()->checkPublishIdea($revision->idea_id,$revision->version_id)):
		        echo $this->htmlLink(array(          
		          'action' => 'edit-history',
		          'id' => $revision->idea_id,
		          'version_id' => $revision->version_id,
		          'route' => 'ynidea_specific',
		          'reset' => true,
		        ), $this->translate('Edit'), array(
		        ));
		        echo "&nbsp;|&nbsp;";
			endif;
		        echo $this->htmlLink(array(          
		          'action' => 'delete-history',
		          'id' => $revision->idea_id,
		          'version_id' => $revision->version_id,
		          'route' => 'ynidea_specific',
		          'reset' => true,
		        ), $this->translate('Delete'), array('class'=>'smoothbox'
		        ));
		}	
        ?>
    </td>
   </tr>
<?php endforeach;?>
</table>

 <input type="hidden" id="ids" name="ids" value=""/>
 <input type="hidden" id="versions" name="versions" value=""/>
</form>
<div style="padding-top: 5px;">
<h4 style="border: none;">
<?php echo $this->htmlLink($this->idea->getHref(),"&laquo; ".$this->translate("Back To Idea Detail")); ?>
</h4>
</div>
</div>