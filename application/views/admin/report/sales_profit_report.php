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
                <?php echo form_open(base_url() . 'admin/report/sales_report/' . $filterBy); ?>

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
                <thead>
                <tr>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('item') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('warehouse') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('invoice_date') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('reference_no') ?></div>
                        <!---->
        </div>
        </th>

        <th class="text-left">
            <div class="pull-left "> <?= lang('client_name') ?></div>
        </th>
        <th class="text-left">
            <div class="pull-left "> <?= lang('client_mobile') ?></div>
        </th>
<!--        <th class="text-left">-->
<!--            <div class="pull-left ">--><?//= lang('username') ?><!--</div>-->
<!--        </th>-->

        <th class="text-right">
            <div class=" "> <?= lang('cost_price') ?></div>
        </th>
        <th class="text-right">
            <div class=" "> <?= lang('unit_price') ?></div>
        </th>

        <th class="text-right">
            <div class=" "> <?= lang('profit_amount') ?></div>
        </th>
        <th class="text-right">
            <div class=" "> <?= lang('tax_amount') ?></div>
        </th>
        </tr>
        </thead>

        <tbody>

        <?php
        $due_total = 0;
        $invoice_total = 0;
        $total_tax = 0;
        $profit_total = 0;

        if (!empty($all_invoices)) {
            foreach ($all_invoices as $key => $invoice) {
                $status = $this->invoice_model->get_payment_status($invoice->invoices_id);
                $text_color = 'info';
                switch ($status) {
                    case lang('fully_paid'):
                        $text_color = 'success';
                        break;
                    case lang('partially_paid'):
                        $text_color = 'warning';
                        break;
                    case lang('not_paid'):
                        $text_color = 'danger';
                        break;

                }
                $invoice_total += $invoice->unit_cost;
                $due_total += $invoice->cost_price;
                $tax_amount = $this->invoice_model->get_invoice_tax_amount($invoice->invoices_id);
//                $profit_amount = ($invoice->unit_cost * $invoice->quantity) - ($invoice->cost_price * $invoice->quantity);
                $profit_amount = ($invoice->unit_cost ) - ($invoice->cost_price);
//                var_dump($invoice->unit_cost ,$invoice->cost_price,$invoice->quantity,($invoice->unit_cost * $invoice->quantity) , ($invoice->cost_price * $invoice->quantity));
//                die;
                $profit_total += $profit_amount;
                $total_tax +=  $invoice->tax;
                ?>
                <tr>
                    <td><?= $invoice->item_name; ?></td>
                    <td><?= $invoice->warehouse_name; ?></td>
                    <td><?= display_date($invoice->invoice_date); ?></td>
                    <td>
                        <a class="hidden-print"
                           href="<?= base_url() ?>admin/invoice/manage_invoice/invoice_details/<?= $invoice->invoices_id ?>"><?= $invoice->reference_no ?></a>
                        <span class="show_print"><?= $invoice->reference_no ?></span>
                    </td>
                    <td>
                        <a class="hidden-print"
                           href="<?= base_url() ?>admin/client/client_details/<?= $invoice->client_id ?>"><?= client_name($invoice->client_id) ?></a>
                        <span class="show_print"><?= client_name($invoice->client_id) ?></span>
                    </td>
                    <td><?= client_mobile($invoice->client_id) ?></td>
<!--                    <td>--><?php //echo $invoice->username; ?><!--</td>-->


                    <td class="text-right">
                        <?= display_money($invoice->cost_price, $cur->symbol); ?></td>
<!--                    <td class="text-right">-->
                    <td class="text-right">
                        <?= display_money($invoice->unit_cost, $cur->symbol); ?></td>
                    <td class="text-right">
                        <?= display_money($profit_amount, $cur->symbol); ?></td>
                    <td class="text-right">
                        <?= display_money($tax_amount, $cur->symbol); ?></td>
                </tr>
            <?php } ?>

            <tr class="hover-muted bt">
                <td colspan="6"><?= lang('total') ?></td>
                <td class="text-right"><?= display_money($due_total, $cur->symbol) ?></td>
                <td class="text-right"><?= display_money($invoice_total, $cur->symbol) ?></td>
                <td class="text-right"><?= display_money($profit_total, $cur->symbol) ?></td>
                <td class="text-right"><?= display_money($total_tax, $cur->symbol) ?></td>
            </tr>

        <?php } else { ?>
            <tr>
                <td colspan="13" style="text-align: center;">
                    <strong><?php echo lang('no_data_to_display') ?> </strong>
                </td>
            </tr>
        <?php } ?>
        <!----></tbody>
        </table>
    </div>


    <!-- Script -->
    <script type="text/javascript">
        $(document).ready(function () {
            $("#client_id").select2({
                ajax: {
                    url: "<?= base_url()?>admin/report/getClients",
                    type: 'GET',
                    dataType: "json",
                    processResults: function (data, params) {
                        // you should map the id and text attributes on version 4.0

                        var select2Data = $.map(data, function (obj) {
                            obj.id = obj.client_id;
                            obj.text = obj.name;
                            return obj;
                        });

                        return {
                            results: select2Data,
                        };
                    }, cache: true

                }

            });
            // $('#client_id').select2().trigger('change');

            $("#saved_items_id").select2({
                ajax: {
                    url: "<?= base_url()?>admin/report/getItems",
                    type: 'GET',
                    dataType: "json",
                    processResults: function (data, params) {
                        // you should map the id and text attributes on version 4.0

                        var select2Data = $.map(data, function (obj) {
                            obj.id = obj.saved_items_id;
                            obj.text = obj.name;
                            return obj;
                        });
                        return {
                            results: select2Data,
                        };
                    }, cache: true
                },
            });
            // $('#saved_items_id').select2().trigger('change');


            $("#user_id").select2({

                ajax: {
                    url: "<?= base_url()?>admin/report/getUsers",
                    type: 'GET',
                    dataType: "json",
                    processResults: function (data, params) {
                        // you should map the id and text attributes on version 4.0

                        var select2Data = $.map(data, function (obj) {
                            obj.id = obj.user_id;
                            obj.text = obj.name;
                            return obj;
                        });

                        return {
                            results: select2Data,
                        };
                    }, cache: true

                }
            });
        });
    </script>

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
