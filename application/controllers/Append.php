<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Append extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone'));
        initialize_session_userdata($this);
    }
    public function text_popup() {
        $this->load->view('append/text_popup',$data);
    }
    public function excel_popup() {
        $this->load->view('append/excel_popup',$data);
    }
    public function group_popup() {
        // $this->uri->segment(3);
        $this->load->model('addressModels');
        $data['result'] = $this->addressModels->get_groups();
        $this->load->view('append/group_popup',$data);
    }
    public function person_popup() {
        if ($this->input->get('sv')) { //검색
            $sg = (int)$this->input->get('sg');
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sg = (int)$this->uri->segment(3);
            $sf = (int)$this->uri->segment(4);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(5)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(6) : $this->uri->segment(5));
        }
        $config['base_url'] = "/append/person_popup/{$sg}/{$sf}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => $sv,
            'gno' => $sg,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );
        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_search_list_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_search_list_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $total_rows;
        $this->load->view('append/person_popup',$data);
    }
    public function person() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        // if ($this->session->userdata('checktime')+3 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        $this->load->model('addressModels');
        $result = $this->addressModels->get_person_mobile_join_groupname($list_array);
        unset($list_array);

        $mobile_by_group = array();
        foreach ($result as $row) {
            $mobile_by_group[$row->gno][] = $row->mobile.'|:|'.$row->name.'|:|'.$row->group_name;
        }

        $keyinfo = '';
        $rand = rand(10,99);
        // $this->load->driver('cache');
        foreach ($mobile_by_group as $group_info) {
            $mobile_array = array();
            $name_array = array();
            $type = '';
            foreach ($group_info as $val) {
                if (!$val) continue;
                //0109999898987|:||:|광명꿈의도시
                $group_array = array();
                $group_array = explode('|:|', $val);
                $mobile_array[] = strip_phone($group_array[0]);
                if ($group_array[1] != '') $name_array[strip_phone($group_array[0])][0] = $group_array[1];
                if ($type == '') $type = $group_array[2];
            }
            $prefix = (count($name_array) ? 'M' : 'G');
            $cache_key = 'sow'.$this->session->userdata('uniqueno').date('is').++$rand;
            $count = count($mobile_array);
            if (!$count) continue;

            $this->cache->redis->save($cache_key, $mobile_array, 3600); //1시간
            unset($mobile_array);

            if ($prefix == 'M') {
                $name_cache_key = 'sow_name'.$this->session->userdata('uniqueno');
                $name_pool = array();
                $cached_array = '';
                $cached_array = $this->cache->redis->get($name_cache_key);
                if (is_array($cached_array)) $name_pool = array_merge($cached_array, $name_array);
                else $name_pool = $name_array;
                $this->cache->redis->save($name_cache_key, $name_pool, 3600); //1시간
                unset($cached_array);
                unset($name_pool);
                unset($name_array);
            }

            if ($keyinfo != '') $keyinfo .= '|,|';
            $keyinfo .= "{$prefix}|:|{$cache_key}|:|{$type}|:|{$count}";
            //M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트|,|M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트
        }
        unset($mobile_by_group);

        if (!$keyinfo) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '0';
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '받는사람 목록에 정상적으로 등록되었습니다.';
        exit (json_encode($data_json));
    }
    public function group() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        // if ($this->session->userdata('checktime')+3 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        $keyinfo = '';
        $rand = rand(10,99);
        // $this->load->driver('cache');
        $this->load->model('addressModels');
        foreach ($list_array as $val) {
            if (!$val) continue;
            $option = array();
            $option['gno'] = $val;
            $result = $this->addressModels->get_groups_mobile_join_groupname($option);

            $mobile_array = array();
            $name_array = array();
            $type = '';
            foreach ($result as $row) {
                $mobile_array[] = strip_phone($row->mobile);
                if ($row->name) $name_array[strip_phone($row->mobile)][0] = $row->name;
                if ($type == '') $type = $row->group_name;
            }
            $prefix = (count($name_array) ? 'M' : 'G');
            $cache_key = 'sow'.$this->session->userdata('uniqueno').date('is').++$rand;
            $count = count($mobile_array);
            if (!$count) continue;

            $this->cache->redis->save($cache_key, $mobile_array, 3600); //1시간
            unset($mobile_array);

            if ($prefix == 'M') {
                $name_cache_key = 'sow_name'.$this->session->userdata('uniqueno');
                $name_pool = array();
                $cached_array = '';
                $cached_array = $this->cache->redis->get($name_cache_key);
                if (is_array($cached_array)) $name_pool = array_merge($cached_array, $name_array);
                else $name_pool = $name_array;
                $this->cache->redis->save($name_cache_key, $name_pool, 3600); //1시간
                unset($cached_array);
                unset($name_pool);
                unset($name_array);
            }

            if ($keyinfo != '') $keyinfo .= '|,|';
            $keyinfo .= "{$prefix}|:|{$cache_key}|:|{$type}|:|{$count}";
            //M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트|,|M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트
        }
        unset($list_array);

        if (!$keyinfo) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '0';
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '받는사람 목록에 정상적으로 등록되었습니다.';
        exit (json_encode($data_json));
    }
    public function string() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        if ($this->uri->segment(3) == 'excel') {
            $_checktime = time();
            if ((int)$this->session->userdata('excel_checktime') >= $_checktime) {
                error_log('exit..........excel double click');
                $data_json['result'] = 'error';
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '중복 요청으로 마지막 요청은 차단되었습니다.';
                exit (json_encode($data_json));
            }
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[text,excel]');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        $mobile_array = array();
        $name_array = array();
        foreach ($list_array as $val) {
            $data_array = array();
            $data_array = explode('|', $val);
            $mobile_array[] = strip_phone($data_array[0]);
            if ($this->uri->segment(3) == 'text') continue;
            if ($data_array[1] != '') $name_array[strip_phone($data_array[0])][0] = $data_array[1];
        }
        unset($list_array);
        $prefix = (count($name_array) ? 'M' : 'G');
        $cache_key = 'sow'.$this->session->userdata('uniqueno').date('is').rand(10,99);
        $type = ($this->input->post('type') == 'text' ? '파일붙여넣기' : '엑셀붙여넣기');
        $count = count($mobile_array);
        if (!$count) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        // $this->load->driver('cache');
        $this->cache->redis->save($cache_key, $mobile_array, 3600); //1시간
        unset($mobile_array);

        if ($prefix == 'M') {
            $name_cache_key = 'sow_name'.$this->session->userdata('uniqueno');
            $name_pool = array();
            $cached_array = '';
            $cached_array = $this->cache->redis->get($name_cache_key);
            if (is_array($cached_array)) $name_pool = array_merge($cached_array, $name_array);
            else $name_pool = $name_array;
            $this->cache->redis->save($name_cache_key, $name_pool, 3600); //1시간
            unset($cached_array);
            unset($name_pool);
            unset($name_array);
        }

        $keyinfo = "{$prefix}|:|{$cache_key}|:|{$type}|:|{$count}|,|";
        //M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트|,|M(이름포함)orG|:|cachekey|:|추가방식(그룹명)|:|카운트

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '0';
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '받는사람 목록에 정상적으로 등록되었습니다.';
        $this->session->set_userdata('excel_checktime', time()+4);
        exit (json_encode($data_json));
    }
    public function init_elect() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        // if ($this->session->userdata('checktime')+3 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        // $this->load->driver('cache');
        // $this->load->helper('cache');
        initialize_elect_cached($this->session, $this->cache->redis);

        $data_json['result'] = 'success';
        $data_json['message'] = '정상적으로 초기화 되었습니다.';
        exit (json_encode($data_json));
    }
    public function elect_person() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        // if ($this->session->userdata('checktime')+3 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }

        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        $this->load->model('addressModels');
        $result = $this->addressModels->get_person_mobile($list_array);
        unset($list_array);

        $mobile_pool = array();
        $name_array = array();
        $kk = 0;
        foreach ($result as $row) {
            // $mobile_by_group[$row->gno][] = $row->mobile.'|:|'.$row->name.'|:|'.$row->group_name;
            if ($row->mobile == '') continue;
            $mobile_pool[] = $row->mobile;
            if ($row->name != '') {
                $name_array[strip_phone($row->mobile)][0] = $row->name;
                $kk ++;
            }
        }
        unset($result);

        //중복제거
        $unique_array = array();
        $unique_array = array_unique($mobile_pool);
        unset($mobile_pool);

        // $this->load->driver('cache');
        $cache_key = 'sow_elect_total'.$this->session->userdata('uniqueno');
        $cached_total_array = $this->cache->redis->get($cache_key);

        //전체중복제거
        if (is_array($cached_total_array)) $added_array = array_diff($unique_array,$cached_total_array);
        else $added_array = $unique_array;
        unset($unique_array);

        if (is_array($cached_total_array)) $new_total_array = array_merge($cached_total_array, $added_array);
        else $new_total_array = $added_array;
        $this->cache->redis->save($cache_key, $new_total_array, 39600); //12시간

        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $tcount = count($new_total_array);
        $this->cache->redis->save($cache_key, $tcount, 39600); //12시간
        unset($cached_total_array);
        unset($new_total_array);

        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        if (is_array($cached_remain_array)) $new_remain_array = array_merge($cached_remain_array, $added_array);
        else $new_remain_array = $added_array;
        $rcount = count($new_remain_array);
        $this->cache->redis->save($cache_key, $new_remain_array, 39600); //12시간

        $i = 0;
        $keyinfo = '';
        foreach ($new_remain_array as $val) {
            if ($i >= 20) break;
            if ($keyinfo != '') $keyinfo .= '|,|';
            $keyinfo .= "P|:|{$val}|:|{$val}|:|1";
            $i++;
        }
        unset($cached_remain_array);
        unset($new_remain_array);
        unset($added_array);

        if ($kk) {
            $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
            $cached_name_array = $this->cache->redis->get($cache_key);
            if (is_array($cached_name_array)) $new_name_array = array_merge($cached_name_array, $name_array);
            else $new_name_array = $name_array;
            $this->cache->redis->save($cache_key, $new_name_array, 39600); //12시간
            unset($cached_name_array);
            unset($new_name_array);
            unset($name_array);
        }
        unset($kk);

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '1';
        $data_json['tcount'] = $tcount;
        $data_json['rcount'] = $rcount;
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '추가된 데이타는 중복제거 된 후 목록에 추가 되었습니다.';
        exit (json_encode($data_json));
    }
    public function elect_group() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        // if ($this->session->userdata('checktime')+3 >= date('ymdHis')) {
        //     $data_json['result'] = 'error';
        //     $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        //     // $data_json['message'] = '더블클릭 방지로 제한되었습니다.';
        //     $data_json['message'] = '';
        //     exit (json_encode($data_json));
        // }
        // $this->session->set_userdata('checktime', date('ymdHis'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        // $this->load->driver('cache');
        $this->load->model('addressModels');
        $mobile_pool = array();
        $name_array = array();
        $kk = 0;
        foreach ($list_array as $val) {
            if (!$val) continue;
            $option = array();
            $option['gno'] = $val;
            $result = $this->addressModels->get_groups_mobile_join_groupname($option);

            foreach ($result as $row) {
                $mobile_pool[] = strip_phone($row->mobile);
                if ($row->name) {
                    $name_array[strip_phone($row->mobile)][0] = $row->name;
                    $kk ++;
                }
            }
        }

        //중복제거
        $unique_array = array();
        $unique_array = array_unique($mobile_pool);
        unset($mobile_pool);

        // $this->load->driver('cache');
        $cache_key = 'sow_elect_total'.$this->session->userdata('uniqueno');
        $cached_total_array = $this->cache->redis->get($cache_key);

        //전체중복제거
        if (is_array($cached_total_array)) $added_array = array_diff($unique_array,$cached_total_array);
        else $added_array = $unique_array;
        unset($unique_array);

        if (is_array($cached_total_array)) $new_total_array = array_merge($cached_total_array, $added_array);
        else $new_total_array = $added_array;
        $this->cache->redis->save($cache_key, $new_total_array, 39600); //12시간

        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $tcount = count($new_total_array);
        $this->cache->redis->save($cache_key, $tcount, 39600); //12시간
        unset($cached_total_array);
        unset($new_total_array);

        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        if (is_array($cached_remain_array)) $new_remain_array = array_merge($cached_remain_array, $added_array);
        else $new_remain_array = $added_array;
        $rcount = count($new_remain_array);
        $this->cache->redis->save($cache_key, $new_remain_array, 39600); //12시간

        $i = 0;
        $keyinfo = '';
        foreach ($new_remain_array as $val) {
            if ($i >= 20) break;
            if ($keyinfo != '') $keyinfo .= '|,|';
            $keyinfo .= "P|:|{$val}|:|{$val}|:|1";
            $i++;
        }
        unset($cached_remain_array);
        unset($new_remain_array);
        unset($added_array);

        if ($kk) {
            $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
            $cached_name_array = $this->cache->redis->get($cache_key);
            if (is_array($cached_name_array)) $new_name_array = array_merge($cached_name_array, $name_array);
            else $new_name_array = $name_array;
            $this->cache->redis->save($cache_key, $new_name_array, 39600); //12시간
            unset($cached_name_array);
            unset($new_name_array);
            unset($name_array);
        }
        unset($kk);

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '1';
        $data_json['tcount'] = $tcount;
        $data_json['rcount'] = $rcount;
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '추가된 데이타는 중복제거 된 후 목록에 추가 되었습니다.';
        exit (json_encode($data_json));
    }
    public function elect_string() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        if ($this->uri->segment(3) == 'excel') {
            $_checktime = time();
            if ((int)$this->session->userdata('excel_checktime') >= $_checktime) {
                error_log('exit..........excel double click');
                $data_json['result'] = 'error';
                $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
                $data_json['message'] = '중복 요청으로 마지막 요청은 차단되었습니다.';
                exit (json_encode($data_json));
            }
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('mobile_list', 'Mobile List', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[text,excel]');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[sms]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $list_array = array();
        $list_array = explode(',', $this->input->post('mobile_list'));

        $mobile_pool = array();
        $name_array = array();
        foreach ($list_array as $val) {
            $data_array = array();
            $data_array = explode('|', $val);
            $mobile = '';
            $mobile = trim(strip_phone($data_array[0]));
            if (!$mobile) continue;
            $mobile_pool[] = $mobile;
            if ($data_array[1] != '') $name_array[$mobile][0] = $data_array[1];
        }
        //중복제거
        $unique_array = array();
        $unique_array = array_unique($mobile_pool);
        unset($mobile_pool);

        // $this->load->driver('cache');
        $cache_key = 'sow_elect_total'.$this->session->userdata('uniqueno');
        $cached_total_array = $this->cache->redis->get($cache_key);

        //전체중복제거
        if (is_array($cached_total_array)) $added_array = array_diff($unique_array,$cached_total_array);
        else $added_array = $unique_array;
        unset($unique_array);

        if (is_array($cached_total_array)) $new_total_array = array_merge($cached_total_array, $added_array);
        else $new_total_array = $added_array;
        $this->cache->redis->save($cache_key, $new_total_array, 39600); //12시간

        $cache_key = 'sow_elect_total_count'.$this->session->userdata('uniqueno');
        $tcount = count($new_total_array);
        $this->cache->redis->save($cache_key, $tcount, 39600); //12시간
        unset($cached_total_array);
        unset($new_total_array);

        $cache_key = 'sow_elect_remain'.$this->session->userdata('uniqueno');
        $cached_remain_array = $this->cache->redis->get($cache_key);
        if (is_array($cached_remain_array)) $new_remain_array = array_merge($cached_remain_array, $added_array);
        else $new_remain_array = $added_array;
        $rcount = count($new_remain_array);
        $this->cache->redis->save($cache_key, $new_remain_array, 39600); //12시간

        $i = 0;
        $keyinfo = '';
        foreach ($new_remain_array as $val) {
            if ($i >= 20) break;
            if ($keyinfo != '') $keyinfo .= '|,|';
            $keyinfo .= "P|:|{$val}|:|{$val}|:|1";
            $i++;
        }
        unset($cached_remain_array);
        unset($new_remain_array);
        unset($added_array);

        if ($kk) {
            $cache_key = 'sow_elect_name'.$this->session->userdata('uniqueno');
            $cached_name_array = $this->cache->redis->get($cache_key);
            if (is_array($cached_name_array)) $new_name_array = array_merge($cached_name_array, $name_array);
            else $new_name_array = $name_array;
            $this->cache->redis->save($cache_key, $new_name_array, 39600); //12시간
            unset($cached_name_array);
            unset($new_name_array);
            unset($name_array);
        }
        unset($kk);

        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['elect'] = '1';
        $data_json['tcount'] = $tcount;
        $data_json['rcount'] = $rcount;
        $data_json['keyinfo'] = $keyinfo;
        $data_json['message'] = '추가된 데이타는 중복제거 된 후 목록에 추가 되었습니다.';
        $this->session->set_userdata('excel_checktime', time()+4);
        exit (json_encode($data_json));
    }
}
