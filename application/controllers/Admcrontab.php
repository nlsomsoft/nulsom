<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admcrontab extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        // $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        // $this->rows_per_page = 100;
        // $this->load->helper(array('form','url','phone','mydate','bill'));
    }
    public function new_bank() {
        $this->load->model('payModels');
        $cnt = (int)$this->payModels->get_bankbook_request_count();
        $cache_key = 'sow_bankbook_cnt';
        $this->cache->redis->save($cache_key, $cnt, 600); //10분 후 자동삭제
    }
    public function new_callback() {
        $this->load->model('userModels');
        $cnt = (int)$this->userModels->get_callback_new_count();
        $cache_key = 'sow_callback_cnt';
        $this->cache->redis->save($cache_key, $cnt, 600); //10분 후 자동삭제
    }
    public function black_list() {
        error_log('['.$_SERVER['STORENAME'].'][S]...[/admcrontab/black_list]', 0);
        $this->load->model('userModels');
        ini_set('memory_limit','1024M');
        $result = $this->userModels->get_black_list();
        $bans_array = array();
        foreach ($result as $row) {
            $mobile = '';
            $mobile = preg_replace('/\r\n|\r|\n/','',$row->mobile);
            $bans_array[] = trim($mobile);
        }
        $cache_key = 'sow_black_list';
        $this->cache->redis->save($cache_key, $bans_array, 172800); //48시간 후 자동삭제
        error_log('['.$_SERVER['STORENAME'].'][F]...[/admcrontab/black_list]', 0);
    }
    public function get_black() {
        $cache_key = 'sow_black_list';
        $bans_array = $this->cache->redis->get($cache_key);
        $ban_cnt = count($bans_array);
// error_log(print_r($bans_array,1),0);

        $unique_array[] = '01052797702';
        $unique_array[] = '01010000827111';
        $send_array = array_diff($unique_array, $bans_array);
error_log(print_r($send_array,1),0);
    }
}
