<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admstats extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','bill','mytext','phone','mydate'));
        initialize_session_userdata($this,true);
    }
    public function bank_dd() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->input->get('ipt_storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->input->get('ipt_userid'));
            $billing_mode = $this->input->get('ipt_billing_mode');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $groupno = $this->uri->segment(6);
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->uri->segment(5);
                $groupno = $this->uri->segment(6);
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->uri->segment(6);
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->uri->segment(7));
            $billing_mode = $this->uri->segment(8);
            $offset =  (int)$this->uri->segment(9);
        }

        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_billing_mode = ($billing_mode == '' ? '000' : $billing_mode);

        $config['base_url'] = "/admstats/bank_dd/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_billing_mode}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);
        $groupno = ($groupno == '000' ? '' : $groupno);
        $userid = ($userid == '000' ? '' : $userid);
        $billing_mode = ($billing_mode == '000' ? '' : $billing_mode);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'groupno' => $groupno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'mode' => $billing_mode,
            'userid' => $userid,
        );
        $this->load->model('resultModels');
        $data['billing_list'] = $this->resultModels->get_stats_billing_mode();
        $config['total_rows'] = (int)$this->resultModels->get_bank_dd_count_by_admin($option);
        $data['result'] = $this->resultModels->get_bank_dd_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_bank_dd_sum_by_admin($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();
        if ($storeno != '') {
            $option = array(
                'storeno' => $storeno
            );
            $data['group_list'] = $this->userModels->get_group_list_by_admin($option);
        }

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['billing_mode'] = $billing_mode;
        $data['userid'] = $userid;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/bank_dd', $data);
        $this->load->view('templates/adm_footer');
    }
    public function bank_mm() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->input->get('ipt_storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->input->get('ipt_userid'));
            $billing_mode = $this->input->get('ipt_billing_mode');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $groupno = $this->uri->segment(6);
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->uri->segment(5);
                $groupno = $this->uri->segment(6);
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->uri->segment(6);
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->uri->segment(7));
            $billing_mode = $this->uri->segment(8);
            $offset =  (int)$this->uri->segment(9);
        }
        
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_billing_mode = ($billing_mode == '' ? '000' : $billing_mode);

        $config['base_url'] = "/admstats/bank_mm/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_billing_mode}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);
        $groupno = ($groupno == '000' ? '' : $groupno);
        $userid = ($userid == '000' ? '' : $userid);
        $billing_mode = ($billing_mode == '000' ? '' : $billing_mode);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'groupno' => $groupno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'mode' => $billing_mode,
            'userid' => $userid,
        );
        $this->load->model('resultModels');
        $data['billing_list'] = $this->resultModels->get_stats_billing_mode();
        $config['total_rows'] = (int)$this->resultModels->get_bank_mm_count_by_admin($option);
        $data['result'] = $this->resultModels->get_bank_mm_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_bank_mm_sum_by_admin($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();
        if ($storeno != '') {
            $option = array(
                'storeno' => $storeno
            );
            $data['group_list'] = $this->userModels->get_group_list_by_admin($option);
        }

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['billing_mode'] = $billing_mode;
        $data['userid'] = $userid;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/bank_mm', $data);
        $this->load->view('templates/adm_footer');
    }
    public function send_channel() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->input->get('ipt_storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->input->get('ipt_userid'));
            $productcode = $this->input->get('ipt_productcode');
            $channel = $this->input->get('ipt_channel');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $groupno = $this->uri->segment(6);
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->uri->segment(5);
                $groupno = $this->uri->segment(6);
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->uri->segment(6);
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->uri->segment(7));
            $productcode = $this->uri->segment(8);
            $channel = $this->uri->segment(9);
            $offset =  (int)$this->uri->segment(10);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_productcode = ($productcode == '' ? '000' : $productcode);
        $ci_channel = ($channel == '' ? '000' : $channel);

        $config['base_url'] = "/admstats/send_channel/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_productcode}/{$ci_channel}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);
        $groupno = ($groupno == '000' ? '' : $groupno);
        $userid = ($userid == '000' ? '' : $userid);
        $productcode = ($productcode == '000' ? '' : $productcode);
        $channel = ($channel == '000' ? '' : $channel);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'groupno' => $groupno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'productcode' => $productcode,
            'userid' => $userid,
            'channel' => $channel,
        );

        $this->load->model('resultModels');
        $data['product'] = $this->resultModels->get_product();
        $config['total_rows'] = (int)$this->resultModels->get_send_channel_count_by_admin($option);
        $data['result'] = $this->resultModels->get_send_channel_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_send_channel_sum_by_admin($option);
        $data['channel_list'] = $this->resultModels->get_channel($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();
        if ($storeno != '') {
            $option = array(
                'storeno' => $storeno
            );
            $data['group_list'] = $this->userModels->get_group_list_by_admin($option);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['productcode'] = $productcode;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['userid'] = $userid;
        $data['channel'] = $channel;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/send_channel', $data);
        $this->load->view('templates/adm_footer');
    }
    public function send_dd() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->input->get('ipt_storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->input->get('ipt_userid'));
            $productcode = $this->input->get('ipt_productcode');
            $channel = $this->input->get('ipt_channel');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $groupno = $this->uri->segment(6);
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->uri->segment(5);
                $groupno = $this->uri->segment(6);
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->uri->segment(6);
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->uri->segment(7));
            $productcode = $this->uri->segment(8);
            $channel = $this->uri->segment(9);
            $offset =  (int)$this->uri->segment(10);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_productcode = ($productcode == '' ? '000' : $productcode);
        $ci_channel = ($channel == '' ? '000' : $channel);

        $config['base_url'] = "/admstats/send_dd/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_productcode}/{$ci_channel}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);
        $groupno = ($groupno == '000' ? '' : $groupno);
        $userid = ($userid == '000' ? '' : $userid);
        $productcode = ($productcode == '000' ? '' : $productcode);
        $channel = ($channel == '000' ? '' : $channel);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'groupno' => $groupno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'productcode' => $productcode,
            'userid' => $userid,
            'channel' => $channel,
        );

        $this->load->model('resultModels');
        $data['product'] = $this->resultModels->get_product();
        $config['total_rows'] = (int)$this->resultModels->get_send_dd_count_by_admin($option);
        $data['result'] = $this->resultModels->get_send_dd_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_send_dd_sum_by_admin($option);
        $data['channel_list'] = $this->resultModels->get_channel($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();
        if ($storeno != '') {
            $option = array(
                'storeno' => $storeno
            );
            $data['group_list'] = $this->userModels->get_group_list_by_admin($option);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['productcode'] = $productcode;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['userid'] = $userid;
        $data['channel'] = $channel;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/send_dd', $data);
        $this->load->view('templates/adm_footer');
    }
    public function send_mm() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->input->get('ipt_storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->input->get('ipt_groupno');
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->input->get('ipt_userid'));
            $productcode = $this->input->get('ipt_productcode');
            $channel = $this->input->get('ipt_channel');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            if ((int)$this->session->userdata('level') > 5) {
                $storeno = $this->uri->segment(5);
                $groupno = $this->uri->segment(6);
            } else if ((int)$this->session->userdata('level') > 3) {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->uri->segment(6);
            } else {
                $storeno = $this->session->userdata('storeno');
                $groupno = $this->session->userdata('groupno');
            }
            $userid = trim($this->uri->segment(7));
            $productcode = $this->uri->segment(8);
            $channel = $this->uri->segment(9);
            $offset =  (int)$this->uri->segment(10);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_productcode = ($productcode == '' ? '000' : $productcode);
        $ci_channel = ($channel == '' ? '000' : $channel);

        $config['base_url'] = "/admstats/send_mm/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_productcode}/{$ci_channel}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);
        $groupno = ($groupno == '000' ? '' : $groupno);
        $userid = ($userid == '000' ? '' : $userid);
        $productcode = ($productcode == '000' ? '' : $productcode);
        $channel = ($channel == '000' ? '' : $channel);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'groupno' => $groupno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'productcode' => $productcode,
            'channel' => $channel,
            'userid' => $userid,
        );
        $this->load->model('resultModels');
        $data['product'] = $this->resultModels->get_product();
        $config['total_rows'] = (int)$this->resultModels->get_send_mm_count_by_admin($option);
        $data['result'] = $this->resultModels->get_send_mm_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_send_mm_sum_by_admin($option);
        $data['channel_list'] = $this->resultModels->get_channel($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();
        if ($storeno != '') {
            $option = array(
                'storeno' => $storeno
            );
            $data['group_list'] = $this->userModels->get_group_list_by_admin($option);
        }
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['productcode'] = $productcode;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['userid'] = $userid;
        $data['channel'] = $channel;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/send_mm', $data);
        $this->load->view('templates/adm_footer');
    }
    public function all_send_dd() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        $config['base_url'] = "/admstats/all_send_dd/{$ci_date_to}/{$ci_date_from}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_all_send_dd_count_by_admin($option);
        $data['result'] = $this->resultModels->get_all_send_dd_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_all_send_dd_sum_by_admin($option);

        $option1 = array(
            'date_from' => date('Y-m-d'),
        );
        $data['result1'] = $this->resultModels->get_company_result_column_by_admin($option1);
