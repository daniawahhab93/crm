<?php

/**
 * Description of Project_Model
 *
 * @author NaYeM
 */
class Warehouse_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_finished_items($data = null)
    {
        $all_data = [];
        if (!empty($data) && ($data['warehouse_id'] != 'all')) {
            $warehouse = $data['warehouse_id'];
            $all_data = $this->db->query("SELECT tbl_warehouses_products.quantity as quantity,minimum_in_stock,item_name,warehouse_name
FROM `tbl_saved_items` 
    INNER JOIN tbl_warehouses_products ON tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id
    INNER JOIN tbl_warehouse ON tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id
    WHERE tbl_warehouses_products.quantity <= tbl_saved_items.minimum_in_stock
 AND  tbl_warehouses_products.warehouse_id=" . $warehouse);
        } else {
            $all_data = $this->db->query('SELECT tbl_warehouses_products.quantity as quantity,minimum_in_stock,item_name,warehouse_name
FROM `tbl_saved_items` 
    INNER JOIN tbl_warehouses_products ON tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id
    INNER JOIN tbl_warehouse ON tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id
WHERE tbl_warehouses_products.quantity <= tbl_saved_items.minimum_in_stock
');
        }

        if (!empty($all_data)) {
            return $all_data->result();
        } else {
            return array();
        }
    }

    public function get_items_info($data = null)
    {
        $data_quantity = [
            'sale_quantity' => 0,
            'returns_quantity' => 0,
            'purchase_quantity' => 0,
        ];
        if (!empty($data) && ($data['warehouse_id'] != 'all' || $data['saved_item_id'] != 'all')) {
            $warehouse_id = $data['warehouse_id'];
            $saved_item_id = $data['saved_item_id'];

            $this->db->select('tbl_items.quantity ,SUM(tbl_items.quantity) as quantity');
            $this->db->join('tbl_items', 'tbl_items.saved_items_id =tbl_saved_items.saved_items_id ');
            $this->db->join('tbl_invoices', 'tbl_invoices.invoices_id  =tbl_items.invoices_id  ');
            $this->db->where('tbl_items.saved_items_id', $saved_item_id);
            $this->db->where('warehouse_id', $warehouse_id);
            $sale_quantity = $this->db->get('tbl_saved_items');
            if ($sale_quantity->num_rows() > 0) {
                $data_quantity['sale_quantity'] = $sale_quantity->row()->quantity?:0;
            }

            $this->db->select('tbl_return_stock_items.quantity , SUM(tbl_return_stock_items.quantity) as quantity');
            $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id=tbl_return_stock_items.saved_items_id');
            $this->db->join('tbl_return_stock', 'tbl_return_stock.return_stock_id  =tbl_return_stock_items.return_stock_id  ');
            $this->db->where('tbl_return_stock_items.saved_items_id', $saved_item_id);
            $this->db->where('warehouse_id', $warehouse_id);
            $returns_quantity = $this->db->get('tbl_return_stock_items');
            if ($returns_quantity->num_rows() > 0) {
                $data_quantity['returns_quantity'] = $returns_quantity->row()->quantity?:0;
            }

            $this->db->select('tbl_purchase_items.quantity, SUM(tbl_purchase_items.quantity) as quantity');
            $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id = tbl_purchase_items.saved_items_id ');
            $this->db->join('tbl_purchases', 'tbl_purchases.purchase_id   =tbl_purchase_items.purchase_id');
            $this->db->where('tbl_purchase_items.saved_items_id', $saved_item_id);
            $this->db->where('warehouse_id', $warehouse_id);
            $purchase_quantity = $this->db->get('tbl_purchase_items');

            if ($purchase_quantity->num_rows() > 0) {
                $data_quantity['purchase_quantity'] = $purchase_quantity->row()->quantity?:0;
            }
        }
        return $data_quantity;
    }


}
