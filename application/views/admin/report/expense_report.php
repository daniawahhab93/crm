<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<div id="printReport">
    <div class="show_print">
        <div style="width: 100%; border-bottom: 2px solid black;">
            <table style="width: 100%; vertical-align: middle;">
                <tr>
                    <td style="width: 50px; border: 0px;">
                        <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                             src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                    </td>
                    <td style="border: 0px;">
                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                    </td>
                
                </tr>
            </table>
        </div>
        <br/>
    </div>
    <div class="panel panel-custom">
        <div class="panel-heading hidden-print">
            <div class="panel-title">
                <strong><?= lang('expense_report') ?></strong>
                <div class="pull-right hidden-print">
                    <a href="<?php echo base_url() ?>admin/report/expense_report_pdf/" class="btn btn-xs btn-success"
                       data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                    <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger" data-toggle="tooltip"
                       data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <div class="hidden-print">
                <div class="criteria-band">
                    <address class="row" style="margin: 2px;">
                        <?php echo form_open(base_url() . 'admin/report/expense_report'); ?>
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
                        </address>
                        <address class="row">
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

            <h5><strong><?= lang('expense_summary') ?></strong></h5>
            <hr>
            <strong>
                <p><?= lang('total_expense') ?>: <?php
                    $where=array();
                    if($warehouse_id!='all')
                        $where=array('warehouse_id'=>$warehouse_id);
                    $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    $mdate = date('Y-m-d');
                    //first day of month
                    $first_day_month = date('Y-m-01');
                    //first day of Weeks
                    $this_week_start = date('Y-m-d', strtotime('previous sunday'));
                    // 30 days before
                    $before_30_days = date('Y-m-d', strtotime('today - 30 days'));
                    
                    $total_expense = $this->db->select_sum('debit')->where($where)->get('tbl_transactions')->row();
                    $this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->where($where)->select_sum('debit')->get('tbl_transactions')->row();
                    $this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->where($where)->select_sum('debit')->get('tbl_transactions')->row();
                    $this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->where($where)->select_sum('debit')->get('tbl_transactions')->row();
                    echo display_money($total_expense->debit, $curency->symbol);
                    ?></p>
                <p><?= lang('total_expense_this_month') ?>
                    : <?= display_money($this_month->debit, $curency->symbol) ?></p>
                <p><?= lang('total_expense_this_week') ?>
                    : <?= display_money($this_week->debit, $curency->symbol) ?></p>
                <p><?= lang('total_expense_last_30') ?>
                    : <?= display_money($this_30_days->debit, $curency->symbol) ?></p>
            </strong>
            <hr>
            
            <h4><?= lang('last_deposit_expense') ?></h4>
            <hr>
            <table class="table table-striped table-bordered ">
                <tbody>
                <tr>
                    <th><?= lang('date') ?></th>
                    <th><?= lang('account') ?></th>
                    <th><?= lang('warehouse') ?></th>
                    <th><?= lang('deposit_category') ?></th>
                    <th><?= lang('paid_by') ?></th>
                    <th><?= lang('description') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('debit') ?></th>
                    <th><?= lang('balance') ?></th>
                </tr>
                <?php
                $total_amount = 0;
                $total_credit = 0;
                $total_balance = 0;
                $all_deposit_info = $this->db->where(array('type' => 'Expense'))->where($where)->limit(20)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                
                foreach ($all_deposit_info as $v_deposit) :
                    $account_info = $this->report_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');
                    $client_info = $this->report_model->check_by(array('client_id' => $v_deposit->paid_by), 'tbl_client');
                    $warehouse_info = $this->report_model->check_by(array('warehouse_id' => $v_deposit->warehouse_id), 'tbl_warehouse');

                    $category_info = $this->report_model->check_by(array('expense_category_id' => $v_deposit->category_id), 'tbl_expense_category');
                    if (!empty($client_info)) {
                        $client_name = $client_info->name;
                    } else {
                        $client_name = '-';
                    }
                    ?>
                    <tr>
                        <td><?= strftime(config_item('date_format'), strtotime($v_deposit->date)); ?></td>
                        <td><?= !empty($account_info->account_name) ? $account_info->account_name : '-' ?></td>
                        <td><?= !empty($warehouse_info->warehouse_name) ? $warehouse_info->warehouse_name : '-' ?></td>
                        <td><?php
                            if (!empty($category_info)) {
                                echo $category_info->expense_category;
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?= $client_name ?></td>
                        <td><?= $v_deposit->notes ?></td>
                        <td><?= display_money($v_deposit->amount, $curency->symbol) ?></td>
                        <td><?= display_money($v_deposit->debit, $curency->symbol) ?></td>
                        <td><?= display_money($v_deposit->total_balance, $curency->symbol) ?></td>
                    </tr>
                    <?php
                    $total_amount += $v_deposit->amount;
                    $total_credit += $v_deposit->debit;
                    $total_balance += $v_deposit->total_balance;
                    ?>
                <?php
                endforeach;
                ?>
                <tr class="custom-color-with-td">
                    <td style="text-align: right;" colspan="6"><strong><?= lang('total') ?>:</strong></td>
                    <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
                    <td><strong><?= display_money($total_credit, $curency->symbol) ?></strong></td>
                    <td><strong><?= display_money($total_balance, $curency->symbol) ?></strong></td>
                </tr>
                </tbody>
            </table>
            <hr>
        
        </div>
    </div>
</div>
<div class="panel panel-custom  hidden-print">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('income_report') . ' ' . lang('graph') . ' ' . date('F-Y') ?></strong>
        </div>
    </div>
    <div class="panel-body">
        <div id="morris-line"></div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        if (typeof Morris === 'undefined') return;

        var chartdata = [
            <?php foreach ($transactions_report as $days => $v_report){
            $total_expense = 0;
            $total_income = 0;
            $total_transfer = 0;
            foreach ($v_report as $Expense) {
                if ($Expense->type == 'Expense') {
                    $total_expense += $Expense->amount;
                }
            }
            ?>
            {
                y: "<?= $days ?>",
                expense: <?= $total_expense?>,
            },
            <?php }?>


        ];
        // Line Chart
        // -----------------------------------

        new Morris.Line({
            element: 'morris-line',
            data: chartdata,
            xkey: 'y',
            ykeys: ["expense"],
            labels: ["<?= lang('expense')?>"],
            lineColors: ["#f05050"],
            parseTime: false,
            resize: true
        });

    });

    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
