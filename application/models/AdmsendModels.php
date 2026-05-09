<?php
class AdmsendModels extends CI_Model {
    private $current_time;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s');
    }

    function get_list_excel_array($option) {
        $this->db->where('date(reserve_time) >=', $option['date_from']);
        $this->db->where('date(reserve_time) <=', $option['date_to']);
        $query = $this->db->get('sow_processunit_msg')->result();
        return $query;
    }
    function get_filter_word_by_admin() {
        $this->db->select('word');
        $query = $this->db->get('filter')->row_array();
        return $query;
    }
    function modify_filter_word_by_admin($option) {
        $data = array(
            'word' => $option['word']
        );
        $query = $this->db->update('filter', $data);
        return $query;
    }
}
