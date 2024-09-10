<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$h_menu = '7';
$h_func = "location.replace('./join_entry')";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

$phoneNumber = $_GET['phoneNumber'];
?>
<style>
    .hidden {
        display: none !important;
    }

    #wrap {
        min-height: 100vh;
        height: 100vh;
        position: relative;
        overflow-y: auto;
    }

    #layoutViewport {
        position: fixed;
        width: 100%;
        height: 100%;
        visibility: hidden;
        background: #FAF2CE;
    }
</style>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">입력하신 휴대폰번호로
            인증번호를 보냈어요.</p>
        <form onsubmit="check_hp('auth'); return false;">
            <input type="hidden" name="HTTP_REFERER" id="HTTP_REFERER" value="<?= $_SERVER["HTTP_REFERER"] ?>">
            <input type="hidden" name="app_token" id="app_token" value="<?= $_SESSION['_mt_token_id'] ?>" />
            <input type="hidden" name="mt_hp_chk" id="mt_hp_chk" value="0">
            <input type="hidden" name="mt_num_chk" id="mt_num_chk" value="0">
            <div class="mt-5">
                <div class="ip_wr mt_hp_chk_msg">
                    <div class="ip_tit">
                        <h5>인증번호</h5>
                    </div>
                    <div class="form-row mt_06">
                        <div class="col-12">
                            <input type="tel" class="form-control input_time_input" placeholder="6자리 숫자" name="mt_num" id="mt_num" maxlength="6" autocomplete="off">
                            <span class="fc_red fs_15 fw_300 bg_gray_100 input_time" id="cert_timer"></span>
                        </div>
                    </div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 인증번호가 일치하지 않습니다.</div>
                </div>
                <div class="ip_wr mt-5 mt_hp_msg">
                    <div class="ip_tit">
                        <h5 class="">입력하신 휴대전화번호로 인증번호가 발송됩니다.</h5>
                    </div>
                    <input type="tel" class="form-control" placeholder="<?= translate('010-0000-0000', $userLang) ?>" id="mt_hp" name="mt_hp" value="<?= $_GET['phoneNumber'] ?>" readonly>
                    <!-- <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div> -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 휴대전화번호를 다시 확인해주세요</div>
                    <button type="button" onclick="check_hp('send', 'join')" class="btn fs_12 fc_primary rounded-pill bg_secondary text-center px_12 py_07 text_dynamic w_fit h_fit_im d-flex align-items-center mt-3" id="re_send_btn">인증번호가 안와요! <i class="xi-arrow-right ml-2"></i></button>
                </div>
            </div>
            <div class="b_botton">
                <!-- <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='join_email.php'"><?= translate('입력했어요!', $userLang) ?></button> -->
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block " id="auth_check_button" onclick="check_hp('auth');"><?= translate('입력했어요!', $userLang) ?></button>
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block d-none" id="next_button" onclick="next_page()">휴대폰 인증을 완료했어요!</button>
            </div>
            <div id="layoutViewport"></div>
        </form>
    </div>
</div>
<script>
    // 페이지가 로드될 때
    window.onload = function() {
        // 입력 폼을 선택하고
        var mtNumInput = document.getElementById("mt_num");
        // 자동완성 값을 비워줍니다.
        mtNumInput.value = "";
    }
    <? if ($_SESSION['_number_chk'] == 0) { ?>
        check_hp('send', 'join');
    <? } ?>
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
                url: "./join_update",
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
                        $('#re_send_btn').addClass("hidden");


                        jalert('인증번호가 발송되었어요!');
                    } else {
                        clearInterval(timer);
                        $("#cert_timer").css("display", "none");
                        $('#re_send_btn').removeClass("hidden");
                        $("#mt_hp").attr("readonly", false);
                        $(".mt_hp_msg").removeClass("ip_valid");
                        $(".mt_hp_msg").addClass("ip_invalid");
                    }
                },
                error: function(d) {
                    console.log(d);
                    console.log("error:" + d.result + "errorMessage:" + d.msg);
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
                url: "./join_update",
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
                        window.location.replace('./join_email?phoneNumber=' + mt_hp);
                    } else {
                        $(".mt_hp_chk_msg").addClass("ip_invalid");
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
                $(".mt_hp_chk_msg").removeClass("ip_valid");
                $(".mt_hp_chk_msg").removeClass("ip_invalid");
            }
            // 1분 지난 후 인증번호 재발송 가능하도록 추가
            if (count < 120) {
                $('#re_send_btn').removeClass("hidden");
            }
        }, 1000);
    }

    function next_page() {
        var mt_hp = $("#mt_hp").val();
        var mt_hp_chk = $("#mt_hp_chk").val();
        var mt_num_chk = $("#mt_num_chk").val();

        if (mt_hp_chk == '1' && mt_num_chk == '1') {
            window.location.replace('./join_email?phoneNumber=' + mt_hp);
        }
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>