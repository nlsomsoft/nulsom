<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_common {
    function index () {
        if (isset($_SERVER['REQUEST_URI'])) {
            $req_uri = explode('/',$_SERVER['REQUEST_URI']);
            if ($req_uri[1] == 'kcp' || $req_uri[1] == 'inicis') {
                get_config(array('csrf_protection' => false));
            }
        }
    }
}
