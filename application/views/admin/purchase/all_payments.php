<?= message_box('success') ?>
<?= message_box('error') ?>

<?php
$edited = can_action('152', 'edited');
$deleted = can_action('152', 'deleted');
?>

<div class="panel panel-custom">
    <header class="panel-heading ">
        <div class="panel-title"> <strong> <?= lang('all_payments') ?></strong></div>
    </header>
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?= lang('payment_date') ?></th>
                    <th><?= lang('purchase_date') ?></th>
                    <th><?= lang('purchase') ?></th>
                    <th><?= lang('supplier_name') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('payment_method') ?></th>
                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                    <th class="hidden-print"><?= lang('action') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <script type="text/javascript">
                list = base_url + "admin/purchase/paymentList";
                </script>
            </tbody>
        </table>
    </div>
</div>