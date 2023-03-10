<div class="panel panel-custom">
    <!-- Default panel contents -->

    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        <div class="panel-title">
            <strong><?= lang('applied_credits') . ' ' . lang('list') ?></strong>
        </div>
    </div>
    <!-- Tabs within a box -->
    <div class="table-responsive">
        <table class="table table-striped DataTables" id="DataTables" width="100%" >
            <thead>
            <tr>
                <th>#</th>
                <th><?= lang('credit_note') ?></th>
                <th><?= lang('amount') . ' ' . ('credited') ?></th>
                <th><?= lang('date') ?></th>
                <th class="col-options no-sort"><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($all_credit_used)) {
                foreach ($all_credit_used as $v_credit_used):
                    $credit_info = get_row('tbl_credit_note', array('credit_note_id' => $v_credit_used->credit_note_id));
                    ?>
                    <tr id="table_reminder_<?= $v_credit_used->credit_used_id ?>">
                        <td>
                            <a href="<?= base_url('admin/credit_note/index/credit_note_details/' . $credit_info->credit_note_id) ?>"> <?= $credit_info->reference_no ?></a>
                        </td>
                        <td><?= display_date($v_credit_used->date) ?></td>
                        <td><?= display_money($v_credit_used->amount, default_currency()) ?></td>
                        <td>
                            <?php echo ajax_anchor(base_url("admin/invoice/delete/delete_applied_credits/" . $v_credit_used->invoices_id . '/' . $v_credit_used->credit_used_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_reminder_" . $v_credit_used->credit_used_id)); ?>
                        </td>
                    </tr>
                <?php
                endforeach;
            } else {
                ?>
                <tr>
                    <td colspan="5"><?= lang('nothing_to_display') ?></td>
                </tr>
            <?php }
            ?>
            </tbody>
        </table>
    </div>
</div>
