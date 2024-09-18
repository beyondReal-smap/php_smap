<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '2';
$h_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if (!$_GET['phoneNumber']) {
    p_alert(translate('잘못된 접근입니다.', $userLang), './'); // "잘못된 접근입니다." 번역
}
$phoneNumber = $_GET['phoneNumber'];
?>
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3"><?= translate("입력하신 휴대폰번호로
            인증번호를 보냈어요.", $userLang) ?></p> <!-- "입력하신 휴대폰번호로 인증번호를 보냈어요." 번역 -->
        <form action="">
            <input type="hidden" name="rtn_url" id="rtn_url" value="<?= $_GET['rtn_url'] ?>" />
            <input type="hidden" name="app_token" id="app_token" value="<?= $_SESSION['_mt_app_token'] ?>" />
            <input type="hidden" name="mt_hp_chk" id="mt_hp_chk" value="0">
            <input type="hidden" name="mt_num_chk" id="mt_num_chk" value="0">
            <div class="mt-5">
                <div class="ip_wr mt_hp_chk_msg">
                    <div class="ip_tit">
                        <h5><?= translate("인증번호", $userLang) ?></h5> <!-- "인증번호" 번역 -->
                    </div>
                    <div class="form-row mt_06">
                        <div class="col-12">
                            <input type="text" class="form-control input_time_input" placeholder="<?= translate("6자리 숫자", $userLang) ?>" name="mt_num" id="mt_num" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, ' $1');" maxlength="6"> <!-- "6자리 숫자" 번역 -->
                            <span class="fc_red fs_15 fw_300 bg_gray_100 input_time" id="cert_timer"></span>
                        </div>
                    </div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= translate("확인되었습니다.", $userLang) ?></div> <!-- "확인되었습니다." 번역 -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate("인증번호가 일치하지 않습니다.", $userLang) ?></div> <!-- "인증번호가 일치하지 않습니다." 번역 -->
                    <div class="form-text ip_invalid2"><i class="xi-error-o"></i> <?= translate("인증시간이 만료되었습니다.", $userLang) ?></div> <!-- "인증시간이 만료되었습니다." 번역 -->
                </div>
                <div class="ip_wr mt-5 mt_hp_msg">
                    <div class="ip_tit">
                        <h5 class=""><?= translate("입력하신 휴대전화번호로 인증번호가 발송됩니다.", $userLang) ?></h5> <!-- "입력하신 휴대전화번호로 인증번호가 발송됩니다." 번역 -->
                    </div>
                    <input type="tel" class="form-control" placeholder="010-0000-0000" id="mt_hp" name="mt_hp" value="<?= $_GET['phoneNumber'] ?>" readonly>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= translate("확인되었습니다.", $userLang) ?></div> <!-- "확인되었습니다." 번역 -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate("아이디를 다시 확인해주세요", $userLang) ?></div> <!-- "아이디를 다시 확인해주세요" 번역 -->
                    <button type="button" class="btn fs_12 fc_primary rounded-pill bg_secondary text-center px_12 py_07 text_dynamic w_fit h_fit_im d-flex align-items-center mt-3"><?= translate("인증번호가 안와요!", $userLang) ?> <i class="xi-arrow-right ml-2"></i></button> <!-- "인증번호가 안와요!" 번역 -->
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " id="auth_check_button" onclick="check_hp('auth');"><?= translate('입력했어요!', $userLang) ?></button> <!-- "입력했어요!" 번역 -->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block d-none" id="next_button" onclick="next_page()"><?= translate("휴대폰 인증을 완료했어요!", $userLang) ?></button> <!-- "휴대폰 인증을 완료했어요!" 번역 -->
            </div>
        </form>
    </div>
</div>

