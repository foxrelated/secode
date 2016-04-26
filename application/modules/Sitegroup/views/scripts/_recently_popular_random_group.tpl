<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _recently_popular_random_group.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.sponsored', 1);
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>
<?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.latitude', 0); ?>
<?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.longitude', 0); ?>
<?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.zoom', 1); ?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postedby', 1); ?>
<script type="text/javascript" >
    function owner(thisobj) {
        var Obj_Url = thisobj.href;
        Smoothbox.open(Obj_Url);
    }
</script>
<script>
    var sitegroups_likes = function (resource_id, resource_type) {
        var content_type = 'sitegroup';
        //var error_msg = '<?php //echo $this->result['0']['like_id'];  ?>';

        // SENDING REQUEST TO AJAX
        var request = createLikegroup(resource_id, resource_type, content_type);

        // RESPONCE FROM AJAX
        request.addEvent('complete', function (responseJSON) {
            if (responseJSON.error_mess == 0) {
                $(resource_id).style.display = 'block';
                if (responseJSON.like_id)
                {
                    $('backgroundcolor_' + resource_id).className = "sitegroup_browse_thumb sitegroup_browse_liked";
                    $('sitegroup_like_' + resource_id).value = responseJSON.like_id;
                    $('sitegroup_most_likes_' + resource_id).style.display = 'none';
                    $('sitegroup_unlikes_' + resource_id).style.display = 'block';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }
                else
                {
                    $('backgroundcolor_' + resource_id).className = "sitegroup_browse_thumb";
                    $('sitegroup_like_' + resource_id).value = 0;
                    $('sitegroup_most_likes_' + resource_id).style.display = 'block';
                    $('sitegroup_unlikes_' + resource_id).style.display = 'none';
                    $('show_like_button_child_' + resource_id).style.display = 'none';
                }

            }
            else {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
                return;
            }
        });
    }
    // FUNCTION FOR CREATING A FEEDBACK
    var createLikegroup = function (resource_id, resource_type, content_type)
    {
        if ($('sitegroup_most_likes_' + resource_id).style.display == 'block')
            $('sitegroup_most_likes_' + resource_id).style.display = 'none';


        if ($('sitegroup_unlikes_' + resource_id).style.display == 'block')
            $('sitegroup_unlikes_' + resource_id).style.display = 'none';
        $(resource_id).style.display = 'none';
        $('show_like_button_child_' + resource_id).style.display = 'block';

        if (content_type == 'sitegroup') {
            var like_id = $(content_type + '_like_' + resource_id).value
        }
        //	var url = '<?php echo $this->url(array('action' => 'global-likes'), 'sitegroup_like', true); ?>';
        var request = new Request.JSON({
            url: '<?php echo $this->url(array('action' => 'global-likes'), 'sitegroup_like', true); ?>',
            data: {
                format: 'json',
                'resource_id': resource_id,
                'resource_type': resource_type,
                'like_id': like_id
            }
        });
        request.send();
        return request;
    }
</script>

