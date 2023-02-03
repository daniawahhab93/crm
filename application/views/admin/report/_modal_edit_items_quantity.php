<style>
    .select2-container--bootstrap {
        width: 100% !important;
    }
</style>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('edit') . ' ' . lang('quantity') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form_validation" data-parsley-validate="" novalidate=""
              action="<?php echo base_url() ?>admin/report/set_items_quantity"
              method="post" class="form-horizontal form-groups-bordered">


            <div class="form-group">

                <label for="field-1" class="col-sm-4 control-label"> <?= lang('select_item') ?></label>
                <div class="col-sm-5">
                    <select class="form-control  select_box saved_items_id" id="saved_items_id" name="saved_item_id">
                        <?php
                        $all_items=$this->db
                            ->join('tbl_warehouses_products','tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id')
                             ->join('tbl_warehouse', 'tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id')
                            ->get('tbl_saved_items')->result();
                        if (!empty($all_items)) {
                            foreach ($all_items as $item) {
                                ?>
                                <option value="<?= $item->id?>"
                                    <?php
                                    if (!empty($saved_item_id)) {
                                        echo $saved_item_id == $item->id ? 'selected' : null;
                                    }
                                    ?>
                                ><?= ucfirst($item->item_name.' - '.  $item->warehouse_name) ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-top: 10px">
                <label for="field-1" class="col-sm-4 select-box control-label"><?= lang('edit_item_quantity') ?></label>
                <div class="col-sm-5">
                    <input type="text" name="quantity" id="quantity" required class="form-control"
                           value="<?= (!empty($quantity) ? $quantity : '') ?>"/>
                </div>

            </div>

            <input style="display: none" type="text" id="warehouse_id" name="warehouse_id" class="form-control"
                   value=""/>
            <input style="display: none" type="text" id="product_id" name="product_id" class="form-control"
                   value=""/>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>

<script>

    $("#saved_items_id").change(function () {
        $.ajax({
            url: "<?= base_url()?>admin/report/get_item_quantity",
            type: 'POST',
            dataType: "json",
            data: {saved_items_id: $("#saved_items_id").val()},
            success: function (data) {
                $("#quantity").val(data.quantity);
                $("#warehouse_id").val(data.warehouse_id);
                $("#product_id").val(data.saved_items_id);
                console.log( $("#warehouse_id").val());
            }
        })
    });

</script>
