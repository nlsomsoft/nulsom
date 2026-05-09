<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('recommend_balls')) {
function recommend_balls($exclusion) {
    while (1) {
        $rand_ball = mt_rand(1,45);
        if (count($exclusion)) {
            if (in_array($rand_ball, $exclusion)) {
                continue;
            }
        }
        break;
    }
    return $rand_ball;
}
}

if (!function_exists('lotto_current_divide_num')) {
function lotto_current_divide_num() {
    $std_divide = 1133;
    $std_timestamp = strtotime('2024-08-17 20:00:00');
    $cur_timestamp = time();
    //604,800 (7일 unixtime)

    $cal_day1 = (int)(($cur_timestamp - $std_timestamp) / 604800);
    $cal_day2 = (int)(($cur_timestamp - $std_timestamp) % 604800);
    $divide = 0;
    $divide = $std_divide + $cal_day1;
    if ($cal_day2) $divide = $divide + 1;

    return $divide;
}
}

if (!function_exists('make_recommend_number')) {
function make_recommend_number($inclusion, $exclusion) {
    $lottocnt = 6;
    $loopcnt = $lottocnt - count($inclusion);

    $recommend = array();
    $recommend = $inclusion;

    $useballs = array();
    $useballs = array_unique(array_merge($inclusion, $exclusion));

    for ($i = 0; $i < $loopcnt; $i++) {
        $offer_ball = recommend_balls($useballs);
        $useballs[] = $offer_ball;
        $recommend[]= $offer_ball;
    }

    sort($recommend);
    return $recommend;
}
}

