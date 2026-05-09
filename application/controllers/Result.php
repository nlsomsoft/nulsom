<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Result extends CI_Controller {
    private $current_time;
    private $rows_per_page;

    function __construct() {
        parent::__construct();
        $this->current_time = date('Y-m-d H:i:s'); //0000-00-00 00:00:00
        $this->rows_per_page = 100;
        $this->load->helper(array('form','url','phone','mydate','bill','mytext'));
        initialize_session_userdata($this);
    }
    public function list() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }

        $config['base_url'] = "/result/list/{$sf}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => $sv,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count($option);
        $data['result'] = $this->resultModels->get_campaign_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('result/list', $data);
        $this->load->view('templates/footer');
    }
    public function list1() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }

        $config['base_url'] = "/result/list/{$sf}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => $sv,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count_mass($option);
        $data['result'] = $this->resultModels->get_campaign_limit_mass($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('result/list1', $data);
        $this->load->view('templates/footer');
    }
    public function reserve() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }

        $config['base_url'] = "/result/reserve/{$sf}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val' => $sv,
            'limit' => $this->rows_per_page,
            'offset' => $offset,
            'status' => true,
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_campaign_count($option);
        $data['result'] = $this->resultModels->get_campaign_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['gno'] = $sg;
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('templates/header');
        $this->load->view('result/reserve', $data);
        $this->load->view('templates/footer');
    }
    public function stats() {
        $data['date_from'] = ($this->input->get('date_from') != '' ? $this->input->get('date_from') : date('Y-m-d'));
        $data['date_to'] = ($this->input->get('date_to') != '' ? $this->input->get('date_to') : date('Y-m-d'));
        if ($data['date_from'] && $data['date_to']) {
            $option = array(
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
            );
            $this->load->model('resultModels');
            $data['result'] = $this->resultModels->get_campaign_stats($option);
        }
        $this->load->view('templates/header');
        $this->load->view('result/stats', $data);
        $this->load->view('templates/footer');
    }
    public function stats_daily() {
        $data['date_from'] = ($this->input->get('date_from') != '' ? $this->input->get('date_from') : date('Y-m-d'));
        $data['date_to'] = ($this->input->get('date_to') != '' ? $this->input->get('date_to') : date('Y-m-d'));
        if ($data['date_from'] && $data['date_to']) {
            $option = array(
                'date_from' => $data['date_from'],
                'date_to' => $data['date_to'],
            );
            $this->load->model('resultModels');
            $data['result'] = $this->resultModels->get_campaign_stats_daily($option);
        }
        $this->load->view('templates/header');
        $this->load->view('result/stats_daily', $data);
        $this->load->view('templates/footer');
    }
    public function detail() {
        if (!$this->uri->segment(3)) {
            $this->session->set_flashdata('notice', '잘못된 접근입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $procid = (int)$this->uri->segment(3);
        $option = array($procid);
        $this->load->model('resultModels');
        $data['result'] = $this->resultModels->get_campaign($option);
        if (!$data['result']) {
            $this->session->set_flashdata('notice', '데이타가 존재하지 않습니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->load->view('templates/header');
        $this->load->view('result/detail', $data);
        $this->load->view('templates/footer');
    }
    public function detail_list() {
        if ($this->input->get('sv')) { //검색
            $sv = urldecode($this->input->get('sv'));
            $offset = 0;
            $sf = '1';
        } else {
            $sf = (int)$this->uri->segment(3);
            $sv = ($sf == '1' ? urldecode($this->uri->segment(4)) : '');
            $offset =  (int)($sf == '1' ? $this->uri->segment(5) : $this->uri->segment(4));
        }
        $config['base_url'] = "/result/detail_list/{$sf}/{$sv}/";
        $config['per_page'] = $this->rows_per_page;

        $option = array(
            'val'       => str_replace('-', '', $sv),
            'limit'     => $this->rows_per_page,
            'offset'    => $offset,
            'procid'    => $this->session->userdata('campaign_procid'),
            'table'     => $this->session->userdata('campaign_table'),
        );

        $this->load->model('resultModels');
        $config['total_rows'] = (int)$this->resultModels->get_result_count($option);
        $data['result'] = $this->resultModels->get_result_limit($option);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data['sf'] = $sf;
        $data['sv'] = $sv;
        $data['procid'] = $this->session->userdata('campaign_procid');
        $data['table'] = $this->session->userdata('campaign_table');
        $data['total_rows'] = $config['total_rows'];
        $this->load->view('result/detail_list', $data);
    }
    public function list_excel() {
        $table = $this->uri->segment(3);
        $procid = (int)$this->uri->segment(4);
        $result = (int)$this->uri->segment(5);
        if (!$table || !$procid || !$result) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $fname = ($result == 1000 ? 'succ' : 'fail');

        // 메모리제한 늘리기
        // error_log('extend memory 256 for excel download', 0);
        // ini_set('memory_limit','256M');

        $option = array(
            'result' => $result,
            'procid' => $procid,
            'table' => $table,
        );
        $this->load->model('resultModels');
        $result_array = $this->resultModels->get_result_limit_excel($option);
        $excel_fields = array(
            '전화번호',
            '이름',
            '통신사'
        );
        $kk = 0;
        $excel_data = array();
        foreach ($result_array as $dkey => $dval) {
            foreach ($dval as $k => $v) {
                if ($k == 'targetno') $excel_data[$dkey][0] = $v;
                else if ($k == 'targetname') $excel_data[$dkey][1] = $v;
                else if ($k == 'telecom') $excel_data[$dkey][2] = convert_result_telecom($v);
            }
        }
        unset($result_array);

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
        unset($excel_data);

        // 파일로 내보낸다.
        $filename = 'rst_'.$fname.'_'.date('ymdHi');
        header('Content-Type: application/vnd.ms-excel; charset=euc-kr');
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
    public function result_excel() {
        $table = $this->uri->segment(3);
        $procid = (int)$this->uri->segment(4);
        $result = (int)$this->uri->segment(5);
        if (!$table || !$procid || !$result) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $fname = ($result == 1000 ? 'succ' : 'fail');

        $option = array(
            'result' => $result,
            'procid' => $procid,
            'table' => $table,
        );
        $this->load->model('resultModels');
        $result_array = $this->resultModels->get_result_limit_excel_array($option);


        $filename = 'rst_'.date('ymdHis').'.csv';
        header("Content-Type: application/force-download");
        header('Content-Type: application/csv; charset=euc-kr;');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // open file...
        $fp = fopen("php://output", 'w');

        // put BOM marker
        fwrite($fp, "\xEF\xBB\xBF");

        // GET CSV Header Texts...
        $headers = array('전화번호','이름','통신사');

        // PUT CSV header line
        fputcsv($fp, $headers);


        $excel_data = array();
        foreach ($result_array as $key => $val) {
            $telecom_val = ($result == 1000 ? convert_result_telecom($val['telecom']) : '');
            fputcsv($fp,array(phone_format($val['targetno']), $val['targetname'], $telecom_val));
        }
        fclose($fp);
        unset($result_array);
    }
    public function daily_excel() {
        $rd_date = $this->uri->segment(3);
        $ipt_page = (int)$this->uri->segment(4);
        $total_cnt = (int)$this->uri->segment(5);
        if (!$rd_date || !$ipt_page || !$total_cnt) {
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $max_page = (int)($ipt_page * 100000);
        $min_page = (int)($max_page - 100000);

        $option = array(
            'send_time' => $rd_date,
        );

        if ($total_cnt >= 350000) {
            error_log('extend memory 384M for csv download userno:'.$this->session->userdata('userno').' send_time:'.$option['send_time'], 0);
            ini_set('memory_limit','384M');
        } else if ($total_cnt >= 150000) {
            error_log('extend memory 256M for csv download userno:'.$this->session->userdata('userno').' send_time:'.$option['send_time'], 0);
            ini_set('memory_limit','256M');
        } else {
            // error_log('extend memory 128M for csv download userno:'.$this->session->userdata('userno').' send_time:'.$option['send_time'], 0);
            ini_set('memory_limit','128M');
        }

        $result = array();
        $this->load->model('resultModels');

        $option['table'] = "result_0";
        $result[0] = $this->resultModels->get_result_daily_excel($option);

        for ($i = MIN_RESULT_CNT; $i <= MAX_RESULT_CNT; $i++) {
            $option['table'] = "result_{$i}";
            $result[$i] = $this->resultModels->get_result_daily_excel($option);
        }
        // 메모리제한 늘리기

        $filename = 'rst_'.$rd_date.'_'.$ipt_page.'.csv';
        header("Content-Type: application/force-download");
        header('Content-Type: application/csv; charset=euc-kr');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        // open file...
        $fp = fopen("php://output", 'w');

        // put BOM marker
        fwrite($fp, "\xEF\xBB\xBF");

        // GET CSV Header Texts...
        $headers = array('전화번호','이름','통신사','결과');

        // PUT CSV header line
        fputcsv($fp, $headers);

        $i = 0;
        foreach ($result as $loop) {
            foreach ($loop as $val) {
                $i ++;
                if (!($i > $min_page && $i <= $max_page)) continue;
                $result_val = (($val['result'] == '0' || $val['result'] == '1000') ? '성공' : '실패');
                $telecom_val = (($val['result'] == '0' || $val['result'] == '1000') ? convert_result_telecom($val['telecom']) : '');
                fputcsv($fp,array(phone_format($val['targetno']), $val['targetname'], $telecom_val, $result_val));
            }
        }
        fclose($fp);
        unset($result);
    }
    public function set_detail() {
        try {
            if (!$this->uri->segment(3)) {
                // $this->session->set_flashdata('notice', '잘못된 접근입니다.');
                // redirect($_SERVER['HTTP_REFERER']);
                throw new Exception('segment error', 1);
            }
            $tbl = (int)$this->uri->segment(4);
            $campaign_table = "result_{$tbl}";

            $this->session->set_userdata('campaign_procid', $this->uri->segment(3));
            $this->session->set_userdata('campaign_table', $campaign_table);
        }
        catch(Exception $e) {
            error_log($e->getMessage().' : '.$e->getCode(), 0);
        }
        redirect('/result/detail_list');
    }
    public function delete_list() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Checked Numbers', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option = array();
        $option = explode(',', $this->input->post('chk_nums'));

        $this->load->model('resultModels');
        $data['result'] = $this->resultModels->delete_campaign($option);
        redirect('/result/list');
    }
    public function delete_reserve() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('chk_nums', 'Checked Numbers', 'required');
        if ($this->form_validation->run() === false) {
            error_log(validation_errors(), 0);
            $this->session->set_flashdata('notice', '파라미터 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $option = array();
        $option = explode(',', $this->input->post('chk_nums'));

        $this->load->model('resultModels');
        $option1 = $this->resultModels->get_campaign_list($option);
        if (!$option1) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }
        $result = $this->resultModels->delete_reserved_campaign($option,$option1);
        if (!$result) {
            $this->session->set_flashdata('notice', '시스템 오류입니다.');
            redirect($_SERVER['HTTP_REFERER']);
        }

        //session 정보 초기화
        $cache_key = 'session_'.$this->session->userdata('storeno').'_'.$this->session->userdata('userid');
        $this->cache->redis->save($cache_key, '1', 3600); //1시간 자동삭제
        redirect('/result/reserve');
    }
}
