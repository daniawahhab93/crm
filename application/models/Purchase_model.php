<?php

/**
 * Description of purchase_model
 *
 * @author NaYeM
 */
class Purchase_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_payment_status($purchase_id, $unmark = null)
    {
        if (!empty($purchase_id)) {
            $tax = $this->get_purchase_tax_amount($purchase_id);
            $discount = $this->get_purchase_discount($purchase_id);
            $purchase_cost = $this->get_purchase_cost($purchase_id);
            $payment_made = round($this->get_purchase_paid_amount($purchase_id), 2);
            $due = round(((($purchase_cost - $discount) + $tax) - $payment_made));
            $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
            if ($purchase_info->status == 'Cancelled' && empty($unmark)) {
                return lang('cancelled');
            } elseif ($payment_made < 1) {
                return lang('not_paid');
            } elseif ($due <= 0) {
                return lang('fully_paid');
            } else {
                return lang('partially_paid');
            }
        }
    }

    function calculate_to($value, $purchase_id)
    {
        switch ($value) {
            case 'purchase_cost':
                return $this->get_purchase_cost($purchase_id);
                break;
            case 'tax':
                return $this->get_purchase_tax_amount($purchase_id);
                break;
            case 'discount':
                return $this->get_purchase_discount($purchase_id);
                break;
            case 'paid_amount':
                return $this->get_purchase_paid_amount($purchase_id);
                break;
            case 'purchase_due':
                return $this->get_purchase_due_amount($purchase_id);
                break;
            case 'total':
                return $this->get_purchase_total_amount($purchase_id);
                break;
        }
    }

    function get_purchase_cost($purchase_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('purchase_id', $purchase_id);
        $this->db->from('tbl_purchase_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_purchase_tax_amount($purchase_id)
    {
        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info->total_tax)) {
            $tax_info = json_decode($purchase_info->total_tax);
        }
        $tax = 0;
        if (!empty($tax_info)) {
            $total_tax = $tax_info->total_tax;
            if (!empty($total_tax)) {
                foreach ($total_tax as $t_key => $v_tax_info) {
                    $tax += $v_tax_info;
                }
            }
        }
        return $tax;
    }

    public function get_purchase_discount($purchase_id)
    {
        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info)) {
            return $purchase_info->discount_total;
        }
    }

    public function get_purchase_paid_amount($purchase_id)
    {

        $this->db->select_sum('amount');
        $this->db->where('purchase_id', $purchase_id);
        $this->db->from('tbl_purchase_payments');
        $query_result = $this->db->get();
        $amount = $query_result->row();
        //        $tax = $this->get_purchase_tax_amount($purchase_id);
        if (!empty($amount->amount)) {
            $result = $amount->amount;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_purchase_due_amount($purchase_id)
    {

        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        if (!empty($purchase_info)) {
            $tax = $this->get_purchase_tax_amount($purchase_id);
            $discount = $this->get_purchase_discount($purchase_id);
            $purchase_cost = $this->get_purchase_cost($purchase_id);
            $payment_made = $this->get_purchase_paid_amount($purchase_id);
            $due_amount = (($purchase_cost - $discount) + $tax) - $payment_made + $purchase_info->adjustment;
            if ($due_amount <= 0) {
                $due_amount = 0;
            }
        } else {
            $due_amount = 0;
        }
        return $due_amount;
    }

    public function get_purchase_total_amount($purchase_id)
    {

        $purchase_info = $this->check_by(array('purchase_id' => $purchase_id), 'tbl_purchases');
        $tax = $this->get_purchase_tax_amount($purchase_id);
        $discount = $this->get_purchase_discount($purchase_id);
        $purchase_cost = $this->get_purchase_cost($purchase_id);
        //        $payment_made = $this->get_purchase_paid_amount($purchase_id);

        $total_amount = $purchase_cost - $discount + $tax + $purchase_info->adjustment;
        if ($total_amount <= 0) {
            $total_amount = 0;
        }
        return $total_amount;
    }

    function ordered_items_by_id($id, $json = null)
    {
        $rows = $this->db->where('purchase_id', $id)->order_by('order', 'asc')->get('tbl_purchase_items')->result();
        if (!empty($json)) {
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $row->qty = $row->quantity;
                    $row->rate = $row->unit_cost;
                    $row->cost_price = $row->unit_cost;
                    $row->new_itmes_id = $row->saved_items_id;
                    $row->taxname = json_decode($row->item_tax_name);
                    $pr[] = $row;
                }
                return json_encode($pr);
            }
        } else {
            return $rows;
        }
    }

    public function get_purchase_report_details($data = null, $range = null)
    {
        $all_data = [];
        if (!empty($data) && ($data['warehouse_id'] != 'all'
                || $data['supplier_id'] != 'all'
                || $data['saved_items_id'] != 'all'
                || $data['customer_group_id'] != 'all'
                || $data['user_id'] != 'all')
        ) {
            $this->db->join('tbl_warehouse', ' tbl_warehouse.warehouse_id =tbl_purchases.warehouse_id')
                ->join('tbl_purchase_items', 'tbl_purchase_items.purchase_id=tbl_purchases.purchase_id')
                ->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_purchase_items.saved_items_id')
                ->join('tbl_customer_group', 'tbl_customer_group.customer_group_id=tbl_saved_items.customer_group_id')
                ->join('tbl_users', 'tbl_users.user_id=tbl_purchases.user_id')
                ->join('tbl_suppliers', 'tbl_suppliers.supplier_id=tbl_purchases.supplier_id');

            if ($data['warehouse_id'] != 'all')
                $this->db->where('tbl_purchases.warehouse_id', $data['warehouse_id']);

            if ($data['saved_items_id'] != 'all')
                $this->db->where('tbl_purchase_items.saved_items_id', $data['saved_items_id']);

            if ($data['customer_group_id'] != 'all')
                $this->db->where('tbl_saved_items.customer_group_id', $data['customer_group_id']);

            if ($data['user_id'] != 'all')
                $this->db->where('tbl_purchases.user_id', $data['user_id']);

            if ($data['supplier_id'] != 'all')
                $this->db->where('tbl_suppliers.supplier_id', $data['supplier_id']);


            $all_data = $this->db->get('tbl_purchases')->result();
        } else {
            $all_data = $this->db->join('tbl_warehouse', ' tbl_warehouse.warehouse_id =tbl_purchases.warehouse_id')
                ->join('tbl_purchase_items', 'tbl_purchase_items.purchase_id=tbl_purchases.purchase_id')
                ->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_purchase_items.saved_items_id')
                ->join('tbl_customer_group', 'tbl_customer_group.customer_group_id=tbl_saved_items.customer_group_id')
                ->join('tbl_users', 'tbl_users.user_id=tbl_purchases.user_id')
                ->join('tbl_suppliers', 'tbl_suppliers.supplier_id=tbl_purchases.supplier_id')
                ->get('tbl_purchases')->result();
        }

        if (empty($data) || !empty($data) && $data['status'] == 'all') {
            $purchase = $all_data;
        } else {
            if (!empty($all_data)) {
                $all_data = array_reverse($all_data);
                foreach ($all_data as $v_purchases) {
                    if ($data['status'] == 'paid') {
                        if ($this->get_payment_status($v_purchases->purchase_id) == lang('fully_paid')) {
                            $purchase[] = $v_purchases;
                        }
                    } else if ($data['status'] == 'not_paid') {
                        if ($this->get_payment_status($v_purchases->purchase_id) == lang('not_paid')) {
                            $purchase[] = $v_purchases;
                        }
                    } else if ($data['status'] == 'partially_paid') {
                        if ($this->get_payment_status($v_purchases->purchase_id) == lang('partially_paid')) {
                            $purchase[] = $v_purchases;
                        }
                    } else if ($data['status'] == 'cancelled') {
                        if ($this->get_payment_status($v_purchases->purchase_id) == lang('cancelled')) {
                            $purchase[] = $v_purchases;
                        }
                    } else if ($data['status'] == 'last_month' || $data['status'] == 'this_months') {
                        if ($data['status'] == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_purchases->purchase_month) == strtotime($month)) {
                            $purchase[] = $v_purchases;
                        }
                    } else if (strstr($data['status'], '_')) {
                        $year = str_replace('_', '', $data['status']);
                        if (strtotime($v_purchases->purchase_year) == strtotime($year)) {
                            $purchase[] = $v_purchases;
                        }
                    }
                }
            }
        }

        if (!empty($purchase)) {
            $purchases = array();

            if (!empty($range[0])) {
                foreach ($purchase as $v_invoice) {
                    if ($v_invoice->purchase_date >= $range[0] && $v_invoice->purchase_date <= $range[1]) {
                        array_push($purchases, $v_invoice);
                    }
                }
                return $purchases;
            } else {
                return $purchase;
            }
        } else {
            return array();
        }
    }


    public function purchase_payable($id)
    {
        return ($this->get_purchase_cost($id) + $this->get_purchase_tax_amount($id) - $this->get_purchase_discount($id));
    }

    public function get_last_purchase_price_same_supplier_items($id, $supplier_id)
    {
        $this->db->select('tbl_purchase_items.unit_cost as unit_cost');
        $this->db->join('tbl_purchases', 'tbl_purchases.purchase_id =tbl_purchase_items.purchase_id ');
        $this->db->where('supplier_id', $supplier_id);
        $this->db->where('saved_items_id', $id);
        $this->db->order_by('order', 'desc');
        $query = $this->db->get('tbl_purchase_items');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function get_lowest_purchase_price_items($id)
    {
        $this->db->select('min(tbl_purchase_items.unit_cost) as unit_cost');
        $this->db->join('tbl_purchases', 'tbl_purchases.purchase_id =tbl_purchase_items.purchase_id ');
        $this->db->where('saved_items_id', $id);
        $query = $this->db->get('tbl_purchase_items');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }


    public function get_last_cost_price_items($id)
    {
        $this->db->select('max(tbl_saved_items.cost_price) as cost_price');
        $this->db->join('tbl_purchases', 'tbl_purchases.purchase_id =tbl_purchase_items.purchase_id ');
        $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_purchase_items.saved_items_id');
        $this->db->where('tbl_purchase_items.saved_items_id', $id);
        $query = $this->db->get('tbl_purchase_items');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function get_lowest_cost_price_items($id)
    {
        $this->db->select('min(tbl_saved_items.cost_price) as cost_price');
        $this->db->join('tbl_purchases', 'tbl_purchases.purchase_id =tbl_purchase_items.purchase_id ');
        $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_purchase_items.saved_items_id');
        $this->db->where('tbl_purchase_items.saved_items_id', $id);
        $query = $this->db->get('tbl_purchase_items');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }
}
