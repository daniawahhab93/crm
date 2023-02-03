<div class="form-group">
    <label class="col-lg-3 control-label"><?= lang('select') . ' ' . lang('warehouse') ?> <span
                class="text-danger">*</span>
    </label>
            <?php
            $usertype = profile();
            if ($usertype->role_id == 3) {
                $warehouseList = $this->admin_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published', 'Warehouse_id' => $this->session->userdata['warehouses_ids']));
//                $selected = ($this->session->userdata['warehouse_id']);
            } else {
                $warehouseList = $this->admin_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published'));
//                $selected = (!empty($warehouseID) ? $warehouseID : '');
            }
            ?>
                <div class="col-lg-4">
                    <select name='warehouse_id' class="form-control select_box " data-live-search="true" required>
                        <?php
                        foreach ($warehouseList as $a_w) {
                            ?>
                            <?php $selected = '';
                            if ($usertype->warehouses_ids != null)
                                $warehouses_idssArray = json_decode($usertype->warehouses_ids, true);
                            if ($warehouses_idssArray && in_array($a_w->warehouse_id, $warehouses_idssArray))
                                $selected = 'selected';
                            ?>
                            <option value='<?php echo $a_w->warehouse_id ?>' <?php echo $selected ?>><?php echo $a_w->warehouse_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!--        echo form_dropdown(' ', $warehouseList, $selected, array('class' => 'form-control selectpicker ' . (!empty($warehouseID) ? $warehouseID : 'mwarehouse') . '', 'onchange' => 'getItemByWarehouse(this.value)', 'data-live-search' => true, 'style' => 'width:100%'));-->
            <!--            echo '<input type=' . 'hidden' . ' name=' . 'warehouse_id' . ' class=' . (!empty($warehouseID) ? $warehouseID : 'WarehouseValue') . ' value=' . $selected . '>';-->
            <!--            ?>-->
            <div class="input-group-addon" title="<?= lang('new_warehouse') ?>" data-toggle="tooltip"
                 data-placement="top">
                <?php if ($usertype->role_id == 1) { ?>
                    <a data-toggle="modal" data-target="#myModal_lg"
                       href="<?= base_url() ?>admin/warehouse/create/0/from_warehouse_id"><i class="fa fa-plus"></i></a>
                <?php } ?>
            </div>