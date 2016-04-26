<div class="generic_layout_container layout_user_list_signups">
<ul>
  <li style="font-size:11px">
	<?php echo $this->translate("Your information is filled to "); echo "<b>".$this->percent."</b>%" ?>  
  </li>
  <li>
	<div class="main-div" style="border-color:<?php echo $this->bordercolor ?>"><div style="width:<?php echo $this->percent ?>%; height:15px; background-color:<?php echo $this->backgroundcolor ?>"></div></div>
  </li>
  <li>
	<?php echo $this->label ?>
  </li>  
</ul>
</div>
