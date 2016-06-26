<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 19.10.13 08:20 Bolot $
 * @author     Bolot
 */
$host = (isset($_SERVER['HTTPS']) ? "https" : "http");
$host_url = $host . '://' . str_ireplace('heevents', '', $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')) . 'admin/heevent/index/ticketsview?id=';

?>
<style>
  #list_items_search .items {
    cursor: pointer;
    display: block;
    height: auto;
    margin: 5px;
    padding: 10px;
    width: 450px;
    overflow: hidden;
  }

  #list_items_search .items:hover {
    background: rgba(0, 0, 0, 0.4);
    width: 450px;
  }
</style>
<h2>
  <?php echo $this->translate("Assign Ticket") ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>

    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<script>
  function select_user_list() {
    $('search_box').setStyle('display', 'block');
    $('background_search').setStyle('display', 'block');
    $('list_items_search').set('html', 'Please type search params in input');
    $('search_input').set('value', '');
    $('type_search').set('value', '1');
  }
  ;
  function select_event_list() {
    $('search_box').setStyle('display', 'block');
    $('background_search').setStyle('display', 'block');
    $('list_items_search').set('html', 'Please type search params in input');
    $('search_input').set('value', '');
    $('type_search').set('value', '2');
  }
  ;
  function setUser(id) {
    $('search_box').setStyle('display', 'none');
    $('background_search').setStyle('display', 'none');
    $('search_input').set('value', '');
    $('list_items_search').set('html', 'Please type search params in input');
    $('type_search').set('value', '2');
    $('user_id').set('value', id);
  }
  ;
  function setEvent(id) {
    $('search_box').setStyle('display', 'none');
    $('background_search').setStyle('display', 'none');
    $('search_input').set('value', '');
    $('list_items_search').set('html', 'Please type search params in input');
    $('type_search').set('value', '2');
    $('event_id').set('value', id);
  }
  ;
  function hide_all() {
    $('search_box').setStyle('display', 'none');
    $('background_search').setStyle('display', 'none');
    $('search_input').set('value', '');
    $('list_items_search').set('html', 'Please type search params in input');
    $('type_search').set('value', '1');
  }
  ;
  function selectDiv(inp) {
    $('list_items_search').set('html', '<div style="text-align: center; width: 100%;height: 300px"><img style="margin: 50px" src="' + en4.core.baseUrl + 'application/modules/Heevent/externals/images/admin/loader.gif">');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'admin/heevent/index/assignsearch',
      data: {
        'search': inp.value,
        'type': $('type_search').value,
        'format': 'smoothbox'
      },
      evalScripts: true,
      onComplete: function (response) {
        var el = new Element('div');
        el.innerHTML = response;

        var lis = el.getElement('#global_content_simple').getElements('li');
        $('list_items_search').set('html', '');
        var len = lis.length;
        if (len > 0) {
          for (var i = 0; i < len; i++) {
            lis[i].inject($('list_items_search'));
          }
        } else {
          $('list_items_search').set('html', ' not found');
        }

      }
    }).send();
  }
  function generateCard() {
    var user = $('user_id').get('value').toInt();
    var event = $('event_id').get('value').toInt();
    if (user > 0 && event > 0) {
      var req = new Request({
        method: 'get',
        url: en4.core.baseUrl + 'admin/heevent/index/generatecard',
        data: {
          'event_id': event,
          'user_id': user,
          'format': 'smoothbox'
        },
        evalScripts: true,
        onComplete: function (response) {
          var id = response.toInt();

          var req = new Request({

            method: 'get',
            url: en4.core.baseUrl + 'admin/heevent/index/ticketsview?id=' + id,
            evalScripts: true,
            onComplete: function (response) {
              var el = new Element('div');
              el.innerHTML = response;
              var lis = el.getElement('#hevent_ticket_list');
              console.log(lis);
              $('content_card').set('html', '');
              lis.inject($('content_card'));

            }
          }).send();
        }
      }).send();
    } else {
      if (!user) {
        alert('user is not selected');
      } else {
        alert('event is not selected');
      }
    }
  }
</script>


<input type="hidden" value="0" name="user_id" id="user_id">
<input type="hidden" value="0" name="event_id" id="event_id">

<button class="select_user" id="select_user" onclick="select_user_list()"> Select User</button>
<button class="select_event" id="select_event" onclick="select_event_list()"> Select Event</button>
<div class="clr" style="margin: 20px"></div>
<button class="generate-card" id="generate-card" onclick="generateCard()">Generate Ticket</button>
<div id="content_card">
</div>
<div class="search_box" id="search_box"
     style="background: #fff; width: 500px; height: 500px;z-index: 100; padding: 15px; position: absolute; top: 100px; left: 500px; display: none">
  <input type="text" onkeyup="selectDiv(this)" placeholder="search" id="search_input" name="search_input"
         style="width: 100%; box-sizing: border-box"/>
  <input type="hidden" id="type_search" value="1"/>
  <ul id="list_items_search"></ul>
</div>
<div class="background_search" id="background_search"
     style="background: rgba(0,0,0,0.5); position: fixed; width: 100%;height: 100%; top: 0px; left: 0px; display: none;"
     onclick="hide_all()"></div>

