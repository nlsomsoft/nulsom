<?php
class SettingModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function get_admin_notice($option) {
        $this->db->where('type', $option['type']);
        $query = $this->db->get('admin_notice')->row();
        return $query;
    }
    function modify_admin_notice($option) {
        $data = array(
            'mobile' => $option['mobile']
        );
        $this->db->where('type', $option['type']);
        $query = $this->db->update('admin_notice', $data);
        return $query;
    }
    function add_admin_notice($option) {
        $data = array(
            'mobile' => $option['mobile'],
            'type' => $option['type'],
        );
        $query = $this->db->insert('admin_notice', $data);
        return $query;
    }
    function delete_admin_notice($option) {
        $this->db->where('type', $option['type']);
        $query = $this->db->delete('admin_notice');
        return $query;
    }
    function get_admin_monitor_list() {
        $this->db->order_by('num', 'asc');
        $query = $this->db->get('admin_monitor')->result();
        return $query;
    }
    function get_admin_monitor_count($option) {
        $this->db->from("{$option['tname']}");
        if ($option['where_key'] && $option['where_val']) {
            $this->db->where("{$option['where_key']}", "{$option['where_val']}");
        }
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_callback_info($option) {
        $this->db->where('callback', $option['callback']);
        $this->db->where_in('status', array('2','3'));
        $query = $this->db->get('callback')->result();
        return $query;
    }
    function get_user_callback_count($option) {
        $this->db->from('callback');
        $this->db->where('userno', $option['userno']);
        $this->db->where_in('status', array('2','3'));
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_user_info($option) {
        if (!defined('KMCIS_CPID') && !defined('KCP_SITE_CD')) return false;

        if (defined('KMCIS_CPID')) {
            $this->db->select('a.*, b.phoneno auth_phoneno, b.name auth_username');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->select('a.*, b.phone_no auth_phoneno, b.user_name auth_username');
        }
        $this->db->where('a.userno', $option['userno']);
        if (defined('KMCIS_CPID')) {
            $this->db->join('kmcis_logs b', 'a.userid = b.userid', 'left');
        } else if (defined('KCP_SITE_CD')) {
            $this->db->join('kcp_logs b', 'a.userid = b.userid', 'left');
        }
        $query = $this->db->get('users a')->row();
        return $query;
    }
    function get_user_callback_sum_count($option) {
        $this->db->select('SUM(success) total_sum');
        $this->db->where('userno', $option['userno']);
        $this->db->where('callback', $option['callback']);
        $query = $this->db->get('sow_processunit_msg')->row();
        return $query;
    }
    function get_table_status() {
        // $sql = "SHOW TABLE STATUS FROM `baosms`";
        // $query = $this->db->query($sql);
        // return $query;
        $result = $this->db->query('SHOW TABLE STATUS FROM `'.DATABASE_NAME.'` WHERE ENGINE IS NOT NULL');
        $tables = $result->result_array();
        return $tables;
    }
    function get_noticebbs_count($option) {
        $this->db->from('notice');
        if ($option['val'] != '') $this->db->where('MATCH (subject,body) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        if ($option['del_flag'] != '') $this->db->where('del_flag', $option['del_flag']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_noticebbs_limit($option) {
        if ($option['val'] != '') $this->db->where('MATCH (subject,body) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        if ($option['del_flag'] != '') $this->db->where('del_flag', $option['del_flag']);
        $this->db->order_by('xid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('notice')->result();
        return $query;
    }
    function get_all_noticebbs_by_admin() {
        $query = $this->db->get('notice')->result();
        return $query;
    }
    function get_noticebbs_by_admin($option) {
        $this->db->where('xid', $option['xid']);
        $query = $this->db->get('notice')->row_array();
        return $query;
    }
    function get_notice_noticebbs() {
        $this->db->where('status', '1');
        $query = $this->db->get('notice')->row_array();
        return $query;
    }
    function modify_noticebbs_by_admin($option) {
        $data = array(
            'subject' => $option['subject'],
            'body' => $option['body'],
        );
        $this->db->where('xid', $option['xid']);
        $query = $this->db->update('notice', $data);
        return $query;
    }
    function add_noticebbs_by_admin($option) {
        $data = array(
            'userid' => $option['userid'],
            'subject' => $option['subject'],
            'body' => $option['body'],
        );
        $this->db->insert('notice', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function modify_notice_noticebbs_by_admin($option) {
        $this->db->trans_begin();
        $data = array(
            'status' => '0',
        );
        $this->db->where('status', '1');
        $query = $this->db->update('notice', $data);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $data = array(
            'status' => '1',
        );
        $this->db->where('xid', $option['xid']);
        $query = $this->db->update('notice', $data);
        if (!$query) {
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
    function modify_delete_noticebbs_by_admin($option,$option1) {
        $data = array(
            'remove_date' => $option['remove_date'],
            'del_flag' => $option['del_flag'],
            'status' => '0'
        );
        $this->db->where_in('xid', $option1);
        $query = $this->db->update('notice', $data);
        return $query;
    }
    function modify_status_noticebbs_by_admin($option) {
        $data = array(
            'status' => $option['status'],
        );
        $this->db->where('xid', $option['xid']);
        $query = $this->db->update('notice', $data);
        return $query;
    }
    function get_admin_auth_num_list($option) {
// error_log($option['limit_date'], 0);
        $this->db->where('add_date >=', $option['limit_date']);
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get('auth_numbers')->result();
        return $query;
    }
}
