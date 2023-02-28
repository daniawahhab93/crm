<div class="form-group">
    <label for="warehouse" class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('warehouse') ?></label>
    <div class="col-sm-5">
        <div class="input-group">
            <?php
            $usertype = profile();
			//print_r($usertype	);
			$malekArry = [];
            if ($usertype->role_id == 3) {
				//malek start edit 
			$str= $usertype -> warehouses_ids ;
			$str1 = substr($str,1,strlen($str)-2);
			//echo $str1;
			$str2 = explode(',' , $str1 );
			//print_r($str2);
			//echo count($str2);
			
			//print_r($malekArry);
			for($i = 0 ; $i < count($str2) ; $i++){
				$int_value = 0 ;
				$value = $str2[$i];
				$st = substr($value,1,strlen($value)-2);
				#echo gettype($st);
				#echo $st .'<br/>';
				$int_value = intval($st);
				//echo $int_value;
				$warehouseList = $this->admin_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published', 'warehouse_id' => $int_value));
                //print_r ($warehouseList);
				if($i == 0 ){
					array_push( $malekArry , $warehouseList[0]); 
				array_push( $malekArry , $warehouseList[$int_value]); 
				}else
					array_push( $malekArry , $warehouseList[$int_value]);
			
			}
                
                //print_r ($warehouseList);
                //print_r (explode(',' , $usertype -> warehouses_ids ));
                
				//$selected = ($this->session->userdata['warehouse_id']);
				 $selected = (!empty($warehouseID) ? $warehouseID : '');
				 $warehouseList = $malekArry;
				// print_r ($selected);
            } else {
                $warehouseList = $this->admin_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published'));
//				print_r ($warehouseList);
			   $selected = (!empty($warehouseID) ? $warehouseID : '');
            }
            echo form_dropdown(' ', $warehouseList, $selected, array('class' => 'form-control selectpicker ' . (!empty($warehouseID) ? $warehouseID : 'mwarehouse') . '', 'onchange' => 'getItemByWarehouse(this.value)', 'data-live-search' => true, 'style' => 'width:100%'));
            echo '<input type=' . 'hidden' . ' name=' . 'warehouse_id' . ' class=' . (!empty($warehouseID) ? $warehouseID : 'WarehouseValue') . ' value=' . $selected . '>';
            
			?>
            <div class="input-group-addon" title="<?= lang('new_warehouse') ?>" data-toggle="tooltip"
                 data-placement="top">
                <?php if ($usertype->role_id == 1) { ?>
                    <a data-toggle="modal" data-target="#myModal_lg"
                       href="<?= base_url() ?>admin/warehouse/create/0/from_warehouse_id"><i class="fa fa-plus"></i></a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>