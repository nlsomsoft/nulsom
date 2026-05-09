<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','lotto','cookie'));
        // initialize_session_userdata($this);

		$redis_auth_num = get_cookie('unique_num');
		if (!$redis_auth_num) {
			$cookie_value = date('His').mt_rand(100,900);
			set_cookie('unique_num', $cookie_value, 7200, '.zzonga.com', '/');
			// $redis_auth_num = $cookie_value;
// error_log(print_r($_SERVER,1), 0);
			redirect($_SERVER['REQUEST_URI']);
		}
    }
	public function index() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if ($auth_num) {
        	redirect('/stock/main');
        }

		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('stock/index', $data);
		// $this->load->view('templates/footer');
	}
	public function login() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('auth_num', 'auth_num', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '인증번호를 입력하세요.');
            redirect($_SERVER['HTTP_REFERER']);
        }
		$auth_num = trim($this->input->post('auth_num'));

		global $LOTTO_CONFIRM_LIST;
// error_log(print_r($LOTTO_CONFIRM_LIST,1),0);
		if (!in_array($auth_num, $LOTTO_CONFIRM_LIST)) {
			redirect($_SERVER['HTTP_REFERER']);
		} else {
	        $redis_auth_num = get_cookie('unique_num');
	        $cache_key = 'lotto_authed_'.$redis_auth_num;
	        $this->cache->redis->save($cache_key, $auth_num, 600);
			redirect('/stock/main');
		}
	}
	public function main() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if (!$auth_num) {
        	redirect('/stock/index');
        }

        if ($this->uri->segment(3) != '') {
	        $yyyymmdd = $this->uri->segment(3);
        } else {
	        $yyyymmdd = date('Y-m-d');
        }

        $option = array(
        	'yyyymmdd' => $yyyymmdd
        );

        $this->load->model('stockModels');
        $data['result'] = $this->stockModels->get_today_recommendation($option);
        $data['result1'] = $this->stockModels->get_today_trading_manager($option);
// error_log(print_r($data['result'],1),0);
// error_log(print_r($data['result1'],1),0);
// error_log($data['result1']->add_date,0);

        $data['yyyymmdd'] = $yyyymmdd;
		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('stock/main', $data);
		// $this->load->view('templates/footer');
	}
	public function trigger() {
// error_log(print_r($_POST,1),0);		
        $this->load->library('form_validation');
        $this->form_validation->set_rules('xid', 'xid', 'required');
        $this->form_validation->set_rules('std_yyyymmdd', 'std_yyyymmdd', 'required');
        $this->form_validation->set_rules('set_trigger', 'set_trigger', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $xid = (int)$this->input->post('xid');
        $set_trigger = $this->input->post('set_trigger');
        $std_yyyymmdd = $this->input->post('std_yyyymmdd');
        $option = array(
        	'xid' => $xid,
        	'trigger' => ($set_trigger == 'on' ? '1' : '0'),
        	'yyyymmdd' => str_replace('-','',$std_yyyymmdd)
        );
        $this->load->model('stockModels');
        $data['result'] = $this->stockModels->upsert_manager_today_trigger($option);

        redirect('/stock/main/'.$std_yyyymmdd);
	}
	public function buy() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('buy_yyyymmdd', 'buy_yyyymmdd', 'required');
        $this->form_validation->set_rules('buyTicker[]', 'buyTicker', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }


        $buyTicker = array();
        $buyTicker = $this->input->post('buyTicker');
        if (!count($buyTicker)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $buy_yyyymmdd = $this->input->post('buy_yyyymmdd');
        $option = array(
        	'yyyymmdd' => str_replace('-','',$buy_yyyymmdd)
        );
        $this->load->model('stockModels');
        $data['result'] = $this->stockModels->update_today_recommendation($option,$buyTicker);
        redirect('/stock/main/'.$buy_yyyymmdd);
	}
}
