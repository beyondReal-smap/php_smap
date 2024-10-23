<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '2';
$h_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if (!$_GET['phoneNumber']) {
    p_alert($translations['txt_invalid_access'], './');
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
        <p class="tit_h1 wh_pre line_h1_3"><?= $translations['txt_enter_new_password'] ?></p>
        <form method="post" name="frm_form" id="frm_form" action="./join_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="change_password" />
            <input type="hidden" name="mt_hp" id="mt_hp" value="<?= $_GET['phoneNumber'] ?>" />
            <div class="mt-5">
                <div class="ip_wr" id="mt_pass_text">
                    <div class="ip_tit">
                        <h5><?= $translations['txt_password'] ?></h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" name="mt_pass" id="mt_pass" class="form-control" maxlength="20" placeholder="<?= $translations['txt_enter_password'] ?>" onkeyup="f_isValid()">
                        <div class="btn btn_password_eye" id="password_show"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic"><?= $translations['txt_password_requirements'] ?></div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_password_valid'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_password_mismatch'] ?></div>
                    <div class="form-text ip_invalid2"><i class="xi-error-o"></i> <?= $translations['txt_password_invalid'] ?></div>
                </div>
                <div class="ip_wr mt_25" id="mt_pass_confirm_text">
                    <div class="ip_tit">
                        <h5><?= $translations['txt_confirm_password'] ?></h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" name="mt_pass_confirm" id="mt_pass_confirm" class="form-control" maxlength="20" placeholder="<?= $translations['txt_reenter_password'] ?>" onkeyup="f_isValid()">
                        <div class="btn btn_password_eye" id="password_show2"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none2"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_password_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_password_mismatch'] ?></div>
                    <!-- <div class="form-text ip_invalid2"><i class="xi-error-o"></i> <?= $translations['txt_password_invalid'] ?></div> -->
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block " disabled><?= $translations['txt_input_complete'] ?></button>
            </div>
        </form>
    </div>
</div>
<script>
    function f_isValid() {
        let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
        let isValidPassword = passwordRegex.test($('#mt_pass').val());
        let isValidPassword2 = passwordRegex.test($('#mt_pass_confirm').val());
        if (isValidPassword && isValidPassword2) {
            $('#frm_form button[type="submit"]').prop('disabled', false);
        } else {
            $('#frm_form button[type="submit"]').prop('disabled', true);
        }
    }

    $(document).ready(function() {
        f_isValid();

        $('#mt_pass_confirm').on('change', function() {
            f_isValid();
        });
    });

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

    $.validator.addMethod("regex", function(value, element, regexpr) {
        return regexpr.test(value);
    }, "비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.");

    $("#frm_form").validate({
        submitHandler: function() {
            // $('#splinner_modal').modal('toggle');

            return true;
        },
        rules: {
            mt_pass: {
                required: true,
                minlength: 9,
                regex: /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/
            },
            mt_pass_confirm: {
                required: true,
                equalTo: mt_pass
            },
        },
        messages: {
            mt_pass: {
                required: "비밀번호를 입력하세요.",
                minlength: "최소 {0}글자이상이어야 합니다",
            },
            mt_pass_confirm: {
                required: "비밀번호 확인을 입력해 주세요.",
                equalTo: "비밀번호가 동일하지 않습니다."
            },
        },
        errorPlacement: function(error, element) {
            $(element)
                .closest("form")
                .find("span[for='" + element.attr("id") + "']")
                .append(error);
        },
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>