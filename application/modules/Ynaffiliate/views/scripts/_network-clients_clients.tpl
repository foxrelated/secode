<?php
    $last_assoc_id = 0;
    if (count($this->client_data) > 0) {
        foreach ($this->client_data as $client) {
            echo $this->partial('_network-clients_client.tpl', array(
                'user_id'=>$client['user_id'],
                'client_data'=>$client['clients'],
                'total_client'=>$client['total_client'],
                'direct_client'=>$client['direct_client'],
                'loaded_clients'=>count($this->client_data),
                'creation_date'=>$client['creation_date'],
                'level'=>$client['level'],
                'levelOptions'=>$this->levelOptions,
                'client_limit'=>$this->client_limit,
                'is_last'=>$client['is_last'],
                'search_user_id'=>$this->search_user_id,
                'max_level'=>$this->max_level
            ));
            $last_assoc_id = $client['assoc_id'];
        }
        if (($this->loaded_clients < $this->direct_client) && !$this->search_user_id) {
            echo '<li class="ynaffiliate-level-item ynaffiliate-btn-more">';
                echo $this->htmlLink('javascript:void(0);', $this->translate('more'),array(
                    'id'=>'loadmore_'.$this->user_id,
                    'onclick'=>'javascript:onLoadMore('.$this->user_id.','.$this->level.','.$last_assoc_id.','.$this->loaded_clients.');'
                ));
            echo '</li>';
        }
    } else { ?>
        <div class="tip">
                 <span>
                    <?php echo $this->translate("No more clients found.") ?>
                 </span>
        </div>
    <?php } ?>