<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('export_to_excel') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" id="from_items"
              action="<?php echo base_url(); ?>admin/purchase/export_to_excel/<?= $module ?>" method="post"
              class="form-horizontal form-groups-bordered">

            <input name="purchases_ids" type="text" hidden>
            <div class="form-group">
                <label
                        class="col-lg-3 control-label"><?= lang('fields') ?></label>
                <div class="col-lg-7">
                    <?php
                    if ($module == 'purchase') {
                        $purchaseFilterExcel = $this->purchase_model->get_purchase_fields_for_excel();
                    }
                    if (!empty($purchaseFilterExcel)) {
                        foreach ($purchaseFilterExcel as $e_Filter) { ?>
                            <label class="radio c-radio">
                                <input  type="checkbox" name="purchase_field_<?php echo $e_Filter['value'] ?>"
                                       value="<?= $e_Filter['value'] ?>">
                                <span class="fa fa-check"></span><?= $e_Filter['name'] ?></label>
                        <?php }
                        $show_custom_fields = custom_form_table(20, null);
                        $indx=0;
                        if (!empty($show_custom_fields)) {
                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                if (!empty($c_label)) { ?>
                                    <label class="radio c-radio">
                                        <input id="inlineradio10" type="checkbox" name="<?php echo $c_label ?>"
                                               value="<?= $c_label ?>">
                                        <span class="fa fa-check"></span><?= $c_label ?></label>
                                <?php }
                            }
                        }
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
                <button type="submit" class="btn btn-purple"><?= lang('export_to_excel') ?></button>
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
            $("input[name='purchases_ids']").val(join_selected_values);
        });
        $(".close_modal").click(function () {
            location.reload();
        });

        $('#myModal').on('hidden.bs.modal', function () {
            location.reload();
        })


        $('[name="purchase_status"]').change(function () {
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