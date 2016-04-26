<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'); ?>

<?php if (!$this->only_list_content): ?>
  <?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
  <div class="siteevent_dashboard_content">
    <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <?php echo $this->form->render($this); ?>
    <div class="siteevent_event_form">
      <div id="siteevent_manage_waitlist_content"> 
      <?php endif; ?> 
      <?php $paginationCount = $this->paginator->getTotalItemCount(); ?><br/>
      <?php if (empty($this->call_same_action)) : ?>
        <div class="siteevent_manage_event">

          <h3 class="mbot10"><?php echo $this->translate('Manage Waitlist') ?></h3>
          <p class="mbot10"><?php echo $this->translate("Here, you can see and manage the waitlist for your event. You can also take appropriate actions using the action links available along with member names in the waitlist."); ?></p>
          
          <?php if($paginationCount > 0): ?>
          <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal">
            <form method="post" class="field_search_criteria" id="filter_form">
              <div>
                <ul>
                  <li>
                    <span><label><?php echo $this->translate("User Name") ?></label></span>
                    <input type="text" name="username" id="username"/> 
                  </li>      
                  
                  <li id="integer-wrapper">
                    <span><label><?php echo $this->translate("Applied Date : ex (2000-12-25)") ?></label></span>
                    <div class="form-element"> 
                      <input type="text" name="creation_date_start" id="creation_date_start" placeholder="<?php echo $this->translate("from"); ?>"/> 
                    </div>
                    <div class="form-element"> 
                      <input type="text" name="creation_date_end" id="creation_date_end" placeholder="<?php echo $this->translate("to"); ?>"/> 
                    </div>
                  </li>

                    <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>

                        <li>
                            <span><label><?php echo $this->translate("Available Dates") ?></label></span>
                                <select name="occurrence_id" id="occurrence_id">
                                  <?php
                                  $noAllOccurrencesField = 1;
                                  $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo, $noAllOccurrencesField);
                                  foreach ($filter_dates as $key => $date):
                                    ?> 
                                    <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
                                  <?php endforeach;
                                  ?>
                                </select>
                        </li>
                        <br/>
                    <?php endif; ?>                  
                  
                  <li class="clear mtop10">
                    <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>     
                  </li>
                  <li>
                    <span id="search_spinner"></span>
                  </li>
                </ul>
              </div>
            </form>
          </div>
         <?php endif; ?>   
         <div id="manage_waitlist_pagination">  <?php endif; ?>
          <?php if ($this->total_item): ?>
             
            <?php foreach ($this->paginator as $waitlist): ?>
                <?php $occurrenceId = $waitlist->occurrence_id;?> 
                <?php $occurrence = Engine_Api::_()->getItem('siteevent_occurrence', $occurrenceId); ?>
                <?php break;?> 
            <?php endforeach; ?>  
             
            <?php if($this->siteevent->capacity && !empty($occurrence) && $occurrence->waitlist_flag): ?>
                <?php $htmlLink = $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'waitlist', 'action' => 'unset-waitlist-flag','occurrence_id' => $occurrence->occurrence_id), $this->translate('click here'), array('class' => 'smoothbox')); ?>
                <div class="tip">
                    <span>
                        <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                            <?php echo $this->translate('Hence waitlist has been triggered for this event, ‘Join Event’ will not visible to site members now. If you want to show ‘Join Event’ option to site members, please %s. Based on your confirmation, Join Event will start showing to site members and members can join for this event as equal to available event capacity.', $htmlLink); ?>
                        <?php else: ?>
                            <?php echo $this->translate('Hence waitlist has been triggered for this event, ‘Book Now’ will not visible to site members now. If you want to show ‘Book Now’ to site members, please %s. Based on your confirmation, Book Now will start showing to site members and tickets can be booked for this event as equal to available ticket quantity.', $htmlLink); ?>
                        <?php endif; ?>
                        <br/>
                    </span>
                </div>
            <?php endif; ?> 
             
            <div class="mbot5">
              <?php echo $this->translate('%s guest request(s) found.', $this->total_item) ?>
            </div>
          <?php endif; ?>
          <div id="manage_waitlist_tab">
            <?php if ($this->total_item): ?>
              <div class="siteevent_detail_table">
                  <form id='multidelete_form' method="post" action="<?php echo $this->url(array('controller' => 'waitlist', 'action' => 'multi-delete', 'event_id' => $this->siteevent->event_id), 'siteevent_extended'); ?>" onSubmit="return multiDelete()">
                <table>
                  <tr class="siteevent_detail_table_head">
                    <th style='width: 1%;' align="left"><input onclick="selectAll()" type='checkbox' class='checkbox'></th>  
                    <th class="txt_center"><?php echo $this->translate('Waitlist Id') ?></th>
                    <th><?php echo $this->translate('Guest') ?></th>
                    <th><?php echo $this->translate('Applied Date') ?></th>
                    <th><?php echo $this->translate('Options') ?></th>
                  </tr>	
                  <?php foreach ($this->paginator as $waitlist): ?>

                    <tr>
                      <td><input name='delete_<?php echo $waitlist->waitlist_id; ?>' type='checkbox' class='checkbox' value="<?php echo $waitlist->waitlist_id ?>"/></td>  
                      <td class="txt_center"><?php echo $waitlist->waitlist_id; ?></td>
                      <td><?php echo $this->htmlLink($waitlist->getOwner()->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($waitlist->getOwner()->getTitle(), 20), array('title' => $waitlist->getOwner()->getTitle(), 'target' => '_blank')); ?></td>             
                      <td><?php echo $this->locale()->toDateTime($waitlist->creation_date); ?></td>
                      <td class="event_actlinks">
                        <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>  
                            <?php echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'join','event_id' => $this->siteevent->event_id, 'occurrence_id' => $waitlist->occurrence_id, 'waitlist_id' => $waitlist->waitlist_id), null, array('class' => 'smoothbox siteevent_icon_user' , 'title' => $this->translate('Attending'))) ?>  
                        <?php endif; ?>
                        <?php if($waitlist->user_id != Engine_Api::_()->user()->getViewer()->getIdentity()): ?>  
                            <?php echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'waitlist', 'action' => 'message-waitlister','waitlist_id' => $waitlist->waitlist_id), null, array('class' => 'smoothbox siteevent_icon_message' , 'title' => $this->translate('Send Message'))) ?>   
                        <?php endif; ?>
                        <?php echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'waitlist', 'action' => 'delete','waitlist_id' => $waitlist->waitlist_id), null, array('class' => 'smoothbox siteevent_icon_delete' , 'title' => $this->translate('Delete'))) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </table>
                      
                <br />
                <div class='buttons'>
                    <button type='submit'><?php echo $this->translate('Delete Selected'); ?></button>
                </div>
                      
                  </form>
              </div>
            </div>
            <div class="clr dblock siteevent_data_paging">
              <div id="event_manage_waitlist_previous" class="paginator_previous siteevent_data_paging_link">
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                 'onclick' => '',
                 'class' => 'buttonlink icon_previous'
                ));
                ?>
                <span id="manage_spinner_prev"></span>
              </div>

              <div id="event_manage_waitlist_next" class="paginator_next siteevent_data_paging_link">
                <span id="manage_spinner_next"></span>
                <?php
                echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                 'onclick' => '',
                 'class' => 'buttonlink_right icon_next'
                ));
                ?>
              </div>

            <?php else: ?>
              <div class="tip"><span>
                  <?php echo $this->translate('No-one has applied for the waitlist.') ?>
                </span></div>
            <?php endif; ?>
          </div>
          <?php if (empty($this->call_same_action)) : ?>
          </div>
        </div>
      <?php endif; ?>

      <script type="text/javascript">

        en4.core.runonce.add(function () {

          var anchor = document.getElementById('manage_waitlist_tab').getParent();
          <?php if ($paginationCount): ?>
            document.getElementById('event_manage_waitlist_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('event_manage_waitlist_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';
            $('event_manage_waitlist_previous').removeEvents('click').addEvent('click', function () {
              $('manage_spinner_prev').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteevent/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteevent/waitlist/manage/event_id/' + <?php echo sprintf('%d', $this->siteevent->event_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  username: $('username').value,
                  creation_date_start: $('creation_date_start').value,
                  creation_date_end: $('creation_date_end').value,
                  occurrence_id: ($('occurrence_id')) ? $('occurrence_id').value : 0,
                  event_id: <?php echo sprintf('%d', $this->siteevent->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('manage_spinner_prev').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });

            $('event_manage_waitlist_next').removeEvents('click').addEvent('click', function () {
              $('manage_spinner_next').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteevent/externals/images/loading.gif" />';

              en4.core.request.send(new Request.HTML({
                url: en4.core.baseUrl + 'siteevent/waitlist/manage/event_id/' + <?php echo sprintf('%d', $this->siteevent->event_id) ?>,
                data: {
                  format: 'html',
                  search: 1,
                  subject: en4.core.subject.guid,
                  call_same_action: 1,
                  username: $('username').value,
                  creation_date_start: $('creation_date_start').value,
                  creation_date_end: $('creation_date_end').value,
                  occurrence_id: ($('occurrence_id')) ? $('occurrence_id').value : 0,
                  event_id: <?php echo sprintf('%d', $this->siteevent->event_id) ?>,
                  page: <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                },
                onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                  $('manage_spinner_next').innerHTML = '';
                }
              }), {
                'element': anchor
              })
            });
        <?php endif; ?>

          $('filter_form').removeEvents('submit').addEvent('submit', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Siteevent/externals/images/loading.gif" />';

            en4.core.request.send(new Request.HTML({
              url: en4.core.baseUrl + 'siteevent/waitlist/manage',
              method: 'POST',
              data: {
                search: 1,
                subject: en4.core.subject.guid,
                call_same_action: 1,
                username: $('username').value,
                creation_date_start: $('creation_date_start').value,
                creation_date_end: $('creation_date_end').value,
                occurrence_id: ($('occurrence_id')) ? $('occurrence_id').value : 0,
                event_id: <?php echo sprintf('%d', $this->siteevent->event_id) ?>
              },
              onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                $('search_spinner').innerHTML = '';
              }
            }), {
              'element': anchor
            })
          });
        });
      </script>
      <?php if (!$this->only_list_content): ?>
      </div>
    </div>	
  </div>	
<?php endif; ?>
</div>

<script type="text/javascript">
    function multiDelete()
    {
        return confirm('<?php echo $this->string()->escapeJavascript("Are you sure you want to delete selected waitlists ?") ?>');
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length - 1; i++) {
            if (!inputs[i].disabled) {
              inputs[i].checked = inputs[0].checked;
            }
        }
    }    
</script>    
