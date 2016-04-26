<?php 
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Socialstore/externals/styles/prettyPhoto.css');
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery-1.4.4.min.js')
       ->appendFile($this->baseUrl() . '/application/modules/Socialstore/externals/scripts/jquery.prettyPhoto.js');
?>
<ul>
<?php if (($this->store->deleted == 1) || ($this->store->view_status != 'show' 
		&& ( ($this->isAdmin != 1) && ($this->store->owner_id != $this->viewer_id)) ) 
		|| ($this->store->approve_status != 'approved' 
		&& ( ($this->isAdmin != 1) && ($this->store->owner_id != $this->viewer_id)) )) :?>
	<li><div class="tip"><span><?php echo $this->translate('You cannot view this store.');?></span></div>
</li>
<?php else: ?>

<li>
<h3> <?php echo $this->store->getTitle();?> </h3>
<div class="store_browse_info_date">
	<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->store->getOwner()->getHref(), $this->store->getOwner()->getTitle()) ?>
	<?php echo $this->timestamp(strtotime($this->store->creation_date)) ?>
</div>
</li>
<li>
	<div class="gallery clearfix">
        <!-- main photo    -->
         <?php if($this->main_photo): ?>  
                <div class="store_ga_large_photo">
                	<a href="<?php echo $this->store->getPhotoUrl()?>" 
                		rel="prettyPhoto[gallery]" 
                		title="<?php echo $this->store->title?>">
                		<img src="<?php echo $this->store->getPhotoUrl("thumb.profile")?>" />
                	</a>
                </div>    
                <div class="store_ga_thumb_photo">
                <?php foreach($this->paginator as $photo ): ?>
            <?php if($this->store->photo_id != $photo->file_id && $photo->slideshow == 1): ?>
            	<span class="detaillevel">
            		<a href="<?php echo $photo->getPhotoUrl()?>" 
            			rel="prettyPhoto[gallery]" 
            			title="<?php echo $this->store->title?>">
            		<img src="<?php echo $photo->getPhotoUrl()?>" width="52px" height="71px" />
            		</a>
            	</span>
            <?php endif; ?>
          <?php endforeach;?>
          </div>
         <?php endif; ?>
	</div>
</li>
<li>
	<h4><span><?php echo $this->translate('Contact Information');?></span></h4>
