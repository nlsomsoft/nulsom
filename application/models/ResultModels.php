<?php
class ResultModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function get_stats_billing_mode() {
        $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
        $this->db->where_in('mode', $modes);
        $query = $this->db->get('billing_mode')->result();
        return $query;
    }
    function get_pay_list_count_by_admin($option) {
        $this->db->from('billing');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
                else $this->db->where('groupno', $this->session->userdata('groupno'));
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        }
        if ($option['groupno'] != '') $this->db->where('groupno', $option['groupno']);
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $this->db->where('date(reg_time) >=', $option['date_from']);
        $this->db->where('date(reg_time) <=', $option['date_to']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_pay_list_limit_by_admin($option) {
        $this->db->select('a.*, b.storename, c.groupid');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('a.storeno', $option['storeno']);
        }
        if ($option['groupno'] != '') $this->db->where('a.groupno', $option['groupno']);
        if ($option['userid'] != '') $this->db->where('a.userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('a.mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('a.mode', $modes);
        }
        $this->db->where('date(a.reg_time) >=', $option['date_from']);
        $this->db->where('date(a.reg_time) <=', $option['date_to']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->order_by('a.xid', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing a')->result();
        return $query;
    }
    function get_pay_list_sum_by_admin($option) {
        $this->db->select('SUM(amount) total_amount');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
                else $this->db->where('groupno', $this->session->userdata('groupno'));
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        }
        if ($option['groupno'] != '') $this->db->where('groupno', $option['groupno']);
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $this->db->where('date(reg_time) >=', $option['date_from']);
        $this->db->where('date(reg_time) <=', $option['date_to']);
        $query = $this->db->get('billing')->row();
        return $query;
    }
    function get_bank_dd_count_by_admin($option) {
        $this->db->from('billing');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $this->db->where('date(reg_time) >=', $option['date_from']);
        $this->db->where('date(reg_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy', 'mm', 'dd'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_bank_dd_limit_by_admin($option) {
        $this->db->select('yyyy,mm,dd,count(amount) cnt, sum(amount) amount');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $this->db->where('date(reg_time) >=', $option['date_from']);
        $this->db->where('date(reg_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy', 'mm', 'dd'));
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing')->result();
        return $query;
    }
    function get_bank_dd_sum_by_admin($option) {
        $this->db->select('count(amount) total_cnt, sum(amount) total_amount');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $this->db->where('date(reg_time) >=', $option['date_from']);
        $this->db->where('date(reg_time) <=', $option['date_to']);
        $query = $this->db->get('billing')->row();
        return $query;
    }
    function get_bank_mm_count_by_admin($option) {
        $this->db->from('billing');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reg_time) >=', $date_from);
        $this->db->where('date(reg_time) <=', $date_to);
        $this->db->group_by(array('yyyy', 'mm'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_bank_mm_limit_by_admin($option) {
        $this->db->select('yyyy,mm,count(amount) cnt, sum(amount) amount');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reg_time) >=', $date_from);
        $this->db->where('date(reg_time) <=', $date_to);
        $this->db->group_by(array('yyyy', 'mm'));
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing')->result();
        return $query;
    }
    function get_bank_mm_sum_by_admin($option) {
        $this->db->select('count(amount) total_cnt, sum(amount) total_amount');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['mode'] != '') $this->db->where('mode', $option['mode']);
        else {
            $modes = array('PA', 'PB', 'PC', 'PD', 'PE', 'MA', 'MB');
            $this->db->where_in('mode', $modes);
        }
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reg_time) >=', $date_from);
        $this->db->where('date(reg_time) <=', $date_to);
        $query = $this->db->get('billing')->row();
        return $query;
    }
    function get_product() {
        $query = $this->db->get('product')->result();
        return $query;
    }
    function get_send_channel_count_by_admin($option) {
        $this->db->from('sow_processunit_msg');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy', 'mm', 'dd'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_send_channel_limit_by_admin($option) {
        $this->db->select('priority,sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $this->db->group_by(array('priority'));
        $this->db->order_by('priority', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_send_channel_sum_by_admin($option) {
        $this->db->select('sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['groupno'] != '') {
            if ($option['groupno'] == '9999') $option['groupno'] = '0';
            $this->db->where('groupno', $option['groupno']);
        }
        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $query = $this->db->get('sow_processunit_msg')->row();
        return $query;
    }
    function get_send_dd_count_by_admin($option) {
        $this->db->from('sow_processunit_msg');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy', 'mm', 'dd'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_send_dd_limit_by_admin($option) {
        $this->db->select('yyyy,mm,dd,sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy', 'mm', 'dd'));
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_send_dd_sum_by_admin($option) {
        $this->db->select('sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $query = $this->db->get('sow_processunit_msg')->row();
        return $query;
    }
    function get_send_mm_count_by_admin($option) {
        $this->db->from('sow_processunit_msg');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reserve_time) >=', $date_from);
        $this->db->where('date(reserve_time) <=', $date_to);
        $this->db->group_by(array('yyyy', 'mm'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_send_mm_limit_by_admin($option) {
        $this->db->select('yyyy,mm,sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reserve_time) >=', $date_from);
        $this->db->where('date(reserve_time) <=', $date_to);
        $this->db->group_by(array('yyyy', 'mm'));
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_send_mm_sum_by_admin($option) {
        $this->db->select('sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(remain_units) remain_units');

        if ($this->session->userdata('level') == '3') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
            else $this->db->where('groupno', $this->session->userdata('groupno'));
        } else if ($this->session->userdata('level') == '5') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
            if ($option['groupno'] != '') {
                if ($option['groupno'] == '9999') $option['groupno'] = '0';
                $this->db->where('groupno', $option['groupno']);
            }
        }

        if ($option['userid'] != '') $this->db->where('userid', $option['userid']);
        if ($option['productcode'] != '') $this->db->where('productcode', $option['productcode']);
        if ($option['channel'] != '') $this->db->where('priority', $option['channel']);
        $this->db->where('status !=', '0');
        $date_from = $option['date_from'].'-01';
        $last_to_day = date('t', strtotime("{$option['date_from']}-01"));
        $date_to = $option['date_to'].'-'.$last_to_day;
        $this->db->where('date(reserve_time) >=', $date_from);
        $this->db->where('date(reserve_time) <=', $date_to);
        $query = $this->db->get('sow_processunit_msg')->row();
        return $query;
    }
    function get_campaign_count_by_admin($option) {
        $this->db->from('sow_processunit_msg');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
                else $this->db->where('groupno', $this->session->userdata('groupno'));
            }
        }
        if (isset($option['status']) == true) $this->db->where('status', '0');
        else $this->db->where('status !=', '0');
        if ((int)$option['total_units'] > 0) $this->db->where('total_units >', (int)$option['total_units']);
        // $this->db->where('deleteflag', 'N');
        if ($option['val']) $this->db->where($option['sfl'], $option['val']);

        if ($option['date_from'] != '') $this->db->where('date(reserve_time) >=', $option['date_from']);
        if ($option['date_to'] != '') $this->db->where('date(reserve_time) <=', $option['date_to']);

        $query = $this->db->count_all_results();
        return $query;
    }
    function get_campaign_limit_by_admin($option) {
        $this->db->select('a.*, b.storename, c.groupid');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }
        if (isset($option['status']) == true) $this->db->where('a.status', '0');
        else $this->db->where('a.status !=', '0');
        if ((int)$option['total_units'] > 0) $this->db->where('a.total_units >', $option['total_units']);
        // $this->db->where('deleteflag', 'N');
        if ($option['val']) $this->db->where('a.'.$option['sfl'], $option['val']);
        if ($option['date_from'] != '') $this->db->where('date(a.reserve_time) >=', $option['date_from']);
        if ($option['date_to'] != '') $this->db->where('date(a.reserve_time) <=', $option['date_to']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->order_by('a.procid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg a')->result();
        return $query;
    }
    function get_campaign_count_mass($option) {
        $this->db->from('sow_processunit_msg');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag !=', 'Y');
        $this->db->where('total_units >', 20);
        if (isset($option['status']) == true) $this->db->where('status', '0');
        // else $this->db->where('status !=', '0');
        if ($option['val']) $this->db->where('MATCH (subject) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_campaign_limit_mass($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag', 'N');
        $this->db->where('total_units >', 20);
        if (isset($option['status']) == true) $this->db->where('status', '0');
        // else $this->db->where('status !=', '0');
        if ($option['val']) $this->db->where('MATCH (subject) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->order_by('procid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_campaign_count($option) {
        $this->db->from('sow_processunit_msg');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag !=', 'Y');
        if (isset($option['status']) == true) $this->db->where('status', '0');
        // else $this->db->where('status !=', '0');
        if ($option['val']) $this->db->where('MATCH (subject) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_campaign_limit($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag', 'N');
        if (isset($option['status']) == true) $this->db->where('status', '0');
        // else $this->db->where('status !=', '0');
        if ($option['val']) $this->db->where('MATCH (subject) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->order_by('procid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_campaign($option) {
        $this->db->where_in('procid', $option);
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag', 'N');
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_campaign_list($option) {
        $this->db->select('procid,userno,userid,storeno,amount');
        $this->db->where_in('procid', $option);
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('deleteflag', 'N');
        $query = $this->db->get('sow_processunit_msg')->result_array();
        return $query;
    }
    function get_campaign_list_by_admin($option) {
        $this->db->select('procid,userno,userid,storeno,amount');
        $this->db->where_in('procid', $option);
        $this->db->where('deleteflag', 'N');
        $query = $this->db->get('sow_processunit_msg')->result_array();
        return $query;
    }
    function get_campaign_stats($option) {
        $this->db->select('productcode,sum(total_units) total_units,sum(success) success,sum(fail) fail,sum(realamount) realamount');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        // $this->db->where('status', '100');
        $this->db->group_by('productcode');
        $this->db->order_by('productcode', 'desc');
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_campaign_stats_daily($option) {
        $this->db->select('sum(total_units) total_units,sum(success) success,sum(fail) fail,yyyy,mm,dd');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $this->db->group_by(array('yyyy','mm','dd'));
        $this->db->order_by('yyyy', 'asc');
        $this->db->order_by('mm', 'asc');
        $this->db->order_by('dd', 'asc');
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_result_count($option) {
        $this->db->from($option['table']);
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('procid', $option['procid']);
        if ($option['val']) $this->db->where('MATCH (targetno) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_result_limit($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('procid', $option['procid']);
        if ($option['val']) $this->db->where('MATCH (targetno) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->order_by('xid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get($option['table'])->result();
        return $query;
    }
    function get_result_limit_excel($option) {
        $result_code = array('1000', '0');
        $this->db->select('targetno,targetname,telecom');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('procid', $option['procid']);
        if ($option['result'] == 1000) {
            $this->db->where_in('result', $result_code);
        }
        else {
            $this->db->where_not_in('result', $result_code);
        }
        $query = $this->db->get($option['table'])->result();
        return $query;
    }
    function get_result_limit_excel_array($option) {
        $result_code = array('1000', '0');
        $this->db->select('targetno,targetname,telecom');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('procid', $option['procid']);
        if ($option['result'] == 1000) {
            $this->db->where_in('result', $result_code);
        }
        else {
            $this->db->where_not_in('result', $result_code);
        }
        $query = $this->db->get($option['table'])->result_array();
        return $query;
    }
    function get_result_daily_excel($option) {
        $this->db->select('targetno,targetname,telecom,result');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('DATE(send_time)', $option['send_time']);
        $query = $this->db->get($option['table'])->result_array();
        return $query;
    }
    function delete_campaign($option) {
        $data = array(
            'deleteflag' => 'Y'
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('procid', $option);
        $query = $this->db->update('sow_processunit_msg', $data);
        return $query;
    }
    function delete_reserved_campaign($option,$option1) {
        $data = array(
            'deleteflag' => 'Y'
        );
        $this->db->trans_begin();
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('procid', $option);
        $query = $this->db->update('sow_processunit_msg', $data);

        $total_amount = 0;
        $i = 0;
        $data = array();
        foreach ($option1 as $row) {
            $data[$i]['procid'] = $row['procid'];
            $data[$i]['userno'] = $row['userno'];
            $data[$i]['userid'] = $row['userid'];
            $data[$i]['storeno'] = $row['storeno'];
            $data[$i]['amount'] = $row['amount'];
            $data[$i]['mode'] = 'PG';
            $data[$i]['reg_time'] = $this->current_time;
            $data[$i]['yyyy'] = (int)substr($this->current_time,0,4);
            $data[$i]['mm'] = (int)substr($this->current_time,5,2);
            $data[$i]['dd'] = (int)substr($this->current_time,8,2);
            $i ++;
            $total_amount += (float)$row['amount'];
        }
        $query = $this->db->insert_batch('billing', $data);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $sql = "
            UPDATE users
            SET cash = cash + {$total_amount}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $this->session->userdata('userno'));
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('procid', $option);
        $this->db->delete('sow_pu_msgdata');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function delete_reserved_campaign_by_admin($option,$option1) {
        $data = array(
            'deleteflag' => 'Y'
        );
        $this->db->trans_begin();
        $this->db->where_in('procid', $option);
        $query = $this->db->update('sow_processunit_msg', $data);

        $userdata = array();
        $i = 0;
        $data = array();
        foreach ($option1 as $row) {
            $data[$i]['procid'] = $row['procid'];
            $data[$i]['userno'] = $row['userno'];
            $data[$i]['userid'] = $row['userid'];
            $data[$i]['storeno'] = $row['storeno'];
            $data[$i]['amount'] = $row['amount'];
            $data[$i]['mode'] = 'PG';
            $data[$i]['reg_time'] = $this->current_time;
            $data[$i]['yyyy'] = (int)substr($this->current_time,0,4);
            $data[$i]['mm'] = (int)substr($this->current_time,5,2);
            $data[$i]['dd'] = (int)substr($this->current_time,8,2);
            $i ++;
            $userdata[$row['userno']] += (float)$row['amount'];
        }
        $query = $this->db->insert_batch('billing', $data);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        foreach ($userdata as $userno => $amount) {
            if (!$userno || !$amount) continue;
            $sql = "
                UPDATE users
                SET cash = cash + {$amount}
                WHERE userno = ?
            ";
            $query = $this->db->query($sql, $userno);
            if (!$query) {
                $this->db->trans_rollback();
                return false;
            }
        }

        $this->db->where_in('procid', $option);
        $this->db->delete('sow_pu_msgdata');
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function modify_priority_campaign_by_admin($option,$option1) {
        $this->db->trans_begin();
        foreach ($option as $channel) {
            $data = array('priority' => $channel);
            $this->db->where_in('procid', $option1[$channel]);
            $query = $this->db->update('sow_processunit_msg', $data);
            if (!$query) {
                $this->db->trans_rollback();
                return false;
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function get_channel($option) {
        if ($option['status']) $this->db->where('status', $option['status']);
        $query = $this->db->get('channel')->result();
        return $query;
    }
    function get_settle_mm_count_by_admin($option) {
        $this->db->from('stat_sending_month');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        }
        $date_from = (int)str_replace('-','',$option['date_from']);
        $date_to = (int)str_replace('-','',$option['date_to']);
        $this->db->where('yyyymm >=', $date_from);
        $this->db->where('yyyymm <=', $date_to);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_settle_mm_limit_by_admin($option) {
        $this->db->select('a.*,b.storename,c.groupid');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        } else {
            if ($option['storeno'] != '') $this->db->where('a.storeno', $option['storeno']);
        }
        $date_from = (int)str_replace('-','',$option['date_from']);
        $date_to = (int)str_replace('-','',$option['date_to']);
        $this->db->where('a.yyyymm >=', $date_from);
        $this->db->where('a.yyyymm <=', $date_to);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('stat_sending_month a')->result();
        return $query;
    }
    function get_agent_mm_count_by_admin($option) {
        $this->db->from('stat_agent_daily');
        if ($option['storeno'] != '') {
            if ((int)$option['storeno'] < 1000) $this->db->where('storeno <', $option['storeno']);
            else $this->db->where('storeno >=', $option['storeno']);
        }
        $date_from = str_replace('-', '', $option['date_from']);
        $date_to = str_replace('-', '', $option['date_to']);
        $this->db->where('yyyymm >=', $date_from);
        $this->db->where('yyyymm <=', $date_to);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_agent_mm_limit_by_admin($option) {
        $this->db->select('*');
        if ($option['storeno'] != '') {
            if ((int)$option['storeno'] < 1000) $this->db->where('storeno <', $option['storeno']);
            else $this->db->where('storeno >=', $option['storeno']);
        }
        $date_from = str_replace('-', '', $option['date_from']);
        $date_to = str_replace('-', '', $option['date_to']);
        $this->db->where('yyyymm >=', $date_from);
        $this->db->where('yyyymm <=', $date_to);
        $this->db->order_by('yyyymmdd', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('stat_agent_daily')->result();
        return $query;
    }
    function get_agent_mm_sum_by_admin($option) {
        $this->db->select('sum(lg_sms) total_lg_sms, sum(lg_lms) total_lg_lms, sum(lg_mms) total_lg_mms, sum(kp_sms) total_kp_sms, sum(kp_lms) total_kp_lms, sum(kp_mms) total_kp_mms, sum(sow_sms) total_sow_sms, sum(sow_lms) total_sow_lms, sum(sow_mms) total_sow_mms');
        if ($option['storeno'] != '') {
            if ((int)$option['storeno'] < 1000) $this->db->where('storeno <', $option['storeno']);
            else $this->db->where('storeno >=', $option['storeno']);
        }
        $date_from = str_replace('-', '', $option['date_from']);
        $date_to = str_replace('-', '', $option['date_to']);
        $this->db->where('yyyymm >=', $date_from);
        $this->db->where('yyyymm <=', $date_to);
        $query = $this->db->get('stat_agent_daily')->row();
        return $query;
    }
    function get_all_send_dd_count_by_admin($option) {
        $this->db->from('company_stat');
        $this->db->where('date(yyyymmdd) >=', $option['date_from']);
        $this->db->where('date(yyyymmdd) <=', $option['date_to']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_all_send_dd_limit_by_admin($option) {
        $this->db->select('date(yyyymmdd) yyyymmdd, company, SUM(total) total,SUM(succ) succ,SUM(fail) fail,SUM(remain) remain');
        $this->db->where('date(yyyymmdd) >=', $option['date_from']);
        $this->db->where('date(yyyymmdd) <=', $option['date_to']);
        $this->db->group_by(array('yyyymmdd','company'));
        $this->db->order_by('yyyymmdd', 'asc');
        $this->db->order_by('company', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('company_stat')->result();
        return $query;
    }
    function get_all_send_dd_sum_by_admin($option) {
        $this->db->select('SUM(total) total,SUM(succ) succ,SUM(fail) fail,SUM(remain) remain');
        $this->db->where('date(yyyymmdd) >=', $option['date_from']);
        $this->db->where('date(yyyymmdd) <=', $option['date_to']);
        $query = $this->db->get('company_stat')->row();
        return $query;
    }
    function get_company_result_column_by_admin($option) {
        $this->db->select('company,col_cnt');
        $this->db->where('date(yyyymmdd) =', $option['date_from']);
        $this->db->order_by('company', 'asc');
        $query = $this->db->get('company_stat')->result();
        return $query;
    }
    function get_sending_telecom_count_by_admin($option) {
        $this->db->from('sow_processunit_telecom');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }
        // if (isset($option['status']) == true) $this->db->where('status', '0');
        // else $this->db->where('status !=', '0');
        // $this->db->where('deleteflag', 'N');
        if ($option['val']) $this->db->where($option['sfl'], $option['val']);

        if ($option['date_from'] != '') $this->db->where('date(reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '') $this->db->where('date(reg_time) <=', $option['date_to']);

        $query = $this->db->count_all_results();
        return $query;
    }
    function get_sending_telecom_limit_by_admin($option) {
        $this->db->select('a.*, b.storename, c.groupid');
        if ((int)$this->session->userdata('level') < 9) {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ((int)$this->session->userdata('level') == 3) {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }
        // if (isset($option['status']) == true) $this->db->where('a.status', '0');
        // else $this->db->where('a.status !=', '0');
        // $this->db->where('deleteflag', 'N');
        if ($option['val']) $this->db->where('a.'.$option['sfl'], $option['val']);
        if ($option['date_from'] != '') $this->db->where('date(a.reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '') $this->db->where('date(a.reg_time) <=', $option['date_to']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->order_by('a.procid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('sow_processunit_telecom a')->result();
        return $query;
    }
}
