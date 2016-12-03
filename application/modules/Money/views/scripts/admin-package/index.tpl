<?php ?>
<div class="headline">
    <h2>
        <?php echo $this->translate('E-money'); ?>
    </h2>
    <div class="tabs">
        <?php
        // Render the menu
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<?php if (!empty($this->error)): ?>
    <ul class="form-errors">
        <li>
            <?php echo $this->error ?>
        </li>
    </ul>

    <br />
<?php /* return; */ endif; ?>


<div>
    <?php
    echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Add Plan'), array(
        'class' => 'buttonlink icon_plan_add',
    ))
    ?>
</div>

<br />

<div class='admin_results'>
    <div>
        <?php $count = $this->paginator->getTotalItemCount() ?>
        <?php echo $this->translate(array("%s plan found", "%s plans found", $count), $count) ?>
    </div>
    <div>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'query' => $this->filterValues,
            'pageAsQuery' => true,
        ));
        ?>
    </div>
</div>

<br />


<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <table class='admin_table'>
        <thead>
            <tr>

                <th style='width: 1%;' class="<?php echo $class ?>">

                    <?php echo $this->translate("ID") ?>

                </th>

                <th>

                    <?php echo $this->translate("Title") ?>

                </th>

                <th style='width: 1%;' >

                    <?php echo $this->translate("Price") ?>

                </th>

                <th style='width: 1%;'>

                    <?php echo $this->translate("Enabled?") ?>

                </th>


                <th style='width: 1%;' class='admin_table_options'>
                    <?php echo $this->translate("Options") ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->paginator as $item): ?>
                <tr>
                    <td><?php echo $item->package_id ?></td>
                    <td class='admin_table_bold'>
                        <?php echo $item->title ?>
                    </td>

                    <td>
                        <?php echo $this->locale()->toNumber($item->price) ?>
                    </td>
                   
                    <td class='admin_table_centered'>
                        <?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') ) ?>
                    </td>



                    <td class='admin_table_options'>
                        <a href='<?php echo $this->url(array('action' => 'edit', 'package_id' => $item->package_id)) ?>'>
                            <?php echo $this->translate("edit") ?>
                        </a>|
                        <a href='<?php echo $this->url(array('action' => 'delete', 'package_id' => $item->package_id)) ?>' class="smoothbox">
                            <?php echo $this->translate("delete") ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>