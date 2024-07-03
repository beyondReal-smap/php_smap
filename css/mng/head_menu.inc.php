<?php
if ($_SESSION['_mt_level'] < 8 && $_SERVER['PHP_SELF'] != "./login") {
    alert("관리자만 접근할 수 있습니다.", APP_DOMAIN . "/mng/login");
}
?>

<?php if ($chk_webeditor == "Y") { ?>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" />
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript" src="<?= CDN_HTTP ?>/js/summernote-ko-KR.js"></script>
<?php } ?>

<div class="container-scroller">
    <!-- 상단바 시작 -->
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-brand-wrapper d-flex justify-content-center">
            <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand brand-logo" href="./">
                    <h3><img src="<?= CDN_HTTP ?>/img/logo_full.png" alt="<?= APP_TITLE ?>" /></h3>
                </a>
                <a class="navbar-brand brand-logo-mini" href="./">
                    <h3><img src="<?= CDN_HTTP ?>/img/logo_m.png" alt="<?= APP_TITLE ?>" /></h3>
                </a>
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize"><span class="mdi mdi-sort-variant"></span></button>
            </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
            <ul class="navbar-nav navbar-nav-right">
                <li class="nav-item nav-profile dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown"><span class="nav-profile-name"><?= $_SESSION['_mt_name'] ?> 님 반갑습니다.</span></a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <a href="../" class="dropdown-item" target="_blank"> <i class="mdi mdi-home text-primary"></i> 홈페이지</a>
                        <a href="./logout" class="dropdown-item"> <i class="mdi mdi-logout text-primary"></i> 로그아웃</a>
                    </div>
                </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas"> <span class="mdi mdi-menu"></span></button>
        </div>
    </nav>
    <!-- 상단바 끝 -->

    <div class="container-fluid page-body-wrapper">
        <!-- 왼쪽메뉴 시작 -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item<?php if ($chk_menu == '') { ?> active<?php } ?>">
                    <a class="nav-link" href="./">
                        <i class="mdi mdi-monitor-dashboard menu-icon"></i>
                        <span class="menu-title">대시보드</span>
                    </a>
                </li>

                <li class="nav-item<?php if ($chk_menu == '1') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#member" aria-expanded="<?php if ($chk_menu == '1') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="member">
                        <i class="mdi mdi-face menu-icon"></i>
                        <span class="menu-title">회원관리</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '1') { ?> show<?php } ?>" id="member">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '1' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./member_list">일반회원</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '1' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./member_retire_list">탈퇴회원</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item<?php if ($chk_menu == '2') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#group" aria-expanded="<?php if ($chk_menu == '2') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="group">
                        <i class="mdi mdi-bus-school menu-icon"></i>
                        <span class="menu-title">그룹관리</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '2') { ?> show<?php } ?>" id="group">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '2' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./group_list">그룹정보</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '2' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./schedule_list">일정정보</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '2' && $chk_sub_menu == '5') { ?> active<?php } ?>" href="./member_location_list">내장소정보</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '2' && $chk_sub_menu == '3') { ?> active<?php } ?>" href="./member_log_list">회원별 이동로그조회</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '2' && $chk_sub_menu == '4') { ?> active<?php } ?>" href="./recomand_location_list">추천장소</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item<?php if ($chk_menu == '3') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#fcm" aria-expanded="<?php if ($chk_menu == '3') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="fcm">
                        <i class="mdi mdi-teach menu-icon"></i>
                        <span class="menu-title">알림관리</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '3') { ?> show<?php } ?>" id="fcm">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '3' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./push_fcm_list">푸시관리</a>
                            </li>
                            <li class="nav-item d-none">
                                <a class="nav-link<?php if ($chk_menu == '3' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./push_email_list">메일관리</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item<?php if ($chk_menu == '6') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#plan" aria-expanded="<?php if ($chk_menu == '6') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="plan">
                        <i class="mdi mdi-cash-usd menu-icon"></i>
                        <span class="menu-title">유료플랜</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '6') { ?> show<?php } ?>" id="plan">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '6' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./plan_use_list">유료플랜 이용내역</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '6' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./recommend_list">추천인 입력내역</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '6' && $chk_sub_menu == '3') { ?> active<?php } ?>" href="./coupon_list">쿠폰관리</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '6' && $chk_sub_menu == '4') { ?> active<?php } ?>" href="./coupon_use_list">쿠폰사용내역</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item<?php if ($chk_menu == '4') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#service" aria-expanded="<?php if ($chk_menu == '4') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="service">
                        <i class="mdi mdi-desktop-mac menu-icon"></i>
                        <span class="menu-title">서비스관리</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '4') { ?> show<?php } ?>" id="service">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '4' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./qna_list">문의관리</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '4' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./faq_list">FAQ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '4' && $chk_sub_menu == '3') { ?> active<?php } ?>" href="./faq_category_list">FAQ 분류</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '4' && $chk_sub_menu == '4') { ?> active<?php } ?>" href="./notice_list">공지사항</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="d-none nav-item<?php if ($chk_menu == '5') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#analytics" aria-expanded="<?php if ($chk_menu == '5') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="analytics">
                        <i class="mdi mdi-air-horn menu-icon"></i>
                        <span class="menu-title">통계</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '5') { ?> show<?php } ?>" id="analytics">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '5' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./analytics_pay_stats">결제통계</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '5' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./analytics_connect_stats">접속통계</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item<?php if ($chk_menu == '90') { ?> active<?php } ?>">
                    <a class="nav-link" data-toggle="collapse" href="#setup" aria-expanded="<?php if ($chk_menu == '90') { ?>true<?php } else { ?>false<?php } ?>" aria-controls="setup">
                        <i class="mdi mdi-settings-outline menu-icon"></i>
                        <span class="menu-title">설정</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse<?php if ($chk_menu == '90') { ?> show<?php } ?>" id="setup">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '90' && $chk_sub_menu == '1') { ?> active<?php } ?>" href="./setup_form">기본설정</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '90' && $chk_sub_menu == '2') { ?> active<?php } ?>" href="./member_admin_list">관리자설정</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '90' && $chk_sub_menu == '3') { ?> active<?php } ?>" href="./banner_list">배너관리</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php if ($chk_menu == '90' && $chk_sub_menu == '4') { ?> active<?php } ?>" href="./popup_list">팝업관리</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
            <script>
                $(".nav-item, .navbar-brand").on('click', function(event) {
                    f_localStorage_reset();
                });
            </script>
        </nav>
        <!-- 왼쪽메뉴 끝 -->

        <div class="main-panel">