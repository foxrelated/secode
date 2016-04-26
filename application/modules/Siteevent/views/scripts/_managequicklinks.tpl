<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _managequicklinks.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $viewer = Engine_Api::_()->user()->getViewer();
$hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
?>

<?php if($this->managePage):?>
    <?php if($this->actionLinks && is_array($this->actionLinks) && (in_array('events', $this->actionLinks) || in_array('events', $this->actionLinks) || in_array('birthday', $this->actionLinks) || in_array('diaries', $this->actionLinks) || (in_array('invites', $this->actionLinks) && $this->invite_count) || in_array('createNewEvent', $this->actionLinks))) :?>
    <div class="siteevent_myevents_top o_hidden b_medium">
        <div class="fleft">
            <?php
            //SHOW EVENTS LINK 
            $birthday = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday');
            ?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('events', $this->actionLinks)):?>
                <span class="siteevent_link_wrap mright5 fleft">
                    <i class="siteevent_icon item_icon_siteevent_event"></i>
                    <a href="javascript:void(0);" onclick="                       
                                $(prevactiveadveventlink).removeClass('bold');this.addClass('bold');showeventlists(this);" class="bold" id="list_events_link">
                           <?php
                           echo $this->translate('Events') . '</a>';
                       ?>

                </span>&nbsp;&nbsp;
            <?php endif;?>
            <?php //SHOW THE BIRTHDAY LINK IF BIRTHDAY MODULE IS ENABLED   ?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('birthday', $this->actionLinks)):?>
                <?php
                if ($birthday) {
                    ?>
                    <span class="siteevent_link_wrap mright5 fleft">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_birthday"></i>
                        <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getBirthdayList(this);" id="birthday_events_link">
                           <?php echo $this->translate('Birthdays'); ?>
                        </a>
                    </span>&nbsp;&nbsp;
                <?php } ?>
            <?php endif;?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('diaries', $this->actionLinks)):?>
            <?php  if (Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, "view")) {?>

                  <span class="siteevent_link_wrap mright5 fleft">
                          <i class="siteevent_icon siteevent_icon_diary"></i>
                          <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getMyDiaries(this);" id="diaries_events_link">
                             <?php echo $this->translate('Diaries'); ?>
                          </a>
                 </span>
                <?php } ?>
            <?php endif;?>
                    
            <?php if(!empty($this->showWaitlistLink)): ?>        
                <span class="siteevent_link_wrap mright5 fleft">
                    <i class="siteevent_icon siteevent_icon_list"></i>
                    <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getEventsInWaitingList(this);" id="inWaiting_events_link">
                       <?php echo $this->translate('Waitlist'); ?>
                    </a>
                </span>                   
            <?php endif;?>        
        </div>
        <div class="fright">
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('invites', $this->actionLinks)):?>
                <?php
                //SHOW INVITE COUNT
                if ($this->invite_count):
                    ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_request"></i>
                        <a href="javascript:void(0);" onclick="getInvitedList('popup', <?php echo $this->invite_count; ?>)" class="bold mrigh5">
                            <?php echo $this->translate('Invites') . '  <span id="invite_count" class="invites_count">' . $this->invite_count . '</span>'; ?>
                        </a>
                    </span>&nbsp;&nbsp;
                <?php endif; ?>    
            <?php endif; ?>  
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('createNewEvent', $this->actionLinks)):?>
            <?php
            //SHOW CREATE EVENT LINK 
            if (Engine_Api::_()->authorization()->isAllowed('siteevent_event', $this->viewer(), "create")):
                ?>
                <?php if ($this->quick): ?>
                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)): ?>
                        <?php
                        $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                        $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
                        ?>
                    <?php endif; ?>
                    <script type="text/javascript">
                            Asset.javascript('<?php echo $this->tinyMCESEAO()->addJS(true) ?>');
                    </script>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon icon_siteevent_add"></i>
                        <?php if ($hasPackageEnable):?>
                        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="bold" ><?php echo $this->translate("Create New Event"); ?></a>
                    <?php else:?>
                        <a href='<?php echo $this->url(array('action' => 'create'), "siteevent_general", true) ?>' class="bold seao_smoothbox"><?php echo $this->translate("Create New Event"); ?></a>
                        <?php endif; ?>
                    </span>
                <?php else: ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon icon_siteevent_add"></i>
                        <?php if ($hasPackageEnable):?>
                        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="bold" ><?php echo $this->translate("Create New Event"); ?></a>
                    <?php else:?>
                        <a href='<?php echo $this->url(array('action' => 'create'), "siteevent_general", true) ?>' class="bold"><?php echo $this->translate("Create New Event"); ?></a> 
                    <?php endif; ?>
                    </span>
                <?php endif; ?>
                    
                    
            <?php endif; ?>
        <?php endif; ?> 
        </div>
    </div>
     <?php endif;?>
