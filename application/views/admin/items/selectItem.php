<!--<a data-toggle="collapse" href="#search_product"-->
<!--   style="text-decoration: underline blink #5d9cec 1px; font-weight: 600;     font-size: 16px;">-->
<!--    --><?php //echo lang('search_product') ?><!--<i class="fa fa-search"></i>-->
<!--</a>-->
<a data-toggle="modal" data-target="#searchProductModal" href="#" style="text-decoration: underline blink #5d9cec 1px; font-weight: 600;     font-size: 16px;">
    <?php echo lang('search_product') ?><i class="fa fa-search"></i>
</a>

<div id="searchProductModal" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 1200px;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"> <?php echo lang('search_product') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row prodcutTable" data-terget="<?= $itemType ?>">
                    <div class="col-md-12" style="padding-left: 9px;">
                        <div class="col-md-7 mt">
                            <address class="row">
                                <div class="col-md-4">
                                    <label><b><?= lang('code_contain') ?></b></label>
                                    <input type="text" name="code_contain" placeholder="<?= lang('code_contain'); ?>"
                                           id="<?= $itemType ?>_code_contain" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label><b><?= lang('group_contain') ?></b></label>
                                    <input type="text" name="group_contain" placeholder="<?= lang('group_contain'); ?>"
                                           id="<?= $itemType ?>_group_contain" class="form-control" value="">
                                </div>
                                <div class="col-md-4">
                                    <label><b><?= lang('desc_contain') ?></b></label>
                                    <input type="text" name="desc_contain" placeholder="<?= lang('desc_contain'); ?>"
                                           id="<?= $itemType ?>_desc_contain" class="form-control" value="">
                                </div>
                                <!--                            <div class="col-md-1">-->
                                <!--                                <button class="btn btn-purple" type="submit">-->
                                <!--                                    <i class="fa fa-search"></i>-->
                                <!--                                </button>-->
                                <!--                            </div>-->
                            </address>

                            <!--                <div class="form-group">-->
                            <!--                    <div class="input-group">-->
                            <!--                        <div class="input-group-addon" title="-->
                            <? //= lang('search_product_by_name_code') ?><!--">-->
                            <!--                            <i class="fa fa-barcode"></i>-->
                            <!--                        </div>-->
                            <!--                        <input type="text" placeholder="-->
                            <? //= lang('search_product_by_name_code'); ?><!--"-->
                            <!--                               id="--><? //= $itemType ?><!--_item" class="form-control">-->
                            <!---->
                            <!--                        <div class="input-group-addon" title="-->
                            <? //= lang('add') . ' ' . lang('manual') ?><!--"-->
                            <!--                             data-toggle="tooltip" data-placement="top">-->
                            <!--                            <a data-toggle="modal" data-target="#myModal_lg"-->
                            <!--                               href="-->
                            <? //= base_url() ?><!--admin/items/manuallyItems"><i class="fa fa-plus"></i></a>-->
                            <!--                        </div>-->
                            <!--                    </div>-->
                            <!--                </div>-->
                        </div>

                        <div class="col-md-5 pull-right">
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo lang('show_quantity_as'); ?></label>
                                <div class="col-sm-8">
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="qty" id="<?php echo lang('qty'); ?>"
                                               name="show_quantity_as" <?php if (isset($items_info) && $items_info->show_quantity_as == 'qty') {
                                            echo 'checked';
                                        } else if (!isset($hours_quantity) && !isset($qty_hrs_quantity)) {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('qty'); ?>
                                    </label>
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="hours" id="<?php echo lang('hours'); ?>"
                                               name="show_quantity_as" <?php if (isset($items_info) && $items_info->show_quantity_as == 'hours' || isset($hours_quantity)) {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('hours'); ?></label>
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="qty_hours" id="<?php echo lang('qty') . '/' . lang('hours'); ?>"
                                               name="show_quantity_as" <?php if (isset($items_info) && $items_info->show_quantity_as == 'qty_hours' || isset($qty_hrs_quantity)) {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('qty') . '/' . lang('hours'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive s_table">
                            <table class="table invoice-items-table items">
                                <thead style="background: #e8e8e8">
                                <tr>
                                    <!--                        <th></th>-->
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
                                <tbody id="<?= $itemType ?>Table">

                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close');?></button>
            </div>
        </div>

    </div>

</div>
<div id="removed-items"></div>
	

<?php
if (!empty($add_items)) {
    if (empty($warehouseId)) {
        $warehouseId = '0';
    }
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            store('<?= $itemType; ?>Items', JSON.stringify(<?= $add_items; ?>));
            store('<?= $itemType; ?>Warehouse', JSON.stringify(<?= $warehouseId; ?>));
        });
    </script>
    <?php
} else { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            remove('<?= $itemType; ?>Items');
            remove('<?= $itemType; ?>Warehouse');
        });
    </script>
<?php } ?>


<?php include_once 'assets/js/product.php'; ?>
<script src="jquery-3.6.1.min.js"></script>
<script type="text/javascript">
         $(document).on('mousedown' , '.fa-plus', function(e){
    
    firstHTML = $(this).parent().html();
	key = $(this).attr('data-key');
	//firstHTML = '<tr>'+ firstHTML + '</tr>';
    // console.log(firstHTML);
	 //console.log(key);
	// $('#tableInvoice').append(firstHTML);
    //  console.log(firstHTML);

   
    });
    </script>