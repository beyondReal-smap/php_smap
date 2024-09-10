<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '5';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
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
</style>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 test_dynamic" style="line-height: 0.7;">
            <?= translate('어서오세요!', $userLang) ?><br>
            <?= translate('로그인하고', $userLang) ?><br>
            <?= translate('SMAP의 모든 기능을 사용하세요.', $userLang) ?>
        </p>
        <form method="post" name="frm_login" id="frm_login" action="./login_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="login" />
            <div class="mt-5">
                <div class="ip_wr mt-5" id="mt_hp_text">
                    <div class="ip_tit">
                        <h5 class=""><?= translate('휴대전화번호', $userLang) ?></h5>
                    </div>
                    <input type="tel" class="form-control" placeholder="010-0000-0000" name="mt_hp" id="mt_hp" maxlength="13" oninput="restrictInput(this);formatPhoneNumber(this);" value="<?= $_GET['phoneNumber'] ?>">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= translate('확인되었습니다.', $userLang) ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate('휴대전화번호를 다시 확인해주세요', $userLang) ?></div>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5><?= translate('비밀번호', $userLang) ?></h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" class="form-control" placeholder="<?= translate('비밀번호를 입력해주세요.', $userLang) ?>" id="mt_pass" name="mt_pass" maxlength="20">
                        <div class="btn btn_password_eye" id="password_show"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic"><?= translate('비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자, 특수문자 조합입니다.', $userLang) ?></div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= translate('확인되었습니다.', $userLang) ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate('비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.', $userLang) ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate('비밀번호가 일치하지 않습니다.', $userLang) ?></div>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block"><?= translate('로그인하기', $userLang) ?></button>
            </div>
            <button type="button" class="btn fs_14 text_gray" onclick="javascript:location.href='./join_entry'"><?= translate('아직 회원가입을 하지 않으셨나요?', $userLang) ?></button>
            <button type="button" class="btn fs_14 text_gray" onclick="find_password()"><?= translate('비밀번호가 기억나지 않나요?', $userLang) ?></button>
        </form>
        <script>
            //휴대전화번호 입력 확인
            var phoneCheck = false;

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

            function find_password() {
                let mt_hp = $("#mt_hp").val();
                var regex = /^(010)-?(\d{4})-?(\d{4})$/;
                if (regex.test(mt_hp)) {
                    $("#mt_hp_text").addClass("ip_valid");
                    $("#mt_hp_text").removeClass("ip_invalid");
                    phoneCheck = true;
                } else {
                    $("#mt_hp_text").addClass("ip_invalid");
                    $("#mt_hp_text").removeClass("ip_valid");
                    phoneCheck = false;
                }
                if (phoneCheck === true) {
                    $.ajax({
                        url: "./join_update.php",
                        type: "POST",
                        data: {
                            act: "check_hp",
                            mt_hp: mt_hp,
                        },
                        dataType: "json",
                        success: function(d, s) {
                            console.log(d);
                            if (d.result == "login") {
                                window.location.href = './form_verify?phoneNumber=' + mt_hp;
                            } else {
                                jalert('<?= translate('해당 휴대폰번호로 가입된 정보가 없습니다.', $userLang) ?>');
                                return;
                            }
                        },
                    });
                }
            }

            function formatPhoneNumber(input) {
                // 입력된 내용에서 숫자만 남기고 모든 문자 제거
                var phoneNumber = input.value.replace(/\D/g, '');

                // 전화번호 형식에 맞게 "-" 추가
                if (phoneNumber.length > 3 && phoneNumber.length <= 7) {
                    phoneNumber = phoneNumber.replace(/(\d{3})(\d{1,4})/, '$1-$2');
                } else if (phoneNumber.length > 7) {
                    phoneNumber = phoneNumber.replace(/(\d{3})(\d{4})(\d{1,4})/, '$1-$2-$3');
                }

                // 최대 길이 제한
                if (phoneNumber.length > 13) {
                    phoneNumber = phoneNumber.substring(0, 13);
                }

                // 형식이 적용된 전화번호로 변경
                input.value = phoneNumber;
            }

            function restrictInput(element) {
                // 숫자와 하이픈만 허용
                element.value = element.value.replace(/[^0-9-]/g, '');
                // 중복된 하이픈 제거
                element.value = element.value.replace(/-{2,}/g, '-');
            }
            $.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "비밀번호는 영문+숫자+특수문자 포함 8자리 이상이며, 특수문자는 !@#$%^만 지원됩니다.");

            $("#frm_login").validate({
                submitHandler: function() {
                    var f = document.frm_login;

                    // $('#splinner_modal').modal('toggle');

                    return true;

                },
                rules: {
                    mt_hp: {
                        required: true,
                        minlength: 11,
                    },
                    mt_pass: {
                        required: true,
                        minlength: 8,
                        regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
                    },
                },
                messages: {
                    mt_hp: {
                        required: "휴대전화번호를 입력해주세요.",
                        minlength: "최소 {0}글자이상이어야 합니다",
                    },
                    mt_pass: {
                        required: "비밀번호를 입력하세요.",
                        minlength: "최소 {0}글자이상이어야 합니다",
                    }
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });

            $("#mt_hp").filter(".lower").on("keyup", function() {
                $(this).val($(this).val().toLowerCase());
            });
        </script>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>