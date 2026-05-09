<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


require_once  APPPATH."/third_party/PHPExcel.php";

class Excel extends PHPExcel {
	private $ymd;
	private $type;
	private $data;

    public function __construct($params = array()) {
        parent::__construct();
        $this->ymd = date('ymdHi');
        $this->type = $params['type'];
        $this->data = $params['data'];
    }
}
