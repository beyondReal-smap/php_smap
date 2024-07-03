<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$h_menu = '7';
$h_func = "back_confirm()";
// $h_func = "location.replace('./join_email?phoneNumber=" . $_GET['phoneNumber'] . "&mtEmail=" . $_GET['mtEmail'] . "')";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_GET['mt_idx']) {
    $DB->where('mt_idx', $_GET['mt_idx']);
    $DB->delete('member_t');
}
?>
<style>
    .ip_password {
        position: relative;
    }

    .ip_password input[type="password"].form-control {
        padding-right: 5rem;
    }

    .btn_password_eye {
        position: absolute;
        right: 1.6rem;
        top: 50%;
        transform: translateY(-50%);
        width: 2.2rem;
        height: 2.2rem;
        padding: 0;
    }

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
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic">SMAP에서 사용하실 나만의
            비밀번호를 설정해 주세요.
        </p>
        <form action="">
            <input type="hidden" name="HTTP_REFERER" id="HTTP_REFERER" value="<?= $_SERVER["HTTP_REFERER"] ?>">
            <input type="hidden" name="mt_hp" id="mt_hp" value="<?= $_GET['phoneNumber'] ?>">
            <input type="hidden" name="mt_email" id="mt_email" value="<?= $_GET['mtEmail'] ?>">
            <input type="hidden" name="mt_token_id" id="mt_token_id" value="<?= $_SESSION['_mt_token_id'] ?>">
            <div class="mt-5">
                <div class="ip_wr" id="mt_pass_text">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" class="form-control" placeholder="비밀번호를 입력해주세요." id="mt_pass" name="mt_pass" maxlength="20">
                        <div class="btn btn_password_eye" id="password_show"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic">비밀번호는 최소 9글자 이상 공백 없이
                        문자, 숫자, 특수문자 조합입니다.</div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 비밀번호가 규칙에 맞습니다. 계속 진행해주세요.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 입력하신 비밀번호가 서로 일치하지 않아요. 다시입력해주세요.</div>
                    <div class="form-text ip_invalid2"><i class="xi-error-o"></i> 비밀번호는 영문 대/소문자, 숫자, 특수문자를 모두 포함해야 합니다. 다시 설정해주세요.</div>
                </div>
                <div class="ip_wr mt_25" id="mt_pass_confirm_text">
                    <div class="ip_tit">
                        <h5>비밀번호 확인</h5>
                    </div>

                    <div class="ip_password">
                        <input type="password" class="form-control" placeholder="비밀번호를 한번 더 입력해주세요." id="mt_pass_confirm" name="mt_pass_confirm" maxlength="20">
                        <div class="btn btn_password_eye" id="password_show2"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none2"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 입력하신 비밀번호가 서로 일치하지 않아요. 다시입력해주세요.</div>
                    <div class="form-text ip_invalid2"><i class="xi-error-o"></i> 비밀번호는 영문 대/소문자, 숫자, 특수문자를 모두 포함해야 합니다. 다시 설정해주세요.</div>
                </div>
            </div>
            <div class="b_botton">
                <!-- <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='join_add.php'">입력했어요!</button> -->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " id="password_chk" onclick="check_pwd()">입력했어요!</button>
            </div>
            <div id="layoutViewport"></div>
        </form>
    </div>