<script>
    <? if ($_SESSION['_number_chk'] == 0) { ?>
    check_hp('send', 'find');
    <?}?>
    //문자인증번호
    var timer;
    var isRunning = false;
    //휴대폰 인증
    function check_hp(type, state) {
        var mt_hp = $("#mt_hp").val();
        if (mt_hp.length < 10) {
            $(".mt_hp_msg").addClass("ip_invalid");
            $(".mt_hp_msg").removeClass("ip_valid");
            $("#mt_hp").focus();
            return false;
        } else {
            $(".mt_hp_msg").addClass("ip_valid");
            $(".mt_hp_msg").removeClass("ip_invalid");
        }
        //발송
        if (type == "send") {
            $.ajax({
                url: "./join_update.php",
                type: "POST",
                data: {
                    act: "send_hp",
                    mt_hp: mt_hp,
                    mt_state: state,
                },
                dataType: "json",
                success: function(d, s) {
                    console.log(d);
                    if (d.result == "_ok") {
                        $(".mt_hp_msg").addClass("ip_valid");
                        $(".mt_hp_msg").removeClass("ip_invalid");
                        $("#mt_hp").attr("readonly", true);
                        var leftSec = 180;
                        $("#cert_timer").css("display", "");
                        display = "#cert_timer";
                        // 이미 타이머가 작동중이면 중지
                        if (isRunning) {
                            clearInterval(timer);
                        }
                        startTimer(leftSec, display);
                        // 재전송
                        $("#mt_hp_chk").val("1");
                        $("#mt_num_chk").val("0");
                        $("#cert_timer").css("display", "");
                    } else {
                        clearInterval(timer);
                        $("#cert_timer").css("display", "none");
                        $("#mt_hp").attr("readonly", false);
                        $(".mt_hp_msg").addClass("ip_invalid");
                        $(".mt_hp_msg").removeClass("ip_valid");
                    }
                },
                error: function(d) {
                    console.log("error:" + d);
                },
            });
        }
        //인증
        if (type == "auth") {
            var mt_num = $("#mt_num").val();
            if (mt_num.length < 6) {
                $(".mt_hp_chk_msg").addClass("ip_invalid");
                $("#mt_num").focus();
                return false;
            }
            $.ajax({
                url: "./join_update.php",
                type: "POST",
                data: {
                    act: "auth_hp",
                    mt_hp: mt_hp,
                    mt_num: mt_num,
                },
                dataType: "json",
                success: function(d, s) {
                    console.log(d);
                    if (d.result == "_ok") {
                        $(".mt_hp_chk_msg").addClass("ip_valid");
                        $(".mt_hp_chk_msg").removeClass("ip_invalid");
                        $(".mt_hp_chk_msg").removeClass("ip_invalid2");
                        $("#mt_hp").attr("readonly", true);
                        $("#mt_num").attr("readonly", true);
                        var leftSec = 180;
                        $("#cert_timer").css("display", "");
                        display = "#cert_timer";
                        // 이미 타이머가 작동중이면 중지
                        if (isRunning) {
                            clearInterval(timer);
                        }
                        startTimer(leftSec, display);

                        // 데이타 성공일때 이벤트 작성
                        $("#mt_num_chk").val("1");
                        clearInterval(timer);
                        $("#cert_timer").css("display", "none");

                        // 성공일 때 버튼 표출
                        $("#auth_check_button").addClass("d-none");
                        $("#next_button").removeClass("d-none");

                        // 바로이동
                        window.location.replace('./change_psd?phoneNumber=' + mt_hp);
                    } else {
                        $(".mt_hp_chk_msg").addClass("ip_invalid");
                        $(".mt_hp_chk_msg").removeClass("ip_invalid2");
                        $(".mt_hp_chk_msg").removeClass("ip_valid");
                        $("#mt_num_chk").val("0");
                    }
                },
            });
        }
    }

    function startTimer(count, display) {
        var minutes, seconds;
        isRunning = true;
        timer = setInterval(function() {
            minutes = parseInt(count / 60, 10);
            seconds = parseInt(count % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            $(display).text(minutes + ":" + seconds);
            // 타이머 끝
            if (--count < 0) {
                clearInterval(timer);
                $(display).text(minutes + ":" + seconds);
                isRunning = false;
                $(".mt_hp_chk_msg").addClass("ip_invalid2");
                $(".mt_hp_chk_msg").removeClass("ip_valid");
                $(".mt_hp_chk_msg").removeClass("ip_invalid");
            }
        }, 1000);
    }

    function next_page() {
        var mt_hp = $("#mt_hp").val();
        var mt_hp_chk = $("#mt_hp_chk").val();
        var mt_num_chk = $("#mt_num_chk").val();

        if (mt_hp_chk == '1' && mt_num_chk == '1') {
            window.location.replace('./change_psd?phoneNumber=' + mt_hp);
        }
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>