<?php
$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
$MODULE_NAME = 'sitegroup';
$RESOURCE_TYPE = 'sitegroup_group';
?>
    <?php if ($this->list_view): ?>
    <div id="rgrid_view_group"  style="display: none;">
        <?php $sitegroup_entry = Zend_Registry::isRegistered('sitegroup_entry') ? Zend_Registry::get('sitegroup_entry') : null; ?>
        <?php if (count($this->sitegroupsitegroup)): ?>
            <?php
            $counter = '1';
            $limit = $this->active_tab_list;
            ?>
            <ul class="seaocore_browse_list">
                <?php foreach ($this->sitegroupsitegroup as $sitegroup): ?>
                    <?php
                    if ($counter > $limit):
                        break;
                    endif;
                    $counter++;
                    ?>
                    <li <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)): ?><?php if ($sitegroup->featured): ?> class="lists_highlight"<?php endif; ?><?php endif; ?>>
                        <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)): ?>
                                <?php if ($sitegroup->featured): ?>
                                <i title="<?php echo $this->translate('Featured') ?>" class="seaocore_list_featured_label"><?php echo $this->translate('Featured') ?></i>
                                <?php endif; ?>
                            <?php endif; ?>
                        <div class='seaocore_browse_list_photo'>
                            <?php if (!empty($sitegroup_entry)) {
                                echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), $this->itemPhoto($sitegroup, 'thumb.normal', '', array('align' => 'left')));
                            } else {
                                exit();
                            } ?>
                            <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)): ?>
                                <?php if (!empty($sitegroup->sponsored)): ?>
                    <?php //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
                    //if (!empty($sponsored)) { 
                    ?>
                                    <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                                            <?php echo $this->translate('SPONSORED'); ?>                 
                                    </div>
                                            <?php //} ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                        </div>
                        <div class='seaocore_browse_list_info'>            
                            <div class='seaocore_browse_list_info_title'>
                                <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.fs.markers', 1)): ?>
                                    <span>
                                        <?php if ($sitegroup->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                <?php endif; ?>
                                <?php if ($sitegroup->featured == 1): ?>
                                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                                <?php endif; ?>
                                    </span>
                            <?php endif; ?>
                                <div class="seaocore_title">
                            <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(), $this->listview_turncation), array('title' => $sitegroup->getTitle())) ?>
                                </div>
                            </div>

                            <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
                                <?php if (($sitegroup->rating > 0)): ?>

                                    <?php
                                    $currentRatingValue = $sitegroup->rating;
                                    $difference = $currentRatingValue - (int) $currentRatingValue;
                                    if ($difference < .5) {
                                        $finalRatingValue = (int) $currentRatingValue;
                                    } else {
                                        $finalRatingValue = (int) $currentRatingValue + .5;
                                    }
                                    ?>

                                    <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                    <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
                                            <span class="rating_star_generic rating_star" ></span>
                                        <?php endfor; ?>
                                        <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
                                            <span class="rating_star_generic rating_star_half" ></span>
                                        <?php endif; ?>
                                    </span>
                                    <?php endif; ?>
            <?php endif; ?>

                            <div class='seaocore_browse_list_info_date'>
                                <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?>
                                <?php if (!empty($this->showpostedBy) && $postedBy): ?>
                                    - <?php echo $this->translate('posted by'); ?>
                                    <?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) ?>
                                <?php endif; ?>
                            </div>

                                <?php if (!empty($this->statistics)) : ?>
                                <div class='seaocore_browse_list_info_date'>
                                    <?php
                                    $statistics = '';

                                    if (@in_array('likeCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) . ', ';
                                    }
                                    if (@in_array('followCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count)) . ', ';
                                    }

                                    if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                                        $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                                        if ($sitegroup->member_title && $memberTitle) {
                                            echo $sitegroup->member_count . ' ' . $sitegroup->member_title . ', ';
                                        } else {
                                            $statistics .= $this->translate(array('%s member', '%s members', $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) . ', ';
                                        }
                                    }

                                    if (!empty($sitegroup->review_count) && @in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                                        $statistics .= $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) . ', ';
                                    }

                                    if (@in_array('commentCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) . ', ';
                                    }

                                    if (@in_array('viewCount', $this->statistics)) {
                                        $statistics .= $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) . ', ';
                                    }
                                    $statistics = trim($statistics);
                                    $statistics = rtrim($statistics, ',');
                                    ?>
                                    <?php echo $statistics; ?>
                                </div>
                                <?php endif; ?>


                            <?php if (!empty($sitegroup->price) && $this->enablePrice): ?>
                                <div class='seaocore_browse_list_info_date'>
                                <?php
                                echo $this->translate("Price: ");
                                echo $this->locale()->toCurrency($sitegroup->price, $currency);
                                ?>
                                </div>
                            <?php endif; ?>						
            <?php
            if (!empty($sitegroup->location) && $this->enableLocation):
                echo "<div class='seaocore_browse_list_info_date'>";
                echo $this->translate("Location: ");
                echo $this->translate($sitegroup->location);
                $location_id = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
                ?><?php if (!empty($this->showgetdirection)) : ?>&nbsp; - <b> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitegroup->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?> </b><?php endif; ?>
                            <?php
                            echo "</div>";
                        endif;
                        ?>

                        </div>
                    </li>
        <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="tip">
                <span>
            <?php echo $this->translate('Nobody has created a group with that criteria.') ?>
                </span>
            </div>
    <?php endif; ?>
    </div>
            <?php endif; ?>
            <?php if ($this->grid_view): ?>
    <div id="rimage_view_group" style="display: none;">
                <?php if (count($this->sitegroupsitegroup)): ?>

                    <?php
                    $counter = 1;
                    $total_sitegroup = count($this->sitegroupsitegroup);
                    $limit = $this->active_tab_image;
                    ?> 
            <div class="sitegroup_img_view o_hidden">
                <div class="sitegroup_img_view_sitegroup">
                    <?php foreach ($this->sitegroupsitegroup as $sitegroup): ?>
            <?php // start like Work on the browse group  ?>
            <?php
            if ($counter > $limit):
                break;
            endif;
            $counter++;
            ?>
                                    <?php
                                    $likeGroup = false;
                                    if (!empty($viewer_id)):
                                        $likeGroup = Engine_Api::_()->sitegroup()->hasGroupLike($sitegroup->group_id, $viewer_id);
                                    endif;
                                    ?>

                        <div class="sitegroup_browse_thumb <?php if ($likeGroup): ?> sitegroup_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitegroup->group_id; ?>" style="width:<?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;" >

                            <div class="sitegroup_browse_thumb_list" <?php if (!empty($viewer_id) && !empty($this->showlikebutton)) : ?> onmouseOver=" $('like_<?php echo $sitegroup->getIdentity(); ?>').style.display = 'block';
                                        if ($('<?php echo $sitegroup->getIdentity(); ?>').style.display == 'none')
                                            $('<?php echo $sitegroup->getIdentity(); ?>').style.display = 'block';"  onmouseout="$('like_<?php echo $sitegroup->getIdentity(); ?>').style.display = 'none';
                                                    $('<?php echo $sitegroup->getIdentity(); ?>').style.display = 'none';" <?php endif; ?> >
                                        <?php // end like Work on the browse group ?>

                                        <?php if (!empty($this->showlikebutton)) : ?>
                                    <a href="javascript:void(0);">
            <?php else : ?>
                                        <a href="<?php echo Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()) ?>">
                                        <?php endif; ?>
                                        <?php
                                        if ($this->photoWidth >= 720):
                                            $photo_type = 'thumb.main';
                                        elseif ($this->photoWidth >= 140):
                                            $photo_type = 'thumb.normal';
                                        elseif ($this->photoWidth >= 100):
                                            $photo_type = 'thumb.icon';
                                        else:
                                            $photo_type = 'thumb.profile';
                                        endif;
                                        $url = $this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/nophoto_group_thumb_profile.png';
                                        $temp_url = $sitegroup->getPhotoUrl($photo_type);
                                        if (!empty($temp_url)): $url = $sitegroup->getPhotoUrl($photo_type);
                                        endif;
                                        ?>
                                        <span style="background-image: url(<?php echo $url; ?>);"> </span>
                                            <?php if (empty($this->showlikebutton)) : ?>
                                            <div class="sitegroup_browse_title">
                                                <p title="<?php echo $sitegroup->getTitle() ?>"><?php echo Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(), $this->turncation); ?></p>
                                            </div>
                                            <?php endif; ?>
                                    </a>

                                            <?php if (!empty($viewer_id)) : ?>
                                        <div id="like_<?php echo $sitegroup->getIdentity() ?>" style="display:none;">
                                                <?php $RESOURCE_ID = $sitegroup->getIdentity(); ?>
                                            <div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
                <?php
                // Check that for this 'resurce type' & 'resource id' user liked or not.
                $check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability($RESOURCE_TYPE, $RESOURCE_ID);
                if (!empty($check_availability)) {
                    $label = 'Unlike this';
                    $unlike_show = "display:block;";
                    $like_show = "display:none;";
                    $like_id = $check_availability[0]['like_id'];
                } else {
                    $label = 'Like this';
                    $unlike_show = "display:none;";
                    $like_show = "display:block;";
                    $like_id = 0;
                }
                ?>
                                                <div class="sitegroup_browse_thumb_hover_color">
                                                </div>
                                                <div class="seaocore_like_button sitegroup_browse_thumb_hover_unlike_button" id="sitegroup_unlikes_<?php echo $RESOURCE_ID; ?>" style='<?php echo $unlike_show; ?>' >
                                                    <a href = "javascript:void(0);" onclick = "sitegroups_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
                                                        <i class="seaocore_like_thumbdown_icon"></i>
                                                        <span><?php echo $this->translate('Unlike') ?></span>
                                                    </a>
                                                </div>
                                                <div class="seaocore_like_button sitegroup_browse_thumb_hover_like_button" id="sitegroup_most_likes_<?php echo $RESOURCE_ID; ?>" style='<?php echo $like_show; ?>'>
                                                    <a href = "javascript:void(0);" onclick = "sitegroups_likes('<?php echo $RESOURCE_ID; ?>', 'sitegroup_group');">
                                                        <i class="seaocore_like_thumbup_icon"></i>
                                                        <span><?php echo $this->translate('Like') ?></span>
                                                    </a>
                                                </div>
                                                <input type ="hidden" id = "sitegroup_like_<?php echo $RESOURCE_ID; ?>" value = '<?php echo $like_id; ?>' />
                                            </div>
                                        </div>
                                        <div id="show_like_button_child_<?php echo $RESOURCE_ID; ?>" style="display:none;" >
                                            <div class="sitegroup_browse_thumb_hover_color"></div>
                                            <div class="sitegroup_browse_thumb_hover_loader">
                                                <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" class="mtop5" />
                                            </div>
                                        </div>
                            <?php endif; ?>
                            <?php // end like Work on the browse group ?>

                                <?php if ($sitegroup->featured == 1 && !empty($this->showfeaturedLable)): ?>
                                        <span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured') ?>"></span>
                            <?php endif; ?>

            <?php if (!empty($this->showlikebutton)): ?>
                                        <div class="sitegroup_browse_title">
                                    <?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($sitegroup->group_id, $sitegroup->owner_id, $sitegroup->getSlug()), Engine_Api::_()->sitegroup()->truncation($sitegroup->getTitle(), $this->turncation), array('title' => $sitegroup->getTitle())); ?>
                                        </div>
                                <?php endif; ?>

                            </div>

                                <?php if (!empty($sitegroup->sponsored) && !empty($this->showsponsoredLable)): ?>
                                    <?php //$sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.image', 1);
                                    //if (!empty($sponsored)) { 
                                    ?>
                                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.color', '#fc0505'); ?>;'>
                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
                                    <?php //} ?>
                                    <?php endif; ?>

                            <div class="sitegroup_browse_thumb_info">

                                    <?php if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) : ?>
                                        <?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                                        if ($sitegroup->member_title && $memberTitle) :
                                            ?>
                                        <div class="member_count">
                                            <?php echo $sitegroup->member_count . ' ' . ucfirst($sitegroup->member_title); ?>
                                        </div>
                                        <?php else : ?>
                                        <div class="member_count">
                                            <?php echo $this->translate(array('%s ' . ucfirst('member'), '%s ' . ucfirst('members'), $sitegroup->member_count), $this->locale()->toNumber($sitegroup->member_count)) ?>
                                        </div> 
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($this->statistics)) : ?>         
                                    <div class='sitegroup_browse_thumb_stats seaocore_txt_light'>
                                        <?php
                                        $statistics = '';
                                        if (@in_array('likeCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) . ', ';
                                        }

                                        if (@in_array('followCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s follower', '%s followers', $sitegroup->follow_count), $this->locale()->toNumber($sitegroup->follow_count)) . ', ';
                                        }

                                        if (@in_array('commentCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) . ', ';
                                        }
                                        if (@in_array('viewCount', $this->statistics)) {
                                            $statistics .= $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) . ', ';
                                        }
                                        $statistics = trim($statistics);
                                        $statistics = rtrim($statistics, ',');
                                        ?>
                                        <?php echo $statistics; ?>
                                    </div>
            <?php endif; ?>
                                        <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
                                    <div class='sitegroup_browse_thumb_stats seaocore_txt_light'>
                                            <?php if ($sitegroup->review_count) : ?>
                                                <?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <?php endif; ?>
                                            <?php if (($sitegroup->rating > 0)): ?>

                                            <?php
                                            $currentRatingValue = $sitegroup->rating;
                                            $difference = $currentRatingValue - (int) $currentRatingValue;
                                            if ($difference < .5) {
                                                $finalRatingValue = (int) $currentRatingValue;
                                            } else {
                                                $finalRatingValue = (int) $currentRatingValue + .5;
                                            }
                                            ?>

                                            <span class="clr" title="<?php echo $finalRatingValue . $this->translate(' rating'); ?>">
                                            <?php for ($x = 1; $x <= $sitegroup->rating; $x++): ?>
                                                    <span class="rating_star_generic rating_star" ></span>
                                        <?php endfor; ?>
                                        <?php if ((round($sitegroup->rating) - $sitegroup->rating) > 0): ?>
                                                    <span class="rating_star_generic rating_star_half" ></span>
                                            <?php endif; ?>
                                            </span>
                                    <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->showpostedBy) && $postedBy): ?>
                                    <div class='seaocore_browse_list_info_date'>
                                    <?php echo $this->translate('posted by'); ?>
                                    <?php echo $this->htmlLink($sitegroup->getOwner()->getHref(), $sitegroup->getOwner()->getTitle()) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($this->showdate)) : ?>
                                    <div class='seaocore_browse_list_info_date'>
                            <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?>
                                    </div>
            <?php endif; ?>
                <?php if (!empty($this->showprice) && !empty($sitegroup->price) && $this->enablePrice): ?>
                                    <div class='seaocore_browse_list_info_date'>
                <?php echo $this->translate("Price: ");
                echo $this->locale()->toCurrency($sitegroup->price, $currency); ?>
                                    </div>
            <?php endif; ?>
                <?php
                if (!empty($sitegroup->location) && $this->enableLocation && !empty($this->showlocation)):
                    echo "<div class='seaocore_browse_list_info_date'>";
                    echo $this->translate("Location: ");
                    echo $this->translate($sitegroup->location);
                    $location_id = Engine_Api::_()->getDbTable('locations', 'sitegroup')->getLocationId($sitegroup->group_id, $sitegroup->location);
                    ?><?php if (!empty($this->showgetdirection)) : ?>&nbsp; - <b> <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitegroup->group_id, 'resouce_type' => 'sitegroup_group', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?> </b><?php endif; ?>
                        <?php
                        echo "</div>";
                    endif;
                    ?>
                            </div>
                        </div>
            <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="tip">
                <span>
        <?php echo $this->translate('Nobody has created a group with that criteria.') ?>
                </span>
            </div>
    <?php endif; ?>
    </div>
