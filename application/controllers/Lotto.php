<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lotto extends CI_Controller {
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
			set_cookie('unique_num', $cookie_value, 3600, '.zzonga.com', '/');
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
        	redirect('/lotto/main');
        }

		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('lotto/index', $data);
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
			redirect('/lotto/main');
		}
	}
	public function main() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if (!$auth_num) {
        	redirect('/lotto/index');
        }

		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('lotto/main', $data);
		// $this->load->view('templates/footer');
	}
	public function winning() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if (!$auth_num) {
        	redirect('/lotto/index');
        }

		$cur_divide = (int)lotto_current_divide_num();
        $this->load->model('lottoModels');
        $result = $this->lottoModels->get_previous_winning_num($cur_divide);
// error_log(print_r($result,1),0);

		$tmp_array = explode('|', trim($result->num));
		$winning_array = explode(',', $tmp_array[0]);
		$bonus_array[] = (int)$tmp_array[1];
// error_log(print_r($winning_array,1),0);

        $winning = $this->lottoModels->get_recommended_winning($cur_divide);
        $data['winning'] = $winning;
// error_log(print_r($winning,1), 0);

		$data['drawing_date'] = trim($result->drawing_date);
		$data['divide'] = (int)$result->divide;
		$data['winning_array'] = $winning_array;
		$data['bonus_array'] = $bonus_array;
		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('lotto/winning', $data);
		// $this->load->view('templates/footer');
	}
	public function roll() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if (!$auth_num) {
        	redirect('/lotto/index');
        }

		$include_num = trim($this->input->post('include_num'));
		$exclude_num = trim($this->input->post('exclude_num'));

		$inp_include = explode(',', $include_num);
		$inp_exclude = explode(',', $exclude_num);

		$inclusion = array();
		$exclusion = array();

		foreach($inp_include as $ival) {
			if (count($inclusion) >= 5) break;

			$ival = (int)$ival;
			if (!$ival) continue;
			if ($ival < 1 || $ival > 45) continue;
			$inclusion[] = (int)$ival;
		}
		$ii = 0;
		foreach($inp_exclude as $eval) {
			if ($ii > 10) break;

			$eval = (int)$eval;
			if (!$eval) continue;
			if ($eval < 1 || $eval > 45) continue;
			$exclusion[] = $eval;
			$ii ++;
		}

		$cur_divide = (int)lotto_current_divide_num();
        $this->load->model('lottoModels');
        $result = $this->lottoModels->get_previous_winning_num($cur_divide);

		$tmp_array = explode('|', trim($result->num));
		$winning_array = explode(',', $tmp_array[0]);
		$bonus_array[] = (int)$tmp_array[1];

		$total_recommend = array();
		for ($i = 0; $i < 5; $i++) {
			$union_inclusion = array();
			$union_exclusion = array();
			if ($i < 6) {
				$union_inclusion = array_unique(array_merge(array($winning_array[$i]), $inclusion));
				$complement = array_values(array_diff($winning_array, array($winning_array[$i])));
				$union_exclusion = array_unique(array_merge($complement, $exclusion));
			} else {
				$union_exclusion = array_unique(array_merge($winning_array, $exclusion));
			}

			$total_recommend[] = make_recommend_number($union_inclusion, $union_exclusion);
		}

		foreach ($total_recommend as $lotto_array) {
			$option = array(
				'num' => implode(',', $lotto_array),
				'divide' => $cur_divide,
				'user' => $auth_num,
				'type' => '0',
				'ip' => $this->input->ip_address()
			);
	        $result = $this->lottoModels->add_lotto_recommend($option);
		}

		$data['include_num'] = $include_num;
		$data['exclude_num'] = $exclude_num;
		$data['total_recommend'] = $total_recommend;
		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('lotto/main', $data);
		// $this->load->view('templates/footer');
	}
	public function hot_roll() {
		$redis_auth_num = get_cookie('unique_num');
		$cache_key = 'lotto_authed_'.$redis_auth_num;
        $auth_num = $this->cache->redis->get($cache_key);
        if (!$auth_num) {
        	redirect('/lotto/index');
        }

		$include_num = trim($this->input->post('include_num'));
		$exclude_num = trim($this->input->post('exclude_num'));

		$inp_include = explode(',', $include_num);
		$inp_exclude = explode(',', $exclude_num);

		$inclusion = array();
		$exclusion = array();

		foreach($inp_include as $ival) {
			if (count($inclusion) >= 5) break;

			$ival = (int)$ival;
			if (!$ival) continue;
			if ($ival < 1 || $ival > 45) continue;
			$inclusion[] = (int)$ival;
		}
		$ii = 0;
		foreach($inp_exclude as $eval) {
			if ($ii > 10) break;

			$eval = (int)$eval;
			if (!$eval) continue;
			if ($eval < 1 || $eval > 45) continue;
			$exclusion[] = $eval;
			$ii ++;
		}

		$except_array = array();
		$restore_array1 = array();
		$restore_array2 = array();
		$restore_array3 = array();
		$restore_array4 = array();
		$restore_array5 = array();

		for ($j = 0; $j < 30; $j ++) {
			$ball = 0;
			$ball = (int)recommend_balls(array());
			$except_array[] = $ball;
			if ($j < 2) {
				$restore_array1[] = $ball;
			} else if ($j >= 2 && $j < 4) {
				$restore_array2[] = $ball;
			} else if ($j >= 4 && $j < 6) {
				$restore_array3[] = $ball;
			} else if ($j >= 6 && $j < 8) {
				$restore_array4[] = $ball;
			} else if ($j >= 8 && $j < 10) {
				$restore_array5[] = $ball;
			} else if ($j >= 10 && $j < 12) {
				$restore_array6[] = $ball;
			} else if ($j >= 12 && $j < 14) {
				$restore_array7[] = $ball;
			} else if ($j >= 14 && $j < 16) {
				$restore_array8[] = $ball;
			} else if ($j >= 16 && $j < 18) {
				$restore_array9[] = $ball;
			} else if ($j >= 18 && $j < 20) {
				$restore_array10[] = $ball;
			}
		}

		$added_exclusion = array();
		$total_unique_array = array_unique($except_array);

		$cur_divide = (int)lotto_current_divide_num();
        $this->load->model('lottoModels');
        $result = $this->lottoModels->get_previous_winning_num($cur_divide);

		$tmp_array = explode('|', trim($result->num));
		$winning_array = explode(',', $tmp_array[0]);
		$bonus_array[] = (int)$tmp_array[1];

		$total_recommend = array();
		for ($i = 0; $i < 5; $i++) {
			$union_inclusion = array();
			$union_exclusion = array();
			if ($i < 6) {
				$union_inclusion = array_unique(array_merge(array($winning_array[$i]), $inclusion));
				$complement = array_values(array_diff($winning_array, array($winning_array[$i])));
				$union_exclusion = array_unique(array_merge($complement, $exclusion));
			} else {
				$union_exclusion = array_unique(array_merge($winning_array, $exclusion));
			}

			$hot_union_exclusion = array();
			if ($i == 0) {
				$added_exclusion = $total_unique_array;
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 1) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array1));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 2) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array2));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 3) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array3));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 4) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array4));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 5) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array5));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 6) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array6));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 7) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array7));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 8) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array8));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 9) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array9));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			} else if ($i == 10) {
				$added_exclusion = array_values(array_diff($added_exclusion, $restore_array10));
				$hot_union_exclusion = array_unique(array_merge($union_exclusion, $added_exclusion));
			}
			$total_recommend[] = make_recommend_number($union_inclusion, $hot_union_exclusion);
		}

		foreach ($total_recommend as $lotto_array) {
			$option = array(
				'num' => implode(',', $lotto_array),
				'divide' => $cur_divide,
				'user' => $auth_num,
				'type' => '1',
				'ip' => $this->input->ip_address()
			);
	        $result = $this->lottoModels->add_lotto_recommend($option);
		}

		$data['include_num'] = $include_num;
		$data['exclude_num'] = $exclude_num;
		$data['total_recommend'] = $total_recommend;
		// $data['_g_search_robots'] = true;
		// $this->load->view('templates/header',$data);
		$this->load->view('lotto/main', $data);
		// $this->load->view('templates/footer');
	}
}