</div>
<!-- 뒤로가기 클릭 시 -->
<div class="modal fade" id="back_confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">잠깐! 회원가입을 마치지 않고 가시려고요?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="location.replace('./join_email?phoneNumber=<?= $_GET['phoneNumber'] ?>&mtEmail=<?= $_GET['mtEmail'] ?>')">네</button>
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">아니요</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // 비밀번호 유효성 검사
    document.getElementById("mt_pass").addEventListener("keyup", function() {
        var mt_pass = document.getElementById("mt_pass").value;

        // 비밀번호 형식 체크
        let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
        let isValidPassword = passwordRegex.test(mt_pass);

        if (isValidPassword) {
            $("#mt_pass_text").addClass("ip_valid");
            $("#mt_pass_text").removeClass("ip_invalid");
            $("#mt_pass_text").removeClass("ip_invalid2");
            $("#mt_pass_confirm_text").removeClass("ip_valid");
            $("#mt_pass_confirm_text").removeClass("ip_invalid");
            $("#mt_pass_confirm_text").removeClass("ip_invalid2");
        } else {
            $("#mt_pass_text").addClass("ip_invalid2");
            $("#mt_pass_text").removeClass("ip_valid");
            $("#mt_pass_text").removeClass("ip_invalid");
            $("#mt_pass_confirm_text").removeClass("ip_valid");
            $("#mt_pass_confirm_text").removeClass("ip_invalid");
            $("#mt_pass_confirm_text").removeClass("ip_invalid2");
        }

    });
    // 비밀번호 확인 유효성 검사
    document.getElementById("mt_pass_confirm").addEventListener("keyup", function() {
        var mt_pass = document.getElementById("mt_pass").value;
        var confirmPassword = document.getElementById("mt_pass_confirm").value;

        // 비밀번호 형식 체크
        let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
        let isValidPassword = passwordRegex.test(confirmPassword);
        if (isValidPassword) {
            if (mt_pass === confirmPassword) {
                $("#mt_pass_confirm_text").addClass("ip_valid");
                $("#mt_pass_confirm_text").removeClass("ip_invalid");
                $("#mt_pass_confirm_text").removeClass("ip_invalid2");
            } else {
                $("#mt_pass_confirm_text").addClass("ip_invalid");
                $("#mt_pass_confirm_text").removeClass("ip_valid");
                $("#mt_pass_confirm_text").removeClass("ip_invalid2");
            }
        } else {
            $("#mt_pass_confirm_text").addClass("ip_invalid2");
            $("#mt_pass_confirm_text").removeClass("ip_valid");
            $("#mt_pass_confirm_text").removeClass("ip_invalid");
        }

    });

    // 버튼 클릭 시 비밀번호 입력값 표시
    $("#password_none").click(function() {
        var passwordInput = document.getElementById("mt_pass");
        var passwordType = passwordInput.getAttribute("type");
        passwordInput.setAttribute("type", "password");
        $("#password_show").removeClass('d-none');
        $("#password_none").addClass('d-none');
    });
    // 버튼 클릭 시 비밀번호 입력값 미표시
    $("#password_show").click(function() {
        var passwordInput = document.getElementById("mt_pass");
        var passwordType = passwordInput.getAttribute("type");
        passwordInput.setAttribute("type", "text");
        $("#password_none").removeClass('d-none');
        $("#password_show").addClass('d-none');
    });

    // 버튼 클릭 시 비밀번호 입력값 표시
    $("#password_none2").click(function() {
        var passwordInput = document.getElementById("mt_pass_confirm");
        var passwordType = passwordInput.getAttribute("type");
        passwordInput.setAttribute("type", "password");
        $("#password_show2").removeClass('d-none');
        $("#password_none2").addClass('d-none');
    });
    // 버튼 클릭 시 비밀번호 입력값 미표시
    $("#password_show2").click(function() {
        var passwordInput = document.getElementById("mt_pass_confirm");
        var passwordType = passwordInput.getAttribute("type");
        passwordInput.setAttribute("type", "text");
        $("#password_none2").removeClass('d-none');
        $("#password_show2").addClass('d-none');
    });

    function check_pwd() {
        // 비밀번호 입력값 가져오기
        let mt_pass = $("#mt_pass").val();
        let mt_pass_confirm = $("#mt_pass_confirm").val();
        let mt_hp = $("#mt_hp").val();
        let mt_email = $("#mt_email").val();
        let mt_token_id = $("#mt_token_id").val();

        // 비밀번호 형식 체크
        let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
        let isValidPassword = passwordRegex.test(mt_pass);

        // 비밀번호가 일치하는지 확인
        let passwordsMatch = mt_pass === mt_pass_confirm;

        // 비밀번호 형식 및 일치 여부 확인
        if (isValidPassword && passwordsMatch) {
            // 비밀번호 형식 및 일치하는 경우 여기에 추가 로직을 작성할 수 있습니다.
            $("#mt_pass_text").addClass("ip_valid");
            $("#mt_pass_confirm_text").addClass("ip_valid");
            $("#mt_pass_text").removeClass("ip_invalid");
            $("#mt_pass_text").removeClass("ip_invalid2");
            $("#mt_pass_confirm_text").removeClass("ip_invalid");


            const passwordBtn = document.getElementById('password_chk');
            passwordBtn.disabled = true;

            // $('#splinner_modal').modal('toggle');

            $.ajax({
                url: "./join_update",
                type: "POST",
                data: {
                    act: "join",
                    mt_pass: mt_pass,
                    mt_pass_confirm: mt_pass_confirm,
                    mt_hp: mt_hp,
                    mt_email: mt_email,
                    mt_token_id: mt_token_id,
                },
                dataType: "json",
                success: function(d, s) {
                    console.log(d);
                    if (d.result == "_ok") {
                        // 회원 로그인 상태 및 위치로그값 추가
                        var message = {
                            "type": "memberLogin",
                        };
                        if (isAndroid()) {
                            window.smapAndroid.memberLogin();
                        } else if (isiOS()) {
                            window.webkit.messageHandlers.smapIos.postMessage(message);
                        };
                        // $('#splinner_modal').modal('hide');
                        passwordBtn.disabled = false;
                        location.replace('./join_add?phoneNumber=' + mt_hp + '&mtEmail=' + mt_email)
                    } else if (d.result == '_already') {
                        // $('#splinner_modal').modal('hide');
                        jalert_url('이미 존재하는 휴대폰번호 입니다. 처음부터 다시 시도해주세요.', './join_entry');
                        passwordBtn.disabled = false;
                    } else {
                        console.log("error:" + d);
                        // $('#splinner_modal').modal('hide');
                        passwordBtn.disabled = false;
                    }
                },
                error: function(d) {
                    console.log("error:" + d);
                    // $('#splinner_modal').modal('hide');
                },
            });
        } else {
            if (!isValidPassword) {
                // 비밀번호 형식이 유효하지 않은 경우 메시지 표시
                $("#mt_pass_text").addClass("ip_invalid2");
                $("#mt_pass_confirm_text").addClass("ip_invalid2");
                $("#mt_pass_text").removeClass("ip_valid");
                $("#mt_pass_text").removeClass("ip_invalid");
                $("#mt_pass_confirm_text").removeClass("ip_valid");
                $("#mt_pass_confirm_text").removeClass("ip_invalid");
            } else if (!passwordsMatch) {
                // 비밀번호가 일치하지 않는 경우 메시지 표시
                // $("#mt_pass_text").addClass("ip_invalid");
                $("#mt_pass_confirm_text").addClass("ip_invalid");
                // $("#mt_pass_text").removeClass("ip_invalid2");
                // $("#mt_pass_text").removeClass("ip_valid");
                $("#mt_pass_confirm_text").removeClass("ip_valid");
                $("#mt_pass_confirm_text").removeClass("ip_invalid2");
            }
        }
    }

    function back_confirm() {

        // 중복된 이메일이 존재하는 경우 모달 표시
        $('#back_confirm').modal('show');
    }


    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>