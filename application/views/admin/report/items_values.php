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
            <div class="panel-title"><strong><?= lang('items_values') ?></strong></div>
        </header>
        <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
             data-title="<?php echo lang('filter_by'); ?>">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu group animated zoomIn" style="width:300px;">
                <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                <li class="divider"></li>
                <li class="dropdown-submenu pull-left " id="by_category">
                    <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('warehouse'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left by_category" style="">
                        <?php
                        if (!empty($warehouseList)) { ?>
                            <?php foreach ($warehouseList as $warehouseId => $warehouseName) {
                                ?>
                                <li class="filter_by" id="<?= $warehouseId ?>" search-type="by_warehouse">
                                    <a href="#"><?php echo $warehouseName; ?></a>
                                </li>
                            <?php }
                            ?>
                            <div class="clearfix"></div>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
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
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/report/items_valuesList";
                            $('.filtered > .dropdown-toggle').on('click', function () {
                                if ($('.group').css('display') == 'block') {
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

