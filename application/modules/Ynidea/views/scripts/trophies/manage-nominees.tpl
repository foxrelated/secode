<script type="text/javascript">
	en4.core.runonce.add(function(){
		$('ideaselectall').addEvent('click', function(){
			check = this.checked;
			list = document.getElementsByName('ideas[]');
			for(var i=0;i<list.length;i++){
				list[i].checked = check;
			}
		});
	});
	function updateIdeaList(event) {	
		mode = 'ideas';	
		if (event.keyCode == 13) {
			var search_text = document.getElementById('search').value;
	
			var element = document.getElementById('ideas-element');
			var content = element.innerHTML;
			element.innerHTML= "<img  src='application/modules/Ynidea/externals/images/loading.gif'></img>";
	
			new Request.JSON({
				'method': 'get',
				'url' : '<?php echo $this->url(array('action' => 'ajax'), 'ynidea_trophies',true) ?>',
				'data' : {
					'format' : 'json',
					'text' : search_text,
					'id' : <?php echo $this->trophy_id?>
				},
				'onSuccess' : function(json) {
	
					element.innerHTML = "";
			
					var input = new Element('INPUT', {
						'type': 'hidden',
						'name': 'ideas'
					});
			
					var ul = new Element('UL', {
						'class':'form-options-wrapper'
					});
			
					element.appendChild(input);
					element.appendChild(ul);
					
					if(json.total == 0)
					{
						document.getElementById('ideas_buttons-wrapper').style.display = 'none';
					}
					else
					{
						document.getElementById('ideas_buttons-wrapper').style.display = 'block';
					}
			
					for(var i=0;i<json.total;i++)
					{
						var item  = json.rows[i];
				
						var li = document.createElement('li');
						var li = new Element('LI');
						var input = new Element('INPUT',{
							'type': 'checkbox',
							'name': mode+'[]',
							'id': mode+'-'+ item.id,
							'value': item.id
						});
						
						var label = new Element('LABEL',{
							'html': item.title,
							'for': mode+'-'+ item.id
						});
						
						li.appendChild(input);
						li.appendChild(label);
						ul.appendChild(li);
					}
				}
			}).send();
		}
	}
	function submitForm()
	{
		var form = document.getElementById('ynidea_form_trophy_ideas');
		form.setAttribute('action', '');
		form.submit();		
	}
</script>
<table style="height: 400px;">
	<tr id="idea_content" style="display: block; padding-left: 10px; padding-top: 10px">
		<td>
			<?php if($this->current_count != 0):?> 
				<?php echo $this -> form -> setAttrib('class', 'global_form_popup') -> render($this);?>
			<?php else:?>
			<div class="tip">
				<span>
					<?php echo $this -> translate('Currently, there are no ideas that you can select.');?>
				</span>
			</div> 
			<?php endif;?>
		</td>
	</tr>
</table>
