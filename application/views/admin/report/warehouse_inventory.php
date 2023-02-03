<?= message_box('success'); ?>
<?= message_box('error');
$edited = can_action('39', 'edited');
//if (!empty($edited)) {
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <style type="text/css">
        .custom-bulk-button {
            display: initial;
        }

        .dt-buttons {
            display: none;

        }
    </style>
    <!-- ************** general *************-->
    <div class="panel panel-custom">
        <header class="panel-heading ">
            <div class="panel-title"><strong><?= lang('warehouse_inventory') ?></strong></div>
        </header>
        <div class="panel-body">

            <span data-toggle="tooltip" data-placement="top" title="" data-original-title="Change Quantity">
                <a href="<?php echo base_url('admin/report/edit_items_quantity')?>" class="btn btn-success  btn-xs" title="Edit" data-toggle="modal" data-placement="top" data-target="#myModal">
                    <span style="font-size: 16px;  font-weight: 600;"><?php echo lang('edit_item_quantity')?></span>
                </a>
            </span>


            <div class="table-responsive" style="margin-top: 25px;">
                <table class="table table-striped DataTables bulk_table" id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= lang('item') ?></th>
                        <?php
                        $invoice_view = config_item('invoice_view');
                        if (!empty($invoice_view) && $invoice_view == '2') {
                            ?>
                            <th><?= lang('hsn_code') ?></th>
                        <?php } ?>
                        <?php if (admin()) { ?>
                            <th class="col-sm-1"><?= lang('cost_price') ?></th>
                        <?php } ?>
                        <th class="col-sm-1"><?= lang('unit_price') ?></th>
                        <th class="col-sm-1"><?= lang('unit') ?></th>
                        <th class="col-sm-2"><?= lang('tax') ?></th>
                        <th class="col-sm-1"><?= lang('group') ?></th>
                        <th class="col-sm-1"><?= lang('warehouse') ?></th>
                        <th class="col-sm-1"><?= lang('quantity') ?></th>

                        <?php $show_custom_fields = custom_form_table(18, null);
                        if (!empty($show_custom_fields)) {
                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                if (!empty($c_label)) {
                                    ?>
                                    <th><?= lang($c_label) ?> </th>
                                <?php }
                            }
                        }
                        ?>
                        <?php if (!empty($edited)) { ?>
                            <th class="col-sm-1"><?= lang('action') ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/report/warehouse_inventoryList";
                            bulk_url = base_url + "admin/items/bulk_delete";
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
                                var search_type = $(this).attr('search-type');
                                if (search_type) {
                                    search_type = '/' + search_type;
                                } else {
                                    search_type = '';
                                }
                                table_url(base_url + "admin/report/warehouse_inventoryList/" + filter_by +
                                    search_type);
                            });
                        });
                    </script>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

