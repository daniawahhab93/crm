<?php

/**
 * Description of return_stock_model
 *
 * @author NaYeM
 */
class Return_stock_model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_payment_status($return_stock_id, $unmark = null)
    {
        if (!empty($return_stock_id)) {
            $tax = $this->get_return_stock_tax_amount($return_stock_id);
            $discount = $this->get_return_stock_discount($return_stock_id);
            $invoice_cost = $this->get_return_stock_cost($return_stock_id);
            $due = round(((($invoice_cost - $discount) + $tax)));
            $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
            if ($return_stock_info->status == 'Cancelled' && empty($unmark)) {
                return ('cancelled');
            } elseif ($due <= 0) {
                return ('fully_paid');
            } else {
                return ('partially_paid');
            }
        }
    }

    public function calculate_to($value, $return_stock_id)
    {
        switch ($value) {
            case 'return_stock_cost':
                return $this->get_return_stock_cost($return_stock_id);
                break;
            case 'tax':
                return $this->get_return_stock_tax_amount($return_stock_id);
                break;
            case 'discount':
                return $this->get_return_stock_discount($return_stock_id);
                break;
            case 'return_stock_due':
                return $this->get_return_stock_due_amount($return_stock_id);
                break;
            case 'total':
                return $this->get_return_stock_total_amount($return_stock_id);
                break;
        }
    }

    public function get_return_stock_cost($return_stock_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('return_stock_id', $return_stock_id);
        $this->db->from('tbl_return_stock_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    public function get_return_stock_tax_amount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info->total_tax)) {
            $tax_info = json_decode($return_stock_info->total_tax);
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

    public function get_return_stock_discount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info)) {
            return $return_stock_info->discount_total;
        }
    }

    public function get_return_stock_due_amount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        if (!empty($return_stock_info)) {
            $tax = $this->get_return_stock_tax_amount($return_stock_id);
            $discount = $this->get_return_stock_discount($return_stock_id);
            $return_stock_cost = $this->get_return_stock_cost($return_stock_id);
            $due_amount = (($return_stock_cost - $discount) + $tax) + $return_stock_info->adjustment;
            if ($due_amount <= 0) {
                $due_amount = 0;
            }
        } else {
            $due_amount = 0;
        }
        return $due_amount;
    }

    public function get_return_stock_total_amount($return_stock_id)
    {
        $return_stock_info = $this->check_by(array('return_stock_id' => $return_stock_id), 'tbl_return_stock');
        $tax = $this->get_return_stock_tax_amount($return_stock_id);
        $discount = $this->get_return_stock_discount($return_stock_id);
        $return_stock_cost = $this->get_return_stock_cost($return_stock_id);

        $total_amount = $return_stock_cost - $discount + $tax + $return_stock_info->adjustment;
        if ($total_amount <= 0) {
            $total_amount = 0;
        }
        return $total_amount;
    }

    public function ordered_items_by_id($id, $json = null)
    {
        $rows = $this->db->where('return_stock_id', $id)->order_by('order', 'asc')->get('tbl_return_stock_items')->result();
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

//    public function get_return_stock_items_report($filterBy = null, $range = null)
//    {
//        $return_stock_items =   $this->db->join('tbl_return_stock', 'tbl_return_stock.return_stock_id=tbl_return_stock_items.return_stock_id')
//            ->where('due_date <',date('Y-m-d'))
//            ->get('tbl_return_stock_items')->result();
//
//        $return_stock_items = array_reverse($return_stock_items);
//
//        if (!empty($return_stock_items)) {
//            $proposal_info = array();
//            if (!empty($range[0])) {
//                foreach ($return_stock_items as $v_return_stock) {
//                    if ($v_return_stock->date_saved >= $range[0] && $v_return_stock->date_saved <= $range[1]) {
//                        array_push($proposal_info, $v_return_stock);
//                    }
//                }
//                return $proposal_info;
//            } else {
//                return $return_stock_items;
//            }
//        } else {
//            return array();
//        }
//    }

    public function get_return_stock_items_report($data = null, $range = null)
    {
        if (!empty($data) && ($data['warehouse_id'] != 'all'
                || $data['saved_items_id'] != 'all'
                || $data['customer_group_id'] != 'all'
                || $data['user_id'] != 'all')
        ) {
            $this->db->join('tbl_warehouse', ' tbl_warehouse.warehouse_id =tbl_return_stock.warehouse_id')
                ->join('tbl_return_stock_items', 'tbl_return_stock_items.return_stock_id=tbl_return_stock.return_stock_id')
                ->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_return_stock_items.saved_items_id')
                ->join('tbl_customer_group', 'tbl_customer_group.customer_group_id=tbl_saved_items.customer_group_id')
                ->join('tbl_users', 'tbl_users.user_id=tbl_return_stock.user_id');

//            if ($data['module_id'] != 'all')
//                $this->db->where('tbl_return_stock.module_id', $data['module_id']);

            if ($data['warehouse_id'] != 'all')
                $this->db->where('tbl_return_stock.warehouse_id', $data['warehouse_id']);

            if ($data['saved_items_id'] != 'all')
                $this->db->where('tbl_return_stock_items.saved_items_id', $data['saved_items_id']);

            if ($data['customer_group_id'] != 'all')
                $this->db->where('tbl_saved_items.customer_group_id', $data['customer_group_id']);

            if ($data['user_id'] != 'all')
                $this->db->where('tbl_return_stock.user_id', $data['user_id']);

            $all_data = $this->db->get('tbl_return_stock')->result();
        } else {
            $all_data = $this->db->join('tbl_warehouse', ' tbl_warehouse.warehouse_id =tbl_return_stock.warehouse_id')
                ->join('tbl_return_stock_items', 'tbl_return_stock_items.return_stock_id=tbl_return_stock.return_stock_id')
                ->join('tbl_saved_items', 'tbl_saved_items.saved_items_id =tbl_return_stock_items.saved_items_id')
                ->join('tbl_customer_group', 'tbl_customer_group.customer_group_id=tbl_saved_items.customer_group_id')
                ->join('tbl_users', 'tbl_users.user_id=tbl_return_stock.user_id')
                ->get('tbl_return_stock')->result();
        }
        if (empty($data) || !empty($data) && $data['status'] == 'all') {
            $invoice = $all_data;
        } elseif ($data == 'recurring') {
            $invoice = $this->recurring_invoices();
        } else {
            if (!empty($all_data)) {
                $all_data = array_reverse($all_data);
                foreach ($all_data as $v_invoices) {
                    if ($data['status'] == 'paid') {
                        if ($this->get_payment_status($v_invoices->return_stock_id ) == lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($data['status'] == 'not_paid') {
                        if ($this->get_payment_status($v_invoices->return_stock_id ) == lang('not_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($data['status'] == 'draft') {
                        if ($this->get_payment_status($v_invoices->return_stock_id ) == lang('draft')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($data['status'] == 'partially_paid') {
                        if ($this->get_payment_status($v_invoices->return_stock_id ) == lang('partially_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($data['status'] == 'cancelled') {
                        if ($this->get_payment_status($v_invoices->return_stock_id ) == lang('cancelled')) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($data['status'] == 'overdue') {
                        $payment_status = $this->get_payment_status($v_invoices->return_stock_id );
                        if (strtotime($v_invoices->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                            $invoice[] = $v_invoices;
                        }
                    }
                }
            }
        }

        if (!empty($invoice)) {
            $invoices = array();

            if (!empty($range[0])) {
                foreach ($invoice as $v_invoice) {
                    if ($v_invoice->return_stock_date >= $range[0] && $v_invoice->return_stock_date <= $range[1]) {
                        array_push($invoices, $v_invoice);
                    }
                }
                return $invoices;
            } else {
                return $invoice;
            }
        } else {
            return array();
        }
    }


    // Get a list of recurring invoices
    public function recurring_invoices($module_id = null)
    {
        if (!empty($module_id)) {
            return $this->db->where(array('recurring' => 'Yes', 'module_id' => $module_id))->get('tbl_return_stock')->result();
        } else {
            return $this->db->where(array('recurring' => 'Yes', 'invoices_id >' => 0))->get('tbl_return_stock')->result();
        }
    }

    public function total_sales($filter = null)
    {
        $total = 0;
        $all_payments = [];
        if ($filter && $filter != 'all') {
            $this->db->join('tbl_return_stock', 'tbl_return_stock.return_stock_id= tbl_return_stock_payments.return_stock_id');
            $this->db->where('warehouse_id', $filter);
            $this->db->from('tbl_return_stock_payments');
            $all_payments = $this->db->get()->result();
        } else {
            $all_payments = get_result('tbl_return_stock_payments');
        }
        //        $currency = get_row(array('symbol' => $payment->currency));
        if ($all_payments) {
            foreach ($all_payments as $payment) {
                $amount = $payment->amount;
                //            if ($payment->currency != config_item('default_currency')) {
                //                $amount = convert_currency($p->currency, $amount);
                //            }
                $total += $amount;
            }
        }
        return $total;
    }

    public function paid_by_date($year, $month = null, $filter = null)
    {
        $total = 0;
        $payments = [];
        if ($filter && $filter != 'all') {
            if (!empty($month)) {
                $where = array('year_paid' => $year, 'month_paid' => $month);
            } else {
                $where = array('year_paid' => $year);
            }
            $this->db->join('tbl_return_stock', 'tbl_return_stock.return_stock_id= tbl_return_stock_payments.return_stock_id');
            $this->db->where('warehouse_id', $filter);
            $this->db->where($where);
            $this->db->from('tbl_return_stock_payments');
            $query_result = $this->db->get();
            $payments = $query_result->result();
        } else {
            if (!empty($month)) {
                $where = array('year_paid' => $year, 'month_paid' => $month);
            } else {
                $where = array('year_paid' => $year);
            }
            $payments = $this->db->where($where)->get('tbl_return_stock_payments')->result();

        }

        if ($payments) {
            foreach ($payments as $p) {
                $amount = $p->amount;
                //            if ($p->currency != config_item('default_currency')) {
                //                $amount = Applib::convert_currency($p->currency, $amount);
                //            }
                $total += $amount;
            }
        }

        return $total;
    }

}
