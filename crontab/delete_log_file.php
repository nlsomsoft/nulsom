<?php
// define filename
$file_name = $_SERVER['PHP_SELF'];
$log_name = $_SERVER['STORENAME'];

//error_log(print_r($_SERVER,1),0);
if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) {
    error_log('['.$log_name.'][E]...[REMOTE_ADDR]['.$file_name.']', 0);
    exit;
}

// $dir = '/data/sow/aone/application/logs';
$dir = $_SERVER['DOCUMENT_ROOT'].'/application/logs';

// error_log('['.$log_name.'][S]...[delete log file.php]', 0);
recursive_file_delete($dir);
// error_log('['.$log_name.'][F]...[delete log file.php]', 0);

function recursive_file_delete($dir) {
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($entry = readdir($dh)) !== false) {
                if ($entry == '.' || $entry == '..') continue;
                $subdir = $dir.'/'.$entry;
                if (is_dir($subdir)) {
                    recursive_file_delete($subdir);
                } else {
                    if ($entry == 'index.html') continue;
                    $sfile = $dir.'/'.$entry;
                    $mtime = (int)filemtime($sfile);
                    $difftime = time() - $mtime;
                    $checktime = (int)(24 * 60 * 60 * 30);
                    //최종수정일이 30일 이상인 파일만 삭제
                    if (file_exists($sfile) && ($difftime <= $checktime)) continue;
                    // 파일삭제
                    @unlink($sfile);
                }
            }
            closedir($dh);
        }
    }
}
exit;