<?php else :?>
    <?php if($this->actionLinks && is_array($this->actionLinks) && (in_array('events', $this->actionLinks) || in_array('events', $this->actionLinks) || in_array('birthday', $this->actionLinks) || in_array('diaries', $this->actionLinks) || (in_array('invites', $this->actionLinks) && $this->invite_count) || in_array('createNewEvent', $this->actionLinks))) :?>
    <div class="siteevent_myevents_top o_hidden b_medium">
        <div class="fleft">
            <?php
            //SHOW EVENTS LINK 
            $birthday = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday');
            ?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('events', $this->actionLinks)):?>
                <span class="siteevent_link_wrap mright5">
                    <i class="siteevent_icon item_icon_siteevent_event"></i>
                    <a href="javascript:void(0);" onclick="                       
                                $(prevactiveadveventlink).removeClass('bold');this.addClass('bold');showeventlists(this);" class="bold" id="list_events_link">
                           <?php
                           echo $this->translate('Events') . '</a>';
                       ?>

                </span>&nbsp;&nbsp;
            <?php endif;?>
            <?php //SHOW THE BIRTHDAY LINK IF BIRTHDAY MODULE IS ENABLED   ?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('birthday', $this->actionLinks)):?>
                <?php
                if ($birthday) {
                    ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_birthday"></i>
                        <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getBirthdayList(this);" id="birthday_events_link">
                           <?php echo $this->translate('Birthdays'); ?>
                        </a>
                    </span>&nbsp;&nbsp;
                <?php } ?>
            <?php endif;?>
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('diaries', $this->actionLinks)):?>
            <?php  if (Engine_Api::_()->authorization()->isAllowed('siteevent_diary', $viewer, "view")) {?>

                  <span class="siteevent_link_wrap mright5">
                          <i class="siteevent_icon siteevent_icon_diary"></i>
                          <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getMyDiaries(this);" id="diaries_events_link">
                             <?php echo $this->translate('Diaries'); ?>
                          </a>
                 </span>
                <?php } ?>
            <?php endif;?>
                    
            <?php if(!empty($this->showWaitlistLink)): ?>        
                <span class="siteevent_link_wrap mright5">
                    <i class="siteevent_icon_strip siteevent_icon item_icon_siteevent_event"></i>
                    <a href="javascript:void(0);" onclick="$(prevactiveadveventlink).removeClass('bold');this.addClass('bold');getEventsInWaitingList(this);" id="inWaiting_events_link">
                       <?php echo $this->translate('Waitlist'); ?>
                    </a>
                </span>                   
            <?php endif;?>                    
                    
        </div>
        <div class="fright">
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('invites', $this->actionLinks)):?>
                <?php
                //SHOW INVITE COUNT
                if ($this->invite_count):
                    ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon_strip siteevent_icon siteevent_icon_request"></i>
                        <a href="javascript:void(0);" onclick="getInvitedList('popup', <?php echo $this->invite_count; ?>)" class="bold mrigh5">
                            <?php echo $this->translate('Invites') . '  <span id="invite_count" class="invites_count">' . $this->invite_count . '</span>'; ?>
                        </a>
                    </span>&nbsp;&nbsp;
                <?php endif; ?>    
            <?php endif; ?>  
            <?php if($this->actionLinks && is_array($this->actionLinks) && in_array('createNewEvent', $this->actionLinks)):?>
            <?php
            //SHOW CREATE EVENT LINK 
            if (Engine_Api::_()->authorization()->isAllowed('siteevent_event', $this->viewer(), "create")):
                ?>
                <?php if ($this->quick): ?>
                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)): ?>
                        <?php
                        $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                        $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
                        ?>
                    <?php endif; ?>
                    <script type="text/javascript">
                            Asset.javascript('<?php echo $this->tinyMCESEAO()->addJS(true) ?>');
                    </script>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon icon_siteevent_add"></i>
                        <?php if ($hasPackageEnable):?>
                        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="bold" ><?php echo $this->translate("Create New Event"); ?></a>
                    <?php else:?>
                        <a href='<?php echo $this->url(array('action' => 'create'), "siteevent_general", true) ?>' class="bold seao_smoothbox"><?php echo $this->translate("Create New Event"); ?></a>
                        <?php endif; ?>
                    </span>
                <?php else: ?>
                    <span class="siteevent_link_wrap mright5">
                        <i class="siteevent_icon icon_siteevent_add"></i>
                        <?php if ($hasPackageEnable):?>
                        <a href='<?php echo $this->url(array('action' => 'index'), "siteevent_package", true) ?>' class="bold" ><?php echo $this->translate("Create New Event"); ?></a>
                    <?php else:?>
                        <a href='<?php echo $this->url(array('action' => 'create'), "siteevent_general", true) ?>' class="bold"><?php echo $this->translate("Create New Event"); ?></a> 
                    <?php endif; ?>
                    </span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?> 
        </div>
    </div>
    <?php endif;?>
