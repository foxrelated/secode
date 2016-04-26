<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package	Poke
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license	http://www.socialengineaddons.com/license/
 * @version	$Id: userpoke.tpl 2010-11-27 9:40:21Z SocialEngineAddOns $
 * @author 	SocialEngineAddOns
 */
?>
<script type="text/javascript">
var cancelpoke = function(poke_id, poke_receiver_id)
{
	en4.core.request.send(new Request.HTML({      	
    url : en4.core.baseUrl + 'poke/pokeusers/cancelpoke/',
    data : {
      format : 'html',
      pokedelete_id : poke_id, 
      poke_receiverid : poke_receiver_id
		},
    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
    }
	}));
};

var pokeback = function(sender_id) {
	Smoothbox.open('<? echo $this->baseUrl() ?>'+'/poke/pokeusers/pokeuser/pokeuser_id/'+sender_id);
};
</script>

<li>
	<?php	echo $this->htmlLink($this->sendername->getOwner()->getHref(), $this->itemPhoto($this->sendername->getOwner(), 'thumb.icon'), array('style'=> 'float:left;')) ; ?>
	<div>
		<div>
			<?php	echo $this->htmlLink($this->sendername->getOwner()->getHref(), $this->sendername->getTitle()) . ' has poked you.' ?>
		</div>
	 	<div>
		 	<?php 
		 	  $user_level = Engine_Api::_()->user()->getViewer()->level_id;
	    	$send = Engine_Api::_()->authorization()->getPermission($user_level, 'poke', 'send');
	    ?>
	    <?php if($send):?>
				<button  onclick="pokeback(<?php echo $this->sender_id?>)">
	     	  <?php echo $this->translate('Poke Back');?>
	    	</button>
	    	<?php echo $this->translate(' or ');?>
	    <?php endif;?>
	      <a href="javascript:void(0);" onclick='cancelpoke(<?php echo $this->poke_id ?>,<?php echo $this->sender_id ?> )'>
	        <?php echo $this->translate('ignore this');?>
	      </a>
    </div>
  </div>
</li>   