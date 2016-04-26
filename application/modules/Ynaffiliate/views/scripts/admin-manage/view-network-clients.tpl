<?php
    if (APPLICATION_ENV == 'production')
        $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
    else
        $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type = "text/javascript">
    // download function placeholder
    function do_downloadCSV(userId) {
        $('download_csv').action = en4.core.baseUrl +'ynaffiliate/index/download-csv';
        $('download_csv_user_id').value = userId;
        $('download_csv').submit();
    }

    var isLoading = false;
    // load a set of clients and put to more
    function onLoadMore(user_id, from_level, last_assoc_id, loaded_clients) {
        if (isLoading) {
            return;
        }
        isLoading = true;
        var url = '<?php echo $this->url(array('controller' => 'index', 'action' => 'load-more-clients'), 'ynaffiliate_extended', true) ?>';
        new Request({
            url: url,
            data: {
                'type': 'ajax',
                'user_id': user_id,
                'from_level': from_level,
                'last_assoc_id': last_assoc_id,
                'search_user_id': 0,
                'loaded_clients': loaded_clients
            },
            onComplete: function(response) {
                isLoading = false;
                document.getElementById('loadmore_' + user_id).parentNode.outerHTML = response;
                // bind click events for new clients
                bindClientEvents();
            }
        }).send();
    }

    // search client and put to main container
    function searchClient(user_id, search_user_id, expandall) {
        var url = '<?php echo $this->url(array('controller' => 'index', 'action' => 'load-more-clients'), 'ynaffiliate_extended', true) ?>';
        new Request({
            url: url,
            data: {
                'type': 'ajax',
                'user_id': user_id,
                'from_level': 0,
                'last_assoc_id': 0,
                'search_user_id': search_user_id
            },
            onComplete: function(response) {
                document.getElementById('clients_container').innerHTML = response;
                bindClientEvents();

                // expand and remove more button, used when search for a clients
                if (expandall) {
                    $$('.ynaffiliate-level-item').toggleClass('ynaffiliate_item_more_explain');
                    moreButtons = $$('.ynaffiliate_btn_action_items-more');
                }
            }
        }).send();
    }

    var maxRecipients = 1;
    var to = {
        id : false,
        type : false,
        guid : false,
        title : false
    };

    function removeFromToValue(id)
    {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById('toValues').value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";
        // reset all clients
        searchClient(<?php echo $this->user_id; ?>, 0, 0);

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray);
            }
        }
        else{
            removeToValue(id, toValueArray);
        }

        // hide the wrapper for usernames if it is empty
        if (document.getElementById('toValues').value==""){
            document.getElementById('toValues-wrapper').style.height = '0';
        }
//        document.getElementById('to').style.display = 'block';
        document.getElementById('to').disabled = false;
        document.getElementById('to').placeholder = "<?php echo $this->translate('Search client name') ?>";
    }

    function removeToValue(id, toValueArray){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById('toValues').value = toValueArray.join();
    }

    function expandClient() {
        this.getElement('.fa-plus').toggleClass('fa-minus');
        this.getParent('.ynaffiliate-level-item').toggleClass('ynaffiliate_item_more_explain');
    }

    // click event for info and more buttons
    function bindClientEvents() {
        $$('.ynaffiliate_btn_action_explain').addEvent('click', function () {
            $$('.ynaffiliate_explain_info').removeClass('ynaffiliate_explain_info');
            this.getParent('.ynaffiliate-level-item').addClass('ynaffiliate_explain_info');
        });

        $$('.ynaffiliate_btn_action_close').addEvent('click', function () {
            this.getParent('.ynaffiliate-level-item').removeClass('ynaffiliate_explain_info');
        });

        $$('.ynaffiliate_btn_action_items-more').addEvent('click', expandClient);
    }

    // auto complete for client search
    en4.core.runonce.add(function() {
            new Autocompleter.Request.JSON('to', '<?php echo $this->url(array('controller' => 'index', 'action' => 'client-suggest'), 'ynaffiliate_extended', true) ?>', {
                'minLength': 1,
                'delay' : 250,
                'selectMode': 'pick',
                'autocompleteType'  : 'message',
                'multiple': false,
                'className': 'message-autosuggest',
                'filterSubset' : true,
                'tokenFormat' : 'object',
                'tokenValueKey' : 'label',
                'injectChoice': function(token){
                    if(token.type == 'user'){
                        var choice = new Element('li', {
                            'class': 'autocompleter-choices',
                            'html': token.photo,
                            'id':token.label
                        });
                        new Element('div', {
                            'html': this.markQueryValue(token.label),
                            'class': 'autocompleter-choice'
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    else {
                        var choice = new Element('li', {
                            'class': 'autocompleter-choices friendlist',
                            'id':token.label
                        });
                        new Element('div', {
                            'html': this.markQueryValue(token.label),
                            'class': 'autocompleter-choice'
                        }).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                },
                onPush : function(){
                    if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
//                        document.getElementById('to').style.display = 'none';
                        document.getElementById('to').disabled = true;
                        document.getElementById('to').placeholder = '';
//                        console.log(document.getElementById('toValues').value);
                        searchClient(<?php echo $this->user_id; ?>, document.getElementById('toValues').value, 1);
                    }
                }
            });

        bindClientEvents();
    });
</script>

<div class="ynaffiliate_download_search_block">
    <button name="" onclick="return do_downloadCSV(<?php echo $this->user_id; ?>);"><?php echo $this->translate('Download CSV'); ?> </button>
    <input type='text' id='to' alt='<?php echo $this->translate('Search client name') ?>' placeholder='<?php echo $this->translate('Search client name') ?>' />
    <div id="toValues-wrapper">
        <input type='hidden' id='toValues' name="toValues" />
        <div id='toValues-element'></div>
    </div>
    <i class="fa fa-search"></i>
</div>

<form id='download_csv' method='post' action=''>
    <input type="hidden" id="download_csv_user_id" name="download_csv_user_id" value=""/>
</form>

<div class="ynaffiliate_owner_clients">
<?php echo $this->htmlLink($this->viewer->getHref(), $this->itemPhoto($this->viewer, 'thumb.profile'), array('class'=>'ynaffiliate_avatar') )?>

    <div class="ynaffiliate_owner_clients_info">
        <?php echo $this->translate('Client network of') ?>
        <span class="ynaffiliate_name"><?php echo $this->viewer->getTitle(); ?></span> 

        <span class="ynaffiliate_total_count">
            <?php echo $this->translate('Total') ?>:
            <b><?php echo $this->total_client; ?></b>
        </span>
    </div>
</div>

<div class="ynaffiliate_level_clients ynaffiliate_client">
    <?php
    if (count($this->client_data) > 0) {
        if ($this->max_level == 1) {
            echo '<ul id="clients_container" class="ynaffiliate-level-last">';
        } else {
            echo '<ul id="clients_container" class="ynaffiliate-level-items">';
        }
        echo $this->partial('_network-clients_clients.tpl', array(
            'client_data'=>$this->client_data,
            'levelOptions'=>$this->levelOptions,
            'client_data'=>$this->client_data,
            'client_limit'=>$this->client_limit,
            'direct_client'=>$this->direct_client,
            'loaded_clients'=>$this->loaded_clients,
            'search_user_id'=>$this->search_user_id,
            'user_id'=>$this->user_id,
            'level'=>0,
            'max_level'=>$this->max_level
        ));
        echo '</ul>';
    } else { ?>
        <div class="tip">
         <span>
            <?php echo $this->translate("You have no clients yet.") ?>
         </span>
        </div>
    <?php } ?>
</div>
