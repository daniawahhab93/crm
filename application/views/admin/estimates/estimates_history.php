<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <a style="margin-top: -5px" href="<?= base_url() ?>admin/estimates/create/edit_estimates" data-original-title="<?= lang('new_estimate') ?>" data-toggle="tooltip" data-placement="top" class="btn btn-icon btn-<?= config_item('button_color') ?> btn-sm pull-right"><i class="fa fa-plus"></i></a>
                <?= lang('all_estimates') ?>
            </div>
            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                        <ul class="nav"><?php
                                        if (!empty($all_estimates_info)) {
                                            foreach ($all_estimates_info as $key => $v_estimate) {
                                                if ($v_estimate->invoiced == 'Yes') {
                                                    $invoice_status = 'INVOICED';
                                                    $label = 'success';
                                                } elseif ($v_estimate->emailed == 'Yes') {
                                                    $invoice_status = 'SENT';
                                                    $label = 'info';
                                                } else {
                                                    $invoice_status = 'DRAFT';
                                                    $label = 'default';
                                                }
                                        ?>
                                    <li class="    <?php
                                                    if ($v_estimate->estimates_id == $this->uri->segment(3)) {
                                                        echo "active";
                                                    }
                                                    ?>"><a href="<?= base_url() ?>admin/estimates/create/estimates_details/<?= $v_estimate->estimates_id ?>">

                                            <?php if ($v_estimate->client_id == '0') { ?>
                                                <span class="label label-success">General Estimate</span>
                                            <?php
                                                } else {
                                                    $client_info = $this->estimates_model->check_by(array('client_id' => $estimates_info->client_id), 'tbl_client');
                                            ?>
                                                <?= ucfirst($client_info->name) ?>
                                            <?php } ?>
                                            <div class="pull-right">
                                                <?php $currency = $this->estimates_model->client_currency_symbol($estimates_info->client_id); ?>
                                                <?= display_money($this->estimates_model->estimate_calculation('estimate_amount', $estimates_info->estimates_id), $currency->symbol) ?>
                                            </div> <br>
                                            <small class="block small text-muted"><?= $v_estimate->reference_no ?> <span class="label label-<?= $label ?>"><?= $invoice_status ?></span></small>

                                        </a> </li>
                            <?php
                                            }
                                        }
                            ?>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <section class="col-sm-9">
        <div class="row">

            <!-- Timeline START -->
            <section class="panel panel-custom">
                <div class="panel-body " id="chat-box">
                    <?php
                    $activities_info = $this->db->where(array('module' => 'estimates', 'module_field_id' => $estimates_info->estimates_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
                    if (!empty($activities_info)) {
                        foreach ($activities_info as $v_activities) {
                            $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                            $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                    ?>
                            <div class="timeline-2">
                                <div class="time-item">
                                    <div class="item-info">
                                        <small data-toggle="tooltip" data-placement="top" title="<?= display_datetime($v_activities->activity_date) ?>" class="text-muted"><?= time_ago($v_activities->activity_date); ?></small>

                                        <p><strong>
                                                <?php if (!empty($profile_info)) {
                                                ?>
                                                    <a href="<?= base_url() ?>admin/user/user_details/<?= $profile_info->user_id ?>" class="text-info"><?= $profile_info->fullname ?></a>
                                                <?php } ?>
                                            </strong> <?= sprintf(lang($v_activities->activity)) ?>
                                            <strong><?= $v_activities->value1 ?></strong>
                                            <?php if (!empty($v_activities->value2)) { ?>
                                        <p class="m0 p0"><strong><?= $v_activities->value2 ?></strong></p>
                                    <?php } ?>
                                    </p>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </section>
        </div>
    </section>
</div>


<!-- end -->