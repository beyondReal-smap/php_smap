<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_GET['sit_code']) {
    $DB->where('sit_code', $_GET['sit_code']);
    $sit_row = $DB->getone('smap_invite_t');

    if ($sit_row['sit_idx']) {
        if ($sit_row['sit_status'] != '2') {
            alert('이미 사용한 초대코드입니다.', 'back');
        } else {
            $DB->where('mt_idx', $sit_row['mt_idx']);
            $DB->where('mt_show', 'Y');
            $DB->where('mt_status', '1');
            $owner_row = $DB->getone('member_t');
        }
    } else {
        alert('존재하지 않는 초대코드입니다.', 'back');
    }
} else {
    // alert('잘못된 접근입니다', 'back');
}
?>
<style>
    .sub_pg {
        /* background: linear-gradient(#0046FE, #FFFFFF 320px); */
        padding-bottom: 0;
        height: 100vh;
        padding-top: 0;
        overflow: auto;
        background-color: #0046FE;
    }

    .ivt_pg .sub_pg_in {
        width: 100%;
        height: 100vh;
    }

    /* 이미지 크롭 */
    .ev_bg {
        width: 100%;
        overflow: hidden;
        position: relative;
        display: block;
    }

    .ev_bg::after {
        content: "";
        display: block;
        padding-bottom: calc(779 / 360 * 100%);
        background-image: url(./img/invite_bg.jpg);
        background-repeat: no-repeat;
        background-size: cover;
    }

    .ev_bg>img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        image-rendering: -webkit-optimize-contrast;
    }
</style>
<div class="container sub_pg ivt_pg px-0">
    <input type="hidden" id="sit_code" name="sit_code" value="<?= $_GET['sit_code'] ?>">
    <div class="sub_pg_in">
        <div class="ev_bg">
            <div class="ivt_inst_box">
                <p class="ivt_inst_tit line_h1_4 text_dynamic"><span><?= $owner_row['mt_name'] ? $owner_row['mt_name'] : '스맵' ?></span> 님이
                    초대링크를 보냈어요!</p>
                <p class="ivt_inst_subtxt">앱설치 후 서비스를 이용해주세요.</p>
                <p class="ivt_inst_subtxt">초대코드 : <?= $_GET['sit_code'] ?></p>
            </div>
            <div class="ivt_btn_wrap">
                <div class="ivt_btn_inner">
                    <!-- <a href="https://drive.google.com/file/d/18mFoM_BFrhP-rj0MNnfS2ysTlDUkGaV8/view" class="btn go_site_btn" target="_blank"><span>SMAP으로 바로가기<i class="xi-long-arrow-right ml-3"></i></span></a> -->
                    <a onclick="f_app_open('<?= $_GET['sit_code'] ?>')" class="btn go_site_btn" target="_blank"><span>SMAP으로 바로가기<i class="xi-long-arrow-right ml-3"></i></span></a>

                    <a class="btn go_site_btn mt-2" id="copy_sitcode"><span>초대코드 복사하기</span></a>

                    <div class="inv_downbtn_wrap">
                        <a href="https://play.google.com/store/apps/details?id=com.dmonster.smap" class="go_appdown_btn" target="_blank">
                            <div class="d-flex align-items-center">
                                <img src="img/ic_googldplay.png">
                                <div class="btn-textwrap">
                                    <small>안드로이드</small>
                                    Google Play
                                </div>
                            </div>
                        </a>
                        <a href="https://apps.apple.com/app/id6480279658" class="go_appdown_btn" target="_blank">
                            <div class="d-flex align-items-center">
                                <img src="img/ic_apple.png">
                                <div class="btn-textwrap">
                                    <small>IOS</small>
                                    App Store
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var visitTime = (new Date()).getTime();
    // 화면 밖으로 나가거나, 안으로 들어왔을때
    $(window).on("blur focus", function(e) {
        prevType = $(this).data("prevType");

        if (prevType != e.type) { //  reduce double fire issues
            switch (e.type) {
                // 화면 밖으로 나갔을때
                case "blur":
                    break;
                    // 화면 안으로 들어왔을때
                case "focus":
                    break;
            }
        }

        $(this).data("prevType", e.type);

    });
    document.querySelector("#copy_sitcode").addEventListener("click", function() {
        var tempElem = document.createElement('textarea');
        tempElem.value = $('#sit_code').val();
        document.body.appendChild(tempElem);

        tempElem.select();
        document.execCommand("copy");
        document.body.removeChild(tempElem);
        jalert('초대코드가 복사되었습니다.');
    });

    function isAppInstalled() {
        // 앱이 설치되어 있는지 여부를 확인할 수 없는 경우 항상 false를 반환
        return false;
    }

    function f_app_open(sit_code) {
        if (isAndroid()) {
            var appTimeout = setTimeout(function() {
                location.href = 'intent://smap?invitation_code=' + sit_code + '#Intent;scheme=smap_app;action=android.intent.action.VIEW;category=android.intent.category.BROWSABLE;package=com.dmonster.smap;end'; // URL 스킴은 smap_app://입니다.
                // Timeout 후에도 실행될 수 있으므로 앱이 설치되지 않았을 경우 설치 페이지로 이동
                window.location.replace("https://play.google.com/store/apps/details?id=com.dmonster.smap");
            }, 500);
        } else if (isiOS()) {
            // iOS 실행
            setTimeout(function() {
                if (prevType == undefined || prevType == 'focus') {
                    window.location = "https://itunes.apple.com/kr/app/podcast/id6480279658";
                }
            }, 1000);
            window.location = 'smapapp://?invitation_code=' + sit_code;
            /*
            setTimeout(function() {
                // location.href = "https://apps.apple.com/app/smapapp/id6480279658";
                location.href = "itms-apps://itunes.apple.com/app/id6480279658";
            }, 2500);
            setTimeout(function() {
                location.replace('smapapp://?invitation_code=' + sit_code); // URL 스킴은 smapapp://입니다.
            }, 0);
            */
            /*
            var appTimeout = setTimeout(function() {
                location.replace('smapapp://?invitation_code=' + sit_code); // URL 스킴은 smapapp://입니다.
                // Timeout 후에도 실행될 수 있으므로 앱이 설치되지 않았을 경우 설치 페이지로 이동
                window.location.replace("https://apps.apple.com/app/smapapp/id6480279658");
            }, 500);
            */
        }
    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
    }
</script>