</li>
<li>
  	<div id="store_google_map_component">
		<div class="store_map_canvas_featured"  title="<?php echo mysql_escape_string($this->store->contact_address) ?>" id="map_canvas" >
		</div>
		<div id="store_loading_google_map">
			<img src="<?php echo $this->baseUrl(). '/application/modules/Socialstore/externals/images/loading.gif' ?>" />
			<?php echo $this->translate("Loading Google Map ...") ?>
		</div>
	</div>
	<div class="store_contact_detail_left">
		<div class="field_listings">
			<ul>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Contact person") ?></span>
					<span><?php echo $this->store->contact_name;?></span>
				</li>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Address") ?></span>
					<span><?php echo $this->store->contact_address;?></span>
				</li>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Website") ?></span>
					<span><a href= "<?php echo $this->store->contact_website;?>" target="_blank"><?php echo $this->store->contact_website;?></a></span>
				</li>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Email") ?></span>
					<span><a href="mailto:<?php echo $this->store->contact_email;?>"><?php echo $this->store->contact_email;?></a></span>
				</li>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Telephone") ?></span>
					<span><?php echo $this->store->contact_phone;?></span>
				</li>
				<li>
					<span class="store_span_title"><?php echo $this->translate("Fax") ?></span>
					<span><?php echo $this->store->contact_fax;?></span>
				</li>
				<li>
					<?php echo $this->fieldValueLoop($this->store, $this->fieldStructure) ?>
				</li>
			</ul>
		</div>
		
	</div>
	<div style="clear: both">&nbsp;</div>
	<div id="company_component_inforbox" style="display:none;">
     <strong><?php echo $this->translate("Address:")?></strong>
     	<span class="desc434"><?php echo $this->store->contact_address?></span>
	<strong><?php echo $this->translate("Phone:")?></strong>
   		<span class="desc434"><?php echo $this->store->contact_phone?></span>
	</div>
  </li>
  <li>
  	<h4><span><?php echo $this->translate("Description")?></span></h4>
  </li>
  <li>
	  <div class="store_detail_description">
	    <?php echo $this->store->description;?>
	  </div>
  </li>  
  <li>
  	<h4><span><?php echo $this->translate("Shipping Methods")?></span></h4>
  </li>
  <li>
  <br />
  <?php if (count($this->rules) > 0): ?>
  <div class="ynstore_methodtb" style = "overflow: auto">
		<table class="admin_table">
	        <thead>
	            <tr>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Name") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Description") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Applied Categories") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Applied Countries") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Free Shipping") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Min Order Cost") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Shipment Fee") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Type") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Type Fee") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Handling Type") ?></th>
	                  <th class = "ynstore_table_text"><?php echo $this->translate("Handling Fee/%") ?></th>
	            </tr>
	        </thead>
	        <!--  Table Contents  -->
		    <tbody>
		        <?php foreach ($this->rules as $rule) : ?>
		        <tr>
			          <td class = "ynstore_table_text"><?php echo $rule['name']?></td>
			          <td class = "ynstore_table_text"><?php echo $rule['description']?></td>
			          <td class = "ynstore_table_text">
			          	<?php 
			          		$cat = '';
			          		$i = 0;
			          		foreach ($rule['category_id'] as $category) {
			          			$i++;
			          			$cat .= Engine_Api::_()->getApi('shipping','socialstore')->getRuleCat($category);
			          			if ($i < count($rule['category_id'])) {
			          				$cat .= ', ';
			          			}
			          		}
			          	echo $cat;?>
		          	  </td>
			          <td class = "ynstore_table_text">
			          	<?php 
			          		$coun = '';
			          		$i = 0;
			          		foreach ($rule['country_id'] as $country) {
			          			$i++;
			          			$coun .= Engine_Api::_()->getApi('shipping','socialstore')->getRuleCountry($country);
			          			if ($i < count($rule['country_id'])) {
			          				$coun .= ', ';
			          			}
			          		}
			          	echo $coun;?>
			          </td>
			          <?php if ($rule['order_minimum'] != 0) :?>
			          <td class = "ynstore_table_text"><?php echo $this->translate('Yes');?></td>
			          <td class = "ynstore_table_number"><?php echo $this->currency($rule['order_minimum']);?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None')?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None')?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None')?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None')?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None')?></td>
			          <?php else : ?>
			          <td class = "ynstore_table_text"><?php echo $this->translate('No');?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate('None');?></td>
			          <td class = "ynstore_table_number"><?php echo $this->currency($rule['order_cost'])?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate(ucfirst($rule['cal_type']))?></td>
			          <td class = "ynstore_table_number"><?php echo $this->currency($rule['type_amount'])?></td>
			          <td class = "ynstore_table_text"><?php echo $this->translate(ucfirst($rule['handling_type']))?></td>
			          <td class = "ynstore_table_number">
			          	<?php 
			          			if($rule['handling_fee_type'] == 'fixed') {
			          				echo $this->currency($rule['handling_fee']);
			          			}
			          			else {
			          				echo $rule['handling_fee'].'%';
			          			}
	          		  	?>
	          		  </td>
	          		  <?php endif;?>
		        </tr>
		        <?php endforeach; ?>
		    </tbody>
    </table>
    </div>
     <br />
    <?php endif;?>
  </li>
  <li>
	<div class="store_follow">
  		<?php echo $this->follow($this->store) ?>
  	</div>
  	<?php if(Engine_Api::_()->socialstore()->checkStoreGroupbuyConnection()):?>
  		<div style="padding-top:5px;">
        <?php echo $this->htmlLink(array(
                  'action' => 'store-requests',
                  'controller' => 'gda',
                  'storeId' => $this->store->store_id,
                  'route' => 'socialstore_extended',
                  'reset' => true,
                ), $this->translate('Deal Requests'), array('class' => 'store_gda'
                )); ?>
    </div>
    <?php endif;?>
  </li>
  <li>
  	<div <?php if ($this->can_rate): ?> 
  						onmouseout="rating_mouseout()" 
  		<?php elseif ($this->viewer->getIdentity() == 0): ?> 
  						onmouseover ="canNotRate(2);" 
  						onmouseout="canNotRate(0);" 
  		<?php else: ?> 
  						onmouseover ="canNotRate(1);" 
  						onmouseout="canNotRate(0);" 
  		<?php endif;?>  
  						id="store_rate">
            <?php for($i = 1; $i <= 5; $i++): ?>
              <img id="rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="rate(<?php echo $i; ?>);" onmouseover="rating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Socialstore/externals/images/<?php if ($i <= $this->store->rate_ave): ?>star_full.png<?php elseif( $i > $this->store->rate_ave &&  ($i-1) <  $this->store->rate_ave): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
            <?php endfor; ?>
            <?php if(!$this->can_rate): ?> <span id='mess_rate'></span>
  <?php endif; ?>
  </div>
  </li>
  <li>
  	<div class="store_share_block">
  	  	<?php echo $this->addThis() ?>	
  	</div>
  </li>
  <li> 
  	<div class="store_comment_block">
	 	 <?php echo $this->action("list", "comment", "core", array("type"=>"social_store", "id"=>$this->store->getIdentity())) ?>
  	</div> 	
  </li>
  <?php endif;?>
