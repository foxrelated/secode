<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  en4.core.runonce.add(function ()
  {
    SmoothboxSEAO.bind($('smooth_open_form'));
  });
</script>
<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Siteeventticket/externals/styles/style_siteeventticket.css'?>');
</script>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>

<div class="siteevent_dashboard_content">
  <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>

  <!--TAX MANDATORY MESSAGE DISPLAY-->
  <?php if ($this->taxMandatoryMessage): ?> 
    <div class="tip"><span>
        <?php echo $this->translate('Admin has set the Tax as mandatory, you need to set tax rate before ticket creation. Please set tax rate %1$shere%2$s', '<a href =' . $this->url(array('controller' => 'tax', 'action' => 'index', 'event_id' => $this->event_id), 'siteeventticket_tax_general') . ' >', '</a>') ?></span>
    </div>
    <?php
    return;
  endif;
  ?>

  <div class="siteevent_event_form" id="smooth_open_form">
    <div class="global_form">
      <div>
        <div>
          <h3> <?php echo $this->translate("Manage Tickets"); ?></h3>
          <p class="form-description"><?php echo $this->translate("Below, you can create and manage tickets for your event."); ?>
          </p>
          <br>
          <?php if ($this->isAllowTicketCreation): ?>
            <div>
              <!--ADD BUTTON-->
              <?php
              echo $this->htmlLink(
                  array('route' => "siteeventticket_ticket", 'action' => 'add', "event_id" => $this->event_id), $this->translate('Add Ticket'), array('class' => 'seao_smoothbox buttonlink seaocore_icon_add'));
              ?>
              <!--ADD BUTTON-->
            </div>
          <?php endif; ?>
          <br>
          <div class='siteevent_package_page'>
            <div>
              <div>

                <?php if (!empty($this->count)): ?>

                  <h3><?php echo $this->translate('Available Tickets') ?></h3> 
                  <div class="siteevent_detail_table mtop5">
                  <table>
                    <tbody>
                      <tr class="siteevent_detail_table_head">
                      	<th style="width: 50%;"><?php echo $this->translate('Ticket name') ?></th>
                        <th style="width: 12%;"><?php echo $this->translate('Price') ?></th>
                        <th style="width: 18%;"><?php echo $this->translate('Quantity') ?></th>
                        <th style="width: 20%;"><?php echo $this->translate('Actions') ?></th>
                      </tr> 
                      <?php foreach ($this->paginator as $item): ?>    
                        <tr class="<?php if ($item->status == 'hidden'): ?> seaocore_txt_light<?php endif; ?>">
                          <td>             
                            <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 30)); ?>
                          </td>
                          <td class="seaocore_txt_light">
                            <?php
                            if ($item->price > 0):echo $this->locale()->toCurrency($item->price, $currency);
                            else: echo $this->translate('Free');
                            endif;
                            ?>
                          </td> 
                          <td><?php echo $item->quantity; ?></td>
                          <td class="event_actlinks"><?php
                        echo $this->htmlLink(
                            array('route' => "siteeventticket_ticket", 'action' => 'detail', 'event_id' => $this->event_id, "ticket_id" => $item->ticket_id), null, array('class' => 'seao_smoothbox siteevent_icon_detail' , 'title' => $this->translate('Details')));
                        ?>
                        <?php if ($this->isAllowTicketCreation): ?>
                          <?php
                          echo $this->htmlLink(
                              array('route' => "siteeventticket_ticket", 'action' => 'edit', 'event_id' => $this->event_id, "ticket_id" => $item->ticket_id), null, array('class' => 'seao_smoothbox siteevent_icon_edit' , 'title' => $this->translate('Edit')));
                          ?>
                          <?php
                          echo $this->htmlLink(
                              array('route' => "siteeventticket_ticket", 'action' => 'delete', 'event_id' => $this->event_id, 'ticket_id' => $item->ticket_id), null, array('class' => 'seao_smoothbox siteevent_icon_delete' , 'title' => $this->translate('Delete')));
                          ?>
                        <?php endif; ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                  </div>
                  <?php else: ?>
                    <div class="tip">
                      <span>
                        <?php $url = $this->url(array('action' => 'add', 'event_id' => $this->event_id), "siteeventticket_ticket", true); ?>
                        <?php echo $this->translate('You have not added any tickets for your event. %1$sClick here%2$s  to add its first ticket.', "<a href='$url' class='seao_smoothbox'>", "</a>"); ?>                                               
                      </span>
                    </div>
                <?php endif; ?>

              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>	
</div>	

</div>