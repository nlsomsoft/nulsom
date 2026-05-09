<?php
class AdmsettleModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function insert_captain_deposit_amount_by_admin($option) {
        // $DB2 = $this->load->database('captain',TRUE);
        // $data = array(
        //     'amount' => $option['auth_amount'],
        //     'company' => $option['company'],
        //     'userid' => $this->session->userdata('userid'),
        //     'ip' => $this->input->ip_address(),
        //     'reg_time' => $this->current_time
        // );
        // $DB2->insert('deposit_list', $data);
        // $query = $DB2->insert_id();
        // return $query;
        $data = array(
            'amount' => $option['auth_amount'],
            'company' => $option['company'],
            'userid' => $this->session->userdata('userid'),
            'ip' => $this->input->ip_address(),
            'reg_time' => $this->current_time
        );
        $this->db->insert('company_deposit_list', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function modify_deposit_memo_by_admin($option) {
        $data = array(
            'memo' => $option['memo'],
            'receipt' => $option['receipt'],
            'onse_receipt' => $option['onse_receipt'],
            'onse_date' => $option['onse_date'],
            'onse_amount' => $option['onse_amount'],
        );
        $this->db->where('xid', $option['xid']);
        $query = $this->db->update('company_deposit_list', $data);
        return $query;
    }
    function get_company_sendlist_by_admin($option, $option1) {
        if ($option['company'] != '') $this->db->where('company', $option['company']);
        if ($option1['cate'] != '') $this->db->where('cate', $option1['cate']);
        if ($option['yyyymm'] != '') {
            $date_from = str_replace('-', '', $option['yyyymm']);
            $this->db->where('yyyymm', $date_from);
        }
        $query = $this->db->get('company_send_list')->result();
        return $query;
    }
    function get_deposit_sum_by_admin($option) {
        $this->db->select('SUM(amount) total_income, SUM(onse_amount) total_expense');
        $this->db->where('company', $option['company']);
        $this->db->where('status', '1');
        $query = $this->db->get('company_deposit_list')->row();
        return $query;
    }
    function get_deposit_list_by_admin($option) {
        $this->db->where('company', $option['company']);
        $this->db->where('status', '1');
        $date_format = "DATE_FORMAT(reg_time, '%Y-%m') = '{$option['date_from']}'";
        $this->db->where($date_format);
        $query = $this->db->get('company_deposit_list')->result();
        return $query;
    }
    function get_sales_mm_by_admin($option) {
        $this->db->select('productcode, price, SUM(success) success, SUM(success * price) amount');
        $this->db->where('status >', '0');
        $date_array = explode('-', $option['date_from']);
        $this->db->where('yyyy', (int)$date_array[0]);
        $this->db->where('mm', (int)$date_array[1]);
        $this->db->group_by(array('productcode','price'));
        $this->db->order_by('price', 'asc');
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_purchase_mm_by_admin($option) {
        $this->db->select('productcode, channel_price, SUM(success) success, SUM(success * channel_price) amount');
        $this->db->where('status >', '0');
        $date_array = explode('-', $option['date_from']);
        $this->db->where('yyyy', (int)$date_array[0]);
        $this->db->where('mm', (int)$date_array[1]);
        $this->db->group_by(array('productcode','channel_price'));
        $this->db->order_by('price', 'asc');
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
}
