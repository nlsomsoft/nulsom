<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone'));
        // initialize_session_userdata($this);
    }
    public function sangin() {
        $url = 'http://dev.sowkorea.com/CRM/sample.wav';
        $filename = end(explode("/", $url));

        // $this->load->helper('file');
        $this->load->helper('download');
        // $data = file_get_contents($url);

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        curl_close($ch);

        error_log($contents, 0);
        force_download($filename, $contents);
    }
}