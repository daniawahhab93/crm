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
        <tr>
            <td colspan="13" style="text-align: center; border: 1px solid #eee;">
                <strong><?php echo lang('no_data')?> </strong>
            </td>
        </tr>
        </tbody>
    </table>
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