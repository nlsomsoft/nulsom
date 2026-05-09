<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('check_elect_cached')) {
	function check_elect_cached($obj1, $obj2) {
        $cache_key = 'sow_elect_total_count'.$obj1->userdata('uniqueno');
        $tcount = $obj2->get($cache_key);
        if (!$tcount) {
		    // $this->load->driver('cache');
		    $cache_key = 'sow_elect_total'.$obj1->userdata('uniqueno');
		    $obj2->save($cache_key, '', 60);
		    $cache_key = 'sow_elect_total_count'.$obj1->userdata('uniqueno');
		    $obj2->save($cache_key, '', 60);
		    $cache_key = 'sow_elect_remain'.$obj1->userdata('uniqueno');
		    $obj2->save($cache_key, '', 60);
		    $cache_key = 'sow_elect_name'.$obj1->userdata('uniqueno');
		    $obj2->save($cache_key, '', 60);
        }
        return true;
	}
}
if (!function_exists('initialize_elect_cached')) {
	function initialize_elect_cached($obj1, $obj2) {
	    $cache_key = 'sow_elect_total'.$obj1->userdata('uniqueno');
	    $obj2->save($cache_key, '', 60);
	    $cache_key = 'sow_elect_total_count'.$obj1->userdata('uniqueno');
	    $obj2->save($cache_key, '', 60);
	    $cache_key = 'sow_elect_remain'.$obj1->userdata('uniqueno');
	    $obj2->save($cache_key, '', 60);
	    $cache_key = 'sow_elect_name'.$obj1->userdata('uniqueno');
	    $obj2->save($cache_key, '', 60);
	    return true;
	}
}
if (!function_exists('set_session_userdata')) {
	function set_session_userdata($option, $option1 = false) {
		$userno = 0;
		$data = array();
        foreach ($option as $key => $val) {
        	if ($key == 'password') continue;
            $data[$key] = $val;
        }
        //common 080 phone number
		if ($data['phone_080'] == '0808000419') {
			if ($data['phone_ext'] == '') {
				$data['phone_ext'] = (int)($data['userno']) + 100;
			}
		} else {
			$data['phone_ext'] = '';
		}

        if ($option1 == true) {
        	$data['address_tname'] = 'address_'.(int)($data['userno'] % 10);
        	$data['uniqueno'] = $data['storename'].$data['userno'].rand(1000,9999);
        	$data['logged_in'] = TRUE;
        }
	    return $data;
	}
}
if (!function_exists('initialize_session_userdata')) {
	function initialize_session_userdata($this1,$admin=false) {
        if ($this1->session->userdata('logged_in') !== true) {
            $this1->session->set_flashdata('notice', '해당 서비스는 로그인 후 이용할 수 있습니다.');
            redirect('/');
        }
        if ($admin == true) {
        	if ((int)$this1->session->userdata('level') < 3 || $this1->session->userdata('authed_manager') !== true) {
	            $this1->session->set_flashdata('notice', '해당 서비스는 사용권한이 없습니다.');
	            redirect('/');
	        }
        }
        if ($admin == false) {
			$_is_session = false;
	        $cache_key = 'session_'.$this1->session->userdata('storeno').'_'.$this1->session->userdata('userid');
	        if ($this1->cache->redis->get($cache_key) == '1' ||
	        	$this1->cache->redis->get('session_change_channel_sms') == $this1->session->userdata('ch_sms') ||
	        	$this1->cache->redis->get('session_change_channel_lms') == $this1->session->userdata('ch_lms') ||
	        	$this1->cache->redis->get('session_change_channel_mms') == $this1->session->userdata('ch_mms'))
	        {
				$_is_session = true;
		        $this1->load->model('userModels');
		        $result = $this1->userModels->get_user_all_info(array('userid' => $this1->session->userdata('userid')));
				if ($result->state == '1' || $result->state == '2') { //차단,탈퇴
					redirect('/signup/logout');
				}
		        // $result = $this1->userModels->get_user_all_info(array('userid' => $this1->session->userdata('userid')));
		        $this1->session->set_userdata(set_session_userdata($result));
		        $this1->cache->redis->save($cache_key, '', 60);
	        }
	        $cache_key = 'callback_'.$this1->session->userdata('storeno').'_'.$this1->session->userdata('userid');
	        if ($this1->cache->redis->get($cache_key) == '1') {
		        if ($_is_session == false) $this1->load->model('userModels');
		        $result = $this1->userModels->get_callback_to_session();
		        $this1->session->set_userdata('callback', $result);
		        $this1->cache->redis->save($cache_key, '', 60);
	        }
	    }
	}
}
if (!function_exists('auth_session_userdata')) {
	function auth_session_userdata($this1,$limit_level) {
        if ((int)$this1->session->userdata('level') < $limit_level) {
            $this1->session->set_flashdata('notice', '해당 서비스에 접근할 권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
	}
}