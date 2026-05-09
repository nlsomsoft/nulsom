<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admsettle extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','bill','mytext','phone','mydate'));
        initialize_session_userdata($this,true);
    }
    public function deposit_form() {
        $this->load->model('admsettleModels');
        $data['result'] = $this->admsettleModels->get_captain_deposit_list_by_admin();

        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/deposit_form', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle() {
        $date_from = '';
        if ($this->input->get('ipt_date_from')) {
            $date_from = $this->input->get('ipt_date_from');
        }
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $date_from = $ci_date_from;

        $company = $this->uri->segment(3);
        $company = ($company == '' ? 'koen3' : $company);

        $option = array(
            'company' => $company,
            'date_from' => $date_from,
        );
        $this->load->model('admsettleModels');
        $data['sum'] = $this->admsettleModels->get_deposit_sum_by_admin($option);
// error_log(print_r($data['sum'],1),0);

        $data['result'] = $this->admsettleModels->get_deposit_list_by_admin($option);

        $option1 = array(
            'cate' => '0'
        );
        $camp_array = $this->admsettleModels->get_company_sendlist_by_admin($option, $option1);
        $data['camp_array'] = $camp_array;
// error_log(print_r($camp_array,1),0);
        $option1 = array(
            'cate' => '1'
        );
        $camp1_array = $this->admsettleModels->get_company_sendlist_by_admin($option, $option1);
        $data['camp1_array'] = $camp1_array;
        $data['sel_company'] = $company;
        $data['date_from'] = $date_from;

        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle_2() {
        $option = array(
            'company' => 'green'
        );
        $this->load->model('admsettleModels');
        $data['result'] = $this->admsettleModels->get_deposit_list_by_admin($option);

        // $option = array(230,630);
        // $result = $this->admsettleModels->get_priority_campaign_by_admin($option);
        // if ($result) {
        //     $camp_array = array();
        //     foreach ($result as $row) {
        //         $yyyymm = sprintf('%04d - %02d', $row->yyyy, $row->mm);
        //         if ((int)$row->priority < 500) {
        //             $camp_array[$yyyymm][0] = $row->cnt;
        //         } else {
        //             $camp_array[$yyyymm][1] = $row->cnt;
        //         }
        //     }
        // }

        $camp_array = $this->admsettleModels->get_company_sendlist_by_admin($option);
        $data['sel_company'] = 'green';
        $data['camp_array'] = $camp_array;
        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle_2', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle_1() {
        $option = array(
            'company' => 'koen'
        );
        $this->load->model('admsettleModels');
        $data['result'] = $this->admsettleModels->get_deposit_list_by_admin($option);

        // $option = array(230,630);
        // $result = $this->admsettleModels->get_priority_campaign_by_admin($option);
        // if ($result) {
        //     $camp_array = array();
        //     foreach ($result as $row) {
        //         $yyyymm = sprintf('%04d - %02d', $row->yyyy, $row->mm);
        //         if ((int)$row->priority < 500) {
        //             $camp_array[$yyyymm][0] = $row->cnt;
        //         } else {
        //             $camp_array[$yyyymm][1] = $row->cnt;
        //         }
        //     }
        // }

        $camp_array = $this->admsettleModels->get_company_sendlist_by_admin($option);
        $data['sel_company'] = 'koen';
        $data['camp_array'] = $camp_array;
        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle_1', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle_3() {
        $option = array(
            'company' => 'koen2'
        );
        $this->load->model('admsettleModels');
        $data['result'] = $this->admsettleModels->get_deposit_list_by_admin($option);

        // $option = array(230,630);
        // $result = $this->admsettleModels->get_priority_campaign_by_admin($option);
        // if ($result) {
        //     $camp_array = array();
        //     foreach ($result as $row) {
        //         $yyyymm = sprintf('%04d - %02d', $row->yyyy, $row->mm);
        //         if ((int)$row->priority < 500) {
        //             $camp_array[$yyyymm][0] = $row->cnt;
        //         } else {
        //             $camp_array[$yyyymm][1] = $row->cnt;
        //         }
        //     }
        // }

        $camp_array = $this->admsettleModels->get_company_sendlist_by_admin($option);
        $data['sel_company'] = 'koen2';
        $data['camp_array'] = $camp_array;
        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle_3', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle_4() {
        $option = array(
            'company' => 'inssa'
        );
        $this->load->model('admsettleModels');
        $data['result'] = $this->admsettleModels->get_deposit_list_by_admin($option);

        // $option = array(230,630);
        // $result = $this->admsettleModels->get_priority_campaign_by_admin($option);
        // if ($result) {
        //     $camp_array = array();
        //     foreach ($result as $row) {
        //         $yyyymm = sprintf('%04d - %02d', $row->yyyy, $row->mm);
        //         if ((int)$row->priority < 500) {
        //             $camp_array[$yyyymm][0] = $row->cnt;
        //         } else {
        //             $camp_array[$yyyymm][1] = $row->cnt;
        //         }
        //     }
        // }

        $camp_array = $this->admsettleModels->get_company_sendlist_by_admin($option);
        $data['sel_company'] = 'inssa';
        $data['camp_array'] = $camp_array;
        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle_4', $data);
        $this->load->view('templates/adm_footer');
    }
    public function settle_mm() {
        $date_from = '';
        if ($this->input->get('ipt_date_from')) { //검색
            $date_from = $this->input->get('ipt_date_from');
        }
        $ci_date_from = ($date_from == '' ? date('Y-m') : $date_from);
        $date_from = $ci_date_from;

        $option = array(
            'date_from' => $date_from,
        );

        $this->load->model('admsettleModels');
        $data['sales_array'] = $this->admsettleModels->get_sales_mm_by_admin($option);
        $data['purchase_array'] = $this->admsettleModels->get_purchase_mm_by_admin($option);
        //$data['rental_array'] = $this->admsettleModels->get_priority_campaign_mm_by_admin($option);
        $data['rental_array'] = $this->admsettleModels->get_company_sendlist_by_admin($option);
        $data['date_from'] = $date_from;

        $this->load->view('templates/adm_header');
        $this->load->view('admsettle/settle_mm', $data);
        $this->load->view('templates/adm_footer');
    }
    public function change_memo() {
        auth_session_userdata($this, 5);

        $this->load->library('form_validation');
        $this->form_validation->set_rules('xid', 'xid', 'required');
        // $this->form_validation->set_rules('new_memo', 'new_memo', 'required');
        $this->form_validation->set_rules('new_receipt', 'new_receipt', 'required');
        $this->form_validation->set_rules('new_onse_receipt', 'new_onse_receipt', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $xid = (int)$this->input->post('xid');
        $memo = trim($this->input->post('new_memo'));
        $receipt = trim($this->input->post('new_receipt'));
        $onse_receipt = trim($this->input->post('new_onse_receipt'));
        $onse_date = trim($this->input->post('new_onse_date'));
        $onse_amount = trim($this->input->post('new_onse_amount'));
        $onse_amount = (int)str_replace(",",'',$onse_amount);

        $option = array(
            'xid' => $xid,
            'memo' => $memo,
            'receipt' => $receipt,
            'onse_receipt' => $onse_receipt,
            'onse_date' => $onse_date,
            'onse_amount' => $onse_amount,
        );

        $this->load->model('admsettleModels');
        $insert_id = $this->admsettleModels->modify_deposit_memo_by_admin($option);
        if (!$insert_id) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function deposit() {
        auth_session_userdata($this, 5);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('sel_company', 'sel_company', 'required');
        $this->form_validation->set_rules('auth_amount', 'auth_amount', 'required');
        $this->form_validation->set_rules('auth_type', 'auth_type', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $sel_company = $this->input->post('sel_company');
        $auth_amount = (int)$this->input->post('auth_amount');
        //1:입금 -1:차감
        $auth_type = (int)$this->input->post('auth_type');

        // if ($sel_company == 'captain') {
        //     $this->session->set_flashdata('notice', '결산이 완료되어 등록할 수 없습니다.');
        //     redirect($_SERVER['HTTP_REFERER']);
        // }

        $req_amount = $auth_amount * $auth_type;
        $option = array(
            'company' => $sel_company,
            'auth_amount' => $req_amount,
        );

        $this->load->model('admsettleModels');
        $insert_id = $this->admsettleModels->insert_captain_deposit_amount_by_admin($option);
        if (!$insert_id) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $param = array(
            'xid' => $insert_id,
            'amount' => $req_amount
        );
        if ($sel_company == 'koen') {
            $url = 'http://manage.koensms.com/nulsom.php';
        } else if ($sel_company == 'koen2') {
            $url = 'http://manage.koensms.com/nulsom2.php';
        } else if ($sel_company == 'koen3') {
            $url = 'http://manage.koensms.com/nulsom3.php';
        } else if ($sel_company == 'dasso') {
            $url = 'http://dasso.net/crontab/nlsom.php';
        } else if ($sel_company == 'kopo') {
            $url = 'http://kopo365.com/crontab/nlsom.php';
        } else if ($sel_company == 'mjmj') {
            $url = 'http://mjmj82.com/crontab/nlsom.php';
        } else if ($sel_company == 'esa') {
            $url = 'http://kopo365.com/crontab/nlsom_esa.php';
        } else if ($sel_company == 'kopo_kt') {
            $url = 'http://kopo365.com/crontab/nlsom_kopo_kt.php';
        }
// error_log('url:'.$url, 0);
        $post_field_string = http_build_query($param, '', '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close ($ch);
// error_log('response:'.$response, 0);

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
}