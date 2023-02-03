<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('edit') . ' ' . lang('quantity') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form_validation"  data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/report/set_item_quantity/<?php echo $saved_items_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('edit') . ' ' . lang('quantity') ?>
                    <span
                        class="required">*</span></label>
                <div class="col-sm-5">
                    <input   type="text" name="quantity" required class="form-control"
                        value="<?= (!empty($quantity) ? $quantity : '') ?>"/>

                    <input   style="display: none" type="text" name="warehouse_id" class="form-control"
                             value="<?= (!empty($warehouse_id) ? $warehouse_id : '') ?>"/>
                    <input   style="display: none" hidden type="text" name="product_id" class="form-control"
                             value="<?= (!empty($saved_items_id) ? $saved_items_id : '') ?>"/>
                </div>

            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
