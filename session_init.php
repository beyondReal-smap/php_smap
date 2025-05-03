<?php
// 세션이 시작되지 않은 경우에만 시작
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 기본 세션 변수 초기화
if (!isset($_SESSION['_mt_idx'])) $_SESSION['_mt_idx'] = 0;
if (!isset($_SESSION['_mt_level'])) $_SESSION['_mt_level'] = 0;
if (!isset($_SESSION['_mt_token_id'])) $_SESSION['_mt_token_id'] = '';
if (!isset($_SESSION['_mt_lat'])) $_SESSION['_mt_lat'] = 0;
if (!isset($_SESSION['_mt_long'])) $_SESSION['_mt_long'] = 0;
if (!isset($_SESSION['_mt_file1'])) $_SESSION['_mt_file1'] = '';
if (!isset($_SESSION['_mt_nickname'])) $_SESSION['_mt_nickname'] = '';
if (!isset($_SESSION['_mt_name'])) $_SESSION['_mt_name'] = '';

// 사용자 기본 언어 설정
if (!isset($_SESSION['user_lang'])) {
    $_SESSION['user_lang'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}

// 세션 디버깅을 위한 로그
error_log("Session initialized/checked. Session ID: " . session_id());
error_log("Session mt_idx: " . $_SESSION['_mt_idx']); 