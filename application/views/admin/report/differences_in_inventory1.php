<?= message_box('success'); ?>
<?= message_box('error');
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
            <div class="panel-title"><strong><?= lang('differences_in_inventory') ?></strong></div>
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

                <li class="dropdown-submenu pull-left  " id="from_account">
                    <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('activity'); ?></a>
                    <ul class="dropdown-menu dropdown-menu-left from_account" style="">

                        <li class="filter_by" id="activity_edit_item_quantity_discount" search-type="by_activity">
                            <a href="#"><?php echo lang('activity_edit_item_quantity_discount'); ?></a>
                        </li>
                        <div class="clearfix"></div>
                        <li class="filter_by" id="activity_edit_item_quantity_add" search-type="by_activity">
                            <a href="#"><?php echo lang('activity_edit_item_quantity_add'); ?></a>
                        </li>
                        <div class="clearfix"></div>
                    </ul>
                </li>
                <div class="clearfix"></div>
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
                <table class="table table-striped DataTables bulk_table" id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= lang('item') ?></th>
                        <th class="col-sm-1"><?= lang('value1') ?></th>
                        <th class="col-sm-1"><?= lang('value2') ?></th>
                        <th class="col-sm-2"><?= lang('activity') ?></th>
                        <th class="col-sm-1"><?= lang('warehouse') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/report/differences_in_inventoryList";
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
                                table_url(base_url + "admin/report/differences_in_inventoryList/" + filter_by +
                                    search_type);
                            });
                        });
                    </script>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

