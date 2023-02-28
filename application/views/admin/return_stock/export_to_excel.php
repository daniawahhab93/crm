<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('export_to_excel') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" id="from_items"
              action="<?php echo base_url(); ?>admin/return_stock/export_to_excel/<?= $module ?>" method="post"
              class="form-horizontal form-groups-bordered">

            <input name="return_stocks_ids" type="text" hidden>
            <div class="form-group">
                <label
                        class="col-lg-3 control-label"><?= lang('fields') ?></label>
                <div class="col-lg-7">
                    <?php
                    if ($module == 'return_stock') {
                        $return_stockFilterExcel = $this->return_stock_model->get_return_stock_fields_for_excel();
                    }
                    //                    if ($module == 'estimate') {
                    //                        $return_stockFilter = $this->estimates_model->get_return_stock_filter();
                    //                    }
                    //                    if ($module == 'credit_note') {
                    //                        $return_stockFilter = $this->credit_note_model->get_credit_note_filter();
                    //                    }
                    //                    if ($module == 'proposal') {
                    //                        $return_stockFilter = $this->proposal_model->get_return_stock_filter();
                    //                    }
                    //                    if ($module == 'payment') {
                    //                        $return_stockFilter = $this->return_stock_model->get_return_stock_payment();
                    //                    }
                    if (!empty($return_stockFilterExcel)) {
                        foreach ($return_stockFilterExcel as $e_Filter) { ?>
                            <label class="radio c-radio">
                                <input  type="checkbox" name="return_stock_field_<?php echo $e_Filter['value'] ?>"
                                       value="<?= $e_Filter['value'] ?>">
                                <span class="fa fa-check"></span><?= $e_Filter['name'] ?></label>
                        <?php }
                    }
                    ?>
                </div>
            </div>
            <div class="period">
                <div class="form-group">
                    <label
                            class="col-lg-3 control-label"><?= lang('from_date') ?></label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input class="form-control datepicker period" type="text"
                                   name="from_date"
                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label
                            class="col-lg-3 control-label"><?= lang('to_date') ?></label>
                    <div class="col-lg-7">
                        <div class="input-group">
                            <input class="form-control datepicker period" type="text"
                                   name="to_date"
                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($client_id)) {
                ?>
                <input type="hidden" name="client_id" value="<?= $client_id ?>">
            <?php } ?>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary close_modal" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-purple "><?= lang('export_to_excel') ?></button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#from_items").submit(function (e) {
            // store id of selected elements in allVals array
            var allVals = [];
            $(".crud_bulk_actions_row_checkbox:checked").each(function () {
                allVals.push($(this).attr('data-primary-key-value'));
            });
            // store id of selected elements in allVals array
            var join_selected_values = allVals.join(",");
            $("input[name='return_stocks_ids']").val(join_selected_values);

        });

        $(".close_modal").click(function () {
            location.reload();
        });

        $('#myModal').on('hidden.bs.modal', function () {
            location.reload();
        })


        $('[name="return_stock_status"]').change(function () {
            var val = $(this).val();
            var year = val.split('_');
            if (val == 'last_month' || val == 'this_months' || $.isNumeric(year[1])) {
                $('.period').hide().attr('disabled', 'disabled');
            } else {
                $('.period').show().removeAttr('disabled');
            }
        });
    });

</script>