<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$created = can_action('187', 'created');
$edited = can_action('187', 'edited');
$deleted = can_action('187', 'deleted');
?><?php if (!empty($created) && !empty($edited)) { ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="active"><a
                href="<?= base_url('admin/items/transferItem') ?>"><?= lang('all_transferItem') ?></a>
        </li>

        <li class=""><a
                href="<?= base_url('admin/items/createTransferItem') ?>"><?= lang('new_transferItem') ?></a>
        </li>

    </ul>
    <div class="tab-content bg-white">
        <?php } else { ?>
        <div class="panel panel-custom">
            <header class="panel-heading ">
                <div class="panel-title"><strong><?= lang('all_transferItem') ?></strong></div>
            </header>
            <?php } ?>
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?= lang('reference_no') ?></th>
                            <th><?= lang('date') ?></th>
                            <th><?=  lang('FROM').' '.lang('warehouse')   ?></th>
                            <th><?=  lang('TO').' '.lang('warehouse')  ?></th>

                            <th><?= lang('total_price') ?></th>
                            <th><?= lang('status') ?></th>
                            <?php if (!empty($edited) && !empty($deleted)) { ?>
                            <th class=""><?= lang('action') ?></th>
                            <?php } ?>

                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <script type="text/javascript">
                    list = base_url + "admin/items/transferItemList";
                    </script>
                </table>
            </div>
        </div>
    </div>