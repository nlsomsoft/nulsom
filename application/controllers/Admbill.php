<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admbill extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','mydate','bill'));
        initialize_session_userdata($this,true);
    }
    public function bankbook() {
        auth_session_userdata($this, 5);

        if ((int)$this->session->userdata('level') > 5) {
            $this->session->set_flashdata('notice', '사용권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $ipt_stx = trim($this->input->get('stx'));
        if ($ipt_stx) { //검색
            $stx = $ipt_stx;
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admbill/bankbook/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('payModels');
        $config['total_rows'] = (int)$this->payModels->get_bankbook_count($option);
        $data['result'] = $this->payModels->get_bankbook_limit($option);

        $bank_cnt = (int)$this->payModels->get_bankbook_request_count();
        $cache_key = 'sow_bankbook_cnt';
        $this->cache->redis->save($cache_key, $bank_cnt, 600); //10분 후 자동삭제

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admbill/bankbook', $data);
        $this->load->view('templates/adm_footer');
    }
    public function bank_auth() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('rd_xid', 'Rd Xid', 'integer|required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $this->input->post('rd_xid'),
        );
        $this->load->model('payModels');
        $result = $this->payModels->get_bankbook($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option['userno'] = $result->userno;
        $option['amount'] = $result->amount;
        $option['storeno'] = $result->storeno;
        $option['userid'] = $result->userid;

        $option1 = array(
            'userno' => $result->userno,
        );
        $this->load->model('userModels');
        $userinfo = $this->userModels->get_groupno_by_userno($option1);
        $option['groupno'] = $userinfo->groupno;

        $result = $this->payModels->modify_bankbook_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        //session 정보 초기화
        $cache_key = 'session_'.$option['storeno'].'_'.$option['userid'];
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function bank_delete() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('rd_xid', 'Rd Xid', 'integer|required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $this->input->post('rd_xid'),
        );
        $this->load->model('payModels');
        $result = $this->payModels->delete_bankbook($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function pay_list() {
        auth_session_userdata($this, 3);

        if ((int)$this->session->userdata('level') > 5) {
            $this->session->set_flashdata('notice', '사용권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $storeno = $this->input->get('ipt_storeno');
            $groupno = $this->input->get('ipt_groupno');
            $userid = $this->input->get('ipt_userid');
            $billing_mode = $this->input->get('ipt_billing_mode');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $storeno = $this->uri->segment(5);
            $groupno = $this->uri->segment(6);
            $userid = $this->uri->segment(7);
            $billing_mode = $this->uri->segment(8);
            $offset =  (int)$this->uri->segment(9);
        }
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);
        $ci_storeno = ($storeno == '' ? '000' : $storeno);
        $ci_groupno = ($groupno == '' ? '000' : $groupno);
        $ci_userid = ($userid == '' ? '000' : $userid);
        $ci_billing_mode = ($billing_mode == '' ? '000' : $billing_mode);

        $config['base_url'] = "/admbill/pay_list/{$ci_date_from}/{$ci_date_to}/{$ci_storeno}/{$ci_groupno}/{$ci_userid}/{$ci_billing_mode}";
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
        $config['total_rows'] = (int)$this->resultModels->get_pay_list_count_by_admin($option);
        $data['result'] = $this->resultModels->get_pay_list_limit_by_admin($option);
        $data['total_result'] = $this->resultModels->get_pay_list_sum_by_admin($option);

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
        $this->load->view('admbill/pay_list', $data);
        $this->load->view('templates/adm_footer');
    }
    public function adm_deposit() {
        auth_session_userdata($this, 9);
        $ipt_stx = trim($this->input->get('stx'));
        if ($ipt_stx) { //검색
            $stx = $ipt_stx;
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admbill/adm_deposit/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_admin_deposit_count($option);
        $data['result'] = $this->userModels->get_admin_deposit_limit($option);
        $data['store_list'] = $this->userModels->get_store_by_admin();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['offset'] = $offset;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admbill/adm_deposit', $data);
        $this->load->view('templates/adm_footer');
    }
    public function add_deposit() {
        auth_session_userdata($this, 9);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_storeno', 'ipt_storeno', 'required');
        $this->form_validation->set_rules('ipt_amount', 'ipt_amount', 'required');
        $this->form_validation->set_rules('ipt_memo', 'ipt_memo', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'storeno' => $this->input->post('ipt_storeno'),
            'amount' => $this->input->post('ipt_amount'),
            'memo' => $this->input->post('ipt_memo'),
        );
        $this->load->model('userModels');
        $result = $this->userModels->add_admin_deposit($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function adm_balance() {
        auth_session_userdata($this, 9);
        $ipt_stx = trim($this->input->get('stx'));
        if ($ipt_stx) { //검색
            $stx = $ipt_stx;
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admbill/adm_balance/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('userModels');
        $config['total_rows'] = (int)$this->userModels->get_admin_balance_count($option);
        $data['result'] = $this->userModels->get_admin_balance_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['offset'] = $offset;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admbill/adm_balance', $data);
        $this->load->view('templates/adm_footer');
    }
}
