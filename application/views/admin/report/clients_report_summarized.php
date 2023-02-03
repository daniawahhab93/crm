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
                <?php echo form_open(base_url() . 'admin/report/clients_report_summarized'); ?>

                <address class="row">
                    <div class="col-md-6">
                        <label><?= lang('select_client_name_or_mobile') ?></label>
                        <select class="form-control client_id" id="client_id" name="client_id">
                            <option value="all" <?= ($client_id == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <?php
                            $all_client = get_result('tbl_client');
                            if (!empty($all_client)) {
                                foreach ($all_client as $v_client) {
                                    ?>
                                    <option value="<?= $v_client->client_id ?>"
                                        <?php
                                        if (!empty($client_id)) {
                                            echo $client_id == $v_client->client_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_client->name).' , mobile: '.$v_client->mobile ?></option>
                                    <?php
                                }
                            } ?>
                        </select>

                    </div>
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
                </address>

                <address class="row">
                    <div class="col-md-3">
                        <label><?= lang('select_item') . ' ' . lang('group') ?></label>
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


    <div class="panel panel-custom">
        <!--        <div class="page-header text-center">-->
        <?php if (!empty($start_date)) { ?>
            <h5><span><?= lang('FROM') ?></span>&nbsp;<?= $start_date ?>
                &nbsp;<span><?= lang('TO') ?></span>&nbsp;<?= $end_date ?></h5>
        <?php } ?>
        <!--        </div>-->

        <div class="panel-body table-responsive">
            <table class="table zi-table table-hover norow-action small">
                <thead>
                <tr>
                    <!--                    <th class="text-left">-->
                    <!--                        <div class="pull-left ">from id </div>-->
                    <!--                    </th>-->
                    <th class="text-left">
                        <div class="pull-left "><?= lang('from_date') ?></div>
                    </th>
                    <!--                    <th class="text-left">-->
                    <!--                        <div class="pull-left ">to id </div>-->
                    <!--                    </th>-->
                    <th class="text-left">
                        <div class="pull-left "><?= lang('to_date') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('total_invoice_amount') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('total_tax_amount') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('total_balance_due') ?></div>
                    </th>
        </div>

        </tr>
        </thead>

        <tbody>

        <?php
        $due_total = 0;
        $invoice_total = 0;
        $total_tax = 0;
        $first = reset($all_invoices);
        $last = end($all_invoices);
        if ($first && $last && $last->invoice_date < $first->invoice_date) {
            $first = end($all_invoices);
            $last = reset($all_invoices);
        }
        if (!empty($all_invoices)) {
            foreach ($all_invoices as $key => $invoice) {
                $invoice_payable = $this->invoice_model->invoice_payable($invoice->invoices_id);
                $invoice_total += $invoice_payable;
                $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice->invoices_id);
                $due_total += $invoice_due;
                $tax_amount = $this->invoice_model->get_invoice_tax_amount($invoice->invoices_id);
                $total_tax += $tax_amount;
            } ?>
            <tr>
                <td><?= $first->invoice_date ?></td>
                <td><?= $last->invoice_date ?></td>
                <td><?= display_money($invoice_total, $cur->symbol) ?></td>
                <td><?= display_money($total_tax, $cur->symbol) ?></td>
                <td><?= display_money($due_total, $cur->symbol) ?></td>
            </tr>

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


        <!-- Script -->
        <script type="text/javascript">
            $(document).ready(function () {
                $("#supplier_id").select2({
                    ajax: {
                        url: "<?= base_url()?>admin/report/getSuppliers",
                        type: 'GET',
                        dataType: "json",
                        processResults: function (data, params) {
                            // you should map the id and text attributes on version 4.0

                            var select2Data = $.map(data, function (obj) {
                                obj.id = obj.supplier_id;
                                obj.text = obj.name;
                                return obj;
                            });

                            return {
                                results: select2Data,
                            };
                        }, cache: true

                    }

                });
                // $('#supplier_id').select2().trigger('change');

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
