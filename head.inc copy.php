<?php
if ($_SUB_HEAD_TITLE) {
    $_APP_TITLE = APP_TITLE . ' - ' . $_SUB_HEAD_TITLE;
} else {
    $_APP_TITLE = APP_TITLE;
}

if ($_SUB_HEAD_IMAGE) {
    $_OG_IMAGE = $_SUB_HEAD_IMAGE;
} else {
    $_OG_IMAGE = OG_IMAGE . '?v=' . $v_txt;
}

// www 있으면 www 제거하기
$base_URL = "";
if (!preg_match('/www/', $_SERVER['SERVER_NAME']) == true) {
    // www 없을때
} else {
    // www 있을때
    $base_URL = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    $base_URL .= ($_SERVER['SERVER_PORT'] != '80') ? $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] : str_replace("www.", "", $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];

    header('Location: ' . $base_URL);
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta name="Generator" content="<?= APP_AUTHOR ?>" />
    <meta name="Author" content="<?= APP_AUTHOR ?>" />
    <meta name="Keywords" content="<?= KEYWORDS ?>" />
    <meta name="Description" content="<?= DESCRIPTION ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-title" content="<?= $_APP_TITLE ?>" />
    <meta content="telephone=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta property="og:image" content="<?= $_OG_IMAGE ?>" />
    <meta property="og:image:width" content="151" />
    <meta property="og:image:height" content="79" />
    <meta property="og:title" content="<?= $_APP_TITLE ?>" />
    <meta property="og:description" content="<?= DESCRIPTION ?>" />
    <meta property="og:url" content="<?= APP_DOMAIN . $_SERVER['REQUEST_URI'] ?>" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?= CDN_HTTP ?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= CDN_HTTP ?>/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= CDN_HTTP ?>/img/favicon-16x16.png">
    <link rel="manifest" href="<?= CDN_HTTP ?>/img/site.webmanifest">
    <link rel="mask-icon" href="<?= CDN_HTTP ?>/img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?= $_APP_TITLE ?></title>

    <!-- 제이쿼리 -->
    <script src="<?= CDN_HTTP ?>/js/jquery.min.js"></script>

    <!--부트스트랩-->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/boot_custom.css">
    <script src="<?= CDN_HTTP ?>/js/bootstrap.bundle.min.js"></script>

    <!-- 로티 -->
    <script src="<?= CDN_HTTP ?>/js/lottie-player.js"></script>

    <!-- xe아이콘 -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/xeicon.min.css">

    <!-- ie css 변수적용 -->
    <script src="<?= CDN_HTTP ?>/js/ie11CustomProperties.min.js"></script>

    <!-- 폰트-->
    <link href="https://cdn.jsdelivr.net/gh/sun-typeface/SUITE/fonts/variable/woff2/SUITE-Variable.css" rel="stylesheet">

    <!-- JS -->
    <script src="<?= CDN_HTTP ?>/js/custom.js?v=<?= $v_txt ?>"></script>

    <!-- swiper -->
    <script src="<?= CDN_HTTP ?>/js/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/swiper-bundle.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/custom.css?v=<?= $v_txt ?>">

    <!-- DEV -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/default_dev.css?v=<?= $v_txt ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <script src="<?= CDN_HTTP ?>/js/jalert.js?v=<?= $v_txt ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script type="text/javascript">
        <!--
        $.extend($.validator.messages, {
            required: "필수 항목입니다.",
            remote: "항목을 수정하세요.",
            email: "유효하지 않은 E-Mail주소입니다.",
            url: "유효하지 않은 URL입니다.",
            date: "올바른 날짜를 입력하세요.",
            dateISO: "올바른 날짜(ISO)를 입력하세요.",
            number: "유효한 숫자가 아닙니다.",
            digits: "숫자만 입력 가능합니다.",
            creditcard: "신용카드 번호가 바르지 않습니다.",
            equalTo: "같은 값을 다시 입력하세요.",
            extension: "올바른 확장자가 아닙니다.",
            maxlength: $.validator.format("{0}자를 넘을 수 없습니다. "),
            minlength: $.validator.format("{0}자 이상 입력하세요."),
            rangelength: $.validator.format("문자 길이가 {0} 에서 {1} 사이의 값을 입력하세요."),
            range: $.validator.format("{0} 에서 {1} 사이의 값을 입력하세요."),
            max: $.validator.format("{0} 이하의 값을 입력하세요."),
            min: $.validator.format("{0} 이상의 값을 입력하세요."),
        });

        $.validator.setDefaults({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            showErrors: function(errorMap, errorList) {
                if (this.numberOfInvalids()) { // 에러가 있으면
                    $.alert({
                        title: '',
                        type: 'blue',
                        typeAnimated: true,
                        content: errorList[0].message,
                        buttons: {
                            confirm: {
                                btnClass: 'btn-default btn-lg btn-block',
                                text: "확인",
                                action: function() {
                                    errorList[0].element.focus()
                                },
                            },
                        },
                    });
                }
            }
        });
        //
        -->
    </script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
    <script type="text/javascript">
        <!--
        moment.locale('ko');
        //
        -->
    </script>

    <script src="<?= CDN_HTTP ?>/js/default_dev.js?v=<?= $v_txt ?>"></script>

    <script>
        $(document).ready(function() {
            <?php
            //안드로이드 웹뷰로 페이지명을 전달해서 홈(index)면 뒤로가기2번으로 종료가 작동하도록 합니다.
            $page_nm = get_page_nm();
            ?>
            // window.smapIos.pageType('<?= $page_nm ?>');
            var message = {
                "type": "pageType",
                "param": "<?= $page_nm ?>"
            };
            if (isAndroid()) {
                window.smapAndroid.pageType('<?= $page_nm ?>');
                // 입력폼 작성시 키패드 감지하여 하단메뉴를 노출/미노출
                if ('visualViewport' in window) {
                    const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
                    window.visualViewport.addEventListener('resize', function(event) {
                        if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                            // 키패드가 열렸을 때 실행할 코드
                            $('.b_menu').hide(); // 하단 메뉴 숨기기
                            $('.opt_bottom').hide(); // 하단 메뉴 숨기기
                        } else {
                            // 키패드가 닫혔을 때 실행할 코드
                            $('.b_menu').show(); // 하단 메뉴 표시하기
                            $('.opt_bottom').show(); // 하단 메뉴 숨기기
                        }
                    });
                }
            } else if (isiOS()) {
                window.webkit.messageHandlers.smapIos.postMessage(message);

                /*if (isiOS()) {
                    // 키패드 열림 이벤트를 감지
                    const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
                    window.visualViewport.addEventListener('resize', function(event) {
                        if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                            <?php if ($b_menu == '4') { ?>
                                // 키패드가 열릴 때 스크롤을 막음
                                preventScrollWhenKeypadOpen();
                                // 키패드가 열렸을 때 실행할 코드
                                $('.b_menu').hide(); // 하단 메뉴 숨기기
                                $('.opt_bottom').hide(); // 하단 메뉴 숨기기 
                            <?php } else { ?>
                                // 키패드가 열렸을 때 실행할 코드
                                $('.b_menu').hide(); // 하단 메뉴 숨기기
                                $('.opt_bottom').hide(); // 하단 메뉴 숨기기
                            <?php } ?>
                        } else {
                            <?php if ($b_menu == '4') { ?>
                                // 키패드가 열릴 때 스크롤을 막음
                                preventScrollWhenKeypadOpen();
                                // 키패드가 닫혔을 때 실행할 코드
                                $('.b_menu').show(); // 하단 메뉴 표시하기
                                $('.opt_bottom').show(); // 하단 메뉴 숨기기
                            <?php } else { ?>
                                // 키패드가 닫혔을 때 실행할 코드
                                $('.b_menu').show(); // 하단 메뉴 표시하기
                                $('.opt_bottom').show(); // 하단 메뉴 숨기기
                                resetBButtonPosition();
                            <?php } ?>
                        }
                    });
                }*/
                var b_botton = document.querySelector('.b_botton');
                var layoutViewport = document.getElementById('layoutViewport');
                var viewport = window.visualViewport;

                function viewportHandler() {
                    <?php if ($b_menu == '4') { ?>
                        // 키패드가 열릴 때 스크롤을 막음
                        preventScrollWhenKeypadOpen();
                    <?php } ?>
                    // 계산된 바의 위치
                    var offsetY = viewport.height - layoutViewport.getBoundingClientRect().height + viewport.offsetTop;
                    // 바의 위치 조정
                    setTimeout(() => {
                        b_botton.style.transform = 'translateX(-50%) translateY(' + offsetY + 'px)';
                    }, 200);
                }

                // 페이지 로드 시 초기 설정
                // viewportHandler();
                // 이벤트 리스너 등록
                window.visualViewport.addEventListener('scroll', viewportHandler);
                window.visualViewport.addEventListener('resize', viewportHandler);
            }
            /* //입력폼 작성시 키패드 감지하여 하단메뉴를 노출/미노출
            if ('visualViewport' in window) {
                const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
                window.visualViewport.addEventListener('resize', function(event) {
                    if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                        $('.b_menu').hide();
                    } else {
                        $('.b_menu').show();
                    }
                });
            } */
        });

        // 키보드가 열릴 때 b_botton을 키패드 위로 이동하는 함수
        function moveBButtonAboveKeypad() {
            var bBotton = document.querySelector('.b_botton');
            var viewportHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
            var bBottonRect = bBotton.getBoundingClientRect();
            if (bBottonRect.bottom >= viewportHeight) {
                // 버튼이 화면 밖으로 나가면 키패드 위로 이동
                var offset = bBottonRect.bottom - viewportHeight + 10; // 여백 추가
                bBotton.style.bottom = offset + 'px';
            } else {
                bBotton.style.bottom = viewportHeight + 'px';

            }
        }
        // 키패드가 닫힐 때 b_botton을 원래 위치로 이동하는 함수
        function resetBButtonPosition() {
            var bBotton = document.querySelector('.b_botton');
            // b_botton 요소를 원래 위치로 이동
            bBotton.style.position = 'fixed'; // 원래 위치로 되돌립니다.
            bBotton.style.bottom = '0';
        }
        // iOS에서 키보드가 열릴 때 스크롤을 막는 함수
        function preventScrollWhenKeypadOpen() {
            // iOS인지 확인
            const isiOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            if (!isiOS) return; // iOS가 아니면 함수 종료

            // 키보드가 열릴 때 실행되는 이벤트 핸들러 등록
            window.addEventListener('focusin', function() {
                // 터치 이벤트가 발생할 때 스크롤을 막음
                window.addEventListener('touchmove', preventDefaultTouch, {
                    passive: false
                });
            });

            // 키보드가 닫힐 때 실행되는 이벤트 핸들러 등록
            window.addEventListener('focusout', function() {
                // 터치 이벤트에서 스크롤 막는 것을 제거
                window.removeEventListener('touchmove', preventDefaultTouch);
            });

            // 터치 이벤트에서 스크롤을 막는 함수
            function preventDefaultTouch(event) {
                event.preventDefault();
            }
        }

        //안드로이드>웹뷰 스크립트 실행
        function backPress() {
            history.back();
        }

        function f_back_chk(v) {
            if (v == 'form_add_info') {
                $.confirm({
                    type: "blue",
                    typeAnimated: true,
                    title: "주의",
                    content: "메인으로 이동하시겠습니까? 추가정보는 설정에서 입력가능합니다.",
                    buttons: {
                        confirm: {
                            text: "확인",
                            action: function() {
                                location.href = './';
                            },
                        },
                        cancel: {
                            btnClass: "btn-outline-default",
                            text: "취소",
                            action: function() {
                                close();
                            },
                        },
                    },
                });
            }

            return false;
        }

        function isAndroid() {
            return navigator.userAgent.match(/Android/i);
        }

        function isiOS() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        }
    </script>

    <?php //if ($chk_admin) { 
    ?>
    <script src="<?= CDN_HTTP ?>/lib/fakeloader/fakeloader.min.js?v=<?= $v_txt ?>">
    </script>
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/lib/fakeloader/fakeloader.css?v=<?= $v_txt ?>">
    <script type="text/javascript">
        <!--
        $(document).ready(
            function() {
                setTimeout(() => {
                    window.FakeLoader.init({
                        auto_hide: true
                    });
                }, 100);

            }
        );
        //
        -->
    </script>
    <?php //} 
    ?>
