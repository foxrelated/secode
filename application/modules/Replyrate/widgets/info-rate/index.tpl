<ul>
  <li>
<table width="95%" border="0" class="main-reply" id="replyrate">
  <tr>
    <td class="replyrate"><?php echo $this->translate("Reply rate");?></td>
    <td valign="middle"><div class="main-div-reply" style="border-color:<?php echo $this->bordercolor ?>"><div style="width:<?php echo $this->percent ?>%; background-color:<?php echo $this->backgroundcolor ?>"></div></div>	</td>
  </tr>
</table>
  </li>
</ul>

<script type="text/javascript">
var Tip;
window.addEvent('domready', function(){
	Tip = new Tips($('replyrate'),{
		'className':'replyrate'
	});
	Tip.addEvent('show', function(tip){
	  $$('.tip-top').set('html',"<div class='reply_popup_main'><div class='reply_popup'><?php echo $this->translate('Reply rate shows the ratio of incoming messages and answers to them. If the Reply rate is low then user answers rarely, if the Reply rate is high then the probability of response is much higher.');?></div></div>");
	  tip.fade('in');
	});
	Tip.addEvent('hide', function(tip){
	  tip.fade('out');
	}); 
});
</script>