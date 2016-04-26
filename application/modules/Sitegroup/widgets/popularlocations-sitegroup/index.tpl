<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl';
?>
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
    form.elements['sitegroup_location'].value = cityValue;
    
		form.submit();
  }

</script>

<ul class="seaocore_browse_category">
  <form id='filter_form_location' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitegroup_general', true) ?>' style='display: none;'>
    <input type="hidden" id="sitegroup_location" name="sitegroup_location"  value=""/>
  </form>
  <?php foreach ($this->sitegroupLocation as $sitegroupLocation): ?>
    <?php if (!empty($sitegroupLocation->city) ||  !empty($sitegroupLocation->state)): ?>
      <li>
        <div class="cat"  <?php if (!empty($this->searchLocation) && ( $this->searchLocation == $sitegroupLocation->city ||  $this->searchLocation == $sitegroupLocation->state ) ): ?>style="font-weight: bold;" <?php endif; ?> >
          <a href="javascript:void(0);" onclick="locationAction('<?php if(!empty($sitegroupLocation->city))echo $sitegroupLocation->city; else echo $sitegroupLocation->state;  ?>')" ><?php echo ucfirst($sitegroupLocation->city) ?><?php $state=null;if(!empty($sitegroupLocation->city)&& !empty($sitegroupLocation->state))$state.=" [";$state.=ucfirst($sitegroupLocation->state);if(!empty($sitegroupLocation->city)&& !empty($sitegroupLocation->state))$state.="] ";echo $state;?></a>
          <?php if(!empty($sitegroupLocation->city)): echo "(" . $sitegroupLocation->count_location . ")"; else: echo "(" . $sitegroupLocation->count_location_state . ")"; endif;?>
        </div>	
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>