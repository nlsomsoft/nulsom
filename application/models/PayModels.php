<?php
class PayModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function get_bill_count($option) {
        $option1 = array('PA','PB','PC','PD','PE','MA');
        $this->db->from('billing');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('mode', $option1);
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(reg_time) <=', $option['date_to']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_bill_limit($option) {
        $option1 = array('PA','PB','PC','PD','PE','MA');
        $this->db->where('userno', $this->session->userdata('userno'));
        $this->db->where_in('mode', $option1);
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(reg_time) <=', $option['date_to']);
        $this->db->order_by('xid', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing')->result();
        return $query;
    }
    function get_service_count($option) {
        $this->db->from('billing');
        $this->db->where('userno', $this->session->userdata('userno'));
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(reg_time) <=', $option['date_to']);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_service_limit($option) {
        $this->db->where('userno', $this->session->userdata('userno'));
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(reg_time) <=', $option['date_to']);
        $this->db->order_by('xid', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing')->result();
        return $query;
    }
    function get_service_count_by_admin($option) {
        $this->db->select('count(*) as cnt');
        $this->db->where('a.userid', $option['userid']);
        if ($this->session->userdata('level') != '9') {
            $this->db->where('b.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') $this->db->where('b.groupno', $this->session->userdata('groupno'));
        }
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(a.reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(a.reg_time) <=', $option['date_to']);
        $this->db->join('users b', 'a.userno = b.userno', 'left');
        $query = $this->db->get('billing a')->row();
        return $query;
    }
    function get_service_limit_by_admin($option) {
        $this->db->select('a.*,c.groupid');
        $this->db->where('a.userid', $option['userid']);
        if ($this->session->userdata('level') != '9') {
            $this->db->where('b.storeno', $this->session->userdata('storeno'));
            if ($this->session->userdata('level') == '3') $this->db->where('b.groupno', $this->session->userdata('groupno'));
        }
        if ($option['date_from'] != '0000-00-00') $this->db->where('date(a.reg_time) >=', $option['date_from']);
        if ($option['date_to'] != '0000-00-00') $this->db->where('date(a.reg_time) <=', $option['date_to']);
        $this->db->join('users b', 'a.userno = b.userno', 'left');
        $this->db->join('group c', 'a.groupno = c.groupno', 'left');
        $this->db->order_by('a.xid', 'asc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('billing a')->result();
        return $query;
    }
    function modify_cash_by_admin($option, $option1) {
        $this->db->trans_begin();
        $sql = "
            UPDATE users
            SET cash = cash + {$option1['amount']},
                sum_cash = sum_cash + {$option1['amount']}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $option['userno']);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $data = array(
            'userno' => $option['userno'],
            'userid' => $option['userid'],
            'storeno' => $option['storeno'],
            'groupno' => $option['groupno'],
            'amount' => $option1['amount'],
            'mode' => $option1['bill_mode'],
            'memo' => $option1['memo'],
            'reg_time' => $this->current_time,
            'yyyy' => (int)substr($this->current_time,0,4),
            'mm' => (int)substr($this->current_time,5,2),
            'dd' => (int)substr($this->current_time,8,2)
        );
        $this->db->insert('billing', $data);
        $xid = (int)$this->db->insert_id();
        if (!$xid) {
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
    function add_bankbook($option) {
        $data = array(
            'userno' => $this->session->userdata('userno'),
            'userid' => $this->session->userdata('userid'),
            'storeno' => $this->session->userdata('storeno'),
            'amount' => $option['amount'],
            'deposit_name' => $option['deposit_name'],
            'proc' => 'N',
            'reg_time' => $this->current_time
        );
        $this->db->insert('payment_request', $data);
        $query = $this->db->insert_id();
        return $query;
    }
    function get_bankbook_count($option) {
        $this->db->from('payment_request');
        if ($this->session->userdata('level') != '9') $this->db->where('storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('MATCH (userid,deposit_name) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_bankbook_request_count() {
        $this->db->from('payment_request');
        //$this->db->where('storeno', $this->session->userdata('storeno'));
        $this->db->where('proc', 'N');
        $query = $this->db->count_all_results();
        return $query;
    }
    function get_bankbook_limit($option) {
        $this->db->select('a.*, b.storename');
        if ($this->session->userdata('level') != '9') $this->db->where('a.storeno', $this->session->userdata('storeno'));
        if ($option['val'] != '') $this->db->where('MATCH (a.userid,a.deposit_name) AGAINST ("+'.$option['val'].'*" IN BOOLEAN MODE)', NULL);
        $this->db->join('store b', 'a.storeno = b.storeno', 'left');
        $this->db->order_by('a.proc asc, a.xid desc');
        $this->db->limit($option['limit'], $option['offset']);
        $query = $this->db->get('payment_request a')->result();
        return $query;
    }
    function modify_bankbook_by_admin($option) {
        $this->db->trans_begin();
        $sql = "
            UPDATE users
            SET cash = cash + {$option['amount']},
                sum_cash = sum_cash + {$option['amount']}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $option['userno']);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $data = array(
            'proc' => 'Y',
            'auth_time' => $this->current_time,
            'adminid' => $this->session->userdata('userid'),
            'ip' => $this->input->ip_address()
        );
        $this->db->where('xid', $option['xid']);
        $query = $this->db->update('payment_request', $data);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $data = array(
            'userno' => $option['userno'],
            'userid' => $option['userid'],
            'storeno' => $option['storeno'],
            'groupno' => $option['groupno'],
            'amount' => $option['amount'],
            'mode' => 'PB',
            'reg_time' => $this->current_time,
            'yyyy' => (int)substr($this->current_time,0,4),
            'mm' => (int)substr($this->current_time,5,2),
            'dd' => (int)substr($this->current_time,8,2)
        );
        $this->db->insert('billing', $data);
        $xid = (int)$this->db->insert_id();
        if (!$xid) {
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
    function get_bankbook($option) {
        $this->db->where('xid', $option['xid']);
        $query = $this->db->get('payment_request')->row();
        return $query;
    }
    function get_inicis_vbank_logs($option) {
        $this->db->where('moid', $option['moid']);
        $query = $this->db->get('inicis_vbank_logs')->row();
        return $query;
    }
    function delete_bankbook($option) {
        $this->db->where('xid', $option['xid']);
        $this->db->where('proc', 'N');
        $query = $this->db->delete('payment_request');
        return $query;
    }
    function add_inicis_vbank_logs($option) {
        $this->db->insert('inicis_vbank_logs', $option);
        $query = $this->db->insert_id();
        return $query;
    }
    function modify_inicis_vbank_logs($option,$option1) {
        $this->db->trans_begin();
        $data = array(
            'no_tid' => $option['no_tid'],
            'amt_input' => $option['amt_input'],
            'mod_date' => $this->current_time
        );
        $this->db->where('moid', $option['moid']);
        $query = $this->db->update('inicis_vbank_logs', $data);
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        $bill_mode = 'PE';
        $data = array(
            'userno' => $option1['userno'],
            'userid' => $option1['userid'],
            'storeno' => $option1['storeno'],
            'groupno' => $option1['groupno'],
            'amount' => $option1['totprice'],
            'mode' => $bill_mode,
            'reg_time' => $this->current_time,
            'yyyy' => (int)substr($this->current_time,0,4),
            'mm' => (int)substr($this->current_time,5,2),
            'dd' => (int)substr($this->current_time,8,2)
        );
        $this->db->insert('billing', $data);
        $xid = (int)$this->db->insert_id();
        if (!$xid) {
            $this->db->trans_rollback();
            return false;
        }

        $sql = "
            UPDATE users
            SET cash = cash + {$option1['totprice']},
                sum_cash = sum_cash + {$option1['totprice']}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $option1['userno']);
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
    function add_inicis_logs($option) {
        $this->db->trans_begin();
        $this->db->insert('inicis_logs', $option);
        $query = $this->db->insert_id();
        if (!$query) {
            $this->db->trans_rollback();
            return false;
        }

        if ($option['paymethod'] == 'DirectBank') $bill_mode = 'PD';
        else if ($option['paymethod'] == 'Card') $bill_mode = 'PC';
        else {
            $this->db->trans_rollback();
            return false;
        }

        $data = array(
            'userno' => $option['userno'],
            'userid' => $option['userid'],
            'storeno' => $option['storeno'],
            'groupno' => $option['groupno'],
            'amount' => $option['totprice'],
            'mode' => $bill_mode,
            'reg_time' => $this->current_time,
            'yyyy' => (int)substr($this->current_time,0,4),
            'mm' => (int)substr($this->current_time,5,2),
            'dd' => (int)substr($this->current_time,8,2)
        );
        $this->db->insert('billing', $data);
        $xid = (int)$this->db->insert_id();
        if (!$xid) {
            $this->db->trans_rollback();
            return false;
        }

        $sql = "
            UPDATE users
            SET cash = cash + {$option['totprice']},
                sum_cash = sum_cash + {$option['totprice']}
            WHERE userno = ?
        ";
        $query = $this->db->query($sql, $option['userno']);
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
}
