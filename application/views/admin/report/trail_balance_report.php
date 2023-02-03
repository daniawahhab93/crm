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
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('trail_balance_report') ?></strong>
            </div>
        </div>
        <?php
        $all_transaction_info = $this->db->get('tbl_transactions')->result();
        ?>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="width: 15%"><?= lang('name') ?></th>
                        <th style="width: 15%"><?= lang('date') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('credit') ?></th>
                        <th><?= lang('debit') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/report/transactions_reportList";
                            $('.filtered > .dropdown-toggle').on('click', function () {
                                if ($('.group').css('display') == 'block') {
                                    $('.group').css('display', 'none');
                                } else {
                                    $('.group').css('display', 'block')
                                }
                            });
                            $('.filter_by').on('click', function () {
                                $('.filter_by').removeClass('active');
                                $('.group').css('display', 'block');
                                $(this).addClass('active');
                                var filter_by = $(this).attr('id');
                                if (filter_by) {
                                    filter_by = filter_by;
                                } else {
                                    filter_by = '';
                                }
                                table_url(base_url + "admin/report/transactions_reportList/" + filter_by);
                            });
                        });
                    </script>

                    <?php
                    $total_amount = 0;
                    $total_debit = 0;
                    $total_credit = 0;
                    $total_balance = 0;
                    $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    if (!empty($all_transaction_info)): foreach ($all_transaction_info as $v_transaction) :
                        $total_debit += $v_transaction->debit;
                        $total_credit += $v_transaction->credit;
                        ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-footer">
            <strong class="col-sm-6"></strong>

            <strong class="col-sm-3"> <?= lang('credit') ?>:<span
                        class="label label-primary"><?= display_money($total_credit, $curency->symbol) ?></span></span>
            </strong>

            <strong class="width: 25%">
                <?= lang('debit') ?>:<span
                        class="label label-danger"><?= display_money($total_debit, $curency->symbol) ?></span></span>

                 </strong>
        </div>

    </div>
</div>