</head>

<?php
//읽지 않은 알림이 있는지?
if ($_SESSION['_mt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('plt_read_chk', 'N');
    $DB->where('plt_show', 'Y');
    $row_alarm = $DB->getone('push_log_t', 'count(*) as cnt');

    if ($row_alarm['cnt'] > 0) {
        $alarm_t = ' on';
    } else {
        $alarm_t = '';
    }
    // 유료회원 마감되었는지 확인
    $current_date = date("Y-m-d H:i:s");
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('mt_level', 5);
    $plan_End_row = $DB->getone('member_t');
    if ($plan_End_row['mt_idx'] && $plan_End_row['mt_level'] == 5 && $current_date > $plan_End_row['mt_plan_date']) {
        unset($arr_query);
        $arr_query = array(
            'mt_level' => '2'
        );
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->update('member_t', $arr_query);
    }
    // 그룹원 초대코드 만료 확인
    group_invite_del($_SESSION['_mt_idx']);
    member_location_history_delete();
}
?>

<body id="wrap">
    <?php if ($h_menu == '1') { ?>
        <!-- head_01 -->
        <div class="h_menu head_01">
            <div class="logo_wr"><a class="logo" href="<?= CDN_HTTP ?>/"><img src="<?= CDN_HTTP ?>/img/logo.png" alt="홈으로 이동"></a></div>
            <div class="mr-5 h_tit">
                <p class="fs_18 fw_600 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div class="d-flex">
                <a href="./setting" class="mr-3"><img src="<?= CDN_HTTP ?>/img/ico_set.png" width="24px" alt="설정" /></a>
                <a href="./alarm_list" class="arm_btn <?= $alarm_t ?>"><img src="<?= CDN_HTTP ?>/img/ico_arm.png" width="24px" alt="알람" /></a>
            </div><!-- on 추가되면 활성화-->
        </div>
    <?php } elseif ($h_menu == '2') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="history.back();"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '3') { ?>
        <!-- head_03 -->
        <div class="h_menu head_03">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="location.href='<?= $h_url ?>'"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="h_tit">
                <p class="fs_17 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./inquiry_form'">문의하기</button></div>
        </div>
    <?php } elseif ($h_menu == '4') { ?>
        <!-- head_07 -->
        <div class="h_menu head_04">
            <div class="h_tit fs_22 fw_700">
                <p class="fs_17 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div><a href="alarm_list" class="arm_btn<?= $alarm_t ?>"><img src="<?= CDN_HTTP ?>/img/ico_arm.png" width="24px" alt="알람" /></a></div><!-- on 추가되면 활성화-->
        </div>
    <?php } elseif ($h_menu == '5') { ?>
        <!-- head_08 -->
        <div class="h_menu head_05 bg_main">
            <div class="h_tit ">
                <p class="fs_22 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '6') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="location.href='<?= $h_url ?>'"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '7') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="<?= $h_func ?>"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '8') { ?>
        <!-- <div class="h_menu head_03">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="history.back();"><img src="./img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="h_tit">
                <p class="fs_17 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./plan_info'">플랜</button></div>
        </div> -->
        <div class="h_menu head_07">
            <div class="h_tit">
                <p class="fs_22 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./plan_info'">플랜</button></div>
        </div>
    <?php } ?>