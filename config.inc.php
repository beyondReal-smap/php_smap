<?php

// 사용자의 브라우저 언어 설정 가져오기
$user_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
// 브라우저 언어 설정에 따라 언어 설정
switch ($user_language) {
    case 'ko':
        // 한국어 설정
        define("APP_AUTHOR", "SMAP - 자녀 일정·위치 확인");
        define("APP_TITLE", 'SMAP - 자녀 일정·위치 확인');
        define("DESCRIPTION", '자녀 위치 확인부터 일정 공유까지, 모든 것을 한 곳에서.');
        break;
    case 'en':
        // 영어 설정
        define("APP_AUTHOR", "SMAP - Child Schedule and Location Tracking");
        define("APP_TITLE", 'SMAP - Child Schedule and Location Tracking');
        define("DESCRIPTION", 'From child location tracking to schedule sharing, everything in one place.');
        break;
    case 'ja':
        // 일본어 설정
        define("APP_AUTHOR", "SMAP - 子供のスケジュール・位置確認");
        define("APP_TITLE", 'SMAP - 子供のスケジュール・位置確認');
        define("DESCRIPTION", '子供の位置確認からスケジュール共有まで、すべてを一か所で。');
        break;
    case 'id':
        // 인도네시아어 설정
        define("APP_AUTHOR", "SMAP - Jadwal dan Pelacakan Lokasi Anak");
        define("APP_TITLE", 'SMAP - Jadwal dan Pelacakan Lokasi Anak');
        define("DESCRIPTION", 'Dari pelacakan lokasi anak hingga berbagi jadwal, semuanya dalam satu tempat.');
        break;
    case 'ms':
        // 말레이시아어 설정
        define("APP_AUTHOR", "SMAP - Jadual dan Penjejakan Lokasi Anak");
        define("APP_TITLE", 'SMAP - Jadual dan Penjejakan Lokasi Anak');
        define("DESCRIPTION", 'Dari penjejakan lokasi anak hingga perkongsian jadual, semuanya dalam satu tempat.');
        break;
    case 'vi':
        // 베트남어 설정
        define("APP_AUTHOR", "SMAP - Lịch trình và Theo dõi Vị trí Trẻ em");
        define("APP_TITLE", 'SMAP - Lịch trình và Theo dõi Vị trí Trẻ em');
        define("DESCRIPTION", 'Từ theo dõi vị trí trẻ em đến chia sẻ lịch trình, tất cả trong một nơi.');
        break;
    case 'th':
        // 태국어 설정
        define("APP_AUTHOR", "SMAP - ตารางเวลาและการติดตามตำแหน่งของเด็ก");
        define("APP_TITLE", 'SMAP - ตารางเวลาและการติดตามตำแหน่งของเด็ก');
        define("DESCRIPTION", 'จากการติดตามตำแหน่งของเด็กไปจนถึงการแชร์ตารางเวลา ทุกอย่างอยู่ในที่เดียว');
        break;
    case 'es':
        // 스페인어 설정
        define("APP_AUTHOR", "SMAP - Horario y Seguimiento de Ubicación Infantil");
        define("APP_TITLE", 'SMAP - Horario y Seguimiento de Ubicación Infantil');
        define("DESCRIPTION", 'Desde el seguimiento de la ubicación del niño hasta compartir horarios, todo en un solo lugar.');
        break;
    case 'hi':
        // 힌디어 설정
        define("APP_AUTHOR", "SMAP - बच्चों का कार्यक्रम और स्थान ट्रैकिंग");
        define("APP_TITLE", 'SMAP - बच्चों का कार्यक्रम और स्थान ट्रैकिंग');
        define("DESCRIPTION", 'बच्चों के स्थान की ट्रैकिंग से लेकर कार्यक्रम साझा करने तक, सब कुछ एक ही जगह पर।');
        break;
    default:
        // 기본 영어 설정
        define("APP_AUTHOR", "SMAP - Child Schedule and Location Tracking");
        define("APP_TITLE", 'SMAP - Child Schedule and Location Tracking');
        define("DESCRIPTION", 'From child location tracking to schedule sharing, everything in one place.');
        break;
}

// 공통 설정
define("APP_DOMAIN", 'https://app2.smap.site');
define("CDN_HTTP", 'https://app2.smap.site');
define("DESIGN_HTTP", 'https://app2.smap.site/design');
define("KEYWORDS", '');
define("ADMIN_NAME", 'SMAP');

