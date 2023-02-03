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
                <?php echo form_open(base_url() . 'admin/report/warehouses_report_details'); ?>

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
                        <label><?= lang('quantity') ?></label>
                        <select class="form-control" name="quantity">
                            <option value="all" <?= ($quantity == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <option
                                    value="available_quantity" <?= ($quantity == 'available_quantity') ? 'selected="selected"' : ''; ?>><?= lang('available_quantity') ?></option>
                            <option
                                    value="negative_quantity" <?= ($quantity == 'negative_quantity') ? 'selected="selected"' : ''; ?>><?= lang('negative_quantity') ?></option>
                             </select>
                    </div>
                    <div class="col-md-3">
                        <label><?= lang('cost_price') ?></label>
                        <select class="form-control" name="cost">
                            <option value="all" <?= ($cost == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <option
                                    value="negative_cost_price" <?= ($cost == 'negative_cost_price') ? 'selected="selected"' : ''; ?>><?= lang('negative_cost_price') ?></option>
                            </select>
                    </div>

<!--                    <div class="col-md-4">-->
<!--                        <label>--><?//= lang('date_range') ?><!--</label>-->
<!--                        <input type="text" name="range" id="reportrange"-->
<!--                               class="pull-right form-control">-->
<!--                        <i class="fa fa-calendar"></i>&nbsp;-->
<!--                        <span></span> <b class="caret"></b>-->
<!--                    </div>-->

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



    <div class="rep-container">
<!--        <div class="page-header text-center">-->
<!--            <h3 class="reports-headerspacing">--><?//= lang($filterBy) ?><!--</h3>-->
<!--            --><?php //if (!empty($start_date)) { ?>
<!--                <h5><span>--><?//= lang('FROM') ?><!--</span>&nbsp;--><?//= $start_date ?>
<!--                    &nbsp;<span>--><?//= lang('TO') ?><!--</span>&nbsp;--><?//= $end_date ?><!--</h5>-->
<!--            --><?php //} ?>
<!--        </div>-->

        <div class="fill-container">
            <table class="table zi-table table-hover norow-action small">
                <thead class="bg-items">
                <tr>
                    <th>#</th>
                    <th><?= lang('item')?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?=  lang('hsn_code'); ?></th>
                    <?php } ?>
                    <th><?php echo lang('qty'); ?></th>
                    <th><?php echo lang('warehouse'); ?></th>
                    <th class="col-sm-1"><?= lang('price'); ?> </th>
                </tr>
                </thead>

                <tbody>
                <?php
                if (!empty($items)) :
                    foreach ($items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->saved_items_id ?>">
                            <td class="item_no dragger pl-lg"><?= $key + 1 ?></td>
                            <td><strong class="block"><?= $item_name ?></strong>
                                <?= nl2br($v_item->item_desc) ?>
                            </td>
                            <?php
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                ?>
                                <td><?= $v_item->hsn_code ?></td>
                            <?php } ?>
                            <td><?= $v_item->quantity ?></td>
                            <td><?= $v_item->warehouse_name  ?></td>
                            <td><?= display_money($v_item->unit_cost) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8"><?= lang('nothing_to_display') ?></td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>


    </div>


    <script type="text/javascript">
        $('#reportrange').daterangepicker({
            autoUpdateInput: <?= !empty($start_date) ? 'true' : 'false'?>,
            locale: {
                format: 'MMMM D, YYYY'
            },
            <?php if(!empty($start_date)){?>
            startDate: '<?=$start_date?>',
            endDate: '<?=$end_date?>',
            <?php }?>
            "opens": "right",
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
        });

        $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    </script>