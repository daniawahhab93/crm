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
                <?php echo form_open(base_url() . 'admin/report/differences_in_inventory'); ?>

                <address class="row">
                    <div class="col-md-3">
                        <label><?= lang('select_warehouse') ?></label>
                        <select class="form-control" name="warehouse_id">
                            <option value="all" <?= ($warehouse_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_warehouse = get_result('tbl_warehouse');
                            if (!empty($all_warehouse)) {
                                foreach ($all_warehouse as $v_warehouse) {
                                    ?>
                                    <option value="<?= $v_warehouse->warehouse_id ?>"
                                        <?php
                                        if (!empty($warehouse_id)) {
                                            echo $warehouse_id == $v_warehouse->warehouse_id ? 'selected' : null;
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
                        <label><?= lang('select_activity') ?></label>
                        <select class="form-control" name="activity">
                            <option value="all" <?= ($activity == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <option
                                    value="activity_edit_item_quantity_discount" <?= ($activity == 'activity_edit_item_quantity_discount') ? 'selected="selected"' : ''; ?>><?= lang('activity_edit_item_quantity_discount') ?></option>
                            <option
                                    value="activity_edit_item_quantity_add" <?= ($activity == 'activity_edit_item_quantity_add') ? 'selected="selected"' : ''; ?>><?= lang('activity_edit_item_quantity_add') ?></option>
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
                        <div class="pull-left "> <?= lang('value1') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('value2') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('activity') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('warehouse') ?></div>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($items)) { ?>
                    <?php foreach ($items as $item){ ?>
                        <tr>
                            <td><?= $item->item_name ?></td>
                            <td><?= $item->value1 ?></td>
                            <td><?= $item->value2 ?></td>
                            <td><?= lang($item->activity) ?></td>
                            <td><?= $item->warehouse_name ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="12" style="text-align: center;">
                            <strong><?php echo lang('no_data_to_display')?> </strong>
                        </td>
                    </tr>
                <?php } ?>
                <!----></tbody>
            </table>
        </div>

