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
            <address class="row"  style="margin: 2px;">
                <?php echo form_open(base_url() . 'admin/report/sales_report/' . $filterBy);  ?>

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
                        <label><?= lang('select_item') ?></label>
                        <select class="form-control saved_items_id" id="saved_items_id" name="saved_items_id">
                            <option value="all" <?= ($saved_items_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_item = get_result('tbl_saved_items');
                            if (!empty($all_item)) {
                                foreach ($all_item as $v_item) {
                                    ?>
                                    <option value="<?= $v_item->saved_items_id ?>"
                                        <?php
                                        if (!empty($saved_items_id)) {
                                            echo $saved_items_id == $v_item->saved_items_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_item->item_name) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label><?= lang('select_item_group') ?></label>
                        <select class="form-control" name="customer_group_id">
                            <option value="all" <?= ($customer_group_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_customer_group = get_result('tbl_customer_group');
                            if (!empty($all_customer_group)) {
                                foreach ($all_customer_group as $v_customer_group) {
                                    ?>
                                    <option value="<?= $v_customer_group->customer_group_id ?>"
                                        <?php
                                        if (!empty($customer_group_id)) {
                                            echo $customer_group_id == $v_customer_group->customer_group_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_customer_group->customer_group) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </address>

                <address class="row">
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
                    <div class="col-md-3">
                        <label><?= lang('status') ?></label>
                        <select class="form-control" name="status">
                            <option
                                    value="all" <?= ($status == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <option
                                    value="paid" <?= ($status == 'paid') ? 'selected="selected"' : ''; ?>><?= lang('paid') ?></option>
                            <option
                                    value="not_paid" <?= ($status == 'not_paid') ? 'selected="selected"' : ''; ?>><?= lang('not_paid') ?></option>
                            <option
                                    value="partially_paid" <?= ($status == 'partially_paid') ? 'selected="selected"' : ''; ?>><?= lang('partially_paid') ?></option>
                            <option
                                    value="cancelled" <?= ($status == 'cancelled') ? 'selected="selected"' : ''; ?>><?= lang('cancelled') ?></option>
                            <!--                            <option  value="draft" --><?php //echo ($status == 'draft') ? 'selected="selected"' : ''; ?><!-->--><?php //echo lang('draft') ?><!--</option>-->
                            <option
                                    value="overdue" <?= ($status == 'overdue') ? 'selected="selected"' : ''; ?>><?= lang('overdue') ?></option>
                            <!--                            <option  value="recurring" --><?php //echo ($status == 'recurring') ? 'selected="selected"' : ''; ?><!-->--><?php //echo  lang('recurring') ?><!--</option>-->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label><?= lang('date_range') ?></label>
                        <input type="text" name="range" id="reportrange"
                               class="pull-right form-control">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
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


    <div class="rep-container">
        <div class="page-header text-center">
            <h3 class="reports-headerspacing"><?= lang($filterBy) ?></h3>
            <?php if (!empty($start_date)) { ?>
                <h5><span><?= lang('FROM') ?></span>&nbsp;<?= $start_date ?>
                    &nbsp;<span><?= lang('TO') ?></span>&nbsp;<?= $end_date ?></h5>
            <?php } ?>
        </div>

        <div class="fill-container">
            <table class="table zi-table table-hover norow-action small">
                <thead class="bg-items">
                <tr>
                    <th>#</th>
                    <th><?= lang('item')?></th>
                    <th><?= lang('reference_no') ?></th>
                    <?php
                    $invoice_view = config_item('invoice_view');
                    if (!empty($invoice_view) && $invoice_view == '2') {
                        ?>
                        <th><?=  lang('hsn_code'); ?></th>
                    <?php } ?>
                    <th><?php echo lang('qty'); ?></th>
                    <th class="col-sm-1"><?= lang('price'); ?> </th>
                    <th class="col-sm-2"><?=  lang('tax'); ?></th>
                    <th class="col-sm-1"><?= lang('total'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php
                if (!empty($return_stock_items)) :
                    foreach ($return_stock_items as $key => $v_item) :
                        $item_name = $v_item->item_name ? $v_item->item_name : $v_item->item_desc;
                        $item_tax_name = json_decode($v_item->item_tax_name);
                        ?>
                        <tr class="sortable item" data-item-id="<?= $v_item->items_id ?>">
                            <td class="item_no dragger pl-lg"><?= $key + 1 ?></td>
                            <td><strong class="block"><?= $item_name ?></strong>
                                <?= nl2br($v_item->item_desc) ?>
                            </td>
                            <td><?= $v_item->reference_no ?></td>
                            <?php
                            $invoice_view = config_item('invoice_view');
                            if (!empty($invoice_view) && $invoice_view == '2') {
                                ?>
                                <td><?= $v_item->hsn_code ?></td>
                            <?php } ?>
                            <td><?= $v_item->quantity . '   &nbsp' . $v_item->unit ?></td>
                            <td><?= display_money($v_item->unit_cost) ?></td>
                            <td><?php
                                if (!empty($item_tax_name)) {
                                    foreach ($item_tax_name as $v_tax_name) {
                                        $i_tax_name = explode('|', $v_tax_name);
                                        echo '<small class="pr-sm">' . $i_tax_name[0] . ' (' . $i_tax_name[1] . ' %)' . '</small>' . display_money($v_item->total_cost / 100 * $i_tax_name[1]) . ' <br>';
                                    }
                                }
                                ?></td>
                            <td><?= display_money($v_item->total_cost, '', 2) ?></td>
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