</ul>
 <script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery("a[rel^='prettyPhoto']").prettyPhoto();
        
    jQuery(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:5000, autoplay_slideshow: true});
    jQuery(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});

    jQuery("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
            custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
            changepicturecallback: function(){ initialize(); }
        });

    jQuery("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
            custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
            changepicturecallback: function(){ _bsap.exec(); }
        });
    });
</script> 

<script type="text/javascript">
    var img_star_full = "application/modules/Socialstore/externals/images/star_full.png";
    var img_star_partial = "application/modules/Socialstore/externals/images/star_part.png";
    var img_star_none = "application/modules/Socialstore/externals/images/star_none.png";  
    var route = '<?php echo $this->route;?>';
    function rating_mousehover(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).src = img_star_full;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
     function canNotRate(status) {
     if(status == 1)
     {
            $('mess_rate').innerHTML = '<?php echo $this->translate("You cannot rate this store!"); ?>';
     }
     else if(status == 2)
     {
            $('mess_rate').innerHTML = '<?php echo $this->translate("You have to log in to rate this store!");?>';
     }
     else
     {
         $('mess_rate').innerHTML = "";
     }
     }
    function rating_mouseout() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->store->rate_ave ?>) {
            $('rate_'+x).src = img_star_full;
          } else if(<?php echo $this->store->rate_ave ?> > (x-1) && x > <?php echo $this->store->rate_ave ?>) {
            $('rate_'+x).src = img_star_partial;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
    function rate(rates){
        $('store_rate').onmouseout = null;
        window.location = en4.core.baseUrl + route + '/store/rate-store/store_id/<?php echo $this->store->getIdentity();?>/rates/'+rates;
      }
    $(window).addEvent('domready', function() {
    	// viewGoogleMap('map_canvas');
    	viewGoogleMapFromAddress('map_canvas');
    });
</script>

 <style type="text/css">
 table.admin_table thead tr th {
    background-color: #E9F4FA;
    border-bottom: 1px solid #AAAAAA;
    font-weight: bold;
    padding: 7px 10px;
    white-space: nowrap;
}
table.admin_table tbody tr td {
    border-bottom: 1px solid #EEEEEE;
    font-size: 0.9em;
    padding: 7px 10px;
    vertical-align: top;
    white-space: normal;
}

</style> 