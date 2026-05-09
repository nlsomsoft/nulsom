<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admsetting extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','mydate','bill'));
        initialize_session_userdata($this,true);
    }
    public function mobile() {
        auth_session_userdata($this,5); // < level

        $option = array(
            'type' => '1'
        );
        $this->load->model('settingModels');
        $data['result'] = $this->settingModels->get_admin_notice($option);
        // if (!$data['result']) {
        //     $this->session->set_flashdata('notice', '시스템 오류입니다.');
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/mobile', $data);
        $this->load->view('templates/adm_footer');
    }
    public function mobile_auth() {
        auth_session_userdata($this,5); // < level

        $this->load->library('form_validation');
        $this->form_validation->set_rules('noti_mobile', 'noti_mobile', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $mobile = str_replace('-','',$this->input->post('noti_mobile'));
        $option = array(
            'mobile' => $mobile,
            'type' => '1'
        );
        $this->load->model('settingModels');

        $result = $this->settingModels->get_admin_notice($option);
        if (!$result->mobile) {
            $result = $this->settingModels->add_admin_notice($option);
            if (!$result) {
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $result = $this->settingModels->modify_admin_notice($option);
            if (!$result) {
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect('/admsetting/mobile');
    }
    public function report() {
        auth_session_userdata($this,5); // < level

        $stx = trim($this->input->get('stx'));
        $sfl = $this->input->get('sfl');
        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $i = 0;
        $report_info = array();
        if ($stx != '') {
            $option = array(
                'callback' => $stx,
            );
            $this->load->model('settingModels');
            $result1 = $this->settingModels->get_callback_info($option);
            foreach ($result1 as $row) {
                if (!$row->userno) continue;

                $option = array(
                    'userno' => $row->userno,
                    'userid' => $row->userid,
                    'callback' => $row->callback,
                );
                $callback_cnt = (int)$this->settingModels->get_user_callback_count($option);
                $result2 = $this->settingModels->get_user_info($option);
                $result3 = $this->settingModels->get_user_callback_sum_count($option);

                $report_info[$i]['userno'] = $row->userno;
                $report_info[$i]['userid'] = $row->userid;
                $report_info[$i]['callback'] = $row->callback;
                $report_info[$i]['cert_type'] = $row->cert_type;
                $report_info[$i]['callback_cnt'] = $callback_cnt;
                $report_info[$i]['add_date'] = $result2->add_date;
                $report_info[$i]['phone_no'] = $result2->auth_phoneno;
                $report_info[$i]['username'] = $result2->auth_username;
                $report_info[$i]['login_date'] = $result2->login_date;
                $report_info[$i]['ip'] = $result2->ip;
                $report_info[$i]['register_ip'] = $result2->register_ip;
                $report_info[$i]['state'] = $result2->state;
                $report_info[$i]['total_sum'] = $result3->total_sum;
                $i ++;
            }
        }
        $data['report_info'] = $report_info;
        $data['sfl'] = $sfl;
        $data['stx'] = $stx;
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/report', $data);
        $this->load->view('templates/adm_footer');
    }
    public function monitor() {
        auth_session_userdata($this,9); // < level

        $disp_array = array();
        $this->load->model('settingModels');
        $result1 = $this->settingModels->get_admin_monitor_list();
        foreach ($result1 as $row) {
            if (!$row->tname) continue;
            if ($row->tname == 'result_') {
                for ($ii = 1; $ii <= MAX_RESULT_CNT; $ii ++) {
                    $tname = $row->tname.$ii;
                    $disp_array[] = $tname;
                }
            } else {
                $tname = $row->tname;
                $disp_array[] = $tname;
            }
        }

        $i = 0;
        $monitor_info = array();
        $result = $this->settingModels->get_table_status();
        foreach ($result as $key => $val) {
            $t_name = $val['Name'];
            $rows = (int)$val['Rows'];
            if (in_array($t_name, $disp_array)) {
                // $monitor_info1[$i] = strtoupper($val['Name']);
                $monitor_info1[$i] = $val['Name'];
                $monitor_info2[$val['Name']] = $rows;
                $i ++;
            }
            else if ($rows > 100000) {
                $monitor_info1[$i] = $val['Name'];
                $monitor_info2[$val['Name']] = $rows;
                $i ++;
            }
        }
        rsort($monitor_info1);
        $data['monitor_info1'] = $monitor_info1;
        $data['monitor_info2'] = $monitor_info2;
        $data['sfl'] = $sfl;
        $data['stx'] = $stx;
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/monitor', $data);
        $this->load->view('templates/adm_footer');
    }
    public function notice() {
        auth_session_userdata($this,5); // < level

        $this->load->model('settingModels');
        $data['result'] = $this->settingModels->get_all_noticebbs_by_admin();
        // if (!$data['result']) {
        //     $this->session->set_flashdata('notice', '시스템 오류입니다.');
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/notice', $data);
        $this->load->view('templates/adm_footer');
    }
    public function write_bbs() {
        auth_session_userdata($this,5); // < level
        $xid =  (int)$this->uri->segment(3);

        $result = array();
        if ($xid) {
            $option = array(
                'xid' => $xid,
            );
            $this->load->model('settingModels');
            $result = $this->settingModels->get_noticebbs_by_admin($option);
        }
        $data['xid'] = $xid;
        $data['result'] = $result;
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/write_bbs', $data);
        $this->load->view('templates/adm_footer');
    }
    public function add_bbs() {
        auth_session_userdata($this,5); // < level
        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', 'subject', 'required');
        $this->form_validation->set_rules('body', 'body', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $xid = $this->input->post('xid');

        $option = array(
            'xid' => $xid,
            'userid' => $this->session->userdata('userid'),
            'subject' => $this->input->post('subject'),
            'body' => $this->input->post('body'),
        );
        $this->load->model('settingModels');
        if ($xid) {
            $result = $this->settingModels->modify_noticebbs_by_admin($option);
        } else {
            $result = $this->settingModels->add_noticebbs_by_admin($option);
        }
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect('/admsetting/notice');
    }
    public function notice_bbs() {
        auth_session_userdata($this,5); // < level
        $xid =  (int)$this->uri->segment(3);
        if (!$xid) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $xid,
        );
        $this->load->model('settingModels');
        $result = $this->settingModels->modify_notice_noticebbs_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect('/admsetting/notice');
    }
    public function hide_bbs() {
        auth_session_userdata($this,5); // < level
        $xid =  (int)$this->uri->segment(3);
        if (!$xid) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'xid' => $xid,
            'status' => '0',
        );
        $this->load->model('settingModels');
        $result = $this->settingModels->modify_status_noticebbs_by_admin($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect('/admsetting/notice');
    }
    public function delete_bbs() {
        auth_session_userdata($this,5); // < level

        $this->load->library('form_validation');
        $this->form_validation->set_rules('act_button', 'act button', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option1 = $this->input->post('chk');
        if (!is_array($option1)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('settingModels');
        $option = array(
            'remove_date' => $this->current_time,
            'del_flag' => '1',
        );
        $result = $this->settingModels->modify_delete_noticebbs_by_admin($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function auth_num() {
        auth_session_userdata($this,9); // < level

        $option = array(
            'limit_date' => date('Y-m-d H:i:s',time() - 600), //10분전
        );
        $this->load->model('settingModels');
        $data['result'] = $this->settingModels->get_admin_auth_num_list($option);
// error_log(print_r($data['result'],1),0);
        $this->load->view('templates/adm_header');
        $this->load->view('admsetting/auth_num', $data);
        $this->load->view('templates/adm_footer');
    }
}
