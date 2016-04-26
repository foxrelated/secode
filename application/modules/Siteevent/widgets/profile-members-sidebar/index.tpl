<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php if ($this->loaded_by_ajax): ?>
    <?php $this->params['content_id']=$this->identity;?>
    <script type="text/javascript">
      window.addEvent('domready',function(){
         en4.siteevent.ajaxTab.sendReq({
            loading:true,
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_profile_members_sidebar')
        });
        });
    </script>
<?php else: ?>
<?php if(!$this->isajax):?>
<ul class="siteevent_side_widget siteevent_sidebar_guests_block" id="siteevent_sidebar_guests_block">
   
  <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?> 
    <li class="txt_center"> 
      <select onchange="occurrence_id = this.value;
                getOccurrenceMembers()" id='date_filter_occurrence'>
                <?php
                $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                foreach ($filter_dates as $key => $date):
                  ?> 
          <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
        <?php endforeach;
        ?>
      </select>
      
    </li>
  
    <li id='show_loading_guests' style='display:none;' >
      <center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" /></center>
    </li>
    
    <?php endif; ?>
  <?php endif;?> 
    <?php  
           if(is_numeric($this->occurrence_id))
             $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->subject()->getIdentity(), 'DESC', $this->occurrence_id); ?>
    <?php 
    if(count($this->members) > 0) {
      //FIRST CASE: ATTENDING.
      if (isset($this->members['2']) && !empty($this->members['2'])) {
          ?>
          <li class="clr o_hidden">
              <div class="siteevent_sidebar_guests_block_head o_hidden mbot10">
                  <?php
                  //$totalAttending = $this->event->membership()->getMemberCount(true, array('rsvp' => 2));
                  echo '<span class="fleft bold f_small"><a href="javascript:void(0);" onclick="showGuestList(2);">' . $this->translate('Confirmed') . ' (' . $this->members['2'][1] . ')</a></span>';
                  if ($this->show_seeall)
                      echo '<span class="fright bold f_small"><a href="javascript:void(0);" onclick="showGuestList(2);">' . $this->translate('See All') . '</a></span>';
                  ?>
              </div>  
              <?php
              //$container = 1;
              foreach ($this->members['2'][0] as $member) :
                  $member = Engine_Api::_()->getItem('user', $member['user_id']);
                  ?>  

                  <div class="member_thumb">
                      <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon', '', array('align' => 'left')), array('class' => 'seao_common_add_tooltip_link', 'rel' => "user $member->user_id")) ?>            
                  </div>
                  <?php
                  // $container++ ;
              endforeach;
              ?>
          </li>
      <?php } ?>

      <?php
      //CASE SECOND: MAYBE ATTENDING.
      if (isset($this->members['1']) && !empty($this->members['1'])) {
          ?>
          <li class="clr o_hidden">
              <div class="siteevent_sidebar_guests_block_head o_hidden mbot10">
                  <?php
                  //$totalAttending = $this->event->membership()->getMemberCount(true, array('rsvp' => 1));
                  echo '<span class="fleft bold f_small"><a href="javascript:void(0);" onclick="showGuestList(1);">' . $this->translate('Maybe') . ' (' . $this->members['1'][1] . ')</a></span>';
                  if ($this->show_seeall)
                      echo '<span class="fright bold f_small"><a href="javascript:void(0);" onclick="showGuestList(1);">' . $this->translate('See All') . '</a></span>';
                  ?>
              </div>
              <?php
              //$container = 1;
              foreach ($this->members['1'][0] as $member) :
                  $member = Engine_Api::_()->getItem('user', $member['user_id']);
                  ?>
                  <div class="member_thumb">
                      <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon', '', array('align' => 'left')), array('class' => 'seao_common_add_tooltip_link', 'rel' => "user $member->user_id")) ?>            
                  </div>
                  <?php
                  // $container++ ;
              endforeach;
              ?>
          </li>
      <?php } ?>

      <?php
      //CASE THIRD: Not ATTENDING.
      if (isset($this->members['0']) && !empty($this->members['0'])) {
          ?>
          <li class="clr o_hidden">
              <div class="siteevent_sidebar_guests_block_head o_hidden mbot10">
                  <?php
                  //$totalAttending = $this->event->membership()->getMemberCount(true, array('rsvp' => 0));
                  echo '<span class="fleft bold f_small"><a href="javascript:void(0);" onclick="showGuestList(0);">' . $this->translate('Not Attending') . ' (' . $this->members['0'][1] . ')</a></span>';
                  if ($this->show_seeall)
                      echo '<span class="fright bold f_small"><a href="javascript:void(0);" onclick="showGuestList(0);">' . $this->translate('See All') . '</a></span>';
                  ?>
              </div>
              <?php
              // $container = 1;
              foreach ($this->members['0'][0] as $member) :
                  $member = Engine_Api::_()->getItem('user', $member['user_id']);
                  ?>
                  <div class="member_thumb">
                      <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon', '', array('align' => 'left')), array('class' => 'seao_common_add_tooltip_link', 'rel' => "user $member->user_id")) ?>            
                  </div>
              <?php endforeach; ?>
          </li>
      <?php } ?> 
      <?php }  
      
      else {  
        
         echo '<li class="f_small o_hidden">';
         if (strtotime($endDate) >= time() || !isset($endDate))
            echo $this->translate('No guest has joined this occurrence yet.') ;
        else
            echo $this->translate('This occurrence had no guests.') ;
         echo '</li>';      
    }
?>
<?php if(!$this->isajax):?>
</ul>
<?php endif;?>

<script type="text/javascript">
  
  var container = $('siteevent_sidebar_guests_block');
    var getOccurrenceMembers = function() { 
     $('show_loading_guests').setStyle('display', 'block');
     $('siteevent_sidebar_guests_block').getElements('li.o_hidden').each(function(el) {
       el.destroy();
     })
     en4.core.request.send(new Request.HTML({
            'url': en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
            'data': {
                'format': 'html',
                'subject': en4.core.subject.guid,
                'occurrence_id': occurrence_id,
                isajax : 1
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {   
                 $('show_loading_guests').setStyle('display', 'none');
                Elements.from(responseHTML).inject(container);          
                Smoothbox.bind(container);
            }
        }), {
      'force':true
     
    });
      
    }
  
    var showGuestList = function(rsvp) {

        SmoothboxSEAO.open('<center><div class="siteevent_profile_loading_image"></div></center>');

        en4.core.request.send(new Request.HTML({
            'url': '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' => 'guest-list'), 'default', true); ?>',
            'data': {
                'format': 'html',
                'subject': en4.core.subject.guid,
                'rsvp': rsvp,
                'is_ajax_load': 1,
                friendsonly: 0,
                occurrence_id: '<?php echo $this->occurrence_id;?>'
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                if ($$('.seao_smoothbox_lightbox_overlay').isVisible() == 'true') {
                    SmoothboxSEAO.close();
                    SmoothboxSEAO.open('<div style="height:400px;">' + responseHTML + '</div>');
                }
            }
        }), {
          'force':true
        });
    }
</script>
<?php endif;?>