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
                <?php echo form_open(base_url() . 'admin/report/items_report'); ?>

                <address class="row">
                    <div class="col-md-3">
                        <label><?= lang('select_warehouse') ?></label>
                        <select class="form-control" name="warehouse_id">
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
                                        }else{
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
                        <div class="pull-left "> <?= lang('purchase_quantity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('sale_quantity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('returns_quantity') ?></div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($items)) {?>
                    <?php foreach ($items as $item_info) {
                        ?>
                        <tr>
                            <td><?= $item_info->item_name ?></td>
                            <td><?= $item_info->purchase_quantity ?></td>
                            <td><?= $item_info->sale_quantity ?></td>
                            <td><?= $item_info->returns_quantity ?></td>
                        </tr>
                    <?php } ?>
                <?php } elseif (!empty($itemInfo)) { ?>
                    <tr>
                        <td><?= $itemInfo->item_name ?></td>
                        <td><?= $itemInfo->purchase_quantity ?></td>
                        <td><?= $itemInfo->sale_quantity ?></td>
                        <td><?= $itemInfo->returns_quantity ?></td>
                    </tr>
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

