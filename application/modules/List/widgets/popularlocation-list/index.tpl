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

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>

<script type="text/javascript">
  var locationAction =function(cityValue)
  {
    if($("tag"))
      $("tag").value='';
    var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_location')){
				form=$('filter_form_location');
			}
    form.elements['list_location'].value = cityValue;
    
		form.submit();
  }
</script>

<ul class="seaocore_browse_category">
  <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'list_general', true) ?>' style='display: none;'>
    <input type="hidden" id="list_location" name="list_location"  value=""/>
  </form>
  <?php foreach ($this->listLocation as $listLocation): ?>
    <?php if (!empty($listLocation->city) || !empty($listLocation->state)): ?>
      <li>
        <div class="cat"  <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $listLocation->city ||  $this->searchLocation == $listLocation->state ) ): ?>style="font-weight: bold;" <?php endif; ?> >
          <a href="javascript:void(0);" onclick="locationAction('<?php if(!empty($listLocation->city))echo $listLocation->city; else echo $listLocation->state;  ?>')" ><?php echo ucfirst($listLocation->city) ?><?php $state=null;if(!empty($listLocation->city)&& !empty($listLocation->state))$state.=" [";$state.=ucfirst($listLocation->state);if(!empty($listLocation->city)&& !empty($listLocation->state))$state.="] ";echo $state;?></a>
          <?php if(!empty($listLocation->city)): echo "(" . $listLocation->count_location . ")"; else: echo "(" . $listLocation->count_location_state . ")"; endif;?>
        </div>	
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>