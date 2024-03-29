<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?php
                if ($this->session->userdata('user_type') == '1') {
                    ?>
                    <a style="margin-top: -5px;" href="<?= base_url() ?>admin/invoice/createinvoice/create_invoice"
                       data-original-title="<?= lang('new_invoice') ?>" data-toggle="tooltip" data-placement="top"
                       class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i
                                class="fa fa-plus"></i></a>
                <?php } ?>
                <?= lang('all_invoices') ?>
            </div>

            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php

                            if (!empty($all_invoices_info)) {
                                $all_invoices_info = array_reverse($all_invoices_info);
                                foreach ($all_invoices_info as $v_invoices) {
                                    if ($this->invoice_model->get_payment_status($v_invoices->invoices_id) == lang('fully_paid')) {
                                        $invoice_status = lang('fully_paid');
                                        $label = "success";
                                    } elseif ($v_invoices->emailed == 'Yes') {
                                        $invoice_status = lang('sent');
                                        $label = "info";
                                    } else {
                                        $invoice_status = lang('draft');
                                        $label = "default";
                                    }
                                    ?>
                                    <li class="<?php
                                    if ($v_invoices->invoices_id == $this->uri->segment(5)) {
                                        echo "active";
                                    }
                                    ?>">
                                        <?php
                                        $client_info = $this->invoice_model->check_by(array('client_id' => $v_invoices->client_id), 'tbl_client');
                                        if (!empty($client_info)) {
                                            $client_name = $client_info->name;
                                        } else {
                                            $client_name = '-';
                                        }
                                        ?>
                                        <a
                                                href="<?= base_url() ?>admin/invoice/manage_invoice/payment/<?= $v_invoices->invoices_id ?>">
                                            <?= $client_name ?>
                                            <div class="pull-right">
                                                <?php $currency = $this->invoice_model->client_currency_symbol($v_invoices->client_id);
                                                if (empty($currency)) {
                                                    $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                                }
                                                ?>
                                                <?= display_money($this->invoice_model->get_invoice_cost($v_invoices->invoices_id), $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-muted"><?= $v_invoices->reference_no ?> <span
                                                        class="label label-<?= $label ?>"><?= $invoice_status ?></span>
                                            </small>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>

                    </div>
                </section>
            </div>
        </div>
    </div>
    <section class="col-sm-9">
        <?= message_box('error') ?>
        <!-- Start create invoice -->
        <section class="panel panel-custom">
            <header class="panel-heading"><?= lang('pay_invoice') ?></header>
            <div class="panel-body">
                <form method="post" data-parsley-validate="" novalidate="" id="myform"
                      action="<?= base_url() ?>admin/invoice/get_payment/<?= $invoice_info->invoices_id ?>"
                      class="form-horizontal">
                    <?php $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
                    if (empty($currency)) {
                        $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    }
                    ?>
                    <input type="hidden" name="currency" value="<?= $currency->symbol ?>">

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('trans_id') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <?php $this->load->helper('string'); ?>
                            <input type="text" class="form-control" value="<?= random_string('nozero', 6); ?>"
                                   name="trans_id" readonly>
                        </div>
                    </div>
                    <!--                    <div class="form-group">-->
                    <!---->
                    <!--                        <label class="col-lg-3 control-label ">-->
                    <?php //echo  lang('amount') ?><!-- (--><?php //echo $currency->symbol ?><!--) <span-->
                    <!--                                class="text-danger">*</span></label>-->
                    <!--                        <div class="col-lg-6">-->
                    <input type="text" style="display: none" class="form-control amount_value"
                           value="<?php echo round($this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id), 2) ?>"
                           name="amount_value">
                    <!--                        </div>-->
                    <!--                    </div>-->

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('payment_date') ?></label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text" required="" name="payment_date" class="form-control datepicker"
                                       value="<?php
                                       if (!empty($payment_info->payment_date)) {
                                           echo $payment_info->payment_date;
                                       } else {
                                           echo date('Y-m-d');
                                       }
                                       ?>"
                                       data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('payment_method') ?> <span
                                    class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <select class="form-control select_box" style="width: 100%" name="payment_methods_id">
                                    <option value="0"><?= lang('select_payment_method') ?></option>
                                    <?php
                                    $apayment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                    if (!empty($apayment_methods)) {
                                        foreach ($apayment_methods as $p_method) {
                                            ?>
                                            <option value="<?= $p_method->method_name ?>" <?php
                                            if (!empty($payment_info->payment_method)) {
                                                echo $payment_info->payment_method == $p_method->payment_methods_id ? 'selected' : '';
                                            }
                                            ?>>
                                                <?= $p_method->method_name ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('payment_method') ?>"
                                     data-toggle="tooltip" data-placement="top">
                                    <a data-toggle="modal" data-target="#myModal"
                                       href="<?= base_url() ?>admin/settings/inline_payment_method"><i
                                                class="fa fa-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('notes') ?></label>
                        <div class="col-lg-6">
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('send_email') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" class="custom-checkbox" name="send_thank_you">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('send') . ' ' . lang('sms') ?></label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" class="custom-checkbox" name="send_sms">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('save_into_default_account') ?>
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"
                               title="<?= lang('will_be_added_into_deposit') ?>"></i>
                        </label>
                        <div class="col-lg-6">
                            <div class="checkbox c-checkbox">
                                <label>
                                    <input type="checkbox" checked class="custom-checkbox" id="use_postmark"
                                           name="save_into_account">
                                    <span class="fa fa-check"></span></label>
                            </div>

                        </div>
                    </div>
                    <div id="postmark_config"
                        <?php echo (empty($payment_info->account_id)) ? 'style="display:block"' : '' ?>>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= lang('account') ?></label>
                            <div class="col-lg-5">
                                <?php
                                $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                if (!empty($account_info)) {
                                    foreach ($account_info as $v_account) : ?>
                                        <?= $v_account->account_name ?>
                                        <input type="text" min="0" class="form-control account_amount"
                                               value=""
                                               name="<?= $v_account->account_id ?>">
                                    <?php endforeach;
                                }
                                ?>
                            </div>
                            <a data-toggle="modal" data-target="#myModal"
                               href="<?= base_url() ?>admin/account/new_account"><i
                                        class="fa fa-plus"></i><?= lang('add_new') ?></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3"></label>
                        <div class="col-lg-6">
                            <button type="submit" class="btn btn-primary"><?= lang('add_payment') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <?php
        if (!empty($all_payments_history)) {
            $reference = ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id) . "' >" . $invoice_info->reference_no . "</a>";
            $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
            ?>
            <section class="panel panel-custom ">
                <header class="panel-heading pb-sm"><?= lang('payment_history_for_this_invoice', $reference) ?></header>
                <div class="panel-body table-responsive">
                    <table class="table table-striped" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('trans_id') ?></th>
                            <th><?= lang('payment_date') ?></th>
                            <th><?= lang('paid_amount') ?></th>
                            <th><?= lang('payment_method') ?></th>
                            <th><?= lang('account') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($all_payments_history as $v_payment_history) {
                            $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
                            if (empty($currency)) {
                                $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                            if (is_numeric($v_payment_history->payment_method)) {
                                $payment_methods = $this->invoice_model->check_by(array('payment_methods_id' => $v_payment_history->payment_method), 'tbl_payment_methods');
                            } else {
                                $payment_methods->method_name = $v_payment_history->payment_method;
                            }
                            $account = '';
                            $accounts = json_decode($v_payment_history->account_id);
                            if (is_array($accounts))
                                foreach ($accounts as $i => $a) {
                                    $account .= get_row('tbl_accounts', array('account_id' => $a), 'account_name');
                                    if ($i != sizeof($accounts) - 1)
                                        $account .= ' , ';
                                }
                            else
                                $account = get_row('tbl_accounts', array('account_id' => $v_payment_history->account_id), 'account_name');


                            ?>
                            <tr>
                                <td>
                                    <a
                                            href="<?= base_url() ?>admin/invoice/manage_invoice/payments_details/<?= $v_payment_history->payments_id ?>">
                                        <?= $v_payment_history->trans_id ?></a>
                                </td>
                                <td>
                                    <?= strftime(config_item('date_format'), strtotime($v_payment_history->payment_date)) ?>
                                </td>
                                <td>
                                    <span><?= display_money($v_payment_history->amount, $currency->symbol); ?></span>
                                </td>
                                <td>
                                    <span><?= !empty($payment_methods->method_name) ? $payment_methods->method_name : '-'; ?></span>
                                </td>
                                <td>
                                    <span><?= !empty($account) ? $account : '-'; ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>
                <footer class="panel-footer no-padder">
                    <div class="row text-center no-gutter">
                        <div class="col-xs-4 b-r b-light">
                        <span
                                class="h4 font-bold m-t block"><?= display_money($this->invoice_model->calculate_to('total', $invoice_info->invoices_id), $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('invoice_amount') ?></small>
                        </div>
                        <div class="col-xs-4 b-r b-light">
                        <span
                                class="h4 font-bold m-t block"><?= display_money($this->invoice_model->calculate_to('paid_amount', $invoice_info->invoices_id), $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('paid_amount') ?></small>
                        </div>
                        <div class="col-xs-4">
                        <span
                                class="h4 font-bold m-t block"><?= display_money($invoice_due, $currency->symbol) ?></span>
                            <small class="text-muted m-b block"><?= lang('total') . ' ' . lang('due_amount') ?></small>

                        </div>
                    </div>
                </footer>
            </section>

        <?php } ?>
    </section>
</div>

<script>

    $('.account_amount').change(function () {
        var tot = 0;
        $(".account_amount").each(function () {
            tot += Number($(this).val());
            // Could be written as
            // tot += +this.value;
        });
        if (tot > $('.amount_value').val())
            toastr.warning("<?php echo lang('overpaid_amount') ?>");
    });
    //
    // $('form#myform :input').change(function() {
    //     var tot = 0;
    //     $("form#lines-form-1 :input").each(function() {
    //         tot += Number($(this).val());
    //         // Could be written as
    //         // tot += +this.value;
    //     });
    //     $('#tot-qty').text(tot);
    // });
</script>

<!-- end -->