<?php endif;?>


<script>

    var invite_count = 0;
    var getInvitedList = function(type, invite_count, event_id) {
        invite_count = invite_count;
        if (typeof event_id == 'undefined')
            event_id = 0;
        if (type == 'popup') {
            SmoothboxSEAO.open('<div id="siteevent_dayevents" style="width:550px;min-height:70px"><div class="seaocore_content_loader" style="margin:20px auto 0;"></div></div>');
        }
        else if (type == 'invite') {
            $('calendar_invited_list').innerHTML = '<div class="seaocore_content_loader" style="margin:10px auto;"></div>';
        }
        new Request.HTML({
            method: 'get',
            url: en4.core.baseUrl + 'siteevent/index/get-invited-list-events',
            data: {
                'format': 'html',
                'subject': en4.core.subject.guid,
                type: type,
                invite_count: invite_count,
                event_id: event_id

            },
            onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                if (type == 'popup') {
                    if ($$('.seao_smoothbox_lightbox_overlay').isVisible() == 'true') {
                        SmoothboxSEAO.close();
                        SmoothboxSEAO.open('<div id="siteevent_invitedlists" style="width:550px;">' + responseHTML + '</div>');
                    }
                }
                else {
                    $('calendar_invited_list').innerHTML = responseHTML;
                }
                SmoothboxSEAO.bind($('calendar_invited_list'));
                //CHECK IF THE RESPONSE IS EMPTY

                if ($('calendar_invited_list')) {
                    var liList = $('manage_events_' + type).getElement("ul li");
                    if (liList == null)
                        $('calendar_invited_list').destroy();
                }

            }


        }).send();

    }

    var eventcontainer;
    var prevactiveadveventlink = 'list_events_link';
    window.addEvent('domready', function() {

        if (document.getElement('.layout_siteevent_manage_events_siteevent .siteevent_manage_event') != null)
            eventcontainer = document.getElement('.layout_siteevent_manage_events_siteevent .siteevent_manage_event')
        else
            eventcontainer = document.getElement('.layout_siteevent_mycalendar_siteevent .siteevent_manage_event');



    });

    var showeventlists = function() {
       prevactiveadveventlink = 'list_events_link';
        $(eventcontainer).setStyle('display', 'block');
        if (document.getElement('.layout_right'))
            document.getElement('.layout_right').setStyle('display', 'block');
        if ($('birthday_lists') != null)
            $('birthday_lists').setStyle('display', 'none');
        if ($('myDiaries_Lists') != null)
          $('myDiaries_Lists').setStyle('display', 'none');  

    }
    
    var myDiaryURL = en4.core.baseUrl + 'widget/index/mod/siteevent/name/diary-browse?search_diary=my_diaries&viewType=list&isAjax=true';
    var getMyDiaries = function() {
        prevactiveadveventlink = 'diaries_events_link';
        $(eventcontainer).setStyle('display', 'none');
        if (document.getElement('.layout_right'))
            document.getElement('.layout_right').setStyle('display', 'none');
        if ($('birthday_lists') != null)
            $('birthday_lists').setStyle('display', 'none');
        
        if ($('eventsInWaiting_lists') != null)
            $('eventsInWaiting_lists').setStyle('display', 'none');
          
       //IF BIRTHDAY LIST IS ALREADY THERE THEN JUST SHOW THAT AND RETURN;
            if ($('myDiaries_Lists') != null)
                $('myDiaries_Lists').setStyle('display', 'block');
            else {

                Elements.from('<div id="myDiaries_Lists"><div class="seaocore_content_loader" style="margin:30px auto 0;"></div></div>').inject($(eventcontainer), 'after');
 
                //CHECK IF QUERY STRING EXIST IN THE URL OR NOT
                var querystring = myDiaryURL.split('?');
                if(querystring.length != 2)
                  myDiaryURL = '?search_diary=my_diaries&viewType=list&isAjax=true';
                new Request.HTML({
                    method: 'get',
                    url: myDiaryURL,
                    data: {
                        'format': 'html',
                        'subject': en4.core.subject.guid
                    },
                    onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        $('myDiaries_Lists').innerHTML = responseHTML;
                        if($('myDiaries_Lists').getElements('.seaocore_tab_select_wrapper').length > 0)
                        $('myDiaries_Lists').getElements('.seaocore_tab_select_wrapper').each(function(el) {
                          el.setStyle('display', 'none');
                          
                        });
                       
                        $('myDiaries_Lists').getElements('ul.paginationControl li').each(function(el) {
                          el.getElement('a').addEvent('click', function(e) {
                            e.stop();
                            myDiaryURL =  this.href ;                           
                           $('myDiaries_Lists').destroy(); 
                           getMyDiaries();
                          });
                        
                        });
                    }



                }).send();

            }    

    }

