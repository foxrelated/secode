<li class="ynaffiliate-level-item <?php if($this -> level == $this->max_level - 1) echo 'ynaffiliate-item-penultimate' ?> ">
    <?php
        $user = Engine_Api::_()->getItem('user', $this->user_id);
        if ($user->getIdentity()) {
            $levelLabel = $this->levelOptions[$user->level_id];
            $creationDate = date_format(date_create($user->creation_date), 'd.m.Y');
            $email = $user->email;
        } else {
            $levelLabel = '';
            $creationDate = '';
            $email = '';
        }
        echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'),array('class'=>'ynaffiliate_avatar'));
        echo $this->htmlLink($user->getHref(), $user->getTitle(),array('class'=>'ynaffiliate_client_name'));
    ?>

    <span class="ynaffiliate_btn_action_explain">
        <i class="fa 

        <?php  if ($this->max_level == $this->level) {
            echo "fa-angle-down";
        } 
            else
                {echo "fa-info-circle";
            } ?> fa-lg">
        </i>
    </span>

    <div class="ynaffiliate_client_item_info">
       <?php echo $this->htmlLink($user->getHref(), $user->getTitle()); ?> 
        <p>
            <?php echo $this->translate('Client level') ?>:
            <?php echo $this->level; ?>
        </p>
        <p>
            <?php echo $this->translate('Member level') ?>:
            <?php echo $levelLabel; ?>
        </p>
        <p>
            <?php echo $this->translate('Register date') ?>:
            <?php echo $creationDate ?>
        </p>
        <p>
            <?php echo $this->translate('Total affiliates') ?>:
            <?php echo $this->total_client; ?>
        </p>
        <p>
            <?php echo $this->translate('Client email') ?>:
            <?php echo $email; ?>
        </p>

        <span class="ynaffiliate_btn_action_close"><i class="fa fa-times fa-lg"></i></span>
    </div>

    <?php if ($this->direct_client && ($this->level < $this->max_level) && !$this->search_user_id): ?>
        <span class="ynaffiliate_btn_action_items-more">
            <i class="fa fa-plus fa-lg"></i>
        </span>
    <?php endif; ?>

    <?php
        if (count($this->client_data) > 0) {
            if ($this->is_last) {
                echo '<ul class="ynaffiliate-level-items-more ynaffiliate_last_level clearfix">';
            } else {
                echo '<ul class="ynaffiliate-level-items-more clearfix">';
            }
            echo $this->partial('_network-clients_clients.tpl', array(
                'client_data'=>$this->client_data,
                'levelOptions'=>$this->levelOptions,
                'client_limit'=>$this->client_limit,
                'loaded_clients'=>count($this->client_data),
                'direct_client'=>$this->direct_client,
                'user_id'=>$this->user_id,
                'level'=>$this->level,
                'search_user_id'=>$this->search_user_id,
                'max_level'=>$this->max_level
            ));
            echo '</ul>';
        }
    ?>
</li>