<?php endif; ?>
<div id="rmap_canvas_view_group" style="display: none;">
    <div class="seaocore_map clr" style="overflow:hidden;">
        <div id="rmap_canvas_group"> </div>
<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
<?php if (!empty($siteTitle)) : ?>
            <div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
<?php endif; ?>
    </div>	

<?php if ($this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
        <a href="javascript:void(0);" onclick="rtoggleBounceGroup()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
        <br />
<?php endif; ?>
</div>

<?php if ($this->enableLocation && $this->map_view): ?>
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
    ?>

    <script type="text/javascript">
    // arrays to hold copies of the markers and html used by the side_bar
    // because the function closure trick doesnt work there
        var rgmarkersGroup = [];

    // global "map" variable
        var rmap_group = null;
    // A function to create the marker and set up the event window function
        function rcreateMarkerGroup(latlng, name, html, title_group) {
            var contentString = html;
            if (name == 0)
            {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_group,
                    title: title_group,
                    animation: google.maps.Animation.DROP,
                    zIndex: Math.round(latlng.lat() * -100000) << 5
                });
            }
            else {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: rmap_group,
                    title: title_group,
                    draggable: false,
                    animation: google.maps.Animation.BOUNCE
                });
            }
            rgmarkersGroup.push(marker);
            google.maps.event.addListener(marker, 'click', function () {
                infowindow.setContent(contentString);
                google.maps.event.trigger(rmap_group, 'resize');

                infowindow.open(rmap_group, marker);

            });
        }

        function rinitializeGroup() {
    // create the map
            var myOptions = {
                zoom: <?php echo $defaultZoom; ?>,
                center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
                navigationControl: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }

            rmap_group = new google.maps.Map(document.getElementById("rmap_canvas_group"),
                    myOptions);

            $$("li.tab_<?php echo $this->identity ?>").addEvent('click', function () {
                google.maps.event.trigger(rmap_group, 'resize');
                rmap_group.setZoom(<?php echo $defaultZoom ?>);
                rmap_group.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>));
            });

            google.maps.event.addListener(rmap_group, 'click', function () {
                infowindow.close();
                google.maps.event.trigger(rmap_group, 'resize');

            });
    <?php $textPostedBy = ''; ?>
    <?php foreach ($this->locations as $location) : ?>
        <?php if ($postedBy): ?>
            <?php $textPostedBy = $this->string()->escapeJavascript($this->translate('posted by')); ?>
            <?php $textPostedBy.= " " . $this->htmlLink($this->sitegroup[$location->group_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->sitegroup[$location->group_id]->getOwner()->getTitle())) ?>
        <?php endif; ?>
        // obtain the attribues of each marker
                var lat = <?php echo $location->latitude ?>;
                var lng =<?php echo $location->longitude ?>;
                var point = new google.maps.LatLng(lat, lng);
        <?php if (!empty($enableBouce)): ?>
                    var sponsored = <?php echo $this->sitegroup[$location->group_id]->sponsored ?>
        <?php else: ?>
                    var sponsored = 0;
        <?php endif; ?>
        // create the marker
        <?php $group_id = $this->sitegroup[$location->group_id]->group_id; ?>
                var contentString = '<div id="content">' +
                        '<div id="siteNotice">' +
                        '</div>' + '  <ul class="sitegroups_locationdetails"><li>' +
                        '<div class="sitegroups_locationdetails_info_title">' +
                        '<a href="<?php echo $this->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view', true) ?>">' + "<?php echo $this->string()->escapeJavascript($this->sitegroup[$location->group_id]->getTitle()); ?>" + '</a>' +
                        '<div class="firght">' +
                        '<span >' +
        <?php if ($this->sitegroup[$location->group_id]->featured == 1): ?>
                    '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>' + <?php endif; ?>
                '</span>' +
                        '<span>' +
        <?php if ($this->sitegroup[$location->group_id]->sponsored == 1): ?>
                    '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>' +
        <?php endif; ?>
                '</span>' +
                        '</div>' +
                        '<div class="clr"></div>' +
                        '</div>' +
                        '<div class="sitegroups_locationdetails_photo" >' +
                        '<?php echo $this->htmlLink(Engine_Api::_()->sitegroup()->getHref($this->sitegroup[$location->group_id]->group_id, $this->sitegroup[$location->group_id]->owner_id, $this->sitegroup[$location->group_id]->getSlug()), $this->itemPhoto($this->sitegroup[$location->group_id], 'thumb.normal')) ?>' +
                        '</div>' +
                        '<div class="sitegroups_locationdetails_info">' +
        <?php if (@in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
            <?php if (($this->sitegroup[$location->group_id]->rating > 0)): ?>
                        '<span class="clr">' +
                <?php for ($x = 1; $x <= $this->sitegroup[$location->group_id]->rating; $x++): ?>
                            '<span class="rating_star_generic rating_star"></span>' +
                <?php endfor; ?>
                <?php if ((round($this->sitegroup[$location->group_id]->rating) - $this->sitegroup[$location->group_id]->rating) > 0): ?>
                            '<span class="rating_star_generic rating_star_half"></span>' +
                <?php endif; ?>
                        '</span>' +
            <?php endif; ?>
        <?php endif; ?>

                '<div class="sitegroups_locationdetails_info_date">' +
                        '<?php echo $this->timestamp(strtotime($this->sitegroup[$location->group_id]->creation_date)) ?>' + ' - <?php echo $textPostedBy ?>' +
                        '</div>' +
        <?php if (!empty($this->statistics)) : ?>
                    '<div class="sitegroups_locationdetails_info_date">' +
            <?php
            $statistics = '';
            if (@in_array('likeCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitegroup[$location->group_id]->like_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->like_count))) . ', ';
            }

            if (@in_array('followCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s follower', '%s followers', $this->sitegroup[$location->group_id]->follow_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->follow_count))) . ', ';
            }

            if (@in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupmember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.member.title', 1);
                if ($this->sitegroup[$location->group_id]->member_title && $memberTitle) {
                    $statistics .= $this->sitegroup[$location->group_id]->member_count . ' ' . $this->string()->escapeJavascript($this->sitegroup[$location->group_id]->member_title) . ', ';
                } else {
                    $statistics .= $this->string()->escapeJavascript($this->translate(array('%s member', '%s members', $this->sitegroup[$location->group_id]->member_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->member_count))) . ', ';
                }
            }

            if (@in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitegroup[$location->group_id]->review_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->review_count))) . ', ';
            }
            if (@in_array('commentCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitegroup[$location->group_id]->comment_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->comment_count))) . ', ';
            }
            if (@in_array('viewCount', $this->statistics)) {
                $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitegroup[$location->group_id]->view_count), $this->locale()->toNumber($this->sitegroup[$location->group_id]->view_count))) . ', ';
            }
            $statistics = trim($statistics);
            $statistics = rtrim($statistics, ',');
            ?>
                    '<?php echo $statistics; ?>' +
                            '</div>' +
        <?php endif; ?>
                '<div class="sitegroups_locationdetails_info_date">' +
        <?php if (!empty($this->sitegroup[$location->group_id]->phone)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitegroup[$location->group_id]->phone ?><br />" +
        <?php endif; ?>
        <?php if (!empty($this->sitegroup[$location->group_id]->email)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitegroup[$location->group_id]->email ?><br />" +
        <?php endif; ?>
        <?php if (!empty($this->sitegroup[$location->group_id]->website)): ?>
                    "<?php echo $this->string()->escapeJavascript($this->translate("Website: ")) . $this->sitegroup[$location->group_id]->website ?>" +
        <?php endif; ?>
                '</div>' +
        <?php if ($this->sitegroup[$location->group_id]->price && $this->enablePrice): ?>
                    '<div class="sitegroups_locationdetails_info_date">' +
                            "<?php echo $this->string()->escapeJavascript($this->translate("Price: "));
            echo $this->locale()->toCurrency($this->sitegroup[$location->group_id]->price, $currency) ?>" +
                            '</div>' +
        <?php endif; ?>
                '<div class="sitegroups_locationdetails_info_date">' +
                        "<?php echo $this->translate("Location: ");
        echo $this->string()->escapeJavascript($location->location); ?>" +
                        '</div>' +
                        '</div>' +
                        '<div class="clr"></div>' +
                        ' </li></ul>' +
                        '</div>';

                var marker = rcreateMarkerGroup(point, sponsored, contentString, "<?php echo str_replace('"', ' ', $this->sitegroup[$location->group_id]->getTitle()); ?>");
    <?php endforeach; ?>

        }

        var infowindow = new google.maps.InfoWindow(
                {
                    size: new google.maps.Size(250, 50)
                });

        function rtoggleBounceGroup() {
            for (var i = 0; i < rgmarkersGroup.length; i++) {
                if (rgmarkersGroup[i].getAnimation() != null) {
                    rgmarkersGroup[i].setAnimation(null);
                }
            }
        }
    //]]>
    </script>
<?php endif; ?>
