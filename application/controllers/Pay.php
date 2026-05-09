<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Pay extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','mydate','bill'));
        if ($this->session->userdata('pay_type') == '1') {
            $this->session->set_flashdata('notice', '후불사용자는 사용 권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        initialize_session_userdata($this);
    }
    public function list() {
        $this->load->view('templates/header');
        if (PG_USEAGE_YN == 'Y' && $this->session->userdata('pg') == 'Y') {
            $this->load->view('pay/list');
        } else {
            $this->load->view('pay/bank_list');
        }
        $this->load->view('templates/footer');
    }
    public function bill() {
        if ($this->input->get('date_from')) { //검색
            $date_from = urldecode($this->input->get('date_from'));
            $date_to = urldecode($this->input->get('date_to'));
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $config['base_url'] = "/pay/bill/{$date_from}/{$date_to}/";
        $config['per_page'] = $this->rows_per_page;

        $date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        $option = array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'limit'     => $this->rows_per_page,
            'offset'    => $offset,
        );
        $this->load->model('payModels');
        $config['total_rows'] = (int)$this->payModels->get_bill_count($option);
        $data['result'] = $this->payModels->get_bill_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('pay/bill', $data);
        $this->load->view('templates/footer');
    }
    public function service() {
        if ($this->input->get('date_from')) { //검색
            $date_from = urldecode($this->input->get('date_from'));
            $date_to = urldecode($this->input->get('date_to'));
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $offset =  (int)$this->uri->segment(5);
        }
        $config['base_url'] = "/pay/service/{$date_from}/{$date_to}/";
        $config['per_page'] = $this->rows_per_page;

        $date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        $option = array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'limit'     => $this->rows_per_page,
            'offset'    => $offset,
        );

        $this->load->model('payModels');
        $config['total_rows'] = (int)$this->payModels->get_service_count($option);
        $data['result'] = $this->payModels->get_service_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('pay/service', $data);
        $this->load->view('templates/footer');
    }
    public function bankbook() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ipt_amount', 'Ipt Amount', 'integer|required');
        $this->form_validation->set_rules('ipt_name', 'Ipt Name', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option = array(
            'amount' => (int)$this->input->post('ipt_amount'),
            'deposit_name' => $this->input->post('ipt_name'),
        );
        $this->load->model('payModels');
        $result = $this->payModels->add_bankbook($option);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        // $this->load->helper('api');
        // $msg = '[무통장신청] ID:'.$this->session->userdata('userid').' 이름:'.$this->input->post('ipt_name').' 금액:'.$this->input->post('ipt_amount');
        // noti_message($msg);
        redirect("/pay/confirm/succ/bank/{$option['amount']}/{$option['deposit_name']}");
    }
    public function confirm() {
        $params = array();
        $params[0] = $this->uri->segment(3);
        $params[1] = $this->uri->segment(4);
        $params[2] = $this->uri->segment(5);
        $params[3] = urldecode($this->uri->segment(6));
        $params[4] = urldecode($this->uri->segment(7));
        $data['params'] = $params;
        $this->load->view('templates/header');
        $this->load->view('pay/confirm', $data);
        $this->load->view('templates/footer');
    }
}