define("OG_IMAGE", CDN_HTTP.'/img/og-image.png');

//css, js 캐시 리셋
$v_txt = "20240522_8";

//키생성
define("DEBUG_JWT", '4zn0BeG0wAlqsJAjXXrSKA==');
define("SECRETKEY", 'Ga5JDKv/PSY3vyS0XF5AjZodEFMBM2Eca2hRCtqnLS0nVqnfBnCM/5/IML6rXPtroy/+s4+NUtdAD1lhhOaRNg==');
define("AUTH_SECRETKEY", '518cbe9ed50bf7e72913eb6b5a5e5fc6a8b99d56200ebda3a5bb365dbdccbdf6');
define("SERVER_NAME", 'API_SMAP');
define("NCPCLIENTID", 'unxdi5mt3f');
// define("GOOGLE_MAPS_API_KEY", 'AIzaSyD5TS3jrOEIotHnrxcLqwkqzUcd7lC1sjY'); //My First Project
define("GOOGLE_MAPS_API_KEY", 'AIzaSyBkWlND5fvW4tmxaj11y24XNs_LQfplwpw'); //com.dmonster.smap
define("NCPCLIENTSECRET", 'bKRzkFBbAvfdHDTZB0mJ81jmO8ufULvQavQIQZmp');
define("FIREBASEKEY", 'BOCzkX45zE3u0HFfNpfZDbUHH33OHNoe3k5KeTalEesHgnaBqCykjJUxnDcS6mv9MPSxU8EV3QHCL61gmwzkXlE');
define("KAKAO_JAVASCRIPT_KEY", 'e7e1c921e506e190875b4c8f4321c5ac');
define("KAKAO_NATIVEAPP_KEY", '56b34b5e5e538073805559cabc81e1d8');
define("KAKAO_JS_SHARE_TITLE", 'SMAP, 친구와 가족을 더 가까이');
define("KAKAO_JS_SHARE_DESC", 'SMAP으로 가족, 친구와 일정, 위치를 실시간 공유하세요!');
define("KAKAO_JS_SHARE_IMG", CDN_HTTP . '/img/kakao_link_img.png');
// define("KAKAO_JS_SHARE_IMG", CDN_HTTP . '/img/og-image.png');
define("ALIGO_USER_ID", 'smap2023');
define("ALIGO_KEY", '6uvw7alcd1v1u6dx5thv31lzic8mxfrt');
define("ALIGO_SENDER", '070-8065-2207');

define("NCP_ACCESS_KEY", "ncp_iam_BPAMKR5amCXCgRSDodA7");
define("NCP_SECRET_KEY", "ncp_iam_BPKMKR3E8B8h1J0FhAafnW8Cw83IKvDohl");

define("FMAIL", 'admin@smap.site');
define("FNAME", 'SMAP');

define("RECOM_CIRCLE", '5000'); //5km
define("LOCATION_MEMBER_NUM", '10'); //위치 그룹원 추가 최대값

$_SUB_HEAD_IMAGE = '';
$chk_webeditor = 'N';

//위치 설정값
$slt_mlt_accuacy = 50;
$slt_mlt_speed = 1;

//게시판 리스팅수
$n_limit_num = 10;
$gp_n_limit_num = 7;
$nt_n_limit_num = 7;

//이미지 업로드 가능 확장자
$ct_image_ext = "jpg;png;gif;jpeg;bmp";

//노이미지 링크
$ct_no_img_url = CDN_HTTP. "/img/no_image.png";

//노프로필이미지 링크
$ct_no_profile_img_url = CDN_HTTP."/img/no_profile.png";

//날씨아이콘 링크
$ct_no_img_dir = $_SERVER['DOCUMENT_ROOT']."/img/weather/";

//이미지 업로드 링크
$ct_img_dir = $_SERVER['DOCUMENT_ROOT']."/img/uploads";
$ct_img_url = CDN_HTTP."/img/uploads";

//pdf 업로드 링크
$ct_pdf_dir = $_SERVER['DOCUMENT_ROOT']."/img/pdf";
$ct_pdf_url = CDN_HTTP."/img/pdf";

//excel 업로드 링크
$ct_excel_dir = $_SERVER['DOCUMENT_ROOT']."/img/excel";
$ct_excel_url = CDN_HTTP."/img/excel";

//초대url
$ct_invite_url = CDN_HTTP."/invite?sit_code=";

//즐겨찾는위치 아이콘
$ct_map_recom_point_img_url = CDN_HTTP."/img/map_recom_point.png";