<?php if ($birthday) : ?>
var birthdayListUrl = '<?php echo $this->url(array('action' => 'view'), 'birthday_extended', true); ?>';
        var getBirthdayList = function() {

            prevactiveadveventlink = 'birthday_events_link';
            //HIDE THE EVENT DISPLAY
            $(eventcontainer).setStyle('display', 'none');
            if ($('myDiaries_Lists') != null)
              $('myDiaries_Lists').setStyle('display', 'none');
            if ($('eventsInWaiting_lists') != null)
                $('eventsInWaiting_lists').setStyle('display', 'none');             
            if (document.getElement('.layout_right'))
                document.getElement('.layout_right').setStyle('display', 'none');

            //IF BIRTHDAY LIST IS ALREADY THERE THEN JUST SHOW THAT AND RETURN;
            if ($('birthday_lists') != null)
                $('birthday_lists').setStyle('display', 'block');
            else {


                Elements.from('<div id="birthday_lists"><div class="seaocore_content_loader" style="margin:30px auto 0;"></div></div>').inject($(eventcontainer), 'after');

                new Request.HTML({
                    method: 'get',
                    url: birthdayListUrl,
                    data: {
                        'format': 'html',
                        'subject': en4.core.subject.guid
                    },
                    onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        $('birthday_lists').innerHTML = responseHTML;
                        if($('birthday_listings_next')) {
                          $('birthday_listings_next').getElement('a.icon_next').erase('onclick').addEvent('click', function(e) {
                            e.stop();
                            birthdayListUrl = en4.core.baseUrl + "birthday/index/view/startindex/" + next_start + "/page/" + (parseInt(birthdayPage) + parseInt(1));                     
                           $('birthday_lists').destroy(); 
                           getBirthdayList();
                          });
                        }
                        if($('birthday_listings_previous')) {
                          $('birthday_listings_previous').getElement('a.icon_previous').erase('onclick').addEvent('click', function(e) {
                            e.stop();
                            birthdayListUrl = en4.core.baseUrl + "birthday/index/view/startindex/" + prev_start + "/page/" + (parseInt(birthdayPage) - parseInt(1));                     
                           $('birthday_lists').destroy(); 
                           getBirthdayList();
                          });
                        }                      

                    }



                }).send();

            }
        }
<?php endif; ?>
    
		var eventsInWaitingUrl = '<?php echo $this->url(array('controller' => 'waitlist', 'action' => 'events-in-waiting'), 'siteevent_extended', true); ?>';
        var getEventsInWaitingList = function() {

            prevactiveadveventlink = 'inWaiting_events_link';
            //HIDE THE EVENT DISPLAY
            $(eventcontainer).setStyle('display', 'none');
            if ($('myDiaries_Lists') != null)
              $('myDiaries_Lists').setStyle('display', 'none');
       
            if (document.getElement('.layout_right'))
                document.getElement('.layout_right').setStyle('display', 'none');

            //IF BIRTHDAY LIST IS ALREADY THERE THEN JUST SHOW THAT AND RETURN;
            if ($('eventsInWaiting_lists') != null)
                $('eventsInWaiting_lists').setStyle('display', 'block');
            else {

                Elements.from('<div id="eventsInWaiting_lists"><div class="seaocore_content_loader" style="margin:30px auto 0;"></div></div>').inject($(eventcontainer), 'after');

                new Request.HTML({
                    method: 'get',
                    url: eventsInWaitingUrl,
                    data: {
                        'format': 'html',
                        'subject': en4.core.subject.guid
                    },
                    onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                        $('eventsInWaiting_lists').innerHTML = responseHTML;
                        if($('eventsInWaiting_listings_next')) {
                          $('eventsInWaiting_listings_next').getElement('a.icon_next').erase('onclick').addEvent('click', function(e) {
                            e.stop();
                            eventsInWaitingUrl = en4.core.baseUrl + "birthday/index/view/startindex/" + next_start + "/page/" + (parseInt(eventsInWaitingPage) + parseInt(1));                     
                           $('eventsInWaiting_lists').destroy(); 
                           getEventsInWaitingList();
                          });
                        }
                        if($('eventsInWaiting_listings_previous')) {
                          $('eventsInWaiting_listings_previous').getElement('a.icon_previous').erase('onclick').addEvent('click', function(e) {
                            e.stop();
                            eventsInWaitingUrl = en4.core.baseUrl + "birthday/index/view/startindex/" + prev_start + "/page/" + (parseInt(eventsInWaitingPage) - parseInt(1));                     
                           $('eventsInWaiting_lists').destroy(); 
                           getEventsInWaitingList();
                          });
                        }                      

                    }



                }).send();

            }
        }                

</script>