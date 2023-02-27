<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('150', 'created');
$edited = can_action('150', 'edited');
$deleted = can_action('150', 'deleted');
if (!empty($created) || !empty($edited)) {
?>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-tagsinput/fm.tagator.jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
<?php include_once 'assets/admin-ajax.php'; ?>
<?php include_once 'assets/js/sales.php'; ?>


<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a
                href="<?= base_url('admin/purchase') ?>"><?= lang('purchase') ?></a>
        </li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a
                href="<?= base_url('admin/purchase/new_purchase') ?>"><?= lang('new_purchase') ?></a>
        </li>

        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/purchase/export_to_excel/purchase"
           class="btn btn-success btn-xs ml-lg" style="margin: 10px"><?= lang('export_to_excel') ?></a>

    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('all_purchase') ?></strong></div>
                </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th data-orderable="false">
                                    <div class="checkbox c-checkbox">
                                        <label class="needsclick">
                                            <input id="check_all" type="checkbox">
                                            <span class="fa fa-check"></span></label>
                                    </div>
                                </th>
                                <th><?= lang('reference_no') ?></th>
                                <th><?= lang('supplier_name') ?></th>
                                <th><?= lang('purchase_date') ?></th>
                                <th><?= lang('due_amount') ?></th>
                                <th><?= lang('status') ?></th>
                                <th><?= lang('tags') ?></th>
                                <?php $show_custom_fields = custom_form_table(20, null);
                                if (!empty($show_custom_fields)) {
                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                        if (!empty($c_label)) {
                                ?>
                                <th><?= $c_label ?> </th>
                                <?php }
                                    }
                                }
                                ?>
                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                <th class="col-options no-sort"><?= lang('action') ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <script type="text/javascript">
                            list = base_url + "admin/purchase/purchaseList";
                            </script>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {
        $(function() {
            //If check_all checked then check all table rows
            $("#check_all").on("click", function () {
                if ($("input:checkbox").prop("checked")) {
                    $("input:checkbox[name='row-check']").prop("checked", true);
                } else {
                    $("input:checkbox[name='row-check']").prop("checked", false);
                }
            });

            // Check each table row checkbox
            $("input:checkbox[name='row-check']").on("click", function () {
                var total_check_boxes = $("input:checkbox[name='row-check']").length;
                var total_checked_boxes = $("input:checkbox[name='row-check']:checked").length;

                // If all checked manually then check check_all checkbox
                if (total_check_boxes === total_checked_boxes) {
                    $("#check_all").prop("checked", true);
                }
                else {
                    $("#check_all").prop("checked", false);
                }
            });
        });

        // init_items_sortable();

    });
    </script>