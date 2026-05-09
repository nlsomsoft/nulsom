<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admsend extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','bill','mytext','phone','mydate','text'));
        initialize_session_userdata($this,true);
    }
    public function list() {
        // if ($this->input->get('stx')) { //검색
        //     $stx = $this->input->get('stx');
        //     $offset = 0;
        // } else {
        //     $stx = '000';
        //     $offset =  (int)$this->uri->segment(4);
        // }
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $stx = trim($this->input->get('stx'));
            $sfl = $this->input->get('sfl');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $stx = $this->uri->segment(5);
            $sfl = $this->uri->segment(6);
            $offset =  (int)$this->uri->segment(7);
        }
        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admsend/list/{$ci_date_from}/{$ci_date_to}/{$stx}/{$sfl}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'val' => ($stx == '000' ? '' : $stx),
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count_by_admin($option);
        $data['result'] = $this->resultModels->get_campaign_limit_by_admin($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admsend/list', $data);
        $this->load->view('templates/adm_footer');
    }
    public function list1() {
        if ($this->input->get('ipt_date_from') && $this->input->get('ipt_date_to')) { //검색
            $date_from = $this->input->get('ipt_date_from');
            $date_to = $this->input->get('ipt_date_to');
            $stx = trim($this->input->get('stx'));
            $sfl = $this->input->get('sfl');
            $offset = 0;
        } else {
            $date_from = $this->uri->segment(3);
            $date_to = $this->uri->segment(4);
            $stx = $this->uri->segment(5);
            $sfl = $this->uri->segment(6);
            $offset =  (int)$this->uri->segment(7);
        }
        if (!$stx) $stx = '000';
        if (!$sfl) $sfl = '000';
        $ci_date_from = ($date_from == '' ? date('Y-m-d') : $date_from);
        $ci_date_to = ($date_to == '' ? date('Y-m-d') : $date_to);

        if ($sfl == 'callback') $stx = str_replace('-','',$stx);

        $config['base_url'] = "/admsend/list1/{$ci_date_from}/{$ci_date_to}/{$stx}/{$sfl}";
        $config['per_page'] = $this->rows_per_page;

        $date_from = $ci_date_from;
        $date_to = $ci_date_to;

        $option = array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'val' => ($stx == '000' ? '' : $stx),
            'sfl' => ($sfl == '000' ? '' : $sfl),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'total_units' => 100,
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count_by_admin($option);
        $data['result'] = $this->resultModels->get_campaign_limit_by_admin($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;
        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['sfl'] = ($stx == '000' ? '' : $sfl);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admsend/list1', $data);
        $this->load->view('templates/adm_footer');
    }
    public function reserve() {
        $ipt_stx = trim($this->input->get('stx'));
        if ($ipt_stx) { //검색
            $stx = $ipt_stx;
            $offset = 0;
        } else {
            $stx = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/admsend/reserve/{$stx}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($stx == '000' ? '' : $stx),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'status' => true,
        );
        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count_by_admin($option);
        $data['result'] = $this->resultModels->get_campaign_limit_by_admin($option);

        $option = array(
            'status' => '1'
        );
        $result = $this->resultModels->get_channel($option);
        $channel_info = array();
        $data['channel'] = array();
        foreach ($result as $row) {
            $data['channel'][$row->type][$row->channel] = $row->channel_exp;
            $channel_info[$row->channel] = $row->channel_exp;
        }
        $data['channel_info'] = $channel_info;

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['stx'] = ($stx == '000' ? '' : $stx);
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/adm_header');
        $this->load->view('admsend/reserve', $data);
        $this->load->view('templates/adm_footer');
    }
    public function change() {
        auth_session_userdata($this, 3);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('act_button', 'act button', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option = $this->input->post('chk');
        if (!is_array($option)) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->load->model('resultModels');
        if ($this->input->post('act_button') == '예약취소') {
            $option1 = $this->resultModels->get_campaign_list_by_admin($option);
            if (!$option1) {
                unset($option);
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }

            $result = $this->resultModels->delete_reserved_campaign_by_admin($option,$option1);
            if (!$result) {
                unset($option);
                unset($option1);
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }

            $usersdata = array();
            foreach ($option1 as $row) {
                $usersdata[$row['userid']] = $row['storeno'];
            }
            foreach ($usersdata as $userid => $storeno) {
                //session 정보 초기화
                $cache_key = 'session_'.$storeno.'_'.$userid;
                $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
            }
        } else { //채널변경
            $prociddata = array();
            $channeldata = array();
            $prioritydata = array();
            foreach ($option as $procid) {
                $priority = $this->input->post("se_priority_{$procid}");
                $prioritydata[] = $priority;
                $prociddata[$priority][] = $procid;
            }
            $channeldata = array_unique($prioritydata);
            $result = $this->resultModels->modify_priority_campaign_by_admin($channeldata,$prociddata);
            if (!$result) {
                unset($option);
                $this->session->set_flashdata('notice', '시스템 오류입니다.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        unset($option);
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function filter() {
        $this->load->model('admsendModels');
        $data['filter'] = $this->admsendModels->get_filter_word_by_admin();

        $this->load->view('templates/adm_header');
        $this->load->view('admsend/filter', $data);
        $this->load->view('templates/adm_footer');
    }
    public function filter_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('word', 'word', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $option['word'] = trim($this->input->post('word'));

        $this->load->model('admsendModels');
        $query = $this->admsendModels->modify_filter_word_by_admin($option);
        if (!$query) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
}