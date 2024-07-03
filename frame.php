<?php

$vv_txt = time();

opcache_reset();

?>

<!doctype html>

<html lang="ko">



<head>

    <meta charset="UTF-8">

    <meta name="Generator" content="SMAP - 자녀 일정·위치 확인">

    <meta name="Author" content="SMAP - 자녀 일정·위치 확인">

    <meta name="Keywords" content="SMAP - 자녀 일정·위치 확인">

    <meta name="Description" content="자녀 위치 확인부터 일정 공유까지, 모든 것을 한 곳에서">

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



    <!--부트스트랩-->

    <link rel="stylesheet" href="./css/boot_custom.css">



    <!-- ie css 변수적용 -->

    <script src="./js/ie11CustomProperties.min.js"></script>



    <!-- 폰트-->

    <link href="https://cdn.jsdelivr.net/gh/sun-typeface/SUITE/fonts/variable/woff2/SUITE-Variable.css" rel="stylesheet">



    <!-- JS -->

    <!-- <script src="./js/custom.js" defer></script> -->



    <!-- CSS -->

    <link rel="stylesheet" href="./css/custom_pc.css?<?= $vv_txt ?>">



    <script>

        // 스크롤 이벤트 핸들러 등록

        window.addEventListener('scroll', function() {

            // 현재 보이는 영역의 크기와 위치를 가져옵니다.

            var visibleHeight = window.visualViewport.height;

            var scrollTop = window.visualViewport.pageTop;



            // 만약 스크롤이 가상 영역을 벗어난 경우에는 스크롤을 막습니다.

            if (window.scrollY + visibleHeight > document.body.offsetHeight - 2) {

                // 스크롤이 가상 영역을 벗어나면 스크롤 위치를 조정하여 가상 영역을 벗어나지 않도록 합니다.

                window.scrollTo(0, document.body.offsetHeight - visualViewport.height - 1);

            }

        });

    </script>







</head>

<style>

    * {

        overscroll-behavior: none;

    }

</style>



<body>

    <div id="wrap">

        <div class="left_wrapper">

            <div class="d-flex flex-column justify-content-center min_h_100">

                <div class="left_logo">

                    <a class="logo" href="https://app.smap.site/"><img src="https://app.smap.site/img/logo.png" alt="홈으로 이동"></a>

                </div>

                <div class="left_cont">

                    <div class="fs_26 fw_600 line_h1_3">언제 어디서든<br>자녀의 일정과 위치를<br>확인하세요</div>

                    <div class="form-row mt-5 mb-5 pb-5">

                        <div class="col">

                            <a type="button" class="btn btn-primary btn-lg btn-block rounded" href="https://youtube.com/shorts/HlKc-1KfAY8?feature=share" target="_blank">회원가입<br>Guide</a>

                        </div>

                        <div class="col">

                            <a type="button" class="btn btn-primary btn-lg btn-block rounded" href="https://youtube.com/shorts/C8RjLPjSVps?feature=share" target="_blank">그룹생성<br>Guide</a>

                        </div>

                        <div class="col">

                            <a type="button" class="btn btn-primary btn-lg btn-block rounded" href="https://youtu.be/Ba83-yfjvBQ" target="_blank">일정입력<br>Guide</a>

                        </div>

                    </div>

                    <div class="mt_20 mb_20 fs_16 line_h1_3">모바일로 서비스를 이용하고 싶으시다면<br><span class="fw_800">QR코드</span>를 이용해보세요</div>

                    <div class="form-row ">

                        <div class="col-6">

                            <a class="btn_qr fs_15 fw_800 d-flex" href="링크" target="_blank">

                                <img src="./img/go_app_ios.png" alt="앱스토어 다운로드">

                                <div class="btn_qr_text d-flex align-items-center justify-content-center">

                                    <img src="./img/go_app_ios_ic.png">

                                    <div class="text-left line_h1_3">App Store<br>바로가기</div>

                                </div>

                            </a>

                        </div>

                        <div class="col-6">

                            <a class="btn_qr fs_15 fw_800 d-flex" href="https://play.google.com/store/apps/details?id=com.dmonster.smap&pli=1" target="_blank">

                                <img src="./img/go_app_ad.png" alt="play 스토어 다운로드">

                                <div class="btn_qr_text d-flex align-items-center justify-content-center">

                                    <img src="./img/go_app_ad_ic.png">

                                    <div class="text-left line_h1_3">Google Play<br>바로가기</div>

                                </div>

                            </a>

                        </div>

                    </div>

                    <div class="d-flex align-items-center justify-content-center mt_20">

                        <a class="px-3" href="https://schedulemap.notion.site/30b32b5ad0bc4f99a39b28c0fe5f1de4" target="_blank">이용약관</a>

                        <span class="fc_gray_500">|</span>

                        <a class="px-3" href="https://schedulemap.notion.site/2ac62e02f97b4d61945d68c2d89109e9?pvs=4" target="_blank">개인정보처리방침</a>

                    </div>

                </div>

            </div>

        </div>

        <div class="right_wrapper">

            <!-- iframe 영역 -->

            <iframe src="https://app.smap.site/" height="100%" width="100%"></iframe>

            <!-- iframe 영역 -->

        </div>

    </div>

</body>



</html>