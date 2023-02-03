<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Report extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('report_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('proposal_model');
        $this->load->model('client_model');
        $this->load->model('purchase_model');
        $this->load->model('warehouse_model');
        $this->load->model('return_stock_model');
        $this->load->model('payroll_model');
        $this->load->model('transactions_model');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function account_statement()
    {
        $data['title'] = lang('account_statement');
        $data['account_id'] = $this->input->post('account_id', TRUE);
        if (!empty($data['account_id'])) {
            $data['report'] = TRUE;
            $data['start_date'] = $this->input->post('start_date', TRUE);
            $data['end_date'] = $this->input->post('end_date', TRUE);
            $data['transaction_type'] = $this->input->post('transaction_type', TRUE);
            $data['all_transaction_info'] = $this->get_account_statement($data['account_id'], $data['start_date'], $data['end_date'], $data['transaction_type']);
        }
        $data['subview'] = $this->load->view('admin/report/account_statement', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function incomeList($id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->load->model('transactions_model');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->column_search = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->order = array('transactions_id' => 'desc');
            // get all invoice
            $fetch_data = $this->datatables->get_deposit($id, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_deposit) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_deposit->transactions_id));
                $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_deposit->transactions_id));
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');

                $sub_array = array();
                $name = null;
                $name .= '<a data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '">' . display_date($v_deposit->date) . '</a>';
                $sub_array[] = $name;

                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');

                $sub_array[] = $v_deposit->notes;

                $sub_array[] = display_money($v_deposit->amount, default_currency());
                $sub_array[] = display_money($v_deposit->credit, default_currency());
                $sub_array[] = display_money($v_deposit->debit, default_currency());
                $sub_array[] = display_money($v_deposit->total_balance, default_currency());

                $action .= '<a class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_deposit->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/create_deposit/' . $v_deposit->transactions_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_deposit/$v_deposit->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data, array('type' => 'Income'));
        } else {
            redirect('admin/dashboard');
        }
    }

    public function expenseList($id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->column_search = array('tbl_transactions.name', 'tbl_accounts.account_name', 'date', 'notes', 'category_id', 'tbl_client.name', 'debit', 'credit', 'amount', 'reference', 'total_balance');
            $this->datatables->order = array('transactions_id' => 'desc');
            // get all invoice
            $this->load->model('transactions_model');
            $fetch_data = $this->datatables->get_expense($id, $type);

            $data = array();

            $edited = can_action('30', 'edited');
            $deleted = can_action('30', 'deleted');
            foreach ($fetch_data as $_key => $v_expense) {
                $action = null;
                $can_edit = $this->transactions_model->can_action('tbl_transactions', 'edit', array('transactions_id' => $v_expense->transactions_id));
                $can_delete = $this->transactions_model->can_action('tbl_transactions', 'delete', array('transactions_id' => $v_expense->transactions_id));
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_expense->account_id), 'tbl_accounts');

                $sub_array = array();

                $date = null;
                $date .= '<a class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_expense->transactions_id . '">' . strftime(config_item('date_format'), strtotime($v_expense->date)) . '</a>';
                $sub_array[] = $date;

                $sub_array[] = (!empty($account_info->account_name) ? $account_info->account_name : '-');
                $sub_array[] = $v_expense->notes;
                $sub_array[] = display_money($v_expense->amount, default_currency());
                $sub_array[] = display_money($v_expense->credit, default_currency());
                $sub_array[] = display_money($v_expense->debit, default_currency());
                $sub_array[] = display_money($v_expense->total_balance, default_currency());

                $action .= '<a class="btn btn-info btn-xs" class="text-info" href="' . base_url() . 'admin/transactions/view_expense/' . $v_expense->transactions_id . '"><span class="fa fa-list-alt"></span></a>' . ' ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/transactions/create_expense/' . $v_expense->transactions_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/transactions/delete_expense/$v_expense->transactions_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data, array('type' => 'Expense'));
        } else {
            redirect('admin/dashboard');
        }
    }

    function get_account_statement($account_id, $start_date, $end_date, $transaction_type)
    {
        if ($transaction_type == 'all_transactions') {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date);
        } elseif ($transaction_type == 'debit') {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date, 'credit' => $transaction_type);
        } else {
            $where = array('account_id' => $account_id, 'date >=' => $start_date, 'date <=' => $end_date, 'debit' => $transaction_type);
        }
        $this->report_model->_table_name = "tbl_transactions"; //table name
        $this->report_model->_order_by = "transactions_id";
        return $this->report_model->get_by($where, FALSE);
    }

    public function account_statement_pdf($account_id, $start_date, $end_date, $transaction_type)
    {

        $data['all_transaction_info'] = $this->get_account_statement($account_id, $start_date, $end_date, $transaction_type);
        $data['title'] = lang('account_statement');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/account_statement_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('account_statement') . ' From:' . $start_date . ' To:', $end_date));
    }

    public function income_report()
    {
        $data['title'] = lang('income_report');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/income_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function income_report_pdf()
    {
        $data['title'] = lang('income_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/income_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('income_report')));
    }

    public function get_transactions_report()
    { // this function is to create get monthy recap report
        $m = date('n');
        $year = date('Y');
        $num = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        for ($i = 1; $i <= $num; $i++) {
            if ($m >= 1 && $m <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                $date = $year . "-" . '0' . $m;
            } else {
                $date = $year . "-" . $m;
            }
            $date = $date . '-' . $i;
            $transaction_report[$i] = $this->db->where('date', $date)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
        }
        return $transaction_report; // return the result
    }

    public function expense_report($category_id = null)
    {
        $data['title'] = lang('expense_report');
        $data['transactions_report'] = $this->get_transactions_report($category_id);
        $warehouse_id = '';
        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id', true);
        }
        $data['warehouse_id'] = $warehouse_id;
        $data['subview'] = $this->load->view('admin/report/expense_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function expense_report_pdf($category_id = null)
    {
        $data['title'] = lang('expense_report');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/expense_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('expense_report')));
    }

    public function income_expense()
    {
        $data['title'] = lang('income_expense');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/income_expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function income_expense_pdf()
    {
        $data['title'] = lang('income_expense');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/income_expense_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('income_expense')));
    }

    public function date_wise_report()
    {
        $data['title'] = lang('date_wise_report');
        $data['start_date'] = $this->input->post('start_date', TRUE);
        $data['end_date'] = $this->input->post('end_date', TRUE);
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $data['report'] = TRUE;
            $data['all_transaction_info'] = $this->db->where('date >=', $data['start_date'], 'date >=', $data['end_date'])->get('tbl_transactions')->result();
        }
        $data['subview'] = $this->load->view('admin/report/date_wise_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function date_wise_report_pdf($start_date, $end_date)
    {
        $data['title'] = lang('date_wise_report');
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $this->load->helper('dompdf');
        $data['all_transaction_info'] = get_order_by('tbl_transactions', array('date >=' => $data['start_date'], 'date <=' => $data['end_date']));
        $viewfile = $this->load->view('admin/report/date_wise_report_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('date_wise_report')));
    }

    public function report_by_month()
    {
        $data['title'] = lang('report_by_month');
        $data['current_month'] = date('m');

        if ($this->input->post('year', TRUE)) { // if input year 
            $data['year'] = $this->input->post('year', TRUE);
        } else { // else current year
            $data['year'] = date('Y'); // get current year
        }
        // get all expense list by year and month
        $data['report_by_month'] = $this->get_report_by_month($data['year']);

        $data['subview'] = $this->load->view('admin/report/report_by_month', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function get_report_by_month($year, $month = NULL)
    { // this function is to create get monthy recap report
        if (!empty($month)) {
            $date = new DateTime($year . '-' . $month . '-01');
            $start_date = $date->modify('first day of this month')->format('Y-m-d');
            $end_date = $date->modify('last day of this month')->format('Y-m-d');
            $get_expense_list = $this->report_model->get_report_by_date($start_date, $end_date); // get all report by start date and in date 
        } else {
            for ($i = 1; $i <= 12; $i++) { // query for months
                $date = new DateTime($year . '-' . $i . '-01');
                $start_date = $date->modify('first day of this month')->format('Y-m-d');
                $end_date = $date->modify('last day of this month')->format('Y-m-d');
                $get_expense_list[$i] = $this->report_model->get_report_by_date($start_date, $end_date); // get all report by start date and in date 
            }
        }
        return $get_expense_list; // return the result
    }

    public function report_by_month_pdf($year, $month)
    {
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'report/report_by_month',
            'module_field_id' => $year,
            'activity' => lang('activity_report_by_month_pdf'),
            'icon' => 'fa-laptop',
            'value1' => $year,
            'value2' => $month
        );
        $this->report_model->_table_name = 'tbl_activities';
        $this->report_model->_primary_key = 'activities_id';
        $this->report_model->save($activity);

        $data['report_list'] = $this->get_report_by_month($year, $month);
        $month_name = date('F', strtotime($year . '-' . $month)); // get full name of month by date query                
        $data['monthyaer'] = $month_name . '  ' . $year;
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('admin/report/report_by_month_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('report_by_month') . '- ' . $data['monthyaer']));
    }

    public function all_income()
    {
        $data['title'] = lang('all_income');
        $data['subview'] = $this->load->view('admin/report/all_income', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function all_expense()
    {
        $data['title'] = lang('all_expense');
        $data['subview'] = $this->load->view('admin/report/all_expense', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function all_transaction()
    {
        $data['title'] = lang('all_transaction');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/all_transaction', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function tasks_assignment()
    {
        $data['title'] = lang('tasks_assignment');
        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('57');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['user_tasks'] = $this->get_tasks_by_user($data['assign_user']);

        $data['subview'] = $this->load->view('admin/report/project_tasks_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }


    function get_tasks_by_user($assign_user, $tasks = null)
    {
        $tasks_info = $this->report_model->get_permission('tbl_task');
        if (!empty($tasks_info)) : foreach ($tasks_info as $v_tasks) :
            if (!empty($tasks)) {
                if ($v_tasks->permission == 'all') {
                    $permission[$v_tasks->permission][$v_tasks->task_status][] = $v_tasks->task_status;
                } else {
                    $get_permission = json_decode($v_tasks->permission);
                    if (!empty($get_permission)) {
                        foreach ($get_permission as $id => $v_permission) {
                            if (!empty($assign_user)) {
                                foreach ($assign_user as $v_user) {
                                    if ($v_user->user_id == $id) {
                                        $permission[$v_user->user_id][$v_tasks->task_status][] = $v_tasks->task_status;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (!empty($v_tasks->project_id)) {

                    if ($v_tasks->permission == 'all') {
                        $permission[$v_tasks->permission][$v_tasks->task_status][] = $v_tasks->task_status;
                    } else {
                        $get_permission = json_decode($v_tasks->permission);
                        if (!empty($get_permission)) {
                            foreach ($get_permission as $id => $v_permission) {
                                if (!empty($assign_user)) {
                                    foreach ($assign_user as $v_user) {
                                        if ($v_user->user_id == $id) {
                                            $permission[$v_user->user_id][$v_tasks->task_status][] = $v_tasks->task_status;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }


        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }
        return $permission;
    }

    public
    function bugs_assignment()
    {
        $data['title'] = lang('bugs_assignment') . ' ' . lang('report');
        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('58');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['user_bugs'] = $this->get_bugs_by_user($data['assign_user']);

        $data['yearly_report'] = $this->get_project_report_by_month();
        $data['subview'] = $this->load->view('admin/report/project_bugs_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function get_project_report_by_month($tickets = null)
    { // this function is to create get monthy recap report

        for ($i = 1; $i <= 12; $i++) { // query for months
            $date = new DateTime(date('Y') . '-' . $i . '-01');
            $start_date = $date->modify('first day of this month')->format('Y-m-d');
            $end_date = $date->modify('last day of this month')->format('Y-m-d');
            if (!empty($tickets)) {
                $where = array('created >=' => $start_date . ' 00:00:00', 'created <=' => $end_date . ' 23:59:59');
                $get_result[$i] = $this->db->where($where)->get('tbl_tickets')->result();; // get all report by start date and in date
            } else {
                $where = array('created_time >=' => $start_date, 'created_time <=' => $end_date);
                $get_result[$i] = $this->db->where($where)->get('tbl_bug')->result();; // get all report by start date and in date
            }
        }

        return $get_result; // return the result
    }

    function get_bugs_by_user($assign_user, $bugs = null)
    {
        $bugs_info = $this->report_model->get_permission('tbl_bug');

        if (!empty($bugs_info)) : foreach ($bugs_info as $v_bugs) :
            if (!empty($bugs)) {
                if ($v_bugs->permission == 'all') {
                    $permission[$v_bugs->permission][$v_bugs->bug_status][] = $v_bugs->bug_status;
                } else {
                    $get_permission = json_decode($v_bugs->permission);
                    if (!empty($get_permission)) {
                        foreach ($get_permission as $id => $v_permission) {
                            if (!empty($assign_user)) {
                                foreach ($assign_user as $v_user) {
                                    if ($v_user->user_id == $id) {
                                        $permission[$v_user->user_id][$v_bugs->bug_status][] = $v_bugs->bug_status;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if (!empty($v_bugs->project_id)) {

                    if ($v_bugs->permission == 'all') {
                        $permission[$v_bugs->permission][$v_bugs->bug_status][] = $v_bugs->bug_status;
                    } else {
                        $get_permission = json_decode($v_bugs->permission);
                        if (!empty($get_permission)) {
                            foreach ($get_permission as $id => $v_permission) {
                                if (!empty($assign_user)) {
                                    foreach ($assign_user as $v_user) {
                                        if ($v_user->user_id == $id) {
                                            $permission[$v_user->user_id][$v_bugs->bug_status][] = $v_bugs->bug_status;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }
        return $permission;
    }

    public function project_report()
    {
        $data['title'] = lang('project_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('57');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['all_project'] = $this->report_model->get_permission('tbl_project');
        $data['user_project'] = $this->get_project_by_user($data['assign_user']);
        $data['subview'] = $this->load->view('admin/report/project_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function tasks_report()
    {
        $data['title'] = lang('project_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('54');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);

        $data['all_tasks'] = $this->report_model->get_permission('tbl_task');
        $data['user_tasks'] = $this->get_tasks_by_user($data['assign_user'], true);

        $data['subview'] = $this->load->view('admin/report/tasks_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function bugs_report()
    {
        $data['title'] = lang('bugs_assignment') . ' ' . lang('report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('58');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);
        $data['user_bugs'] = $this->get_bugs_by_user($data['assign_user'], true);

        $data['yearly_report'] = $this->get_project_report_by_month();
        $data['subview'] = $this->load->view('admin/report/bugs_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_project_by_user($assign_user)
    {
        $all_project = $this->report_model->get_permission('tbl_project');
        if (!empty($all_project)) : foreach ($all_project as $v_project) :
            if ($v_project->permission == 'all') {
                $permission[$v_project->permission][$v_project->project_status][] = $v_project->project_status;
            } else {
                $get_permission = json_decode($v_project->permission);
                if (!empty($get_permission)) {
                    foreach ($get_permission as $id => $v_permission) {
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $v_user) {
                                if ($v_user->user_id == $id) {
                                    $permission[$v_user->user_id][$v_project->project_status][] = $v_project->project_status;
                                }
                            }
                        }
                    }
                }
            }
        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }

        return $permission;
    }

    public function tickets_report()
    {
        $data['title'] = lang('tickets_report');
        // get permission user by menu id
        $permission_user = $this->report_model->all_permission_user('7');
        // get all admin user
        $admin_user = $this->db->where('role_id', 1)->get('tbl_users')->result();
        // if not exist data show empty array.
        if (!empty($permission_user)) {
            $permission_user = $permission_user;
        } else {
            $permission_user = array();
        }
        if (!empty($admin_user)) {
            $admin_user = $admin_user;
        } else {
            $admin_user = array();
        }
        $data['assign_user'] = array_merge($admin_user, $permission_user);
        $data['user_tickets'] = $this->get_tickets_by_user($data['assign_user']);

        $data['yearly_report'] = $this->get_project_report_by_month(true);

        $data['subview'] = $this->load->view('admin/report/tickets_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    function get_tickets_by_user($assign_user)
    {
        $all_ticktes = $this->report_model->get_permission('tbl_tickets');
        if (!empty($all_ticktes)) : foreach ($all_ticktes as $v_ticktes) :
            if ($v_ticktes->permission == 'all') {
                $permission[$v_ticktes->permission][$v_ticktes->status][] = $v_ticktes->status;
            } else {
                $get_permission = json_decode($v_ticktes->permission);
                if (!empty($get_permission)) {
                    foreach ($get_permission as $id => $v_permission) {
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $v_user) {
                                if ($v_user->user_id == $id) {
                                    $permission[$v_user->user_id][$v_ticktes->status][] = $v_ticktes->status;
                                }
                            }
                        }
                    }
                }
            }
        endforeach;
        endif;
        if (empty($permission)) {
            $permission = array();
        }

        return $permission;
    }

    public function client_report()
    {
        $data['title'] = lang('client_report');
        $data['all_client_info'] = $this->db->get('tbl_client')->result();

        $data['subview'] = $this->load->view('admin/report/client_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function sales_report($filterBy = null)
    {
        $data['title'] = lang('sales') . ' ' . lang('report');
        if (!empty($filterBy)) {
            $data['filterBy'] = $filterBy;
            if ($filterBy == 'invoices') {

                $start_date = null;
                $end_date = null;
                $status = 'all';
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->invoice_model->get_invoice_report($data['status'], $range);
            } elseif ($filterBy == 'sales_report_details') {
                $start_date = null;
                $end_date = null;
                $status = 'all';
                $client_id = 'all';
                $warehouse_id = 'all';
                $saved_items_id = 'all';
                $customer_group_id = 'all';
                $user_id = 'all';
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                    $client_id = $this->input->post('client_id', true);
                    $warehouse_id = $this->input->post('warehouse_id', true);
                    $saved_items_id = $this->input->post('saved_items_id', true);
                    $customer_group_id = $this->input->post('customer_group_id', true);
                    $user_id = $this->input->post('user_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['client_id'] = $client_id;
                $data['warehouse_id'] = $warehouse_id;
                $data['saved_items_id'] = $saved_items_id;
                $data['customer_group_id'] = $customer_group_id;
                $data['user_id'] = $user_id;
                $data['all_invoices'] = $this->invoice_model->get_sales_report_details($data, $range);
            } elseif ($filterBy == 'sales_profit_report') {
                $start_date = null;
                $end_date = null;
                $status = 'all';
                $client_id = 'all';
                $warehouse_id = 'all';
                $saved_items_id = 'all';
                $customer_group_id = 'all';
                $user_id = 'all';
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                    $client_id = $this->input->post('client_id', true);
                    $warehouse_id = $this->input->post('warehouse_id', true);
                    $saved_items_id = $this->input->post('saved_items_id', true);
                    $customer_group_id = $this->input->post('customer_group_id', true);
                    $user_id = $this->input->post('user_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['client_id'] = $client_id;
                $data['warehouse_id'] = $warehouse_id;
                $data['saved_items_id'] = $saved_items_id;
                $data['customer_group_id'] = $customer_group_id;
                $data['user_id'] = $user_id;
                $data['all_invoices'] = $this->invoice_model->get_sales_profit_report($data, $range);
            } elseif ($filterBy == 'sales_report_summarized') {
                $start_date = null;
                $end_date = null;
                $status = 'all';
                $client_id = 'all';
                $warehouse_id = 'all';
                $saved_items_id = 'all';
                $customer_group_id = 'all';
                $user_id = 'all';
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                    $client_id = $this->input->post('client_id', true);
                    $warehouse_id = $this->input->post('warehouse_id', true);
                    $saved_items_id = $this->input->post('saved_items_id', true);
                    $customer_group_id = $this->input->post('customer_group_id', true);
                    $user_id = $this->input->post('user_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['client_id'] = $client_id;
                $data['warehouse_id'] = $warehouse_id;
                $data['saved_items_id'] = $saved_items_id;
                $data['customer_group_id'] = $customer_group_id;
                $data['user_id'] = $user_id;
                $data['all_invoices'] = $this->invoice_model->get_sales_report_details($data, $range);
            } elseif ($filterBy == 'sales_analytics_report') {
                $warehouse_id = 'all';
                if ($this->input->post()) {
                    $warehouse_id = $this->input->post('warehouse_id', true);
                }
                $data['warehouse_id'] = $warehouse_id;
            } elseif ($filterBy == 'payments') {
                $start_date = null;
                $end_date = null;
                $client_id = null;
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $client_id = $this->input->post('client_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $client_id;
                $data['all_payments'] = $this->invoice_model->get_payment_report($data['status'], $range);
            } else if ($filterBy == 'estimates' || $filterBy == 'estimate_by_client') {
                $start_date = null;
                $end_date = null;
                if ($filterBy == 'estimate_by_client') {
                    $client = get_result('tbl_client');
                    $status = !empty($client) ? $client[0]->client_id : '0';
                } else {
                    $status = 'all';
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_estimates'] = $this->estimates_model->get_estimate_report($data['status'], $range);
            } else if ($filterBy == 'proposals' || $filterBy == 'proposal_by_client') {
                $start_date = null;
                $end_date = null;
                if ($filterBy == 'proposal_by_client') {
                    $client = get_result('tbl_client');
                    $status = !empty($client) ? $client[0]->client_id : '0';
                } else {
                    $status = 'all';
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_proposals'] = $this->proposal_model->get_proposals_report($data['status'], $range);
            } elseif ($filterBy == 'sales_report_by_item_details'
                || $filterBy == 'sales_report_by_item_grouped'
                || $filterBy == 'sales_report_by_item_group_grouped'
                || $filterBy == 'sales_report_by_user_details'
                || $filterBy == 'sales_report_by_user_grouped') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->db->invoice_model->get_invoice_with_item_details_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/invoice_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } elseif ($filterBy == 'return_stock_items') {
                $start_date = null;
                $end_date = null;
                $status = 'all';
                $module_id = 'all';
                $warehouse_id = 'all';
                $saved_items_id = 'all';
                $customer_group_id = 'all';
                $user_id = 'all';
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                    $module_id = $this->input->post('module_id', true);
                    $warehouse_id = $this->input->post('warehouse_id', true);
                    $saved_items_id = $this->input->post('saved_items_id', true);
                    $customer_group_id = $this->input->post('customer_group_id', true);
                    $user_id = $this->input->post('user_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['module_id'] = $module_id;
                $data['warehouse_id'] = $warehouse_id;
                $data['saved_items_id'] = $saved_items_id;
                $data['customer_group_id'] = $customer_group_id;
                $data['user_id'] = $user_id;
                $data['return_stock_items'] = $this->return_stock_model->get_return_stock_items_report($data, $range);
            } elseif ($filterBy == 'wasted_items') {
                $start_date = null;
                $end_date = null;
                $client_id = null;
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $client_id = $this->input->post('client_id', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $client_id;
                $data['wasted_items'] = $this->proposal_model->get_proposals_items_report($data['status'], $range);
            }
        }
        $data['subview'] = $this->load->view('admin/report/sales_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public
    function sales_report_pdf($filterBy = null, $status, $start_date = null, $end_date = null)
    {

        $data['title'] = lang('sales') . ' ' . lang('report');
        if (!empty($filterBy)) {
            $data['filterBy'] = $filterBy;
            if ($filterBy == 'invoices'
                || $filterBy == 'invoice_by_client'
                || $filterBy == 'invoice_by_warehouse'
                || $filterBy == 'invoice_by_item') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->invoice_model->get_invoice_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/invoice_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } elseif ($filterBy == 'payments') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_payments'] = $this->invoice_model->get_payment_report($data['status'], $range);

                $viewfile = $this->load->view('admin/report/payment_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } else if ($filterBy == 'estimates' || $filterBy == 'estimate_by_client') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_estimates'] = $this->estimates_model->get_estimate_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/estimate_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } else if ($filterBy == 'proposals' || $filterBy == 'proposal_by_client') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                if ($this->input->post()) {
                    $range = explode('-', $this->input->post('range', true));
                    if (!empty($range[0])) {
                        $start_date = date('Y-m-d', strtotime($range[0]));
                        $end_date = date('Y-m-d', strtotime($range[1]));
                        $data['range'] = array($start_date, $end_date);
                    }
                    $status = $this->input->post('status', true);
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_proposals'] = $this->proposal_model->get_proposals_report($data['status'], $range);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
                $viewfile = $this->load->view('admin/report/proposals_report_pdf', $data, TRUE);
            } elseif ($filterBy == 'sales_report_by_item_details'
                || $filterBy == 'sales_report_by_item_grouped'
                || $filterBy == 'sales_report_by_item_group_grouped'
                || $filterBy == 'sales_report_by_user_details'
                || $filterBy == 'sales_report_by_user_grouped') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['all_invoices'] = $this->db->invoice_model->get_invoice_with_item_details_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/invoice_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } elseif ($filterBy == 'wasted_items') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['wasted_items'] = $this->proposal_model->get_proposals_items_report($data['status'], $range);
                $viewfile = $this->load->view('admin/report/payment_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            } elseif ($filterBy == 'return_stock_items') {
                if (!empty($start_date)) {
                    $data['range'] = array($start_date, $end_date);
                    $date = lang('FROM') . ' ' . display_date($start_date) . ' ' . lang('TO') . ' ' . display_date($end_date);
                } else {
                    $start_date = null;
                    $end_date = null;
                    $date = null;
                }
                $range = array($start_date, $end_date);
                $data['status'] = $status;
                $data['return_stock_items'] = $this->return_stock_model->get_return_stock_items_report($data['status'], $range);
//                $viewfile = $this->load->view('admin/report/payment_report_pdf', $data, TRUE);
                if (is_numeric($status)) {
                    $status = client_name($status);
                }
                $title = lang($filterBy) . ' ' . lang('report') . '- ' . $status . '- ' . $date;
            }
        }
        $this->load->helper('dompdf');
        if (!empty($viewfile) && !empty($title)) {
            pdf_create($viewfile, slug_it($title));
        }

        //        $data['subview'] = $this->load->view('admin/report/sales_report', $data, TRUE);
        //        $this->load->view('admin/_layout_main', $data); //page load
    }

    // Get Clients
    public function getClients()
    {
        $data[] = array("client_id" => 'all', "name" => lang('all'));
        // Fetch users
        if (!empty($this->input->get("q")) && $this->input->get("q") != 'all') {
            $this->db->select('*');
            $this->db->where("name like '%" . $this->input->get("q") . "%' ");
            $this->db->or_where("mobile like '%" . $this->input->get("q") . "%' ");
            $fetched_records = $this->db->limit(10)->get('tbl_client');
            $users = $fetched_records->result_array();

            // Initialize Array with fetched data
            $data = array();
            foreach ($users as $user) {
                $name = $user['name'] . ' , mobile: ' . $user['mobile'];
                $data[] = array("client_id" => $user['client_id'], "name" => $name);
            }
        }

        echo json_encode($data);


    }


    // Get Suppliers
    public function getSuppliers()
    {
        $data[] = array("client_id" => 'all', "name" => lang('all'));
        // Fetch users
        if (!empty($this->input->get("q")) && $this->input->get("q") != 'all') {
            $this->db->select('*');
            $this->db->where("name like '%" . $this->input->get("q") . "%' ");
            $this->db->or_where("mobile like '%" . $this->input->get("q") . "%' ");
            $fetched_records = $this->db->limit(10)->get('tbl_suppliers');
            $users = $fetched_records->result_array();

            // Initialize Array with fetched data
            $data = array();
            foreach ($users as $user) {
                $name = $user['name'] . ' , mobile: ' . $user['mobile'];
                $data[] = array("supplier_id" => $user['supplier_id'], "name" => $name);
            }
        }

        echo json_encode($data);


    }

    // Get Clients
    public function getClientsMobile()
    {
        $data[] = array("client_id" => 'all', "name" => lang('all'));
        // Fetch users
        if (!empty($this->input->get("q")) && $this->input->get("q") != 'all') {
            $this->db->select('*');
            $this->db->where("mobile like '%" . $this->input->get("q") . "%' ");
            $fetched_records = $this->db->limit(10)->get('tbl_client');
            $users = $fetched_records->result_array();

            // Initialize Array with fetched data
            $data = array();
            foreach ($users as $user) {
                $data[] = array("client_id" => $user['client_id'], "name" => $user['name']);
            }
        }

        echo json_encode($data);
    }

    public function getItems()
    {
        $data[] = array("saved_items_id" => 'all', "name" => lang('all'));

        // Fetch users
        if (!empty($this->input->get("q")) && $this->input->get("q") != 'all') {
            $this->db->select('*');
            $this->db->where("item_name like '%" . $this->input->get("q") . "%' ");
            $fetched_records = $this->db->limit(10)->get('tbl_saved_items');
            $users = $fetched_records->result_array();

            // Initialize Array with fetched data
            $data = array();
            foreach ($users as $user) {
                $data[] = array("saved_items_id" => $user['saved_items_id'], "name" => $user['item_name']);
            }
        }

        echo json_encode($data);
    }

    public function getUsers()
    {
        $data[] = array("user_id" => 'all', "name" => lang('all'));

        // Fetch users
        if (!empty($this->input->get("q")) && $this->input->get("q") != 'all') {
            $this->db->select('*');
            $this->db->where("username like '%" . $this->input->get("q") . "%' ");
            $fetched_records = $this->db->limit(10)->get('tbl_users');
            $users = $fetched_records->result_array();

            // Initialize Array with fetched data
            $data = array();
            foreach ($users as $user) {
                $data[] = array("user_id" => $user['user_id'], "name" => $user['username']);
            }
        }

        echo json_encode($data);
    }


    public function purchase_report_details()
    {
        $data['title'] = lang('purchase_report_details');
        $start_date = null;
        $end_date = null;
        $status = 'all';
        $supplier_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $supplier_id = $this->input->post('supplier_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['supplier_id'] = $supplier_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_purchases'] = $this->purchase_model->get_purchase_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/purchase_report_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function purchase_report_summarized()
    {
        $data['title'] = lang('purchase_report_summarized');
        $start_date = null;
        $end_date = null;
        $status = 'all';
        $supplier_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $supplier_id = $this->input->post('supplier_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['supplier_id'] = $supplier_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_purchases'] = $this->purchase_model->get_purchase_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/purchase_report_summarized', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function suppliers_report_details()
    {
        $data['title'] = lang('suppliers_report_details');
        $start_date = null;
        $end_date = null;
        $status = 'all';
        $supplier_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $supplier_id = $this->input->post('supplier_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['supplier_id'] = $supplier_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_purchases'] = $this->purchase_model->get_purchase_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/suppliers_report_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function suppliers_report_summarized()
    {
        $data['title'] = lang('suppliers_report_summarized');
        $start_date = null;
        $end_date = null;
        $status = 'all';
        $supplier_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $supplier_id = $this->input->post('supplier_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['supplier_id'] = $supplier_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_purchases'] = $this->purchase_model->get_purchase_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/suppliers_report_summarized', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function clients_report_details()
    {
        $data['title'] = lang('clients_report_details');
        $start_date = null;
        $end_date = null;
        $status = 'all';
        $client_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $client_id = $this->input->post('client_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['client_id'] = $client_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_invoices'] = $this->invoice_model->get_sales_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/clients_report_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function clients_report_summarized()
    {
        $data['title'] = lang('clients_report_summarized');

        $start_date = null;
        $end_date = null;
        $status = 'all';
        $client_id = 'all';
        $warehouse_id = 'all';
        $saved_items_id = 'all';
        $customer_group_id = 'all';
        $user_id = 'all';
        if ($this->input->post()) {
            $range = explode('-', $this->input->post('range', true));
            if (!empty($range[0])) {
                $start_date = date('Y-m-d', strtotime($range[0]));
                $end_date = date('Y-m-d', strtotime($range[1]));
                $data['range'] = array($start_date, $end_date);
            }
            $status = $this->input->post('status', true);
            $client_id = $this->input->post('client_id', true);
            $warehouse_id = $this->input->post('warehouse_id', true);
            $saved_items_id = $this->input->post('saved_items_id', true);
            $customer_group_id = $this->input->post('customer_group_id', true);
            $user_id = $this->input->post('user_id', true);
        }
        $range = array($start_date, $end_date);
        $data['status'] = $status;
        $data['client_id'] = $client_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_items_id'] = $saved_items_id;
        $data['customer_group_id'] = $customer_group_id;
        $data['user_id'] = $user_id;
        $data['all_invoices'] = $this->invoice_model->get_sales_report_details($data, $range);
        $data['subview'] = $this->load->view('admin/report/clients_report_summarized', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function finished_items()
    {
        $data['title'] = lang('finished_items');
        $warehouse_id = 'all';
        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id');
        }
        $data['warehouse_id'] = $warehouse_id;
        $data['all_finished_items'] = $this->warehouse_model->get_finished_items($data);
        $data['subview'] = $this->load->view('admin/report/finished_items', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function items_report()
    {
        $data['title'] = lang('items_report');

        $saved_item_id = 'all';
        $warehouse_id = '';
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_item_id'] = $saved_item_id;

        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id');
            $saved_item_id = $this->input->post('saved_item_id');
            $data['warehouse_id'] = $warehouse_id;

            if ($saved_item_id && $saved_item_id != 'all') {
                $data['saved_item_id'] = $saved_item_id;
                $savedItemInfo = get_row('tbl_saved_items', array('saved_items_id' => $saved_item_id));

                $itemInfoQuantity = $this->warehouse_model->get_items_info($data);
                if ($savedItemInfo) {
                    $savedItemInfo->purchase_quantity = $itemInfoQuantity['purchase_quantity'];
                    $savedItemInfo->sale_quantity = $itemInfoQuantity['sale_quantity'];
                    $savedItemInfo->returns_quantity = $itemInfoQuantity['returns_quantity'];
                    $data['itemInfo'] = $savedItemInfo;

                } else {
                    $data['itemInfo'] = null;

                }


            } else {
                $data['saved_item_id'] = $saved_item_id;

                $items = get_result('tbl_saved_items');
                foreach ($items as $item) {
                    $data_info['saved_item_id'] = $item->saved_items_id;
                    $data_info['warehouse_id'] = $warehouse_id;
                    $itemInfoQuantity = $this->warehouse_model->get_items_info($data_info);
                    $item->purchase_quantity = $itemInfoQuantity['purchase_quantity'];
                    $item->sale_quantity = $itemInfoQuantity['sale_quantity'];
                    $item->returns_quantity = $itemInfoQuantity['returns_quantity'];
                }
                $data['items'] = $items;

            }

        }

        $data['subview'] = $this->load->view('admin/report/items_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function transfer_items_report()
    {
        $data['title'] = lang('transfer_items_report');

        $saved_item_id = 'all';
        $warehouse_id = '';
        $user_id = 'all';
        $data['warehouse_id'] = $warehouse_id;
        $data['saved_item_id'] = $saved_item_id;
        $data['user_id'] = $user_id;

        if ($this->input->post()) {

            $warehouse_id = $this->input->post('warehouse_id');
            $saved_item_id = $this->input->post('saved_item_id');
            $user_id = $this->input->post('user_id');

            $data['warehouse_id'] = $warehouse_id;
            $data['saved_item_id'] = $saved_item_id;
            $data['user_id'] = $user_id;

            $where = array();

            if ($warehouse_id && $warehouse_id != 'all')
                $where['warehouse_id'] = $warehouse_id;

            if ($saved_item_id && $saved_item_id != 'all')
                $where['tbl_transfer_itemlist.saved_items_id'] = $saved_item_id;

            if ($user_id && $user_id != 'all')
                $where['user_id'] = $user_id;

            $this->db->select('*,tbl_transfer_itemlist.quantity as transfer_qty, tbl_saved_items.quantity as base_qty');
            $this->db->join('tbl_transfer_item', 'tbl_transfer_item.transfer_item_id=tbl_transfer_itemlist.transfer_item_id');
            $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id=tbl_transfer_itemlist.saved_items_id');
            $this->db->join('tbl_users', 'tbl_users.user_id=tbl_transfer_item.user_id');

            $this->db->where($where);
            $items = $this->db->get('tbl_transfer_itemlist')->result();;

            $data['items'] = $items;
        }

        $data['subview'] = $this->load->view('admin/report/transfer_items_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function payroll_report()
    {
        $data['title'] = lang('payroll_summary');
        $search_type = $this->input->post('search_type', true);
        if (!empty($search_type)) {
            $data['search_type'] = $search_type;
            if ($search_type == 'employee') {
                $data['user_id'] = $this->input->post('user_id', true);
            }
            if ($search_type == 'month') {
                $data['by_month'] = $this->input->post('by_month', true);
            }
            if ($search_type == 'period') {
                $data['start_month'] = $this->input->post('start_month', true);
                $data['end_month'] = $this->input->post('end_month', true);
            }
        }
        $data['subview'] = $this->load->view('admin/report/payroll_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }


    public function payment_historyList($user_id = null)
    {
        if (!empty($user_id)) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_salary_payment';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_salary_payment.user_id = tbl_account_details.user_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->order = array('salary_payment_id' => 'desc');

            $where = array('tbl_salary_payment.user_id' => $user_id);
            $all_payment_history = make_datatables($where);

            $pdata = array();
            foreach ($all_payment_history as $p_key => $v_history) {
                if (!empty($v_history)) {
                    $salary_payment_history = get_result('tbl_salary_payment_details', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_salary_amount = 0;
                    if (!empty($salary_payment_history)) {
                        foreach ($salary_payment_history as $v_payment_history) {
                            if (is_numeric($v_payment_history->salary_payment_details_value)) {
                                if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                    $rate = $v_payment_history->salary_payment_details_value;
                                }
                                $total_salary_amount += $v_payment_history->salary_payment_details_value;
                            }
                        }
                    }
                    $salary_allowance_info = get_result('tbl_salary_payment_allowance', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_allowance = 0;
                    if (!empty($salary_allowance_info)) {
                        foreach ($salary_allowance_info as $v_salary_allowance_info) {
                            $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                        }
                    }
                    if (empty($rate)) {
                        $rate = 0;
                    }
                    $salary_deduction_info = get_result('tbl_salary_payment_deduction', array('salary_payment_id' => $v_history->salary_payment_id));
                    $total_deduction = 0;
                    if (!empty($salary_deduction_info)) {
                        foreach ($salary_deduction_info as $v_salary_deduction_info) {
                            $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                        }
                    }

                    $total_paid_amount = $total_salary_amount + $total_allowance - $rate;

                    $action = null;
                    $psub_array = array();
                    $psub_array[] = date('F-Y', strtotime($v_history->payment_month));
                    $psub_array[] = display_date($v_history->paid_date);
                    $psub_array[] = display_money($total_paid_amount, default_currency());
                    $psub_array[] = display_money($total_deduction, default_currency());
                    $psub_array[] = display_money($net_salary = $total_paid_amount - $total_deduction, default_currency());

                    if (!empty($v_history->fine_deduction)) {
                        $fine_deduction = $v_history->fine_deduction;
                    } else {
                        $fine_deduction = 0;
                    }
                    $psub_array[] = display_money($fine_deduction, default_currency());
                    $psub_array[] = display_money($net_salary - $fine_deduction, default_currency());
                    //
                    $total_invoice = $this->invoice_model->paid_by_date_and_employee(date('Y'), date('m'), $v_history->user_id);
                    $ratio = $v_history->ratio_on_sale;
                    $total_with_ratio = $total_invoice * $ratio / 100;
                    $psub_array[] = display_money($total_invoice, default_currency());
                    $psub_array[] = display_money($total_with_ratio, default_currency());

                    $psub_array[] = '<a href="' . base_url() . 'admin/payroll/salary_payment_details/' . $v_history->salary_payment_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a>';
                    $pdata[] = $psub_array;
                }
            }
            render_table($pdata, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function payment_historyMonth($month = null)
    {
        if ($this->input->is_ajax_request()) {

            $this->load->model('datatables');
            $this->datatables->table = 'tbl_salary_payment';
            $this->datatables->join_table = array('tbl_account_details', 'tbl_designations', 'tbl_departments');
            $this->datatables->join_where = array('tbl_salary_payment.user_id = tbl_account_details.user_id', 'tbl_designations.designations_id  = tbl_account_details.designations_id', 'tbl_departments.departments_id  = tbl_designations.departments_id');
            $this->datatables->column_order = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->column_search = array('tbl_account_details.fullname', 'tbl_account_details.employment_id', 'tbl_salary_payment.comments', 'tbl_salary_payment.payment_type', 'tbl_salary_payment.payment_month', 'tbl_salary_payment.fine_deduction', 'tbl_salary_payment.paid_date');
            $this->datatables->order = array('salary_payment_id' => 'desc');

            $where = array('tbl_salary_payment.payment_month' => $month);
            $fetch_data = make_datatables($where);

            $data = array();
            foreach ($fetch_data as $_key => $v_payroll) {

                $salary_payment_history = get_result('tbl_salary_payment_details', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_salary_amount = 0;
                if (!empty($salary_payment_history)) {
                    foreach ($salary_payment_history as $v_payment_history) {
                        if (is_numeric($v_payment_history->salary_payment_details_value)) {
                            if ($v_payment_history->salary_payment_details_label == 'overtime_salary') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            } elseif ($v_payment_history->salary_payment_details_label == 'hourly_rates') {
                                $rate = $v_payment_history->salary_payment_details_value;
                            }
                            $total_salary_amount += $v_payment_history->salary_payment_details_value;
                        }
                    }
                }
                $salary_allowance_info = get_result('tbl_salary_payment_allowance', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_allowance = 0;
                if (!empty($salary_allowance_info)) {
                    foreach ($salary_allowance_info as $v_salary_allowance_info) {
                        $total_allowance += $v_salary_allowance_info->salary_payment_allowance_value;
                    }
                }
                if (empty($rate)) {
                    $rate = 0;
                }
                $salary_deduction_info = get_result('tbl_salary_payment_deduction', array('salary_payment_id' => $v_payroll->salary_payment_id));
                $total_deduction = 0;
                if (!empty($salary_deduction_info)) {
                    foreach ($salary_deduction_info as $v_salary_deduction_info) {
                        $total_deduction += $v_salary_deduction_info->salary_payment_deduction_value;
                    }
                }

                $total_paid_amount = $total_salary_amount + $total_allowance - $rate;

                $action = null;
                $sub_array = array();
                $sub_array[] = date('F-Y', strtotime($v_payroll->payment_month));
                $sub_array[] = display_date($v_payroll->paid_date);
                $sub_array[] = display_money($total_paid_amount, default_currency());
                $sub_array[] = display_money($total_deduction, default_currency());
                $sub_array[] = display_money($net_salary = $total_paid_amount - $total_deduction, default_currency());

                if (!empty($v_payroll->fine_deduction)) {
                    $fine_deduction = $v_payroll->fine_deduction;
                } else {
                    $fine_deduction = 0;
                }
                $sub_array[] = display_money($fine_deduction, default_currency());
                $sub_array[] = display_money($net_salary - $fine_deduction, default_currency());

                $total_invoice = $this->invoice_model->paid_by_date_and_employee(date('Y'), date('m'), $v_payroll->user_id);
                $ratio = $v_payroll->ratio_on_sale;
                $total_with_ratio = $total_invoice * $ratio / 100;
                $sub_array[] = display_money($total_invoice, default_currency());
                $sub_array[] = display_money($total_with_ratio, default_currency());
                $sub_array[] = '<a href="' . base_url() . 'admin/payroll/salary_payment_details/' . $v_payroll->salary_payment_id . '"
                               class="btn btn-info btn-xs" title="' . lang('view') . '" data-toggle="modal"
                               data-target="#myModal_lg"><span class="fa fa-list-alt"></span></a>';
                $data[] = $sub_array;
            }

            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function trail_balance_report()
    {
        $data['title'] = lang('trail_balance_report');
        $data['transactions_report'] = $this->get_transactions_report();
        $data['subview'] = $this->load->view('admin/report/trail_balance_report', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function transactions_reportList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_transactions';
            $this->datatables->join_table = array('tbl_accounts', 'tbl_client');
            $this->datatables->join_where = array('tbl_accounts.account_id=tbl_transactions.account_id', 'tbl_transactions.paid_by=tbl_client.client_id');
            $this->datatables->column_order = array('name', 'type', 'date', 'amount', 'tbl_accounts.account_name');
            $this->datatables->column_search = array('name', 'type', 'date', 'amount', 'tbl_accounts.account_name');
            $this->datatables->order = array('transactions_id' => 'desc');
            $where = null;
            if (!empty($filterBy)) {
                $where = array('tbl_transactions.account_id' => $filterBy);
            }
            // get all invoice
            $fetch_data = $this->datatables->get_transactions_report($filterBy);

            $data = array();

            $total_amount = 0;
            $total_debit = 0;
            $total_credit = 0;
            $total_balance = 0;
            foreach ($fetch_data as $_key => $v_transaction) {
                $action = null;
                $account_info = $this->transactions_model->check_by(array('account_id' => $v_transaction->account_id), 'tbl_accounts');

                $sub_array = array();
                $client_name = '-';
                $client_info = $this->transactions_model->check_by(array('client_id' => $v_transaction->paid_by), 'tbl_client');
                if (!empty($client_info)) {
                    $client_name = $client_info->name;
                }
                $sub_array[] = $client_name;
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_transaction->date));
                $sub_array[] = lang($v_transaction->type);
                $sub_array[] = display_money($v_transaction->credit, default_currency());
                $sub_array[] = display_money($v_transaction->debit, default_currency());

                $data[] = $sub_array;

                $total_credit += $v_transaction->credit;
                $total_debit += $v_transaction->debit;
            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function warehouse_inventory($id = NULL, $opt = null)
    {
        $data['title'] = lang('warehouse_inventory');
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                $data['items_info'] = $this->items_model->check_by(array('saved_items_id' => $id), 'tbl_saved_items');
            } else {
                if ($id == 'manufacturer') {
                    $data['active'] = 4;
                    $data['manufacturer_info'] = $this->items_model->check_by(array('manufacturer_id' => $opt), 'tbl_manufacturer');
                } else {
                    $data['active'] = 3;
                    $data['group_info'] = $this->items_model->check_by(array('customer_group_id' => $opt), 'tbl_customer_group');
                }
            }
        } else {
            $data['active'] = 1;
        }
        $data['warehouseList'] = $this->items_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published'));
        $data['all_customer_group'] = $this->items_model->select_data('tbl_customer_group', 'customer_group_id', 'customer_group', array('type' => 'items'));
        $data['all_manufacturer'] = $this->items_model->select_data('tbl_manufacturer', 'manufacturer_id', 'manufacturer');
//        $data['all_items']  = $this->db->join('tbl_warehouses_products','tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id')->get('tbl_saved_items')->result();
        $data['dropzone'] = 1;
        $data['subview'] = $this->load->view('admin/report/warehouse_inventory', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function warehouse_inventoryList($group_id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_saved_items';
            $this->datatables->join_table = array('tbl_warehouses_products', 'tbl_warehouse');
            $this->datatables->join_where = array('tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id', 'tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id');

            $custom_field = custom_form_table_search(18);
            $action_array = array('saved_items_id');
            $main_column = array('item_name', 'code', 'hsn_code', 'tbl_warehouses_products.quantity', 'unit_cost', 'unit_type', 'tbl_customer_group.customer_group', 'tbl_manufacturer.manufacturer', 'item_location_in_stock');
            $result = array_merge($main_column, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->order = array('saved_items_id' => 'desc');

            // get all invoice
            if (!empty($type) && $type == 'by_group') {
                $where = array('tbl_saved_items.customer_group_id' => $group_id);
            } else if (!empty($type) && $type == 'by_manufacturer') {
                $where = array('tbl_saved_items.manufacturer_id' => $group_id);
            } else if (!empty($type) && $type == 'by_warehouse') {
                $where = array('tbl_warehouses_products.warehouse_id' => $group_id);
            } else {
                $where = null;
            }
            $fetch_data = make_datatables($where);
            $data = array();
            $edited = can_action('39', 'edited');
            foreach ($fetch_data as $_key => $v_items) {
                $action = null;
                $item_name = !empty($v_items->item_name) ? $v_items->item_name : $v_items->item_name;

                $sub_array = array();
                $sub_array[] = '<a data-toggle="modal" data-target="#myModal_extra_lg" href="' . base_url('admin/items/items_details/' . $v_items->saved_items_id) . '"><strong class="block">' . $item_name . '</strong></a>';

                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    $sub_array[] = $v_items->hsn_code;
                }
                if (!empty(admin())) {
                    $sub_array[] = display_money($v_items->cost_price, default_currency());
                }
                $sub_array[] = display_money($v_items->unit_cost, default_currency());
                $sub_array[] = $v_items->unit_type;
                if (!is_numeric($v_items->tax_rates_id)) {
                    $tax_rates = json_decode($v_items->tax_rates_id);
                } else {
                    $tax_rates = null;
                }
                $rates = null;
                if (!empty($tax_rates)) {
                    if (is_array($tax_rates)) {
                        foreach ($tax_rates as $key => $tax_id) {
                            $taxes_info = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                            if (!empty($taxes_info)) {
                                $rates .= $key + 1 . '. ' . $taxes_info->tax_rate_name . '&nbsp;&nbsp; (' . $taxes_info->tax_rate_percent . '% ) <br>';
                            }
                        }
                    } else {
                        $rates = $this->db->where('tax_rates_id', $tax_rates)->get('tbl_tax_rates')->row()->tax_rate_name;
                    }
                }
                $sub_array[] = $rates;

                $sub_array[] = (!empty($v_items->customer_group) ? '<span class="tags">' . $v_items->customer_group . '</span>' : ' ');
                $sub_array[] = (!empty($v_items->warehouse_name) ? '<span class="tags">' . $v_items->warehouse_name . '</span>' : ' ');
                $sub_array[] = (!empty($v_items->quantity) ? '<span class="tags">' . $v_items->quantity . '</span>' : ' ');
                $custom_form_table = custom_form_table(18, $v_items->saved_items_id);

                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }

                if (!empty($edited)) {
                    $action .= '<span data-toggle="tooltip" data-placement="top" title="Change Quantity">' . btn_edit_modal('admin/report/edit_item_quantity/' . $v_items->saved_items_id . '/' . $v_items->warehouse_id . '/' . $v_items->quantity) . '</span>' . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function set_item_quantity()
    {
        $warehouse_id = $this->input->post("warehouse_id", true);
        $quantity = $this->input->post("quantity", true);
        $product_id = $this->input->post("product_id", true);

        $action = 'activity_edit_item_quantity';

        $_data['warehouse_id'] = $warehouse_id;
        $_data['product_id'] = $product_id;
        $check = get_row('tbl_warehouses_products', array('warehouse_id ' => $_data['warehouse_id'], 'product_id' => $_data['product_id']));
        $this->items_model->_table_name = 'tbl_warehouses_products';
        $this->items_model->_primary_key = 'id';
        if (!empty($check)) {
            $_data['quantity'] = $quantity;
            $_data['product_id'] = $product_id;
            $_data_id = $check->id;
            $this->items_model->save($_data, $_data_id);

            if ($quantity < $check->quantity)
                $action = 'activity_edit_item_quantity_discount';
            elseif ($quantity > $check->quantity)
                $action = 'activity_edit_item_quantity_add';

        }

        //
        $id = $product_id;
        $msg = lang('edit_item_quantity');
        $items_info = $this->items_model->check_by(array('saved_items_id' => $id), 'tbl_saved_items');
        $warehouse_info = $this->warehouse_model->check_by(array('warehouse_id' => $warehouse_id), 'tbl_warehouse');

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'items',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $check->quantity,
            'value2' => $quantity,
            'item_name' => $items_info->item_name,
            'warehouse_id' => $warehouse_id,
            'warehouse_name' => $warehouse_info->warehouse_name,

        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/report/warehouse_inventory');
    }

    public function edit_item_quantity($saved_items_id, $warehouse_id, $quantity)
    {
        $data['saved_items_id'] = $saved_items_id;
        $data['warehouse_id'] = $warehouse_id;
        $data['quantity'] = $quantity;
        $data['modal_subview'] = $this->load->view('admin/report/_modal_edit_item_quantity', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function items_values($id = NULL, $opt = null)
    {
        $data['title'] = lang('items_values');
        if (!empty($id)) {
            if (is_numeric($id)) {
                $data['active'] = 2;
                $data['items_info'] = $this->items_model->check_by(array('saved_items_id' => $id), 'tbl_saved_items');
            } else {
                if ($id == 'manufacturer') {
                    $data['active'] = 4;
                    $data['manufacturer_info'] = $this->items_model->check_by(array('manufacturer_id' => $opt), 'tbl_manufacturer');
                } else {
                    $data['active'] = 3;
                    $data['group_info'] = $this->items_model->check_by(array('customer_group_id' => $opt), 'tbl_customer_group');
                }
            }
        } else {
            $data['active'] = 1;
        }
        $data['warehouseList'] = $this->items_model->select_data('tbl_warehouse', 'warehouse_id', 'warehouse_name', array('status' => 'published'));
        $data['all_customer_group'] = $this->items_model->select_data('tbl_customer_group', 'customer_group_id', 'customer_group', array('type' => 'items'));
        $data['all_manufacturer'] = $this->items_model->select_data('tbl_manufacturer', 'manufacturer_id', 'manufacturer');
        $data['dropzone'] = 1;
        $data['subview'] = $this->load->view('admin/report/items_values', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function items_valuesList($group_id = null, $type = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_saved_items';
            $this->datatables->join_table = array('tbl_warehouses_products', 'tbl_warehouse');
            $this->datatables->join_where = array('tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id', 'tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id');

            $custom_field = custom_form_table_search(18);
            $action_array = array('saved_items_id');
            $main_column = array('item_name', 'code', 'hsn_code', 'tbl_warehouses_products.quantity', 'unit_cost', 'unit_type', 'tbl_customer_group.customer_group', 'tbl_manufacturer.manufacturer', 'item_location_in_stock');
            $result = array_merge($main_column, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->order = array('saved_items_id' => 'desc');

            // get all invoice
            if (!empty($type) && $type == 'by_group') {
                $where = array('tbl_saved_items.customer_group_id' => $group_id);
            } else if (!empty($type) && $type == 'by_manufacturer') {
                $where = array('tbl_saved_items.manufacturer_id' => $group_id);
            } else if (!empty($type) && $type == 'by_warehouse') {
                $where = array('tbl_warehouses_products.warehouse_id' => $group_id);
            } else {
                $where = null;
            }
            $fetch_data = make_datatables($where);
            $data = array();
            $edited = can_action('39', 'edited');
            foreach ($fetch_data as $_key => $v_items) {
                $action = null;
                $item_name = !empty($v_items->item_name) ? $v_items->item_name : $v_items->item_name;

                $sub_array = array();
                $sub_array[] = '<a data-toggle="modal" data-target="#myModal_extra_lg" href="' . base_url('admin/items/items_details/' . $v_items->saved_items_id) . '"><strong class="block">' . $item_name . '</strong></a>';

                $invoice_view = config_item('invoice_view');
                if (!empty($invoice_view) && $invoice_view == '2') {
                    $sub_array[] = $v_items->hsn_code;
                }
                if (!empty(admin())) {
                    $sub_array[] = display_money($v_items->cost_price, default_currency());
                }
                $sub_array[] = display_money($v_items->unit_cost, default_currency());
                $sub_array[] = $v_items->unit_type;
                if (!is_numeric($v_items->tax_rates_id)) {
                    $tax_rates = json_decode($v_items->tax_rates_id);
                } else {
                    $tax_rates = null;
                }
                $rates = null;
                if (!empty($tax_rates)) {
                    if (is_array($tax_rates)) {
                        foreach ($tax_rates as $key => $tax_id) {
                            $taxes_info = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                            if (!empty($taxes_info)) {
                                $rates .= $key + 1 . '. ' . $taxes_info->tax_rate_name . '&nbsp;&nbsp; (' . $taxes_info->tax_rate_percent . '% ) <br>';
                            }
                        }
                    } else {
                        $rates = $this->db->where('tax_rates_id', $tax_rates)->get('tbl_tax_rates')->row()->tax_rate_name;
                    }
                }
                $sub_array[] = $rates;

                $sub_array[] = (!empty($v_items->customer_group) ? '<span class="tags">' . $v_items->customer_group . '</span>' : ' ');
                $sub_array[] = (!empty($v_items->warehouse_name) ? '<span class="tags">' . $v_items->warehouse_name . '</span>' : ' ');
                $sub_array[] = (!empty($v_items->quantity) ? '<span class="tags">' . $v_items->quantity . '</span>' : ' ');
                $custom_form_table = custom_form_table(18, $v_items->saved_items_id);

                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $data[] = $sub_array;
            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function differences_in_inventory()
    {
        $data['title'] = lang('differences_in_inventory');
        $warehouse_id = 'all';
        $activity = 'all';

        $this->warehouse_model->_table_name = 'tbl_activities';
        $this->warehouse_model->_order_by = 'activities_id';

        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id');
            $activity = $this->input->post('activity');

            if ($activity != 'all') {
                if ($activity == 'activity_edit_item_quantity_discount') {
                    $this->db->where('activity', 'activity_edit_item_quantity_discount');
                } elseif ($activity == 'activity_edit_item_quantity_add') {
                    $this->db->where('activity =', 'activity_edit_item_quantity_add');
                }
            } else {
                $this->db->where_in('activity', ['activity_edit_item_quantity_add', 'activity_edit_item_quantity_discount']);

            }

            if ($warehouse_id != 'all') {
                $this->db->where('warehouse_id =', $warehouse_id);
            }
        }
        $this->db->where('module', 'items');
        $data['items'] = $this->warehouse_model->get();

        $data['warehouse_id'] = $warehouse_id;
        $data['activity'] = $activity;
        $data['subview'] = $this->load->view('admin/report/differences_in_inventory', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function warehouses_report_details()
    {
        $data['title'] = lang('warehouses_report_details');
        $warehouse_id = 'all';
        $quantity = 'all';
        $cost = 'all';
        $start_date = null;
        $end_date = null;

        $this->warehouse_model->_table_name = 'tbl_warehouses_products';
        $this->warehouse_model->_order_by = 'id';
        $this->db->join('tbl_saved_items', 'tbl_saved_items.saved_items_id=tbl_warehouses_products.product_id');
        $this->db->join('tbl_warehouse', 'tbl_warehouse.warehouse_id=tbl_warehouses_products.warehouse_id');

        if ($this->input->post()) {
            $warehouse_id = $this->input->post('warehouse_id');
            $quantity = $this->input->post('quantity');
//            $range = explode('-', $this->input->post('range', true));
//            if (!empty($range[0])) {
//                $start_date = date('Y-m-d', strtotime($range[0]));
//                $end_date = date('Y-m-d', strtotime($range[1]));
//                $this->db->where('tbl_warehouses_products.quantity >=', 0);
//
//                $data['range'] = array($start_date, $end_date);
//            }

            if ($quantity != 'all') {
                if ($quantity == 'available_quantity') {
                    $this->db->where('tbl_warehouses_products.quantity >=', 0);
                } elseif ($quantity == 'negative_quantity') {
                    $this->db->where('tbl_warehouses_products.quantity <', 0);
                }
            }

            if ($warehouse_id != 'all') {
                $this->db->where('tbl_warehouses_products.warehouse_id =', $warehouse_id);
            }

            if ($cost != 'all') {
                if ($cost == 'negative_cost_price') {
                    $this->db->where('cost_price <', 0);
                }
            }
        }

//        $range = array($start_date, $end_date);

        $data['items'] = $this->warehouse_model->get();

        $data['warehouse_id'] = $warehouse_id;
        $data['quantity'] = $quantity;
        $data['cost'] = $cost;
        $data['subview'] = $this->load->view('admin/report/warehouses_report_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data); //page load
    }

    public function set_items_quantity()
    {
        $warehouse_id = $this->input->post("warehouse_id", true);
        $quantity = $this->input->post("quantity", true);
        $product_id = $this->input->post("product_id", true);

        $action = 'activity_edit_item_quantity';

        $_data['warehouse_id'] = $warehouse_id;
        $_data['product_id'] = $product_id;
        $check = get_row('tbl_warehouses_products', array('warehouse_id ' => $_data['warehouse_id'], 'product_id' => $_data['product_id']));
        $this->items_model->_table_name = 'tbl_warehouses_products';
        $this->items_model->_primary_key = 'id';
        if (!empty($check)) {
            $_data['quantity'] = $quantity;
            $_data['product_id'] = $product_id;
            $_data_id = $check->id;
            $this->items_model->save($_data, $_data_id);

            if ($quantity < $check->quantity)
                $action = 'activity_edit_item_quantity_discount';
            elseif ($quantity > $check->quantity)
                $action = 'activity_edit_item_quantity_add';

        }

        //
        $id = $product_id;
        $msg = lang('edit_item_quantity');
        $items_info = $this->items_model->check_by(array('saved_items_id' => $id), 'tbl_saved_items');
        $warehouse_info = $this->warehouse_model->check_by(array('warehouse_id' => $warehouse_id), 'tbl_warehouse');

        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'items',
            'module_field_id' => $id,
            'activity' => $action,
            'icon' => 'fa-circle-o',
            'value1' => $check->quantity,
            'value2' => $quantity,
            'item_name' => $items_info->item_name,
            'warehouse_id' => $warehouse_id,
            'warehouse_name' => $warehouse_info->warehouse_name,

        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        // messages for user
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/report/warehouse_inventory');
    }

    public function edit_items_quantity()
    {
        $data['modal_subview'] = $this->load->view('admin/report/_modal_edit_items_quantity', [], FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function get_item_quantity()
    {
        $saved_items_id = $this->input->post('saved_items_id');
        $this->db->where(array('tbl_warehouses_products.id' => $saved_items_id));
        $this->db->join('tbl_warehouses_products', 'tbl_warehouses_products.product_id=tbl_saved_items.saved_items_id');
        $info = $this->db->get('tbl_saved_items')->row();
        echo json_encode($info);
    }


}