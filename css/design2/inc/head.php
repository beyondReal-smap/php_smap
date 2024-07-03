<?php
   $v_txt = time();
   opcache_reset();
?>

<!doctype html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="Generator" content="smap">
	<meta name="Author" content="smap">
	<meta name="Keywords" content="smap">
	<meta name="Description" content="우리의 슬기로운 루틴생활">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<meta name="apple-mobile-web-app-title" content="smap">
	<meta content="telephone=no" name="format-detection">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta property="og:title" content="smap">
	<meta property="og:description" content="smap">
	<meta property="og:image" content="./img/og-image.png">
	<link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="./img/favicon-16x16.png">
	<link rel="manifest" href="">
	<link rel="mask-icon" href="" color="#ffffff">
	<meta name="msapplication-TileColor" content="">
	<meta name="theme-color" content="">
	<title>smap</title>

	<!-- 제이쿼리 -->
	<script src="./js/jquery.min.js"></script>

	<!--부트스트랩-->
    <link rel="stylesheet" href="./css/boot_custom.css">
	<script src="./js/bootstrap.bundle.min.js"></script>

    <!-- 로티 -->
    <script src="./js/lottie-player.js"></script>

	<!-- xe아이콘 -->
	<link rel="stylesheet" href="./css/xeicon.min.css">

	<!-- ie css 변수적용 -->
	<script src="./js/ie11CustomProperties.min.js"></script>

	<!-- 폰트-->
    <link href="https://cdn.jsdelivr.net/gh/sun-typeface/SUITE/fonts/variable/woff2/SUITE-Variable.css" rel="stylesheet">
    
    
    <!-- 스와이퍼 -->
    <script src="./js/swiper-bundle.min.js"></script>
	<link rel="stylesheet" href="./css/swiper-bundle.min.css">

	<!-- JS -->
	<script src="./js/custom.js" defer></script>

	<!-- CSS -->
	<link rel="stylesheet" href="./css/custom.css">
	<link rel="stylesheet" href="./css/design_sy.css">
    <link rel="stylesheet" href="./css/design_jh.css">


</head>

<body id="wrap">
<?php
    if ($_GET['hd_num'] == '1') {
        include_once('./inc/head_01.php');
    } else if ($_GET['hd_num'] == '2') {
        include_once('./inc/head_02.php');
    } else if ($_GET['hd_num'] == '3') {
        include_once('./inc/head_03.php');
    } else if ($_GET['hd_num'] == '4') {
        include_once('./inc/head_04.php');
    } else if ($_GET['hd_num'] == '5') {
        include_once('./inc/head_05.php');
    }else {
        
    }
?>