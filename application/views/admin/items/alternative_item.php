<div class="bt">
    <div class="row prodcutTable" data-terget="<?= $itemType ?>">
        <div class="col-md-12">
            <div class="col-md-6 mt">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" placeholder="<?= lang('search_product_by_name_code'); ?>"
                               id="<?= $itemType ?>_item" class="form-control">
                    </div>
                </div>
            </div>

            <div class="table-responsive s_table">
                <table class="table invoice-items-table items">
                    <thead style="background: #e8e8e8">
                    <tr>
                        <th></th>
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
                        <th class="qty col-sm-1"><?php echo $qty_heading; ?></th>
                        <th class="col-sm-2"><?= lang('price') ?></th>
                        <th class="hidden-print"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody id="<?= $itemType ?>Table">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
if (!empty($add_items)) {
    if (empty($warehouseId)) {
        $warehouseId = '0';
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            store('<?= $itemType; ?>Items', JSON.stringify(<?= $add_items; ?>));
        });
    </script>
    <?php
} else { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            remove('<?= $itemType; ?>Items');
        });
    </script>
<?php } ?>
<?php include_once 'assets/js/item.php'; ?>