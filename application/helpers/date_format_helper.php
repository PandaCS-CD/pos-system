<?php

defined('BASEPATH') or exit('No direct script access allowed');

//                                 input     format   พ.ศ/ค.ศ 
// ตัวอย่างการใช้ format_datetime('2025-08-15', 'd-m-Y', 'th,en');
function format_datetime($datetime = false, $format = 'Y-m-d', $lang = 'en')
{
    if (!$datetime) {
        return '';
    }

    $datetime = trim($datetime);

    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})(.*)$/', $datetime, $m)) {
        $day = (int) $m[1];
        $month = (int) $m[2];
        $year = (int) $m[3];
        $time = trim($m[4]);
    } elseif (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})(.*)$/', $datetime, $m)) {
        $year = (int) $m[1];
        $month = (int) $m[2];
        $day = (int) $m[3];
        $time = trim($m[4]);
    } else {
        $timestamp = strtotime($datetime);
        if ($timestamp === false) return '';
        return date($format, $timestamp);
    }

    if ($year > 2400) {
        $year -= 543;
    }

    $timestamp = strtotime("$year-$month-$day $time");

    if (!$timestamp) return '';

    // ====== เดือนย่อ ======
    $months_en = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $months_th = ["", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."];

    if (strtolower($lang) === 'th') {
        $year_th = date('Y', $timestamp) + 543;
        $month_th = $months_th[(int)date('n', $timestamp)];

        $result = date($format, $timestamp);
        $result = str_replace(date('Y', $timestamp), $year_th, $result);
        $result = str_replace(date('M', $timestamp), $month_th, $result);

        return $result;
    } else {
        $month_en = $months_en[(int)date('n', $timestamp)];
        $result = date($format, $timestamp);
        $result = str_replace(date('M', $timestamp), $month_en, $result);
        return $result;
    }
}
