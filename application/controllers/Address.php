<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Address extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone'));
        initialize_session_userdata($this);
    }

    public function ban() {
        $config['base_url'] = '/address/ban/';
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'limit' => $this->rows_per_page,
            'offset' => (int)$this->uri->segment(3),
        );
        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_ban_count();
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_ban_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['total_rows'] = $total_rows;
        $this->load->view('templates/header');
        $this->load->view('address/ban',$data);
        $this->load->view('templates/footer');
    }
    public function search_ban() {
        $sv = ($this->input->get('sv') != null ? $this->input->get('sv') : urldecode($this->uri->segment(3)));
        // error_log(print_r($this->input->post(),1), 0);
        $config['base_url'] = '/address/search_ban/'.$sv.'/';
        $config['per_page'] = $this->rows_per_page;


        $option = array(
            'val' => $sv,
            'limit' => $this->rows_per_page,
            'offset' => (int)$this->uri->segment(4),
        );

        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_search_ban_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_search_ban_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['total_rows'] = $total_rows;
        $this->load->view('templates/header');
        $this->load->view('address/ban',$data);
        $this->load->view('templates/footer');
    }
    public function delete_ban() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Delete List', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/address/ban');
        }

        $ban_arr = explode(',', $this->input->post('chk_nums'));
        $this->load->model('addressModels');
        $result = $this->addressModels->delete_ban_selected($ban_arr);
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect('/address/ban');
        }
        $this->session->set_flashdata('notice', '정상적으로 처리했습니다.');
        redirect('/address/ban');
    }
    public function ban_excel() {
        $this->load->model('addressModels');
        $result = $this->addressModels->get_bans();
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect('/address/ban');
        }

        $excel_fields = array(
            '차단번호',
            '이름',
            '등록일'
        );
        $kk = 0;
        $excel_data = array();
        foreach ($result as $dkey => $dval) {
            foreach ($dval as $k => $v) {
                if ($k == 'mobile') $excel_data[$dkey][0] = $v;
                else if ($k == 'name') $excel_data[$dkey][1] = $v;
                else if ($k == 'add_date') $excel_data[$dkey][2] = $v;
            }
        }

        // 라이브러리를 로드한다.
        $this->load->library('excel',$params);
        // 시트를 지정한다.
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sheet1');

        // 첫 줄에 필드명을 기록한다.
        // $fields = $this->read_field_names();
        $col = 0;
        foreach ($excel_fields as $field_name) {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field_name);
            $col ++;
        }

        $max_cols = count($excel_fields);
        // 데이터를 읽어서 순차로 기록한다.
        // $records = $this->read_table_records();
        // $row = 2;
        foreach ($excel_data as $ekey => $eval) {
            foreach ($eval as $col => $val) {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($ekey+2), $val);
            }
            // $row ++;
        }
        // 메모리제한 늘리기
        // ini_set('memory_limit','512M');
        ini_set('memory_limit','256M');

        // 파일로 내보낸다.
        $filename = BRAND.'_'.date('ymdHi');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // Excel5 포맷(excel 2003 .XLS file)으로 저장한다.
        // 두 번째 매개변수를 'Excel2007'로 바꾸면 Excel 2007 .XLSX 포맷으로 저장한다.
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //출력버퍼를 지우고 출력 버퍼링을 종료
        ob_end_clean();
        //출력 버퍼링 시작
        ob_start();
        // 이용자가 다운로드하여 컴퓨터 HD에 저장하도록 강제한다.
        $objWriter->save('php://output');
    }
    public function phone080() {
        auth_session_userdata($this, 5);
        if (STORE_080EXT_USEAGE_YN != 'Y') {
            $this->session->set_flashdata('notice', '사용권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        if ($this->input->get('sv')) { //검색
            $sv = $this->input->get('sv');
            $offset = 0;
        } else {
            $sv = '000';
            $offset =  (int)$this->uri->segment(4);
        }
        $config['base_url'] = "/address/phone080/{$sv}";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => ($sv == '000' ? '' : $sv),
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );

        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_phone_080_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_phone_080_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['total_rows'] = $total_rows;
        $data['offset'] = $offset;
        $this->load->view('templates/header');
        $this->load->view('address/phone080',$data);
        $this->load->view('templates/footer');
    }
    public function phone080_excel() {
        auth_session_userdata($this, 5);
        if (STORE_080EXT_USEAGE_YN != 'Y') {
            $this->session->set_flashdata('notice', '사용권한이 없습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->model('addressModels');
        $result = $this->addressModels->get_phone_080_list();
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect('/address/ban');
        }

        $excel_fields = array(
            '차단번호',
            '등록일'
        );
        $kk = 0;
        $excel_data = array();
        foreach ($result as $dkey => $dval) {
            foreach ($dval as $k => $v) {
                if ($k == 'mobile') $excel_data[$dkey][0] = $v;
                else if ($k == 'reg_time') $excel_data[$dkey][1] = $v;
            }
        }

        // 라이브러리를 로드한다.
        $this->load->library('excel',$params);
        // 시트를 지정한다.
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sheet1');

        // 첫 줄에 필드명을 기록한다.
        // $fields = $this->read_field_names();
        $col = 0;
        foreach ($excel_fields as $field_name) {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field_name);
            $col ++;
        }

        $max_cols = count($excel_fields);
        // 데이터를 읽어서 순차로 기록한다.
        // $records = $this->read_table_records();
        // $row = 2;
        foreach ($excel_data as $ekey => $eval) {
            foreach ($eval as $col => $val) {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($ekey+2), $val);
            }
            // $row ++;
        }
        // 메모리제한 늘리기
        // ini_set('memory_limit','512M');
        ini_set('memory_limit','256M');

        // 파일로 내보낸다.
        $filename = BRAND.'_'.date('ymdHi');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // Excel5 포맷(excel 2003 .XLS file)으로 저장한다.
        // 두 번째 매개변수를 'Excel2007'로 바꾸면 Excel 2007 .XLSX 포맷으로 저장한다.
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //출력버퍼를 지우고 출력 버퍼링을 종료
        ob_end_clean();
        //출력 버퍼링 시작
        ob_start();
        // 이용자가 다운로드하여 컴퓨터 HD에 저장하도록 강제한다.
        $objWriter->save('php://output');
    }
    public function group() {
        $this->load->model('addressModels');
        $data['result'] = $this->addressModels->get_groups();

        $this->load->view('templates/header');
        $this->load->view('address/group',$data);
        $this->load->view('templates/footer');
    }
    public function add_group() {
        header("Content-Type: application/json");
        if ((int)$this->session->userdata('state') > 0) {
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '사용 권한이 없습니다. 관리자에게 문의하세요.';
            exit (json_encode($data_json));
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('group_name', 'Group name', 'required|min_length[1]|max_length[50]');
        $this->form_validation->set_rules('where', 'Auth', 'required|in_list[address]');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $data_json['result'] = 'error';
            $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
            $data_json['message'] = '파라미터 오류입니다.';
            exit (json_encode($data_json));
        }
        $option['name'] = $this->input->post('group_name');
        $this->load->model('addressModels');
        $data['result'] = $this->addressModels->add_group($option);
        $data_json['result'] = 'success';
        $data_json['csrf_sowkorea_name'] = $this->security->get_csrf_hash();
        $data_json['message'] = '정상적으로 처리되었습니다.';
        exit (json_encode($data_json));
    }
    public function bulk_add() {
        if ($this->input->post('bulk_type') == 'ban_type') $return_url = '/address/ban';
        else $return_url = '/address/group';

        if ((int)$this->session->userdata('state') > 0) {
            $this->session->set_flashdata('notice', '사용 권한이 없습니다. 관리자에게 문의하세요.');
            redirect($return_url);
        }

        $this->load->library('form_validation');
        // $this->form_validation->set_rules('bulklist_telnumlist', 'Bulk List', 'required');
        $this->form_validation->set_rules('bulk_list', 'Bulk List', 'required');
        $this->form_validation->set_rules('bulk_gno', 'Bulk Gno', 'required|integer');
        $this->form_validation->set_rules('bulk_current_count', 'Bulk Count', 'required|integer');
        $this->form_validation->set_rules('bulk_count', 'Bulk Count', 'required|integer');
        $this->form_validation->set_rules('bulk_type', 'Bulk Type', 'required|in_list[ban_type,address_type]');

        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($return_url);
        }

        $list_array = array();
        // $list_array = explode(PHP_EOL, $this->input->post('bulklist_telnumlist'));
        $list_array = explode(',', $this->input->post('bulk_list'));

        $group_array = array();
        $group_array['mobile_cnt'] = 0;
        $group_array['phone_cnt'] = 0;
        $group_array['fax_cnt'] = 0;
        $group_array['email_cnt'] = 0;
        $group_array['total_cnt'] = 0;
        $group_array['gno'] = (int)$this->input->post('bulk_gno');

        $batch_array = array();
        foreach ($list_array as $key => $val) {
            $addr_info = null;
            $addr_info = explode('|', $val);
            if (!$addr_info[0] && !$addr_info[1]) continue;

            $batch_array[$key]['userno'] = $this->session->userdata('userno');
            if ($this->input->post('bulk_type') == 'address_type') {
                $batch_array[$key]['gno'] = (int)$this->input->post('bulk_gno');
            }
            if ($addr_info[0] != '') $group_array['mobile_cnt'] ++;
            $batch_array[$key]['mobile'] = $addr_info[0];
            $batch_array[$key]['mobile1'] = substr($addr_info[0],-8,4);
            $batch_array[$key]['mobile2'] = substr($addr_info[0],-4);
            $batch_array[$key]['name'] = $addr_info[1];
            $batch_array[$key]['add_date'] = $this->current_time;
            $group_array['total_cnt'] ++;
        }

        $this->load->model('addressModels');
        if ($this->input->post('bulk_type') == 'address_type') {
            $result = $this->addressModels->is_groups($this->input->post('bulk_gno'));
            if (!$result) {
                $this->session->set_flashdata('notice', '파라미터 오류입니다.');
                redirect($return_url);
            }
            $result = $this->addressModels->add_address_bulk($group_array,$batch_array);
        } else {
            $result = $this->addressModels->add_ban_bulk($batch_array);
        }
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 에러입니다.');
            redirect($return_url);
        }
        $this->session->set_flashdata('notice', '정상적으로 등록되었습니다.');
        redirect($return_url);
    }
    public function group_info() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_value', 'Group name', 'required|min_length[1]|max_length[20]');
        $this->form_validation->set_rules('gno', 'Group Number', 'required|integer');

        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect('/address/group');
        }

        $group = array(
            'gno' => (int)$this->input->post('gno'),
            'name' => $this->input->post('new_value'),
        );
        $this->load->model('addressModels');
        $result = $this->addressModels->change_group_name($group);
        redirect('/address/group');
    }
    public function delete_group() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Delete List', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $group_arr = explode(',', $this->input->post('chk_nums'));
        $this->load->model('addressModels');
        $result = $this->addressModels->delete_group_selected($group_arr);
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        // $this->session->set_flashdata('message', '정상적으로 등록했습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function list() {
        $config['base_url'] = '/address/list/'.$this->uri->segment(3);
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'limit' => $this->rows_per_page,
            'offset' => (int)$this->uri->segment(4),
            'gno' => (int)$this->uri->segment(3),
        );

        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_list_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_list_limit($option);
        $data['group_result'] = $this->addressModels->get_groups();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['gno'] = (int)$this->uri->segment(3);
        $data['total_rows'] = $total_rows;
        $this->load->view('templates/header');
        $this->load->view('address/list',$data);
        $this->load->view('templates/footer');
    }
    public function delete_list() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Delete List', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $list_arr  = explode(',', $this->input->post('chk_nums'));
        $group_arr = explode(',', $this->input->post('gno_nums'));

        $this->load->model('addressModels');
        $result = $this->addressModels->delete_list_selected($list_arr,$group_arr);
        if (!$result) {
            $this->session->set_flashdata('notice', '처리과정 중 오류가 발생했습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function list_info() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_name', 'New Name', 'min_length[1]|max_length[20]');
        $this->form_validation->set_rules('new_mobile', 'New Mobile', 'integer|min_length[10]|max_length[11]');
        $this->form_validation->set_rules('sel_column', 'Selected column', 'required');
        $this->form_validation->set_rules('ano', 'Address Number', 'required|integer');

        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $new_value = ($this->input->post('sel_column') == 'mobile' ? $this->input->post('new_mobile') : $this->input->post('new_name'));
        if (!$new_value) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $list = array(
            'ano' => (int)$this->input->post('ano'),
            'column' => $this->input->post('sel_column'),
            'value' => $new_value
        );

        $this->load->model('addressModels');
        $result = $this->addressModels->change_list_info($list);
        $this->session->set_flashdata('notice', '정상적으로 처리되었습니다.');
        redirect($_SERVER['HTTP_REFERER']);
    }
    public function search_list() {
        $sv = ($this->input->get('sv') != null ? $this->input->get('sv') : urldecode($this->uri->segment(4)));
        $sg = (int)($this->input->get('sg') != null ? $this->input->get('sg') : urldecode($this->uri->segment(3)));

        // error_log(print_r($this->input->post(),1), 0);
        $config['base_url'] = "/address/search_list/{$sg}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => $sv,
            'gno' => $sg,
            'limit' => $this->rows_per_page,
            'offset' => (int)$this->uri->segment(5),
        );

        $this->load->model('addressModels');
        $total_rows = (int)$this->addressModels->get_search_list_count($option);
        $config['total_rows'] = $total_rows;
        $data['result'] = $this->addressModels->get_search_list_limit($option);
        $data['group_result'] = $this->addressModels->get_search_groups($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = '1';
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $total_rows;
        $this->load->view('templates/header');
        $this->load->view('address/list',$data);
        $this->load->view('templates/footer');
    }
    public function list_excel() {
        $sg = (int)$this->uri->segment(3);
        $sv = urldecode($this->uri->segment(4));
        if (!$sg) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option = array(
            'val' => $sv,
            'gno' => $sg,
        );
        $this->load->model('addressModels');
        $result = $this->addressModels->get_search_list_excel($option);

        $excel_fields = array(
            '전화번호',
            '이름',
            '그룹명',
            '등록일'
        );
        $kk = 0;
        $excel_data = array();
        foreach ($result as $dkey => $dval) {
            foreach ($dval as $k => $v) {
                if ($k == 'mobile') $excel_data[$dkey][0] = $v;
                else if ($k == 'name') $excel_data[$dkey][1] = $v;
                else if ($k == 'add_date') $excel_data[$dkey][2] = $v;
            }
        }

        // 라이브러리를 로드한다.
        $this->load->library('excel',$params);
        // 시트를 지정한다.
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('Sheet1');

        // 첫 줄에 필드명을 기록한다.
        // $fields = $this->read_field_names();
        $col = 0;
        foreach ($excel_fields as $field_name) {
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field_name);
            $col ++;
        }

        $max_cols = count($excel_fields);
        // 데이터를 읽어서 순차로 기록한다.
        // $records = $this->read_table_records();
        // $row = 2;
        foreach ($excel_data as $ekey => $eval) {
            foreach ($eval as $col => $val) {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, ($ekey+2), $val);
            }
            // $row ++;
        }
        // 메모리제한 늘리기
        // ini_set('memory_limit','512M');
        ini_set('memory_limit','256M');

        // 파일로 내보낸다.
        $filename = 'sms_'.date('ymdHi');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // Excel5 포맷(excel 2003 .XLS file)으로 저장한다.
        // 두 번째 매개변수를 'Excel2007'로 바꾸면 Excel 2007 .XLSX 포맷으로 저장한다.
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        //출력버퍼를 지우고 출력 버퍼링을 종료
        ob_end_clean();
        //출력 버퍼링 시작
        ob_start();
        // 이용자가 다운로드하여 컴퓨터 HD에 저장하도록 강제한다.
        $objWriter->save('php://output');
    }
}
