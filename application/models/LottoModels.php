<?php
class LottoModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function add_lotto_recommend($option) {
        $data = array(
            'num' => $option['num'],
            'divide' => $option['divide'],
            'user' => $option['user'],
            'type' => $option['type'],
            'ip' => $option['ip'],
        );
        $query = $this->db->insert('lotto_recommend', $data);
        return $query;
    }

    function get_previous_winning_num($cur_divide) {
        $divide = $cur_divide - 1;
        $this->db->where('divide', $divide);
        $query = $this->db->get('lotto_winning_num')->row();
        return $query;
    }

    function get_recommended_winning($cur_divide) {
        $divide = $cur_divide - 1;
        $this->db->where('divide', $divide);
        $this->db->where('grade >', '0');
        $this->db->order_by('grade ASC');
        $query = $this->db->get('lotto_recommend')->result();
        return $query;
    }
}