// error_log(print_r($data['result1'],1),0);
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/all_send_dd', $data);
        $this->load->view('templates/adm_footer');
    }
    public function get_group() {
        header("Content-Type: application/json");

        $this->load->library('form_validation');
        $this->form_validation->set_rules('storeno', 'storeno', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[admstats]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $option['storeno'] = (int)$this->input->post('storeno');
        $this->load->model('userModels');
        $group_list = $this->userModels->get_group_list_by_admin($option);
        $i = 0;
        $group = array();
        if (is_array($group_list)) $group[$i++] = '9999|그룹없음';
        foreach ($group_list as $row) {
            $group[$i++] = $row->groupno.'|'.$row->groupid;
        }

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 처리되었습니다.';
        $data_json['group'] = $group;
        exit (json_encode($data_json));
    }
    public function settle_mm() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $storeno = $this->input->get('ipt_storeno');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $offset =  (int)$this->uri->segment(9);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);

        $config['base_url'] = "/admstats/settle_mm/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_billing_mode}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_settle_mm_count_by_admin($option);
        $data['result'] = $this->resultModels->get_settle_mm_limit_by_admin($option);

        $this->load->model('userModels');
        $data['store_list'] = $this->userModels->get_store_by_admin();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['storeno'] = $storeno;
        $data['groupno'] = $groupno;
        $data['billing_mode'] = $billing_mode;
        $data['userid'] = $userid;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/settle_mm', $data);
        $this->load->view('templates/adm_footer');
    }
    public function agent_mm() {
        auth_session_userdata($this,9);
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $storeno = $this->input->get('ipt_storeno');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $offset =  (int)$this->uri->segment(6);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);

        $config['base_url'] = "/admstats/agent_mm/{$ci_date_to}/{$ci_date_from}/{$ci_storeno}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;
        $storeno = ($storeno == '000' ? '' : $storeno);

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'storeno' => $storeno,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_agent_mm_count_by_admin($option);
        $data['result'] = $this->resultModels->get_agent_mm_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_agent_mm_sum_by_admin($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['storeno'] = $storeno;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admstats/agent_mm', $data);
        $this->load->view('templates/adm_footer');
    }
}