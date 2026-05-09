<?php
class StockModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function get_today_recommendation($option) {
        $yyyymmdd = str_replace('-','',$option['yyyymmdd']);

        $this->db->where('yyyymmdd', $yyyymmdd);
        $this->db->order_by('reasons desc');
        $query = $this->db->get('kiwoom_trading')->result();
        return $query;
    }
    function get_today_trading_manager($option) {
        $yyyymmdd = str_replace('-','',$option['yyyymmdd']);

        $this->db->where('yyyymmdd', $yyyymmdd);
        $query = $this->db->get('kiwoom_manager')->row();
        return $query;
    }
    function upsert_manager_today_trigger($option) {
        if (!$option['xid']) {
            $data = array(
                'yyyymmdd' => $option['yyyymmdd'],
                'trigger' => $option['trigger']
            );
            $query = $this->db->insert('kiwoom_manager', $data);
        } else {
            $data = array(
                'trigger' => $option['trigger']
            );
            $this->db->where('yyyymmdd', $option['yyyymmdd']);
            $query = $this->db->update('kiwoom_manager', $data);
        }
        return $query;
    }
    function update_today_recommendation($option,$ticker) {
        $data = array(
            'participation' => '0'
        );
        $this->db->where('yyyymmdd', $option['yyyymmdd']);
        $query = $this->db->update('kiwoom_trading', $data);

        $data = array(
            'participation' => '1'
        );
        $this->db->where('yyyymmdd', $option['yyyymmdd']);
        $this->db->where_in('ticker', $ticker);
        $query = $this->db->update('kiwoom_trading', $data);
        return $query;
    }
}
