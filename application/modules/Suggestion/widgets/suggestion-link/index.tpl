<?php

  $getBaseURL = $this->base_url . 'suggestion/index/switch-popup/modName/' . $this->modName .'/modContentId/' . $this->subject_id . '/modError/1';

?>

<script type="text/javascript">
function smoothbox_open()
{
  var getBaseURL = '<?php echo $getBaseURL; ?>';
  Smoothbox.open (getBaseURL);							
} 
</script>

<div class="quicklinks">
	<ul class="navigation">
		<li>
			<a href="javascript: void(0)" onclick="smoothbox_open();" class="buttonlink icon_suggestion"><?php echo $this->translate("Suggest to Friend"); ?></a>
		</li>	
	</ul>
</div>