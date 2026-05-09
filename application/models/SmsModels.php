<?php
class SmsModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function save_msg($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'userid' => $this->session->userdata('userid'),
            'subject' => $option['subject'],
            'msg' => $option['msg'],
            'send_type' => $option['send_type'],
            'bytes' => $option['bytes'],
            'photo_image' => ($option['photo_image'] != '' ? $option['photo_image'] : ''),
            'add_date'  => $this->current_time
        );
        $this->db->insert('saved_messages', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_saved_msg_count($option) {
        $this->db->from('saved_messages');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('send_type', $option['send_type']);
        $this->db->where('state', '0');
        if ($option['val']) $this->db->where('MATCH (subject,msg) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_saved_msg_limit($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('send_type', $option['send_type']);
        $this->db->where('state', '0');
        if ($option['val']) $this->db->where('MATCH (subject,msg) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->order_by('smno', 'desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('saved_messages')->result();
        return $query;
    }
    function delete_saved_message($option) {
        $data = array(
            'state' => '1'
        );
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('smno', $option);
        $query = $this->db->update('saved_messages', $data);
        return $query;
    }
    function get_priority_restrict_merge($option) {
        $this->db->where('priority', $option['priority']);
        $query = $this->db->get('restrict_merge')->row();
        return $query;
    }
    function add_campaign_bulk($option1,$option2,$option3) {
        if (!$option1['scount']) return false;
        //$option3[$val][0] [*n*]
        //$option3[$val][1] [*1*]
        //$option3[$val][2] [*2*]
        //$option3[$val][3] [*3*]
        //$option3[$val][4] [*4*]
        //$option3[$val][5] 050여부
        $reserve_time = ($option1['send_time'] != '' ? $option1['send_time'] : $this->current_time);

        if ($option1['divide_yn'] == 'Y') $unixtime = strtotime($reserve_time);
        $option1['subject'] = str_replace('&lt;', '<', $option1['subject']);
        $option1['msg'] = str_replace('&lt;', '<', $option1['msg']);

error_log('[S]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
        $this->db->select('cash');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where('state', '0');
        $row = $this->db->get('users')->row();
        $cur_cash = (float)$row->cash;
        if ($cur_cash < (float)$option1['amount']) {
error_log('[E1]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        }
        $sql = "
            UPDATE users
            SET cash = cash - {$option1['amount']}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $this->session->userdata('userno'));
        if (!$query) {
error_log('[E2]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        }

        // $this->session->set_userdata('cash', $new_cash);
        $cache_key = 'session_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 120);

        $this->db->trans_begin();

        $nproid = 0;
        if (0 && ($option1['productcode'] == 'LMS1' || $option1['productcode'] == 'LMS2') && $option1['merge_yn'] == 'N') {
            $npro_filecnt = 1;
            // if ($option1['msg'] != '' && $option1['file_path_1'] != '') $npro_filecnt = 2;
            $data = '';
            $data = array(
                'MMS_REQ_DATE' => $this->current_time,
                'SOW_SEND_DATE' => $reserve_time,
                'FILE_CNT' => $npro_filecnt,
                'MMS_BODY' => $option1['msg'],
                'MMS_SUBJECT' => $option1['subject']
            );
            $this->db->insert('NPRO_MMS_CONTENTS_INFO', $data);
            $nproid = (int)$this->db->insert_id();
            if (!$nproid) {
error_log('[E7]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
                $this->db->trans_rollback();
                $sql = "
                    UPDATE users
                    SET cash = cash + {$option1['amount']}
                    WHERE userno = ?
                ";
                $this->db->query($sql, $this->session->userdata('userno'));
                unset($data);
                unset($option1);
                unset($option2);
                unset($option3);
                return false;
            }
        }

        $data = '';
        $data = array(
            'userno'        => $this->session->userdata('userno'),
            'userid'        => $this->session->userdata('userid'),
            'storeno'       => $option1['storeno'],
            'groupno'       => $option1['groupno'],
            'productcode'   => $option1['productcode'],
            'priority'      => $option1['priority'],
            'reg_time'      => $this->current_time,
            'reserve_time'  => $reserve_time,
            'merge'         => $option1['merge_yn'],
            'divide'        => $option1['divide_yn'],
            'div_count'     => $option1['div_cnt'],
            'div_minute'    => $option1['div_min'],
            'ban_units'     => $option1['bcount'], //ban count
            'total_units'   => $option1['scount'], //send count
            'remain_units'  => $option1['scount'],
            'price'         => $option1['price'],
            'amount'        => $option1['amount'],
            'realamount'    => $option1['realamount'],
            'ip'            => $option1['ip'],
            'callback'      => $option1['callback'],
            'file_cnt'      => $option1['file_cnt'],
            'file_path_1'   => $option1['file_path_1'],
            'file_path_2'   => $option1['file_path_2'],
            'file_path_3'   => $option1['file_path_3'],
            'subject'       => $option1['subject'],
            'msg'           => $option1['msg'],
            'yyyy'          => (int)substr($reserve_time,0,4),
            'mm'            => (int)substr($reserve_time,5,2),
            'dd'            => (int)substr($reserve_time,8,2),
            'nproid'        => (int)$nproid,
            'refund_val'    => (int)$this->session->userdata('refund_val'),
        );
        $this->db->insert('sow_processunit_msg', $data);
        $procid = (int)$this->db->insert_id();
        if (!$procid) {
error_log('[E3]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            $this->db->trans_rollback();
            $sql = "
                UPDATE users
                SET cash = cash + {$option1['amount']}
                WHERE userno = ?
            ";
            $this->db->query($sql, $this->session->userdata('userno'));
            unset($data);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        }

        $data = '';
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'userid' => $this->session->userdata('userid'),
            'storeno' => $option1['storeno'],
            'groupno' => $this->session->userdata('groupno'),
            'procid' => $procid,
            'amount' => $option1['amount'] * (-1),
            'mode' => $option1['bill_mode'],
            'reg_time' => $reserve_time,
            'yyyy' => (int)substr($reserve_time,0,4),
            'mm' => (int)substr($reserve_time,5,2),
            'dd' => (int)substr($reserve_time,8,2)
        );
        $this->db->insert('billing', $data);
        $xid = (int)$this->db->insert_id();
        if (!$xid) {
error_log('[E4]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);

            $this->db->trans_rollback();
            $sql = "
                UPDATE users
                SET cash = cash + {$option1['amount']}
                WHERE userno = ?
            ";
            $this->db->query($sql, $this->session->userdata('userno'));
            unset($data);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        }

        $i = 0;
        $seq = 1;
        $data = array();
        foreach ($option2 as $val) {
            $data[$i]['procid'] = $procid;
            $data[$i]['userno'] = $this->session->userdata('userno');
            $data[$i]['seq'] = $seq++;
            $data[$i]['targetno'] = $val;
            $data[$i]['targetname'] = $option3[$val][0];
            if (substr($val, 0, 3) == '050') {
                $data[$i]['vnumber'] = substr($val, 0, 4);
            } else {
                $data[$i]['vnumber'] = '';
            }
            if ($option1['divide_yn'] == 'Y') {
                $div = 0;
                $div = (int)($i / $option1['div_cnt']);
                if (!$div) $data[$i]['reserve_time'] = $reserve_time;
                else {
                    $data[$i]['reserve_time'] = date('Y-m-d H:i:s', $unixtime+($div * $option1['div_min'] * 60));
                }
            } else {
                $data[$i]['reserve_time'] = $reserve_time;
            }
            if ($option1['merge_yn'] == 'Y') {
                $search = array('[*n*]', '[*1*]', '[*2*]', '[*3*]', '[*4*]');
                $replace = array($option3[$val][0], $option3[$val][1], $option3[$val][2], $option3[$val][3], $option3[$val][4]);
                $data[$i]['msgbody'] = str_replace($search, $replace, $option1['msg']);
            } else {
                // $data[$i++]['msgbody'] = $option1['msg'];
            }
            $i ++;
        }
        $query = $this->db->insert_batch('sow_pu_msgdata', $data);
        if (!$query) {
error_log('[E5]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            $this->db->trans_rollback();
            $sql = "
                UPDATE users
                SET cash = cash + {$option1['amount']}
                WHERE userno = ?
            ";
            $this->db->query($sql, $this->session->userdata('userno'));
            unset($data);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        }

        $this->db->trans_complete();
        //check if transaction status TRUE or FALSE
        if ($this->db->trans_status() === false) {
error_log('[E6]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            //if something went wrong, rollback everything
            $this->db->trans_rollback();
            $sql = "
                UPDATE users
                SET cash = cash + {$option1['amount']}
                WHERE userno = ?
            ";
            $this->db->query($sql, $this->session->userdata('userno'));
            unset($data);
            unset($option1);
            unset($option2);
            unset($option3);
            return false;
        } else {
error_log('[F]processing....'.$this->session->userdata('storename').'/'.$this->session->userdata('userid').'/'.$option1['scount'], 0);
            //if everything went right, commit the data to the database
            $this->db->trans_commit();
            unset($data);
            unset($option1);
            unset($option2);
            unset($option3);
            return true;
        }
    }
    function get_channel($priority) {
        $this->db->where('channel', $priority);
        $query = $this->db->get('channel')->row();
        return $query;
    }
}
