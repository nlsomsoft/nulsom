<?php
class UserModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }
    function get_users_count($option) {
        $this->db->from('users');
        if ($this->session->userdata('level') != '9') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
                else $this->db->where('groupno', $this->session->userdata('groupno'));
            }
        }
        if ($option['val'] != '') $this->db->where('MATCH (userid,realname,mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        if ($option['state'] != '') $this->db->where('state', $option['state']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_users_limit($option) {
        if (defined('KMCIS_CPID')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phoneno auth_phoneno');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phone_no auth_phoneno');
        } else {
            $this->db->select('a.*, b.storename, c.groupid');
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') $this->db->where('a.groupno', $this->session->userdata('groupno'));
        }
        if ($option['val'] != '') $this->db->where('MATCH (a.userid,a.realname,a.mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        if ($option['state'] != '') $this->db->where('a.state', $option['state']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        }
        $this->db->order_by('a.userno', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('users a')->result();
        return $query;
    }
    function get_users_count_by_admin($option) {
        $this->db->from('users');
        if ($this->session->userdata('level') != '9') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') $this->db->where('groupno', $this->session->userdata('groupno'));
        }
        if ($option['val'] != '') $this->db->where('MATCH (userid,realname,mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->where_in('state', $option['state']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_users_limit_by_admin($option) {
        if (defined('KMCIS_CPID')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phoneno auth_phoneno');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phone_no auth_phoneno');
        } else {
            $this->db->select('a.*, b.storename, c.groupid');
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') $this->db->where('a.groupno', $this->session->userdata('groupno'));
        }
        if ($option['val'] != '') $this->db->where('MATCH (a.userid,a.realname,a.mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->where_in('a.state', $option['state']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        }
        if ($option['order'] != '') {
            $this->db->order_by($option['order'], 'desc');
        } else {
            $this->db->order_by('a.userno', 'desc');
        }
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('users a')->result();
        return $query;
    }
    function get_users_count_by_admin_new($option) {
        $this->db->from('users');
        if ($this->session->userdata('level') != '9') {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('groupno', '99999');
                else $this->db->where('groupno', $this->session->userdata('groupno'));
            }
        }
        if ($option['stx'] != '') $this->db->where($option['sfl'], $option['stx']);
        $this->db->where_in('state', $option['state']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_users_limit_by_admin_new($option) {
        if (defined('KMCIS_CPID')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phoneno auth_phoneno');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phone_no auth_phoneno');
        } else {
            $this->db->select('a.*, b.storename, c.groupid');
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }
        if ($option['stx'] != '') $this->db->where('a.'.$option['sfl'], $option['stx']);
        $this->db->where_in('a.state', $option['state']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        }
        if ($option['order'] != '') {
            $this->db->order_by($option['order'], 'desc');
        } else {
            $this->db->order_by('a.userno', 'desc');
        }
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('users a')->result();
        return $query;
    }
    function get_users_birthday_count_by_admin_new($option) {
        $this->db->select('COUNT(a.userid) cnt');
        if (defined('KMCIS_CPID')) {
            if ($option['stx'] != '') $this->db->where('d.birthday', $option['stx']);
        } else if (defined('KCP_SITE_CD')) {
            if ($option['stx'] != '') $this->db->where('d.birth_day', $option['stx']);
        } else {
            if ($option['stx'] != '') $this->db->where('d.birthdate', $option['stx']);
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }
        $this->db->where('d.userid !=', '');
        $this->db->where_in('a.state', $option['state']);
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        } else {
            $this->db->join('nice_logs d', 'a.userid = d.userid', 'left');
        }
        // $query = $this->db->count_all_results();
        $query = $this->db->get('users a')->row();
        return $query;
    }
    function get_users_birthday_limit_by_admin_new($option) {
        if (defined('KMCIS_CPID')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phoneno auth_phoneno');
            if ($option['stx'] != '') $this->db->where('d.birthday', $option['stx']);
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.*, b.storename, c.groupid, d.phone_no auth_phoneno');
            if ($option['stx'] != '') $this->db->where('d.birth_day', $option['stx']);
        } else {
            $this->db->select('a.*, b.storename, c.groupid');
            if ($option['stx'] != '') $this->db->where('d.birthdate', $option['stx']);
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }

        $this->db->where_in('a.state', $option['state']);
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        } else {
            $this->db->join('nice_logs d', 'a.userid = d.userid', 'left');
        }
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('`group` c', 'a.groupno = c.groupno', 'left');
        if ($option['order'] != '') {
            $this->db->order_by($option['order'], 'desc');
        } else {
            $this->db->order_by('a.userno', 'desc');
        }
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('users a')->result();
        return $query;
    }
    function get_group_count($option) {
        $this->db->from('group');
        $this->db->where('groupid', $option['groupid']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_group($option) {
        $this->db->select('a.*, b.storename');
        $this->db->where('a.groupid', $option['groupid']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $query = $this->db->get('group a')->row();
        return $query;
    }
    function get_group_list($option) {
        $this->db->select('a.*, b.storename');
        if ($option['storename'] != '') $this->db->where('b.storename', $option['storename']);
        if ($option['type'] != '') $this->db->where('a.type', $option['type']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $query = $this->db->get('group a')->result();
        return $query;
    }
    function get_group_list_by_admin($option) {
        $this->db->where('storeno', $option['storeno']);
        $query = $this->db->get('group')->result();
        return $query;
    }
    function get_group_count_by_admin() {
        $this->db->from('group');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_group_by_admin() {
        $this->db->select('a.*,b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.groupid', 'asc');
        $query = $this->db->get('group a')->result();
        return $query;
    }
    function get_register_info_by_admin($option) {
        $this->db->where('userid', $option['userid']);
        $query = $this->db->get('kcp_logs')->row();
        $query->auth_agent = 'KCP';
        if ($query->userid == '') {
            $this->db->where('userid', $option['userid']);
            $query = $this->db->get('kmcis_logs')->row();
            if ($query->birthday != '')  $query->birth_day = $query->birthday;
            $query->auth_agent = 'KMCIS';
        }
        return $query;
    }
    function add_group($option) {
        $data = array(
            'storeno'  => $option['storeno'],
            'groupid'  => $option['groupid'],
            'group_name'  => $option['group_name'],
            'userid'   => $option['userid'],
            'phone'    => $option['phone'],
            'type'    => $option['type'],
            'add_date' => $this->current_time
        );
        $this->db->insert('group', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_callback_count($option) {
        $this->db->from('callback');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('MATCH (userid,callback) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_limit($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('MATCH (a.userid,a.callback) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.status asc, a.xid desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('callback a')->result();
        return $query;
    }
    function get_callback_count_by_admin($option) {
        $this->db->from('callback');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where($option['sfl'], $option['val']);
        $this->db->where_in('status', array('1','3','4'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_limit_by_admin($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('a.'.$option['sfl'], $option['val']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->where_in('a.status', array('1','3','4'));
        $this->db->order_by('a.status asc, a.xid desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('callback a')->result();
        return $query;
    }
    function get_callback_ban_count_by_admin($option) {
        $this->db->from('callback');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where($option['sfl'], $option['val']);
        $this->db->where_in('status', array('2'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_ban_limit_by_admin($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('a.'.$option['sfl'], $option['val']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->where_in('a.status', array('2'));
        $this->db->order_by('a.status asc, a.xid desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('callback a')->result();
        return $query;
    }
    function get_store($option) {
        if ($option['storeno'] != '') $this->db->where('storeno', $option['storeno']);
        if ($option['storename'] != '') $this->db->where('storename', $option['storename']);
        $this->db->order_by('storeno', 'asc');
        $query = $this->db->get('store')->row();
        return $query;
    }
    function get_store_count_by_admin() {
        $this->db->from('store');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_store_by_admin() {
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        $this->db->order_by('storeno', 'asc');
        $query = $this->db->get('store')->result();
        return $query;
    }
    function add_store($option) {
        $data = array(
            'storename'         => $option['storename'],
            'url'               => $option['url'],
            'sms1'              => $option['sms1'],
            'sms2'              => $option['sms2'],
            'lms1'              => $option['lms1'],
            'lms2'              => $option['lms2'],
            'mms1'              => $option['mms1'],
            'mms2'              => $option['mms2'],
            'kat'               => $option['kat'],
            'kft'               => $option['kft'],
            'kftm'              => $option['kftm'],
            'ch_sms'            => $option['ch_sms'],
            'ch_lms'            => $option['ch_lms'],
            'ch_mms'            => $option['ch_mms'],
            'ch_kko'            => $option['ch_kko'],
            'contract_sms'      => $option['contract_sms'],
            'contract_lms'      => $option['contract_lms'],
            'contract_mms'      => $option['contract_mms'],
            'contract_kko'      => $option['contract_kko'],
            'restrict_sending'  => $option['restrict_sending'],
            'check_balance'     => $option['check_balance'],
        );

        $this->db->insert('store', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_user_by_admin($option) {
        $this->db->select('a.*, b.storename, c.groupid');
        $this->db->where('a.userno', $option['userno']);
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $query = $this->db->get('users a')->row();
        return $query;
    }
    function get_user($option) {
        if ($option['userid'] == '' && $option['realname'] == '' && $option['mobile'] == '') return false;

        $this->db->select('a.*, b.storename, c.groupid');
        if ($option['userid'] != '') $this->db->where('a.userid', $option['userid']);
        if ($option['realname'] != '') $this->db->where('a.realname', $option['realname']);
        if ($option['mobile'] != '') $this->db->where('a.mobile', $option['mobile']);
        $this->db->where('a.state', '0');
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $query = $this->db->get('users a')->row();
        return $query;
    }
    function get_user_all_info($option) {
        if ($option['userid'] == '' && $option['realname'] == '' && $option['mobile'] == '') return false;

        $this->db->select('a.*, b.storename, c.groupid');
        if ($option['userid'] != '') $this->db->where('a.userid', $option['userid']);
        if ($option['realname'] != '') $this->db->where('a.realname', $option['realname']);
        if ($option['mobile'] != '') $this->db->where('a.mobile', $option['mobile']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $query = $this->db->get('users a')->row();
        return $query;
    }
    function is_callback($option) {
        // $data = array(
        //     'userno' => $option['userno'],
        //     'callback' => $option['callback']
        // );
        // $query = $this->db->get_where('callback', $data)->row();
        // 어드민등록 시에는 예외로 처리한다.2020.03.29
        if ($option['storeno'] && $option['userno']) $this->db->where('userno', $option['userno']);
        else $this->db->where('storeno', $option['storeno']);
        $this->db->where('callback', $option['callback']);
        $query = $this->db->get('callback')->row();
        return $query;
    }
    function is_callback_in_store_unique($option) {
        // $this->db->where('storeno', $option['storeno']);
        // $this->db->where('userno', $option['userno']);
        $this->db->where('callback', $option['callback']);
        $query = $this->db->get('callback')->row();
        return $query;
    }
    function get_callback_new_count() {
        $this->db->from('callback');
        $this->db->where('status', '1');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_by_admin($option) {
        $this->db->where_in('xid', $option);
        $query = $this->db->get('callback')->result();
        return $query;
    }
    function get_delete_callback_by_admin($option) {
        $this->db->where_in('xid', $option);
        $query = $this->db->get('callback_deleted')->result();
        return $query;
    }
    function get_callback() {
        $data = array(
            'userno' => $this->session->userdata('userno'),
        );
        $this->db->order_by('status desc, xid desc');
        $query = $this->db->get_where('callback', $data)->result_array();
        return $query;
    }
    function get_callback_by_userno_callbackno($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'callback' => $option['callback'],
            'status' => $option['status'],
        );
        $query = $this->db->get_where('callback', $data)->row_array();
        return $query;
    }
    function get_callback_to_session() {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'status' => '3'
        );
        $this->db->select('callback, name');
        $query = $this->db->get_where('callback', $data)->result_array();
        return $query;
    }
    function modify_callback_memo($option) {
        $data = array(
            'name' => $option['name']
        );
        $this->db->where('xid', $option['xid']);
        $this->db->where('userno', $this->session->userdata('userno'));
        $query = $this->db->update('callback', $data);
        return $query;
    }
    function add_callback($option) {
        $cb_phone = trim($option['callback']);
        if (!$cb_phone) return false;
        $data = array(
            'callback' => $cb_phone
        );
        $callback = $this->db->get_where('callback', $data)->row();
        if ($callback->xid) {
            error_log('duplicated callback phone number', 0);
            return false;
        }

        $data = array(
            'userno'    => $option['userno'],
            'storeno'   => $option['storeno'],
            'userid'    => $option['userid'],
            'name'      => $option['name'],
            'memo'      => $option['memo'],
            'callback'  => $cb_phone,
            'authcode'  => $option['authcode'],
            'cert_type' => $option['cert_type'],
            'status'    => $option['status'],
            'reg_time'  => $this->current_time
        );
        $this->db->insert('callback', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_user_callback($option) {
        $data = array(
            'userno' => $option['userno'],
        );
        $this->db->order_by('xid desc');
        $query = $this->db->get_where('callback', $data)->result_array();
        return $query;
    }
    function get_auth_mobile($option) {
        $this->db->where('userid', $option['userid']);
        $this->db->where('state', '0');
        $this->db->where('type', $option['type']);
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get('auth_numbers')->row();
        return $query;
    }
    function add_auth_mobile($option) {
        // $data = array(
        //     'USER_ID' => 'A',
        //     'SMS_MSG' => "인증번호 [{$option['auth_no']}] ".BRAND.' 에서 보낸 인증번호입니다.',
        //     'NOW_DATE' => date('YmdHis'),
        //     'SEND_DATE' => date('YmdHis'),
        //     'CALLBACK' => str_replace('-', '', CALLCENTER),
        //     'DEST_COUNT' => '1',
        //     'DEST_INFO' => 'a^'.$option['mobile'],
        //     'RESERVED1' => time(),
        //     'RESERVED5' => 'Y'
        // );
        // $this->db->insert('SDK_SMS_SEND', $data);

        // $data = array(
        //     'TR_SENDDATE' => $this->current_time,
        //     'TR_SENDSTAT' => '0',
        //     'TR_MSGTYPE' => '0',
        //     'TR_PHONE' => $option['mobile'],
        //     'TR_CALLBACK' => str_replace('-', '', CALLCENTER),
        //     'TR_MSG'      => "인증번호 [{$option['auth_no']}] ".BRAND.' 에서 보낸 인증번호입니다.',
        //     'TR_ETC5' => 'Y',
        // );
        // $this->db->insert('SC_TRAN_MAIN', $data);

        // $data = array(
        //     'kind' => '0',
        //     'callbackNo' => str_replace('-', '', CALLCENTER),
        //     'receiveNo' => $option['mobile'],
        //     'subject' => '',
        //     'message' => "인증번호 [{$option['auth_no']}] ".BRAND.' 에서 보낸 인증번호입니다.',
        //     'reqdate' => $this->current_time,
        //     'registTime' => $this->current_time,
        //     'etc4' => 'Y'
        // );
        // $this->db->insert('sowshot_que', $data);

        $data = array(
            'userid'    => $option['userid'],
            'ip'        => $this->input->ip_address(),
            'memo'      => $option['memo'],
            'mobile'    => $option['mobile'],
            'auth_no'   => $option['auth_no'],
            'type'      => $option['type'],
            'add_date'  => $this->current_time
        );
        $this->db->insert('auth_numbers', $data);
        $query = $this->db->insert_id();
        if (!$query) {
            return false;
        }
        return true;

    }
    function add_auth_ars($option) {
        $data = array(
            'USER_ID' => 'A',
            'MSG_SUBTYPE' => '30',
            'NOW_DATE' => date('YmdHis'),
            'SEND_DATE' => date('YmdHis'),
            'CALLBACK' => str_replace('-', '', CALLCENTER),
            'TTS_MSG' => "발신번호 등록서비스 입니다. 인증번호 {$option['auth_no']} 번을 입력해 주세요. 인증번호 {$option['auth_no']} 번을 입력해 주세요.",
            'DEST_COUNT' => '1',
            'DEST_INFO' => 'a^'.$option['mobile'],
            'RESERVED1' => time(),
            'RESERVED5' => 'Y'
        );
        $this->db->insert('SDK_VMS_SEND', $data);

        $data = array(
            'userid'    => $option['userid'],
            'ip'        => $this->input->ip_address(),
            'memo'      => $option['memo'],
            'mobile'    => $option['mobile'],
            'auth_no'   => $option['auth_no'],
            'type'      => $option['type'],
            'add_date'  => $this->current_time
        );
        $this->db->insert('auth_numbers', $data);
        $query = $this->db->insert_id();
        if (!$query) {
            return false;
        }
        return true;

    }
    function modify_auth_mobile($option) {
        $data = array(
            'state' => '1'
        );
        $this->db->where('userid', $this->session->userdata('userid'));
        $this->db->where('ano', $option['ano']);
        $query = $this->db->update('auth_numbers', $data);
        return $query;
    }
    function get_user_by_userno() {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'state' => '0'
        );
        $query = $this->db->get_where('users', $data)->row();
        return $query;
    }
    function add_user($option,$option1,$option2) {
        $this->db->trans_begin();
        $data = array(
            'storeno'   => $option['storeno'],
            'groupno'   => $option['groupno'],
            'user_type' => $option['user_type'],
            'userid'    => $option['userid'],
            'realname'  => $option['realname'],
            'mobile'    => $option['mobile'],
            'phone'     => $option['phone'],
            'email'     => $option['email'],
            'password'  => $option['password'],
            'com_name'  => $option['com_name'],
            'com_number'=> $option['com_number'],
            'sms1'      => $option['sms1'],
            'sms2'      => $option['sms2'],
            'lms1'      => $option['lms1'],
            'lms2'      => $option['lms2'],
            'mms1'      => $option['mms1'],
            'mms2'      => $option['mms2'],
            'kat'       => $option['kat'],
            'kft'       => $option['kft'],
            'kftm'      => $option['kftm'],
            'ch_sms'    => $option['ch_sms'],
            'ch_lms'    => $option['ch_lms'],
            'ch_mms'    => $option['ch_mms'],
            'ch_kko'    => $option['ch_kko'],
            'register_ip' => $option['register_ip'],
            'add_date'  => $this->current_time
        );
        $this->db->insert('users', $data);
        $userno = $this->db->insert_id();
        if (!$userno) {
            $this->db->trans_rollback();
            return false;
        }

        $cb_phone = trim($option1['callback']);
        $data = array(
            'callback' => $cb_phone
        );
        $callback = $this->db->get_where('callback', $data)->row();
        if (!$callback->xid) {
            $data = array(
                'userno'    => $userno,
                'storeno'   => $option['storeno'],
                'userid'    => $option1['userid'],
                'name'      => $option1['name'],
                'callback'  => $cb_phone,
                'authcode'  => $option1['authcode'],
                'cert_type' => $option1['cert_type'],
                'status'    => $option1['status'],
                'reg_time'  => $this->current_time,
            );
            $this->db->insert('callback', $data);
            $result = $this->db->insert_id();
            if (!$result) {
                $this->db->trans_rollback();
                return false;
            }
        }

        $data = array(
            'userid' => $option1['userid']
        );
        $this->db->where('kno', $option2['kmc_kno']);
        $result = $this->db->update($option2['kmc_table'], $data);
        if (!$result) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();
        //check if transaction status TRUE or FALSE
        if ($this->db->trans_status() === false) {
            //if something went wrong, rollback everything
            $this->db->trans_rollback();
            return false;
        } else {
            //if everything went right, commit the data to the database
            $this->db->trans_commit();
            return true;
        }
    }
    function add_user_by_admin($option,$option1) {
        $this->db->trans_begin();
        $data = array(
            'storeno'   => $option['storeno'],
            'groupno'   => $option['groupno'],
            'userid'    => $option['userid'],
            'realname'  => $option['realname'],
            'mobile'    => $option['mobile'],
            'email'     => $option['email'],
            'password'  => $option['password'],
            'sms1'      => $option['sms1'],
            'sms2'      => $option['sms2'],
            'lms1'      => $option['lms1'],
            'lms2'      => $option['lms2'],
            'mms1'      => $option['mms1'],
            'mms2'      => $option['mms2'],
            'kat'       => $option['kat'],
            'kft'       => $option['kft'],
            'kftm'      => $option['kftm'],
            'ch_sms'    => $option['ch_sms'],
            'ch_lms'    => $option['ch_lms'],
            'ch_mms'    => $option['ch_mms'],
            'ch_kko'    => $option['ch_kko'],
            'add_date'  => $this->current_time
        );
        $this->db->insert('users', $data);
        $userno = $this->db->insert_id();
        if (!$userno) {
            $this->db->trans_rollback();
            return false;
        }
        if ($option['mobile'] != '') {
            $cb_phone = trim($option1['callback']);
            $data = array(
                'callback' => $cb_phone
            );
            $callback = $this->db->get_where('callback', $data)->row();
            if (!$callback->xid) {
                $data = array(
                    'userno'    => $userno,
                    'storeno'   => $option['storeno'],
                    'userid'    => $option1['userid'],
                    'name'      => $option1['name'],
                    'callback'  => $cb_phone,
                    'authcode'  => $option1['authcode'],
                    'cert_type' => $option1['cert_type'],
                    'status'    => $option1['status'],
                    'reg_time'  => $this->current_time,
                );
                $this->db->insert('callback', $data);
                $result = $this->db->insert_id();
                if (!$result) {
                    $this->db->trans_rollback();
                    return false;
                }
            }
        }

        $this->db->trans_complete();
        //check if transaction status TRUE or FALSE
        if ($this->db->trans_status() === false) {
            //if something went wrong, rollback everything
            $this->db->trans_rollback();
            return false;
        } else {
            //if everything went right, commit the data to the database
            $this->db->trans_commit();
            return true;
        }
    }
    function record_password_history($option) {
        $data = array(
            'userno' => $option['userno'],
            'userid' => $option['userid'],
            'password' => $option['password'],
            'ip' => $option['ip'],
            'type' => $option['type'],
            'add_date' => $this->current_time
        );
        $this->db->insert('password_history', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function modify_user_by_admin($option,$option1) {
        $this->db->where_in('userno', $option1);
        $query = $this->db->update('users', $option);
        return $query;
    }
    function modify_user_by_query_by_admin($option,$option1) {
        $userno_str = implode(',', $option1);
        $sql = "
            UPDATE users
            SET remove_date= CASE WHEN remove_date = '0000-00-00 00:00:00' THEN NOW() ELSE remove_date END,
                state = '{$option['state']}'
            WHERE userno IN ($userno_str)
        ";
        $query = $this->db->query($sql);
        return $query;
    }
    function modify_user($data) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $query = $this->db->update('users', $data);
        return $query;
    }
    function modify_password($data) {
        $this->db->where('userid', $data['userid']);
        $query = $this->db->update('users', $data);
        return $query;
    }
    function withdrawal_user() {
        //state : 1:차단 2:탈퇴
        $data = array(
            'state'         => '2',
            'remove_date'   => $this->current_time
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $query = $this->db->update('users', $data);
        return $query;
    }
    function delete_callback_selected($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('status', '1');
        $this->db->where_in('xid', $option);
        $query = $this->db->delete('callback');
        return $query;
    }
    function add_kmcis($option) {
        $data = array(
            'storename' => $option['storename'],
            'encmsg' => $option['encmsg'],
            'certnum' => $option['certnum'],
            'curdate' => $option['curdate'],
            'ci' => $option['ci'],
            'di' => $option['di'],
            'phoneno' => $option['phoneno'],
            'phonecorp' => $option['phonecorp'],
            'birthday' => $option['birthday'],
            'nation'=> $option['nation'],
            'gender' => $option['gender'],
            'name' => $option['name'],
            'result' => $option['result'],
            'certmet' => $option['certmet'],
            'ip' => $option['ip'],
            'm_name' => $option['m_name'],
            'm_birthday' => $option['m_birthday'],
            'm_gender' => $option['m_gender'],
            'm_nation' => $option['m_nation'],
            'plusinfo' => $option['plusinfo'],
            'add_date' => $this->current_time
        );
        $this->db->insert('kmcis_logs', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_kmcis($option) {
        $data = array(
            'storename' => $option['storename'],
            'ci' => $option['ci'],
        );
        $this->db->select('kno, userid');
        $query = $this->db->get_where('kmcis_logs', $data)->row();
        return $query;
    }
    function add_kcp($option) {
        $data = array(
            'storename' => $option['storename'],
            'phone_no' => $option['phone_no'],
            'comm_id' => $option['comm_id'],
            'user_name' => $option['user_name'],
            'birth_day' => $option['birth_day'],
            'sex_code' => $option['sex_code'],
            'local_code' => $option['local_code'],
            'ci' => $option['ci_url'],
            'di' => $option['di_url'],
            'web_siteid'=> $option['web_siteid'],
            'add_date' => $this->current_time
        );
        $this->db->insert('kcp_logs', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_kcp($option) {
        $data = array(
            'storename' => $option['storename'],
            'ci' => $option['ci'],
        );
        $this->db->select('kno, userid');
        $query = $this->db->get_where('kcp_logs', $data)->row();
        return $query;
    }
    function get_kcp_number($option) {
        $data = array(
            'kno' => $option['kmc_kno']
        );
        $this->db->select('kno, userid');
        $query = $this->db->get_where($option['kmc_table'], $data)->row();
        return $query;
    }
    function get_kcp_available_count($option) {
        $this->db->from('kcp_logs');
        $this->db->where('ci', $option['ci']);
        $this->db->where('userid !=', '');
        $query = $this->db->count_all_results();
        return $query;
    }
    function modify_callback_by_admin($option,$option1) {
        $this->db->where_in('xid', $option1);
        $query = $this->db->update('callback', $option);
        return $query;
    }
    function get_admin_balance() {
        $this->db->select('a.*, b.storename, b.restrict_sending, b.check_balance');
        $this->db->where('a.storeno', $this->session->userdata('storeno'));
        $this->db->where('b.check_balance', '1');
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $query = $this->db->get('store_balance a')->row();
        return $query;
    }
    function get_admin_deposit_count($option) {
        $this->db->from('store_billing');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_admin_deposit_limit($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.sno asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('store_billing a')->result();
        return $query;
    }
    function add_admin_deposit($option) {
        $data = array(
            'storeno' => $option['storeno'],
            'amount' => $option['amount'],
            'memo' => $option['memo'],
        );

        $this->db->insert('store_billing', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_admin_balance_count($option) {
        $this->db->from('store_balance');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_admin_balance_limit($option) {
        $this->db->select('a.*, b.storename, b.restrict_sending');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.sno asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('store_balance a')->result();
        return $query;
    }
    function delete_callback_by_admin($option) {
        $xid_val = implode(',', $option);
        if ($xid_val == '') return false;

        $this->db->trans_begin();
        $sql = "
            INSERT INTO callback_deleted
            SELECT *
            FROM callback
            WHERE xid IN ({$xid_val})
        ";
        $query = $this->db->query($sql);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->where_in('xid', $option);
        $query = $this->db->delete('callback');
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();
        //check if transaction status TRUE or FALSE
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }
    function get_auth_userinfo_by_agent($option) {
        if (!defined('KMCIS_CPID') && !defined('KCP_SITE_CD')) return false;

        $this->db->where('userid', $option['userid']);
        if (defined('KMCIS_CPID')) {
            $this->db->select('\'KMCIS\' auth_agent, birthday auth_birthday, phoneno auth_phoneno');
            $this->db->where('userid', $option['userid']);
            $query = $this->db->get('kmcis_logs')->row_array();
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('\'KCP\' auth_agent, birth_day auth_birthday, phone_no auth_phoneno');
            $this->db->where('userid', $option['userid']);
            $query = $this->db->get('kcp_logs')->row_array();
        }
        return $query;
    }
    function get_userid_by_userno_for_session($option) {
        $this->db->select('userid,storeno,groupno');
        $this->db->where_in('userno', $option);
        $query = $this->db->get('users')->result();
        return $query;
    }
    function get_sent_count_by_callback_xid($xid) {
        $this->db->where('xid', $xid);
        $cb = $this->db->get('callback')->row();

        $this->db->select('total_units cnt');
        $this->db->where('callback', $cb->callback);
        $cp = $this->db->get('sow_processunit_msg')->row();

        $sent_array = array(
            'callback' => $cb->callback,
            'sent_cnt' => $cp->cnt,
        );
        return $sent_array;
    }
    function get_groupno_by_userno($option) {
        $this->db->select('groupno');
        $this->db->where('userno', $option['userno']);
        $query = $this->db->get('users')->row();
        return $query;
    }
    function get_channel_list_by_admin($option) {
        $this->db->where('type', $option['type']);
        if ($option['status']) $this->db->where('status', $option['status']);
        $query = $this->db->get('channel')->result();
        return $query;
    }
    function modify_channel_all_by_admin($option) {
        $data = array();
        if ($option['channel_type'] == 'sms') {
            $data['ch_sms'] = $option['channel_target'];
        } else if ($option['channel_type'] == 'lms') {
            $data['ch_lms'] = $option['channel_target'];
        } else if ($option['channel_type'] == 'mms') {
            $data['ch_mms'] = $option['channel_target'];
        } else {
            return false;
        }

        if ($option['channel_type'] == 'sms') {
            $this->db->where('ch_sms', $option['channel_dest']);
        } else if ($option['channel_type'] == 'lms') {
            $this->db->where('ch_lms', $option['channel_dest']);
        } else if ($option['channel_type'] == 'mms') {
            $this->db->where('ch_mms', $option['channel_dest']);
        }
        $this->db->where_in('state', array('0','3'));
        $query = $this->db->update('users', $data);
        return $query;
    }
    function get_users_cash_by_admin($option) {
        $this->db->select('SUM(cash) cash');
        if ($option['ch_sms']) $this->db->where('ch_sms', $option['ch_sms']);
        $this->db->where_in('state', array('0','3'));
        $query = $this->db->get('users')->row();
        return $query->cash;
    }
    function get_black_list() {
        $this->db->select('mobile');
        $query = $this->db->get('black_list')->result();
        return $query;
    }
    function add_callback_files($option) {
        $data = array(
            'storeno' => $option['storeno'],
            'userno' => $option['userno'],
            'userid' => $option['userid'],
            'file_path' => $option['file_path'],
            'image_path' => $option['image_path'],
        );
        $this->db->insert('callback_files', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_callback_files_count_by_admin($option) {
        $this->db->from('callback_files');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where($option['sfl'], $option['val']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_files_limit_by_admin($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('a.'.$option['sfl'], $option['val']);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.xid desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('callback_files a')->result();
        return $query;
    }
    function modify_callback_files_by_admin($option) {
        $data = array(
            'memo' => $option['memo']
        );
        $this->db->where('xid', $option['xid']);
        $this->db->where('userno', $this->session->userdata('userno'));
        $query = $this->db->update('callback_files', $data);
        return $query;
    }
    function get_callback_user_count_by_admin($option) {
        $this->db->from('callback');
        // $this->db->where('storeno', $option['storeno']);
        $this->db->where('userno', $option['userno']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function add_ban_list_by_admin($option) {
        $data = array(
            'userno' => $option['userno'],
            'type' => $option['type'],
            'ban' => $option['ban'],
        );
        $this->db->insert('ban_list', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_ban_list_by_admin($option) {
        $this->db->order_by('bno', 'asc');
        $this->db->where('userno', $option['userno']);
        $query = $this->db->get('ban_list')->result();
        return $query;
    }
    function modify_allow_login_by_admin($option) {
        $data = array(
            'level' => 1
        );
        $this->db->where('userno', $option['userno']);
        $this->db->where('level', '0');
        $query = $this->db->update('users', $data);
        return $query;
    }
    function get_filter_word() {
        $this->db->select('word');
        $query = $this->db->get('filter')->row_array();
        return $query;
    }
    function add_nice($option) {
        $data = array(
            'storename' => $option['storename'],
            'req_seq' => $option['req_seq'],
            'auth_type' => $option['auth_type'],
            'res_seq' => $option['res_seq'],
            'user_name' => $option['user_name'],
            'birthdate' => $option['birthdate'],
            'gender' => $option['gender'],
            'nainfo' => $option['nainfo'],
            'ci' => $option['ci'],
            'di' => $option['di'],
            'mobile_no'=> $option['mobile_no'],
            'mobile_co'=> $option['mobile_co'],
            'add_date' => $this->current_time
        );
// error_log(print_r($data,1),0);
        $this->db->insert('nice_logs', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_nice($option) {
        $data = array(
            'storename' => $option['storename'],
            'di' => $option['di'],
        );
        $this->db->select('kno, userid');
        $query = $this->db->get_where('nice_logs', $data)->row();
        return $query;
    }
    function get_auth_mobile_for_admin_auth($option) {
        $this->db->where('userid', $option['userid']);
        $this->db->where('mobile', $option['mobile']);
        $this->db->where('state', '0');
        $this->db->where('type', $option['type']);
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get('auth_numbers')->row();
        return $query;
    }
    function get_user_birth_excel() {
        if (defined('KMCIS_CPID')) {
            $this->db->select('a.userid, a.realname, a.mobile, a.login_date, a.add_date, a.state, d.birthday birth_day');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.userid, a.realname, a.mobile, a.login_date, a.add_date, a.state, d.birth_day');
        } else {
            $this->db->select('a.userid, a.realname, a.mobile, a.login_date, a.add_date, a.state, d.birthdate birth_day');
        }
        if ($this->session->userdata('level') != '9') {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') {
                if ((int)$this->session->userdata('groupno') == 0) $this->db->where('a.groupno', '99999');
                else $this->db->where('a.groupno', $this->session->userdata('groupno'));
            }
        }

        // $this->db->where_in('a.state', $option['state']);
        $this->db->where('a.level !=', '9');
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs d', 'a.userid = d.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs d', 'a.userid = d.userid', 'left');
        } else {
            $this->db->join('nice_logs d', 'a.userid = d.userid', 'left');
        }
        $this->db->order_by('a.userno', 'desc');
        $query = $this->db->get('users a')->result();
        return $query;
    }
    function get_auth_mobile_authed($option) {
        $this->db->where('userid', $option['userid']);
        $this->db->where('state', '1');
        $this->db->where('type', $option['type']);
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get('auth_numbers')->row();
        return $query;
    }
    function modify_auth_mobile_authed($option) {
        $data = array(
            'state' => $option['state']
        );
        $this->db->where('userid', $option['userid']);
        $this->db->where('ano', $option['ano']);
        $this->db->where('type', $option['type']);
        $query = $this->db->update('auth_numbers', $data);
        return $query;
    }
    function add_users_history($option) {
        $data = array(
            'userid' => $option['userid'],
            'memo' => $option['memo'],
            'ip' => $option['ip']
        );
        $this->db->insert('users_history', $data);
        $query = $this->db->insert_id();
        return $query;
    }
}
