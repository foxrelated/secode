<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<div id="usertextboxcontent" style="display:none;">
  <?php echo nl2br($this->userListingstext); ?>
</div>

<script language="JavaScript">
 
 var usertextboxcontent = $('usertextboxcontent').innerHTML;
  function function1() {
    $("write").style.display="none";
    $("write1").style.display="block";
    $('fname').focus();
  }
 
  function upperCase()
  {
    var listing_id = '<?php echo $this->list->listing_id; ?>';
    var str = document.getElementById('fname').value.replace(/\n/g,'<br />');
    var str_temp = document.getElementById('fname').value;

    $('write1').style.display="none";
    $('id_saveimage').style.display="block"; 

		en4.core.request.send(new Request.HTML({
			url : en4.core.baseUrl + 'list/index/display/',
			data : {
				format : 'html',
				strr : str_temp,
				listing_id : listing_id
			},
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				if (str == '') {
					str = "<div class='write_link'><a href='javascript:void(0);' onclick='function1()'>" + 'Write something about '+ '<?php echo $this->list->title;?>' + "</a></div>";
					$('write').innerHTML = str
				}
				else {
						$('write').innerHTML = '<div class="des_edit"><div class="edit_icon"><a href="javascript:void(0);" onclick="function1()"></a></div><div class="des">' + str +' </div></div>';
					}
				usertextboxcontent = str; 
				$('write').style.display="block";
				$('id_saveimage').style.display="none"; 
			}
		}));
  }
</script>

<?php if($this->list->owner_id == $this->viewer_id): ?>
	<div class="list_write_overview">
		<div id="write">
                    <?php echo '<div class="des_edit"><div class="edit_icon"><a href="javascript:void(0);" onclick="function1()"></a></div><div class="des">' .htmlspecialchars_decode(nl2br($this->userListingstext),  ENT_QUOTES).'</div></div>' ?>
		</div>
		
		<div style='display:none;' id="write1" >
		  <div class="textarea">
		  	<textarea rows="2" cols="10" onblur="upperCase()" id="fname" style='display:block;'><?php echo $this->userListingstext;?></textarea>
		  </div>
		  <div class="edit_icon">
		  	<a href="javascript:void(0);" onclick="function1()"></a>
		  </div>	
		</div>

		<div class="des_edit" style='display:none;' id="id_saveimage">
			<center>
		  	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/spinner.gif" alt="" />
			</center>
		</div>
	</div>
<?php elseif(!empty($this->userListingstext)): ?>
	<div class="list_write_overview">
		<div class="details" id="write">
			<?php echo htmlspecialchars_decode(nl2br($this->userListingstext),  ENT_QUOTES) ;?>
		</div>
	</div>
<?php endif; ?>

<script language="JavaScript">
window.addEvent('load',function() {
        
   <?php if(empty($this->userListingstext)): ?>
			
      var content = "<div class='write_link'><a href='javascript:void(0);' onclick='function1()'>" + 'Write something about '+ '<?php echo $this->list->title;?>' + "</a></div>";
      $('write').innerHTML = content;
   <?php endif; ?>
        
});
    
</script>
