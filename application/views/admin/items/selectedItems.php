<div class="table-responsive s_table">
    <table class="table invoice-items-table selected_items">
        <thead style="background: #e8e8e8">
        <tr>
            <th><?= lang('item_name') ?></th>
            <th><?= lang('description') ?></th>
            <?php
            $invoice_view = config_item('invoice_view');
            if (!empty($invoice_view) && $invoice_view == '2') {
                ?>
                <th class="col-sm-2"><?= lang('hsn_code') ?></th>
            <?php } ?>
            <?php
            $qty_heading = lang('qty');
            if (isset($items_info) && $items_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                $qty_heading = lang('hours');
            } else if (isset($items_info) && $items_info->show_quantity_as == 'qty_hours') {
                $qty_heading = lang('qty') . '/' . lang('hours');
            }
            ?>
            <th class="col-sm-1"><?= lang('old_code') ?></th>
            <th class="col-sm-2"><?= lang('alternative_items') ?></th>
            <th><?= lang('quantity_in_warehouse') ?></th>
            <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
            <th class="col-sm-2"><?= lang('price') ?></th>
            <th class="col-sm-2"><?= lang('tax_rate') ?> </th>
            <th class="col-sm-1"><?= lang('total') ?></th>
            <?php if ($itemType == 'invoice') { ?>
                <th class="col-sm-1"><?= lang('last_sale_price') ?></th>
                <th class="col-sm-1"><?= lang('lowest_sale_price_item') ?></th>
            <?php } ?>
            <?php if ($itemType == 'purchase') { ?>
                <th class="col-sm-1"><?= lang('last_purchase_price_same_supplier') ?></th>
                <th class="col-sm-1"><?= lang('lowest_purchase_price') ?></th>
                <th class="col-sm-1"><?= lang('last_cost_price') ?></th>
                <th class="col-sm-1"><?= lang('lowest_cost_price') ?></th>
            <?php } ?>
            <th class="col-sm-1"><?= lang('item_location_in_stock') ?></th>
            <th class="hidden-print"><?= lang('action') ?></th>
        </tr>
        </thead>
        <tbody id="<?= $itemType ?>SelectedTable">
<!--        <tr>-->
<!--            <td colspan="13" style="text-align: center; border: 1px solid #eee;">-->
<!--                <strong>--><?php //echo lang('no_data')?><!-- </strong>-->
<!--            </td>-->
<!--        </tr>-->
        </tbody>
    </table>
</div>

            <div class="row">
                <div class="col-xs-8 pull-right">
                    <table class="table text-right">
                        <tbody>
                        <tr id="subtotal">
                            <td><span class="bold"><?php echo lang('sub_total'); ?> :</span>
                            </td>
                            <td class="subtotal">
                            </td>
                        </tr>
                        <tr id="discount_percent">
                            <?php
                            $adjustmentText = 'shipping_cost';
                            if ($itemType != 'transfer') {
                                $adjustmentText = 'adjustment';
                                ?>
                                <td>
                                    <div class="row">
                                        <div class="col-md-7">
                                                <span class="bold"><?php echo lang('discount'); ?>
                                                    (%)</span>
                                        </div>
                                        <div class="col-md-5">
                                            <?php
                                            $discount_percent = 0;
                                            if (isset($purchase_info)) {
                                                if ($purchase_info->discount_percent != 0) {
                                                    $discount_percent = $purchase_info->discount_percent;
                                                }
                                            } ?>
                                            <input type="text" data-parsley-type="number"
                                                   value="<?php echo $discount_percent; ?>"
                                                   class="form-control pull-left" min="0" max="100"
                                                   name="discount_percent">
                                        </div>
                                    </div>
                                </td>
                                <td class="discount_percent"></td>
                                <?php
                            } ?>

                        </tr>
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-7">
                                        <span class="bold"><?php echo lang($adjustmentText); ?></span>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" data-parsley-type="number"
                                               value="<?php if (isset($purchase_info)) {
                                                   echo $purchase_info->adjustment;
                                               } else {
                                                   echo 0;
                                               } ?>" class="form-control pull-left" name="adjustment">
                                    </div>
                                </div>
                            </td>
                            <td class="adjustment"></td>
                        </tr>
                        <tr>
                            <td><span class="bold"><?php echo lang('total'); ?> :</span>
                            </td>
                            <td class="total">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

<div class="btn-bottom-toolbar text-right">
    <input type="button" id="Preset" value="<?= lang('reset') ?>" name="update" class="btn btn-danger">
    <?php
    if (!empty($add_items)) { ?>
        <input type="hidden" name="isedit" value="1">
        <input type="submit" value="<?= lang('update') ?>" name="create" class="btn btn-primary">
        <button type="button" onclick="goBack()" class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
    <?php } else { ?>
        <input type="submit" value="<?= lang('save_as_draft') ?>" name="save_as_draft" class="btn btn-primary">
        <input type="submit" value="<?= lang('update') ?>" name="update" class="btn btn-success">
    <?php }
    ?>
</div>