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

        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/invoice/export_to_excel/purchase"
           class="btn btn-success btn-xs ml-lg" style="margin: 10px"><?= lang('export_to_excel') ?></a>

    </ul>
    <div class="tab-content bg-white">
        <?php
        if (!empty($purchase_info)) {
            $purchase_id = $purchase_info->purchase_id;
        } else {
            $purchase_id = null;
        }
        ?>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
            <?php echo form_open(base_url('admin/purchase/save_purchase/' . $purchase_id), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>
            <div class="mb-lg purchase accounting-template">
                <div class="row">
                    <div class="col-sm-6 col-xs-12 br pv">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('reference_no') ?> <span
                                            class="text-danger">*</span></label>
                                <div class="col-lg-7">
                                    <input type="text" class="form-control" value="<?php
                                    if (!empty($purchase_info)) {
                                        echo $purchase_info->reference_no;
                                    } else {
                                        if (empty(config_item('proposal_number_format'))) {
                                            echo config_item('purchase_prefix');
                                        }
                                        if (config_item('increment_purchase_number') == 'FALSE') {
                                            $this->load->helper('string');
                                            echo random_string('nozero', 6);
                                        } else {
                                            echo $this->purchase_model->generate_purchase_number();
                                        }
                                    }
                                    ?>" name="reference_no">
                                </div>
                            </div>
                            <div class="f_supplier_id">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('supplier_name') ?> <span
                                                class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <select class="form-control select_box" style="width: 100%" id="supplier_id"
                                                    name="supplier_id" required="">
                                                <option value=""><?= lang('select') . ' ' . lang('supplier') ?>
                                                </option>
                                                <?php
                                                if (!empty($all_supplier)) {
                                                    foreach ($all_supplier as $v_supplier) {
                                                        if (!empty($purchase_info->supplier_id)) {
                                                            $supplier_id = $purchase_info->supplier_id;
                                                        }
                                                        ?>
                                                        <option value="<?= $v_supplier->supplier_id ?>" <?php
                                                        if (!empty($supplier_id)) {
                                                            echo $supplier_id == $v_supplier->supplier_id ? 'selected' : null;
                                                        }
                                                        ?>>
                                                            <?= $v_supplier->name ?></option>
                                                        <?php
                                                    }
                                                }
                                                $_created = can_action('151', 'created');
                                                ?>
                                            </select>
                                            <?php if (!empty($_created)) { ?>
                                                <div class="input-group-addon"
                                                     title="<?= lang('new_supplier') ?>"
                                                     data-toggle="tooltip" data-placement="top">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/supplier/new_supplier"><i
                                                                class="fa fa-plus"></i></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php $role = $this->session->userdata('user_type');
                            if ($role == 1) { ?>
                                <div class="form-group">
                                    <label
                                            class="col-lg-3 control-label"><?= lang('purchase_date') ?></label>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <input type="text" name="purchase_date" class="form-control datepicker"
                                                   value="<?php
                                                   if (!empty($purchase_info->purchase_date)) {
                                                       echo $purchase_info->purchase_date;
                                                   } else {
                                                       echo date('Y-m-d');
                                                   }
                                                   ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label
                                            class="col-lg-3 control-label"><?= lang('purchase_date') ?></label>
                                    <div class="col-lg-7">
                                        <div class="input-group">
                                            <input type="text" name="purchase_date" readonly class="form-control"
                                                   value="<?php
                                                   if (!empty($purchase_info->purchase_date)) {
                                                       echo $purchase_info->purchase_date;
                                                   } else {
                                                       echo date('Y-m-d');
                                                   }
                                                   ?>"
                                                   data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-lg-3 control-label"><?= lang('due_date') ?></label>
                                <div class="col-lg-7">
                                    <div class="input-group">
                                        <input type="text" name="due_date" class="form-control datepicker"
                                               value="<?php
                                               if (!empty($purchase_info->due_date)) {
                                                   echo $purchase_info->due_date;
                                               } else {
                                                   echo date('Y-m-d');
                                               }
                                               ?>"
                                               data-date-format="<?= config_item('date_picker_format'); ?>">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-calendar"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $this->load->view('admin/items/warehouselist') ?>
                            <?php
                            $permissionL = null;
                            if (!empty($purchase_info->permission)) {
                                $permissionL = $purchase_info->permission;
                            }
                            ?>
                            <?= get_permission(3, 7, $permission_user, $permissionL, ''); ?>


                            <?php
                            if (!empty($purchase_info)) {
                                $purchase_id = $purchase_info->purchase_id;
                            } else {
                                $purchase_id = null;
                            }
                            ?>
                            <?= custom_form_Fields(20, $purchase_id); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12 br pv">
                        <div class="row">
                            <div class="form-group">
                                <label for="field-1"
                                       class="col-sm-4 control-label"><?= lang('sales_agent') ?></label>
                                <div class="col-sm-7">
                                    <select class="form-control select_box" required style="width: 100%" name="user_id">
                                        <option value="">
                                            <?= lang('select') . ' ' . lang('sales') . ' ' . lang('agent') ?>
                                        </option>
                                        <?php
                                        $all_user = get_staff_details();
                                        if (!empty($all_user)) {
                                            foreach ($all_user as $v_user) {
                                                $profile_info = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                                if (!empty($profile_info)) {
                                                    ?>
                                                    <option value="<?= $v_user->user_id ?>" <?php
                                                    if (!empty($purchase_info->user_id)) {
                                                        echo $purchase_info->user_id == $v_user->user_id ? 'selected' : null;
                                                    } else {
                                                        echo $this->session->userdata('user_id') == $v_user->user_id ? 'selected' : null;
                                                    }
                                                    ?>>
                                                        <?= $profile_info->fullname ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="discount_type"
                                       class="control-label col-sm-4"><?= lang('update_stock') ?></label>
                                <div class="col-sm-7">
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="Yes"
                                               name="update_stock" <?php if (isset($purchase_info) && $purchase_info->update_stock == 'Yes') {
                                            echo 'checked';
                                        } elseif (empty($purchase_info)) {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('yes'); ?>
                                    </label>
                                    <label class="radio-inline c-radio">
                                        <input type="radio" value="No"
                                               name="update_stock" <?php if (isset($purchase_info) && $purchase_info->update_stock == 'No') {
                                            echo 'checked';
                                        } ?>>
                                        <span class="fa fa-circle"></span><?php echo lang('no'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="discount_type"
                                       class="control-label col-sm-4"><?= lang('discount_type') ?></label>
                                <div class="col-sm-7">
                                    <select name="discount_type" class="selectpicker" data-width="100%">
                                        <option value="" selected><?php echo lang('no') . ' ' . lang('discount'); ?>
                                        </option>
                                        <option value="before_tax" <?php
                                        if (isset($purchase_info)) {
                                            if ($purchase_info->discount_type == 'before_tax') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo lang('before_tax'); ?>
                                        </option>
                                        <option value="after_tax" <?php if (isset($purchase_info)) {
                                            if ($purchase_info->discount_type == 'after_tax') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo lang('after_tax'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="purchase_type"
                                       class="control-label col-sm-4"><?= lang('purchase_type') ?></label>
                                <div class="col-sm-7">
                                    <select name="purchase_type" class="form-control purchase_type" data-width="100%">
                                        <option value="internal" <?php
                                        if (isset($purchase_info)) {
                                            if ($purchase_info->purchase_type == 'internal') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo lang('internal'); ?> </option>
                                        <option value="external" <?php
                                        if (isset($purchase_info)) {
                                            if ($purchase_info->purchase_type == 'external') {
                                                echo 'selected';
                                            }
                                        } ?>><?php echo lang('external'); ?> </option>

                                    </select>
                                </div>
                            </div>
                            <div class="purchase_type_div">
                                <div class="form-group">
                                    <label for="purchase_tax" class="control-label col-sm-4"><?= lang('tax') ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" min="0" data-parsley-type="number" class="form-control"
                                               name="purchase_tax" id="purchase_tax"
                                               value="<?php if (isset($purchase_info)) {
                                                   echo $purchase_info->purchase_tax;
                                               } ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="customs_amount"
                                           class="control-label col-sm-4"><?= lang('customs_amount') ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" min="0" data-parsley-type="number" class="form-control"
                                               name="customs_amount" id="customs_amount"
                                               value="<?php if (isset($purchase_info)) {
                                                   echo $purchase_info->customs_amount;
                                               } ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_price"
                                           class="control-label col-sm-4"><?= lang('shipping_price') ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" min="0" data-parsley-type="number" class="form-control"
                                               name="shipping_price" id="shipping_price"
                                               value="<?php if (isset($purchase_info)) {
                                                   echo $purchase_info->shipping_price;
                                               } ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="exchange_rate"
                                           class="control-label col-sm-4"><?= lang('exchange_rate') ?></label>
                                    <div class="col-sm-7">
                                        <input type="text" min="0" data-parsley-type="number" class="form-control"
                                               name="exchange_rate" id="exchange_rate"
                                               value="<?php if (isset($purchase_info)) {
                                                   echo $purchase_info->exchange_rate;
                                               } ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tags" class="control-label col-sm-4"><?= lang('tags') ?></label>
                                <div class="col-sm-7">
                                    <input type="text" name="tags" data-role="tagsinput" class="form-control"
                                           value="<?php
                                           if (!empty($purchase_info->tags)) {
                                               echo $purchase_info->tags;
                                           }
                                           ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4"><?= lang('notes') ?> </label>
                                <div class="col-sm-7">
                                    <textarea name="notes" class="textarea"><?php
                                        if (!empty($purchase_info)) {
                                            echo $purchase_info->notes;
                                        } else {
                                            echo $this->config->item('purchase_notes');
                                        }
                                        ?></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <style type="text/css">
                .dropdown-menu > li > a {
                    white-space: normal;
                }

                .dragger {
                    background: url(<?= base_url() ?>assets/img/dragger.png) 10px 32px no-repeat;
                    cursor: pointer;
                }

                <?php if ( !empty($purchase_info)) {
                ?>.dragger {
                    background: url(<?= base_url() ?>assets/img/dragger.png) 10px 32px no-repeat;
                    cursor: pointer;
                }

                <?php
            }

            ?>.input-transparent {
                    box-shadow: none;
                    outline: 0;
                    border: 0 !important;
                    background: 0 0;
                    padding: 3px;
                }
            </style>
            <?php
            $pdata['itemType'] = 'purchase';
            if (!empty($purchase_info)) {

                $pdata['add_items'] = $this->purchase_model->ordered_items_by_id($purchase_info->purchase_id, true);
                $pdata['warehouseId'] = $purchase_info->warehouse_id;
            }
            $this->load->view('admin/items/selectItem', $pdata);
            $this->load->view('admin/items/selectItem2', $pdata);

            ?>
            <?php echo form_close(); ?>
        </div>
        <?php } else { ?>
    </div>
    <?php } ?>

    <script>
        $(document).ready(function () {
            $('.purchase_type_div').hide();
            if ($('.purchase_type').val() === 'external') {
                $('.purchase_type_div').show();
            } else {
                $('.purchase_type_div').hide();
            }

            $('.purchase_type').change(function () {
                if ($('.purchase_type').val() === 'external') {
                    $('.purchase_type_div').show();
                } else {
                    $('.purchase_type_div').hide();
                    $('.purchase_tax').val('');
                    $('.exchange_rate').val('');
                    $('.purchase_type').val('');
                    $('.shipping_price').val('');
                }
            });
        });
    </script>
