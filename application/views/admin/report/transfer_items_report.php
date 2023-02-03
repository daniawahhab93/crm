<!-- Include Required Prerequisites -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<?php
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty($range[0])) {
    $start_date = date('F d, Y', strtotime($range[0]));
    $end_date = date('F d, Y', strtotime($range[1]));
}
$status = (isset($status)) ? $status : 'all';
?>


<div class="">
    <div class="hidden-print">
        <div class="criteria-band">
            <address class="row" style="margin: 2px;">
                <?php echo form_open(base_url() . 'admin/report/transfer_items_report'); ?>

                <address class="row">
                    <div class="col-md-3">
                        <label><?= lang('select_warehouse') ?></label>
                        <select class="form-control" name="warehouse_id">
                            <option value="all" <?= ($warehouse_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php

                            $all_warehouse = get_result('tbl_warehouse');
                            $warehouse_intialize = $all_warehouse[0]->warehouse_id; ?>
                            <?php
                            if (!empty($all_warehouse)) {
                                foreach ($all_warehouse as $v_warehouse) {
                                    ?>
                                    <option value="<?= $v_warehouse->warehouse_id ?>"
                                        <?php
                                        if (!empty($warehouse_id)) {
                                            echo $warehouse_id == $v_warehouse->warehouse_id ? 'selected' : '';
                                        } else {
                                            echo $warehouse_id == $warehouse_intialize;
                                        }
                                        ?>
                                    ><?= ucfirst($v_warehouse->warehouse_name) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label><?= lang('select_item') ?></label>
                        <select class="form-control select_box" name="saved_item_id">
                            <option value="all" <?= ($saved_item_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_items = get_result('tbl_saved_items');
                            if (!empty($all_items)) {
                                foreach ($all_items as $item) {
                                    ?>
                                    <option value="<?= $item->saved_items_id ?>"
                                        <?php
                                        if (!empty($saved_item_id)) {
                                            echo $saved_item_id == $item->saved_items_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($item->item_name) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label><?= lang('select_sales_agent') ?></label>
                        <select class="form-control user_id" id="user_id" name="user_id">
                            <option value="all" <?= ($user_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_users = get_result('tbl_users');
                            if (!empty($all_users)) {
                                foreach ($all_users as $v_user) {
                                    ?>
                                    <option value="<?= $v_user->user_id ?>"
                                        <?php
                                        if (!empty($user_id)) {
                                            echo $user_id == $v_user->user_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_user->username) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>

                </address>

                <address class="row">
                    <div class="col-md-5">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-purple" type="submit" style="padding: 8px 60px;">
                            <?= lang('run_report') ?>
                        </button>
                    </div>
                </address>
            </address>
        </div>
        </form>
    </div>


    <div class="panel panel-custom">
        <div class="panel-body table-responsive">
            <table class="table zi-table table-hover norow-action small">
                <thead>
                <tr>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('item_name') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('username') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('quantity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('transferred_quantity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('remaining_quantity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('FROM') . ' ' . lang('warehouse') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('TO') . ' ' . lang('warehouse') ?></div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($items)) { ?>
                    <?php foreach ($items as $item_info) {
                        $formmane = get_row('tbl_warehouse', array('warehouse_id' => $item_info->from_warehouse_id));
                        $tomane = get_row('tbl_warehouse', array('warehouse_id' => $item_info->to_warehouse_id));

                        ?>
                        <tr>
                            <td><?= $item_info->item_name ?></td>
                            <td><?= $item_info->username ?></td>
                            <td><?= $item_info->base_qty ?></td>
                            <td><?= $item_info->transfer_qty ?></td>
                            <td><?= $item_info->base_qty-$item_info->transfer_qty ?></td>
                            <td><?= $formmane->warehouse_name ?></td>
                            <td><?= $tomane->warehouse_name ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="12" style="text-align: center;">
                            <strong><?php echo lang('no_data_to_display') ?> </strong>
                        </td>
                    </tr>
                <?php } ?>
                <!----></tbody>
            </table>
        </div>

