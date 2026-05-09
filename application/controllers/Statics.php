<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statics extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('form','url'));
    }
    public function newsms() {
        $this->load->view('templates/header');
        $this->load->view('statics/newsms',$data);
        $this->load->view('templates/footer');
    }
    public function sms() {
        $this->load->view('templates/header');
        $this->load->view('statics/sms',$data);
        $this->load->view('templates/footer');
    }
    public function address() {
        $this->load->view('templates/header');
        $this->load->view('statics/address',$data);
        $this->load->view('templates/footer');
    }
    public function stats() {
        $this->load->view('templates/header');
        $this->load->view('statics/stats',$data);
        $this->load->view('templates/footer');
    }
    public function pay() {
        $this->load->view('templates/header');
        $this->load->view('statics/pay',$data);
        $this->load->view('templates/footer');
    }
    public function info() {
        $this->load->view('templates/header');
        $this->load->view('statics/info',$data);
        $this->load->view('templates/footer');
    }
    public function catalog() {
        $this->load->view('templates/header');
        $this->load->view('statics/catalog',$data);
        $this->load->view('templates/footer');
    }
    public function pwa() {
        $this->load->view('statics/pwa',$data);
    }
}
