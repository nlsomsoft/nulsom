<?php
class AddressModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
    }

    function get_search_ban_count($option) {
        $this->db->from('address_ban');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('state', '0');
        $this->db->where('MATCH (name,mobile,mobile1,mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_search_ban_limit($option) {
        // $data = array(
        //     $this->session->userdata('userno'),
        //     "+{$option['val']}*",
        //     $option['offset'],
        //     $option['limit']
        // );
        // $sql = "
        //     SELECT *
        //     FROM address_ban
        //     WHERE userno = ?
        //         AND state = '0'
        //         AND MATCH(NAME,mobile,mobile1,mobile2) AGAINST(? IN BOOLEAN MODE)
        //     ORDER BY abno DESC
        //     LIMIT ?, ?
        // ";
        // $query = $this->db->query($sql, $data)->result();
        // return $query;
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('state', '0');
        $this->db->where('MATCH (name,mobile,mobile1,mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->order_by('abno', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('address_ban')->result();
        return $query;
    }
    function get_search_groups($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'state'=> '0'
        );
        $query = $this->db->get_where('address_group', $data)->result();

        $group_info = array();
        foreach ($query as $key => $row) {
            $group_info[$key]['gno'] = $row->gno;
            $group_info[$key]['name'] = $row->name;

            $this->db->from($this->session->userdata('address_tname'));
            $this->db->where('userno', $this->session->userdata('userno'));
            $this->db->where('gno', $row->gno);
            $this->db->where('state', '0');
            $this->db->where('MATCH (name,mobile,mobile1,mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
            $group_info[$key]['total_cnt'] = (int)$this->db->count_all_results();
        }
        return $group_info;
    }
    function get_search_list_count($option) {
        $this->db->from("{$this->session->userdata('address_tname')} a");
        $this->db->where('a.userno', $this->session->userdata('userno'));
        if ((int)$option['gno'] > 0) $this->db->where('a.gno', $option['gno']);
        $this->db->where('a.state', '0');
        $this->db->where('b.state', '0');
        if ($option['val']) $this->db->where('MATCH (a.name,a.mobile,a.mobile1,a.mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_search_list_limit($option) {
        $this->db->select('a.*, b.name group_name');
        $this->db->where('a.userno', $this->session->userdata('userno'));
        $this->db->where('a.state', '0');
        $this->db->where('b.state', '0');
        if ((int)$option['gno'] > 0) $this->db->where('a.gno', $option['gno']);
        if ($option['val']) $this->db->where('MATCH (a.name,a.mobile,a.mobile1,a.mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $this->db->order_by('a.ano', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get("{$this->session->userdata('address_tname')} a")->result();
        return $query;
    }
    function get_search_list_excel($option) {
        $this->db->select('a.mobile,a.name,b.name group_name,a.add_date');
        $this->db->where('a.userno', $this->session->userdata('userno'));
        $this->db->where('a.state', '0');
        if ((int)$option['gno'] > 0) $this->db->where('a.gno', $option['gno']);
        if ($option['val']) {
            $this->db->where('MATCH (a.name,a.mobile,a.mobile1,a.mobile2) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        }
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $this->db->order_by('a.ano', 'desc');
        $query = $this->db->get("{$this->session->userdata('address_tname')} a")->result();
        return $query;
    }
    function get_ban_count() {
        $this->db->from('address_ban');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('state', '0');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_ban_limit($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('state', '0');
        $this->db->order_by('abno', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('address_ban')->result();
        return $query;
    }
    function get_phone_080_count($option) {
        $this->db->from('phone_080');
        $this->db->where('phone_080', $this->session->userdata('phone_080'));
        if ($this->session->userdata('phone_ext') != '') $this->db->where('ext', $this->session->userdata('phone_ext'));
        if ($option['val'] != '') $this->db->where('MATCH (mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->where('state !=', '2');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_phone_080_limit($option) {
        $this->db->where('phone_080', $this->session->userdata('phone_080'));
        if ($this->session->userdata('phone_ext') != '') $this->db->where('ext', $this->session->userdata('phone_ext'));
        if ($option['val'] != '') $this->db->where('MATCH (mobile) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->where('state !=', '2');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('phone_080')->result();
        return $query;
    }
    function get_phone_080_count_by_admin($option) {
        $this->db->from('phone_080');
        if ((int)$this->session->userdata('level') == 3) {
            $this->db->where('storeno', $this->session->userdata('storeno'));
            $this->db->where('groupno', $this->session->userdata('groupno'));
        }
        if ($option['val'] != '') $this->db->where('MATCH (mobile,userid) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        // $this->db->where('state !=', '2');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_phone_080_limit_by_admin($option) {
        $this->db->select('a.*, b.storename, c.groupid');
        if ((int)$this->session->userdata('level') == 3) {
            $this->db->where('a.storeno', $this->session->userdata('storeno'));
            $this->db->where('a.groupno', $this->session->userdata('groupno'));
        }
        if ($option['val'] != '') $this->db->where('MATCH (a.mobile,a.userid) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        // $this->db->where('state !=', '2');
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->order_by('a.xid', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('phone_080 a')->result();
        return $query;
    }
    function get_phone_080_list() {
        $this->db->where('phone_080', $this->session->userdata('phone_080'));
        if ($this->session->userdata('phone_ext') != '') $this->db->where('ext', $this->session->userdata('phone_ext'));
        $this->db->where('state !=', '2');
        $query = $this->db->get('phone_080')->result();
        return $query;
    }
    function get_phone_080() {
        $this->db->select('mobile');
        $this->db->where('phone_080', $this->session->userdata('phone_080'));
        if ($this->session->userdata('phone_ext') != '') $this->db->where('ext', $this->session->userdata('phone_ext'));
        $this->db->where('state !=', '2');
        $query = $this->db->get('phone_080')->result();
        return $query;
    }
    function modify_phone_080($option) {
        $data = array(
            'state' => '2',
            'remove_date' => $this->current_time
        );
        $this->db->where_in('xid', $option);
        $query = $this->db->update('phone_080', $data);
        return $query;
    }
    function add_phone_080($option) {
        $data = array(
            'mobile' => $option['mobile'],
            'phone_080' => $option['phone_080'],
            'storeno' => $option['storeno'],
            'state' => $option['state'],
            'adm_flag' => '1',
            'reg_time' => $option['reg_time']
        );
        $this->db->insert('phone_080', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_bans() {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'state'=> '0'
        );
        $this->db->select('mobile,name,add_date');
        $query = $this->db->get_where('address_ban', $data)->result();
        return $query;
    }
    function get_person_mobile($option) {
        $this->db->select('gno, mobile, name');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('ano', $option);
        $this->db->where('state', '0');
        $this->db->order_by('gno', 'asc');
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get("{$this->session->userdata('address_tname')}")->result();
        return $query;
    }
    function get_person_mobile_join_groupname($option) {
        $this->db->select('a.gno, a.mobile, a.name, b.name group_name');
        $this->db->where('a.userno', $this->session->userdata('userno'));
        $this->db->where_in('a.ano', $option);
        $this->db->where('a.state', '0');
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $this->db->order_by('a.gno', 'asc');
        $this->db->order_by('a.ano', 'desc');
        $query = $this->db->get("{$this->session->userdata('address_tname')} a")->result();
        return $query;
    }
    function get_groups_mobile($option) {
        if (!$option['gno']) return false;
        $this->db->select('mobile, name');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('gno', $option['gno']);
        $this->db->where('state', '0');
        $this->db->order_by('ano', 'desc');
        $query = $this->db->get("{$this->session->userdata('address_tname')}")->result();
        return $query;
    }
    function get_groups_mobile_join_groupname($option) {
        if (!$option['gno']) return false;
        $this->db->select('a.mobile, a.name, b.name group_name');
        $this->db->where('a.userno', $this->session->userdata('userno'));
        $this->db->where('a.gno', $option['gno']);
        $this->db->where('a.state', '0');
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $this->db->order_by('a.ano', 'desc');
        $query = $this->db->get("{$this->session->userdata('address_tname')} a")->result();
        return $query;
    }
    function get_groups() {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'state'=> '0'
        );
        $query = $this->db->get_where('address_group', $data)->result_array();
        return $query;
    }
    function is_groups($gno) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'gno' => (int)$gno,
            'state'=> '0'
        );
        $this->db->select('gno');
        $query = $this->db->get_where('address_group', $data)->row();
        return $query->gno;
    }
    function get_list_count($option) {
        $this->db->from($this->session->userdata('address_tname'));
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('gno', $option['gno']);
        $this->db->where('state', '0');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_list_limit($option) {
        $this->db->select('a.*, b.name group_name');
        $this->db->where('a.userno', $this->session->userdata('userno'));
        $this->db->where('a.gno', $option['gno']);
        $this->db->where('a.state', '0');
        $this->db->join('address_group b', 'a.gno = b.gno', 'left');
        $this->db->order_by('a.ano', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get("{$this->session->userdata('address_tname')} a")->result();
        return $query;
    }

    function get_addr($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'ano' => $option['ano'],
            'state'=> '0'
        );
        $query = $this->db->get_where($this->session->userdata('address_tname'), $data)->row();
        return $query;
    }
    function add_group($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'name' => $option['name'],
            'add_date' => $this->current_time
        );
        $this->db->insert('address_group', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function delete_ban_selected($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('abno', $option);
        $query = $this->db->delete('address_ban');
        return $query;
    }
    function add_ban_bulk($option) {
        $query = $this->db->insert_batch('address_ban', $option);
        return $query;
    }
    function add_address_bulk($option1,$option2) {
        $data = array(
            $option1['mobile_cnt'],
            $option1['phone_cnt'],
            $option1['fax_cnt'],
            $option1['email_cnt'],
            $option1['total_cnt'],
            $this->session->userdata('userno'),
            $option1['gno']
        );
        $sql = "
            UPDATE address_group
            SET mobile_cnt = mobile_cnt + ?,
                phone_cnt = phone_cnt + ?,
                fax_cnt = fax_cnt + ?,
                email_cnt = email_cnt + ?,
                total_cnt = total_cnt + ?
            WHERE userno = ? AND gno = ? AND state = '0'
        ";
        $this->db->trans_begin();
        $query = $this->db->query($sql, $data);
        $query = $this->db->insert_batch($this->session->userdata('address_tname'), $option2);
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
    function change_group_name($option) {
        $data = array(
            'name' => $option['name']
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('gno', $option['gno']);
        $query = $this->db->update('address_group', $data);
        return $query;
    }
    function delete_group_selected($option) {
        $data = array(
            'state' => '1',
            'remove_date' => $this->current_time
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('gno', $option);
        $query = $this->db->update('address_group', $data);
        return $query;
    }

    function change_list_info($option) {
        $data = array(
            $option['column'] => $option['value']
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('ano', $option['ano']);
        $query = $this->db->update($this->session->userdata('address_tname'), $data);
        return $query;
    }
    function delete_list_selected($option1,$optoin2) {
        $data = array(
            'state' => '1',
            'remove_date' => $this->current_time
        );
        $this->db->trans_begin();
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('ano', $option1);
        $this->db->update($this->session->userdata('address_tname'), $data);
        foreach($optoin2 as $gno) {
            $this->db->from($this->session->userdata('address_tname'));
            $this->db->where('userno', $this->session->userdata('userno'));
            $this->db->where('state', '0');
            $this->db->where('gno', $gno);
            $this->db->where("mobile <> ''", NULL);
            $mobile_cnt = (int)$this->db->count_all_results();

            $this->db->from($this->session->userdata('address_tname'));
            $this->db->where('userno', $this->session->userdata('userno'));
            $this->db->where('state', '0');
            $this->db->where('gno', $gno);
            $total_cnt = (int)$this->db->count_all_results();

            $data = array(
                'mobile_cnt' => $mobile_cnt,
                'total_cnt' => $total_cnt
            );
            $this->db->where('userno', $this->session->userdata('userno'));
            $this->db->where('state', '0');
            $this->db->where('gno', $gno);
            $this->db->update('address_group', $data);
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
}
