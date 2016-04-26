<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.locationfield', 1); ?>
<?php if ($this->detactLocation): ?>
  <?php
//GET API KEY
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
  ?>
  <div id="sitegroup_location_map_none" style="display: none;"></div>
<?php endif; ?>
<?php if ($this->totalCount && !$this->noMoreContent): ?>
  <?php
  $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js')
          ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js');
  ?>

  <?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_board.css'); ?>

  <?php $enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.price.field', 1); ?>

  <?php if ($this->autoload): ?>
    <div id="pinboard_<?php echo $this->identity ?>">
      <?php if(isset ($this->params['defaultLoadingImage']) && $this->params['defaultLoadingImage']): ?>
        <div class="seaocore_loading_image"></div>
      <?php endif; ?>    
    </div>
    <script type="text/javascript">
      en4.core.runonce.add(function(){
        var locationsParams={}; 
        var setPinboardLayout = function(){
          var layoutColumn='middle_group_browse';
          PinBoardSeaoColumn.push(layoutColumn);
          PinBoardSeaoObject[layoutColumn] = new PinBoardSeao(layoutColumn);
          PinBoardSeaoObject[layoutColumn].add({
            contentId:'pinboard_<?php echo $this->identity ?>',
            widgetId:'<?php echo $this->identity ?>',
            totalCount:'<?php echo $this->totalCount ?>',
            requestParams :$merge(<?php echo json_encode($this->params) ?> ,locationsParams),
            responseContainerClass :'layout_sitegroup_pinboard_browse'
          });
          PinBoardSeaoObject[layoutColumn].start();
        };
        if (<?php echo $this->detactLocation ?> && navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position){
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
                                                                            			
            mapGetDirection = new google.maps.Map(document.getElementById("sitegroup_location_map_none"),  {
              zoom: 8 ,
              center: new google.maps.LatLng(lat,lng),
              navigationControl: true,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            });
                                                                                
            if(!position.address) {
              var service = new google.maps.places.PlacesService(mapGetDirection);
              var request = {
                location: new google.maps.LatLng(lat,lng), 
                radius: 500
              };
                                                                                      
              service.search(request, function(results, status) { 
                if (status  ==  'OK') {
                  var index = 0;
                  var radian = 3.141592653589793/ 180;
                  var my_distance = 1000; 
                  for (var i = 0; i < results.length; i++){
                    var R = 6371; // km
                    var lat2 = results[i].geometry.location.lat();
                    var lon2 = results[i].geometry.location.lng(); 
                    var dLat = (lat2-lat) * radian;
                    var dLon = (lon2-lng) * radian;
                    var lat1 = lat * radian;
                    var lat2 = lat2 * radian;

                    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                    var d = R * c;
                    if(d < my_distance) {
                      index = i;
                      my_distance = d;
                    }
                  }   
                                                                      

                                      
                  locationsParams.sitegroup_location = (results[index].vicinity) ? results[index].vicinity :'';
                  locationsParams.Latitude = lat;
                  locationsParams.Longitude = lng;
                  locationsParams.locationmiles = <?php echo $this->defaultlocationmiles ?>;

                  setLocationsParams();
                  setPinboardLayout();
                                                                                        
                } 
              });
            } else {
              var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';
              var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';

              locationsParams.sitegroup_location = location;
              locationsParams.Latitude = lat;
              locationsParams.Longitude = lng;
              locationsParams.locationmiles = <?php echo $this->defaultlocationmiles ?>;
              setLocationsParams();
              //form submit by ajax
              setPinboardLayout();
            }
          },function(){
           setPinboardLayout();
          },{
            maximumAge:6000,
            timeout:3000
          });
          var setLocationsParams =  function(){
            if(!document.getElementById('sitegroup_location'))
              return;
            document.getElementById('sitegroup_location').value = locationsParams.sitegroup_location;
            if(document.getElementById('Latitude'))
              document.getElementById('Latitude').value = locationsParams.Latitude;
            if(document.getElementById('Longitude'))
              document.getElementById('Longitude').value = locationsParams.Longitude;
            if(document.getElementById('locationmiles'))
              document.getElementById('locationmiles').value = locationsParams.locationmiles;
          }              
        } else {
          setPinboardLayout();     
        }
                                         
      });
    </script>
  <?php else: ?>
    <script type="text/javascript">
      var layoutColumn='middle_group_browse';
    <?php if ($this->currentgroup < 2): ?>
        PinBoardSeaoObject[layoutColumn].widgets[0].totalCount='<?php echo $this->totalCount ?>';
    <?php endif; ?>
    <?php if ($this->currentgroup == $this->params['noOfTimes']): ?>
        PinBoardSeaoObject[layoutColumn].currentIndex++
    <?php endif; ?>
    </script>
    <?php $countButton = count($this->show_buttons); ?>
    <?php foreach ($this->paginator as $group): ?>

      <?php
      $noOfButtons = $countButton;
      if ($this->show_buttons):
        $alllowComment = (in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $group->authorization()->isAllowed($this->viewer(), "comment");
        if (in_array('comment', $this->show_buttons) && !$alllowComment) {
          $noOfButtons--;
        }
        if (in_array('like', $this->show_buttons) && !$alllowComment) {
          $noOfButtons--;
        }

      endif;
      ?>
      <div class="seaocore_list_wrapper" style="width:<?php echo $this->params['itemWidth'] ?>px;">
        <div class="seaocore_board_list b_medium" style="width:<?php echo $this->params['itemWidth'] - 18 ?>px;">
          <div>
            <?php if (!empty($this->showfeaturedLable)): ?>
							<?php if ($group->featured): ?>
								<i class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
							<?php endif; ?>
            <?php endif; ?>
            <div class="seaocore_board_list_thumb">
              <a href="<?php echo $group->getHref() ?>" class="seaocore_thumb">
                <table>
                  <tr valign="middle">
                    <td>
                      <?php
                      $options = array('align' => 'center');

                      if (isset($this->params['withoutStretch']) && $this->params['withoutStretch']):
                        $options['style'] = 'width:auto; max-width:' . ($this->params['itemWidth'] - 18) . 'px;';
                      endif;
                      ?>  
                      <?php echo $this->itemPhoto($group, ($this->params['itemWidth'] > 200) ? 'thumb.main' : 'thumb.profile', '', $options); ?>
                      

                    </td> 
                  </tr> 
                </table>
              </a>
            </div>
            
            <div class="seaocore_board_list_btm">
              <?php if ($this->postedby): ?>
                <?php echo $this->htmlLink($group->getOwner()->getHref(), $this->itemPhoto($group->getOwner(), 'thumb.icon', '', array())) ?>
              <?php endif; ?>  
              <div class="o_hidden seaocore_stats seaocore_txt_light">
                <?php if ($this->postedby): ?>
                  <b><?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?></b><br />
                <?php endif; ?>
                <?php if ($group->category_id): ?>  
                  <?php echo $this->translate("in %s", $this->htmlLink($group->getCategory()->getHref(), $this->translate($group->getCategory()->getTitle(true)))) ?> - <?php endif; ?> 
                <?php echo $this->timestamp(strtotime($group->creation_date)) ?>
              </div>
            </div>
            
            <?php if (!empty($this->showsponsoredLable)): ?>
            <?php if (!empty($group->sponsored)): ?>
              <div class="seaocore_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#FC0505'); ?>">
                <?php echo $this->translate('SPONSORED'); ?>                 
              </div>
            <?php endif; ?>
            <?php endif; ?>
            
            <div class="seaocore_board_list_cont">
              <div class="seaocore_title">
                <?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
              </div>

              <?php if ($this->truncationDescription): ?>
                <div class="seaocore_description">
                  <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($group->getDescription(), $this->truncationDescription) ?>
                </div>  
              <?php endif; ?>

              <?php if (in_array('price', $this->showOptions) && $group->price && $enablePrice): ?>
                <div class="seaocore_stats seaocore_txt_light mtop5">
                  <span><?php echo $this->translate('Price:'); ?></span>
                  <span><?php echo $this->locale()->toCurrency($group->price, Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD')) ?></span>
                </div>
              <?php endif; ?>
              <?php if (in_array('location', $this->showOptions) && $group->location && $enableLocation): ?>
                <div class="seaocore_stats seaocore_txt_light mtop5">
                  <span><?php echo $this->translate('Location:'); ?></span>
                  <span><?php echo $group->location ?>&nbsp; - 
                    <b>
                      <?php $location_id = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($group->group_id, $group->location);
                      echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $group->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('class' => 'smoothbox')); ?>
                    </b>
                  </span>
                </div>
              <?php endif; ?>
              <?php if (in_array('viewCount', $this->showOptions) || in_array('likeCount', $this->showOptions) || in_array('commentCount', $this->showOptions) || in_array('followCount', $this->showOptions) || (in_array('commentCount', $this->showOptions) && $this->membersEnabled)): ?>
                <div class="seaocore_stats seaocore_txt_light">
                  <?php
                  if (in_array('viewCount', $this->showOptions)) {
                    echo $this->translate(array('%s view', '%s views', $group->view_count), $this->locale()->toNumber($group->view_count)) . '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }

                  if (in_array('likeCount', $this->showOptions)) {
                    echo '<span class="pin_like_st_' . $group->getGuid() . '">' . $this->translate(array('%s like', '%s likes', $group->like_count), $this->locale()->toNumber($group->like_count)) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                  }

                  if (in_array('followCount', $this->showOptions)) {
                    echo '<span id="pin_followt_st_' . $group->getGuid() . '_' . $this->identity . '">' . $this->translate(array('%s follower', '%s followers', $group->follow_count), $this->locale()->toNumber($group->follow_count)) . '</span>&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
                                    
                  
                  if (in_array('commentCount', $this->showOptions)) {
                    echo '<span id="pin_comment_st_' . $group->getGuid() . '_' . $this->identity . '">' . $this->translate(array('%s comment', '%s comments', $group->comment_count), $this->locale()->toNumber($group->comment_count)) . '</span>'. '&nbsp;&nbsp;&nbsp;&nbsp;';
                  }
									if(in_array('memberCount', $this->showOptions) && $this->membersEnabled){
										$memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'groupmember.member.title' , 1);
										if ($group->member_title && $memberTitle) :

												echo $group->member_count . ' ' .  $group->member_title;  

										else :
											echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count));
										endif; 
									}
                  ?>
                  <?php //echo $statistics; ?> 
                </div>     
              <?php endif; ?>
              <?php if (!empty($this->showOptions) && in_array('reviewsRatings', $this->showOptions) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview') && $group->rating > 0): ?>
                <?php
                  $currentRatingValue = $group->rating;
                  $difference = $currentRatingValue- (int)$currentRatingValue;
                  if($difference < .5) {
                    $finalRatingValue = (int)$currentRatingValue;
                  }
                  else {
                    $finalRatingValue = (int)$currentRatingValue + .5;
                  }
                ?>

                <div class='seaocore_browse_list_info_date'>          
                  <?php 
                  if (in_array('reviewsRatings', $this->showOptions) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview')) {
                    echo '<span id="pin_review_st_' . $group->getGuid() . '_' . $this->identity . '">' . $this->translate(array('%s review', '%s reviews', $group->review_count), $this->locale()->toNumber($group->review_count)) . '</span>&nbsp;&nbsp;';
                  }
                  ?>
                  <span class="sitegroup_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
                    <span class="clr">
                      <?php for ($x = 1; $x <= $group->rating; $x++): ?>
                      <span class="rating_star_generic rating_star" ></span>
                      <?php endfor; ?>
                      <?php if ((round($group->rating) - $group->rating) > 0): ?>
                      <span class="rating_star_generic rating_star_half" ></span>
                      <?php endif; ?>
                    </span>
                  </span>
                </div>
           
              <?php endif; ?>
              
            </div>
            
            <?php if($this->commentSection):?>    
              <div class="seaocore_board_list_comments o_hidden">
                <?php echo $this->action("list", "pin-board-comment", "seaocore", array("type" => $group->getType(), "id" => $group->getIdentity(), 'widget_id' => $this->identity)); ?>
              </div>
            <?php endif; ?>    
            <?php if (!empty($this->show_buttons)): ?>
              <div class="seaocore_board_list_action_links">
                <?php $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $group->getHref()); ?>

                <?php if ((in_array('comment', $this->show_buttons) || in_array('like', $this->show_buttons)) && $alllowComment): ?>
                  <?php if (in_array('comment', $this->show_buttons)): ?>
                    <a href='javascript:void(0);' onclick="en4.seaocorepinboard.comments.addComment('<?php echo $group->getGuid() . "_" . $this->identity ?>')" class="seaocore_board_icon comment_icon" title="Comment"><!--<?php echo $this->translate('Comment'); ?>--></a> 
                  <?php endif; ?>
                  <?php if (in_array('like', $this->show_buttons)): ?>
                    <a href="javascript:void(0)" title="Like" class="seaocore_board_icon like_icon <?php echo $group->getGuid() ?>like_link" id="<?php echo $group->getType() ?>_<?php echo $group->getIdentity() ?>like_link" <?php if ($group->likes()->isLike($this->viewer())): ?>style="display: none;" <?php endif; ?>onclick="en4.seaocorepinboard.likes.like('<?php echo $group->getType() ?>', '<?php echo $group->getIdentity() ?>');" ><!--<?php echo $this->translate('Like'); ?>--></a>

                    <a  href="javascript:void(0)" title="Unlike" class="seaocore_board_icon unlike_icon <?php echo $group->getGuid() ?>unlike_link" id="<?php echo $group->getType() ?>_<?php echo $group->getIdentity() ?>unlike_link" <?php if (!$group->likes()->isLike($this->viewer())): ?>style="display:none;" <?php endif; ?> onclick="en4.seaocorepinboard.likes.unlike('<?php echo $group->getType() ?>', '<?php echo $group->getIdentity() ?>');"><!--<?php echo $this->translate('Unlike'); ?>--></a> 
                  <?php endif; ?>
                <?php endif; ?>

                <?php if (in_array('share', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'route' => 'default', 'type' => $group->getType(), 'id' => $group->getIdentity(), 'not_parent_refresh' => '1', 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon share_icon' , 'title' => 'Share')); ?>
                <?php endif; ?>

                <?php if (in_array('facebook', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink('http://www.facebook.com/share.php?u=' . $urlencode . '&t=' . $group->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon fb_icon' , 'title' => 'Facebook')) ?>
                <?php endif; ?>

                <?php if (in_array('twitter', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink('http://twitter.com/share?url=' . $urlencode . '&text=' . $group->getTitle(), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon tt_icon' , 'title' => 'Twitter')) ?> 
                <?php endif; ?>

                <?php if (in_array('pinit', $this->show_buttons)): ?>
                  <a href="http://pinterest.com/pin/create/button/?url=<?php echo $urlencode; ?>&media=<?php echo urlencode((!preg_match("~^(?:f|ht)tps?://~i", $group->getPhotoUrl('thumb.profile')) ? (((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] ) : '') . $group->getPhotoUrl('thumb.profile')); ?>&description=<?php echo $group->getTitle(); ?>"  class="pb_ch_wd seaocore_board_icon pin_icon" title="Pin It" ><!--<?php echo $this->translate('Pin It') ?>--></a>
                <?php endif; ?>

                <?php if (in_array('tellAFriend', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('action' => 'tell-a-friend', 'route' => 'sitegroup_profilegroup', 'id' => $group->getIdentity()), $this->translate(''), array('class' => 'smoothbox seaocore_board_icon taf_icon' , 'title' => 'Tell a Friend')); ?>
                <?php endif; ?>

                <?php if (in_array('print', $this->show_buttons)): ?>
                  <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sitegroup_profilegroup', 'type' => $group->getType(), 'group_id' => $group->getIdentity()), $this->translate(''), array('class' => 'pb_ch_wd seaocore_board_icon print_icon' , 'title' => 'Print')); ?> 
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
<?php else: ?>
  <?php if ($this->is_ajax_load && $this->detactLocation): ?>
    <script type="text/javascript">
      var layoutColumn='middle_group_browse';
      PinBoardSeaoObject[layoutColumn].currentIndex++;
    </script>
  <?php endif; ?>
  <?php if ($this->currentgroup < 2): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No Groups have been created yet.'); ?>
      </span>
    </div>
  <?php endif; ?>
<?php endif; ?>