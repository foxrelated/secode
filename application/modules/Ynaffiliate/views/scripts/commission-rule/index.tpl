<div id="formborder" class="ynaffiliate_table">
    <form id="commission" method="post" name="commissionform">
        <?php if (count($this->paginator)): ?>


        <div class="ynaffiliate_select">

            <span><?php echo $this->translate('Member Level'); ?></span>

            <select name="kitty" onchange="javascript:$('commission').submit();">
                <?php
                    $option_id_selected = $this->level_id;

                    foreach ($this->levelOptions as $row) { ?>
                        <option value="<?php echo $row['level_id']; ?>"
                            <?php
                                if ($row['level_id'] == $option_id_selected) {
                                    echo 'selected = "selected"';
                                }
                            ?>
                        >

                        <?php echo $row['label']; ?>
                        </option>

                     <?php } ?>
            </select>
        </div>


        <div class="ynaffiliate_table_scroll">
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                <tr class="table_th_row">
                    <th class="table_th">
                        <?php echo $this->translate('Payment Types'); ?>
                    </th>
                    <?php for ($l = 1; $l <= $this->MAX_COMMISSION_LEVEL; $l++) { ?>
                    <th class="table_th"><?php echo $this->translate("Level %s", $l) ?></th>
                    <?php } ?>
                </tr>

                <?php
                $rule = $this->paginator;
                foreach ($rule as $type) {
                    if ($type['enabled'] == 1) { ?>
                        <tr>
                            <td class="table_td"><?php echo $this->translate($type['rule_title']) ?></td>
                        <?php for ($l = 1; $l <= $this->MAX_COMMISSION_LEVEL; $l++) { ?>
                            <?php if (isset($type["level_$l"])) { ?>
                                <td class="table_td"><?php echo $type["level_$l"] . " %" ?></td>
                            <?php } else { ?>
                                <td class="table_td"><?php echo $this->translate("N/A") ?></td>
                            <?php } ?>
                        <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>

            </table>
        </div>

        <div>
            <?php echo $this->paginationControl($this->paginator, null, null, array(
                'query' => $this->formValues
            )); ?>
        </div>

        <?php else: ?>
            <div class="tip">
                  <span>
                     <?php echo $this->translate("There is no rule yet.") ?>
                  </span>
            </div>
        <?php endif; ?>
    </form>
</div>
