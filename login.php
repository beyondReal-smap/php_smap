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
        <p class="tit_h1 wh_pre line_h1_3 test_dynamic" style="line-height: 1.2;">
            <?= $translations['txt_welcome'] ?>
        </p>
        <form method="post" name="frm_login" id="frm_login" action="./login_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="act" id="act" value="login" />
            <div class="mt-5">
                <div class="ip_wr mt-5" id="login_input_box">
                    <div class="ip_tit">
                        <h5 class="">
                            <?php if ($userLang == 'ko'): ?>
                                <?= $translations['txt_phone_number'] ?>
                            <?php else: ?>
                                <?= $translations['txt_email_address'] ?>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <?php if ($userLang == 'ko'): ?>
                        <input type="tel" class="form-control" placeholder="<?= $translations['txt_phone_placeholder'] ?>" name="mt_hp" id="mt_hp" maxlength="13" oninput="restrictInput(this);formatPhoneNumber(this);" value="<?= $_GET['phoneNumber'] ?>">
                    <?php else: ?>
                        <input type="email" class="form-control" placeholder="example@domain.com" name="mt_email" id="mt_email" value="<?= $_GET['email'] ?>">
                    <?php endif; ?>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i>
                        <?php if ($userLang == 'ko'): ?>
                            <?= $translations['txt_check_phone_number'] ?>
                        <?php else: ?>
                            <?= $translations['txt_check_email_address'] ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5><?= $translations['txt_password'] ?></h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" class="form-control" placeholder="<?= $translations['txt_enter_password'] ?>" id="mt_pass" name="mt_pass" maxlength="20">
                        <div class="btn btn_password_eye" id="password_show"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic"><?= $translations['txt_password_requirements'] ?></div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_password_requirements_short'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_password_mismatch'] ?></div>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block"><?= $translations['txt_login'] ?></button>
            </div>
            <button type="button" class="btn fs_14 text_gray" onclick="javascript:location.href='./join_entry'"><?= $translations['txt_no_membership'] ?></button>
            <button type="button" class="btn fs_14 text_gray" onclick="find_password()"><?= $translations['txt_forgot_password'] ?></button>
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
                <?php if ($userLang == 'ko'): ?>
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
                                    jalert('<?= $translations['txt_no_phone_info'] ?>'); // 해당 휴대폰번호로 가입된 정보가 없습니다.
                                    return;
                                }
                            },
                        });
                    }
                <?php else: ?>
                    let mt_email = $("#mt_email").val();
                    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (regex.test(mt_email)) {
                        $("#login_input_box").addClass("ip_valid");
                        $("#login_input_box").removeClass("ip_invalid");
                        emailCheck = true;
                    } else {
                        $("#login_input_box").addClass("ip_invalid");
                        $("#login_input_box").removeClass("ip_valid");
                        emailCheck = false;
                    }
                    if (emailCheck === true) {
                        $.ajax({
                            url: "./join_update.php",
                            type: "POST",
                            data: {
                                act: "check_email",
                                mt_email: mt_email,
                            },
                            dataType: "json",
                            success: function(d, s) {
                                console.log(d);
                                if (d.result == "login") {
                                    // 네이버웍스 api를 활용해서 비밀번호 재설정 메일을 보내야한다.
                                    $.ajax({
                                        url: "./send_email.php",
                                        type: "POST",
                                        data: {
                                            mt_email: mt_email,
                                        },
                                        dataType: "json",
                                        success: function(response) {
                                            if (response == 201) {
                                                jalert('<?= $translations['txt_email_sent_success'] ?>'); // 인증 이메일이 성공적으로 발송되었어요! 받은 편지함을 확인해 주세요.
                                            } else {
                                                jalert('<?= $translations['txt_email_sent_failure'] ?>'); // 이메일 발송에 문제가 발생했어요. 다시 시도해 주세요!
                                            }
                                        },
                                        error: function(xhr, status, error) {
                                            console.log('Error: ' + error);
                                            console.log('Response: ' + xhr.responseText);
                                        }
                                    });
                                } else {
                                    jalert('<?= $translations['txt_no_email_info'] ?>'); // 해당 이메일주소로 가입된 정보가 없습니다.
                                    return;
                                }
                            },
                        });
                    }
                <?php endif; ?>
            }

            <?php if ($userLang == 'ko'): ?>

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
            <?php endif; ?>

            $.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "<?= $translations['txt_password_requirements'] ?>");

            $("#frm_login").validate({
                submitHandler: function() {
                    var f = document.frm_login;

                    // $('#splinner_modal').modal('toggle');

                    return true;

                },
                rules: {
                    <?php if ($userLang == 'ko'): ?>
                        mt_hp: {
                            required: true,
                            minlength: 11,
                        }, // 추가된 쉼표
                    <?php else: ?>
                        mt_email: {
                            required: true,
                            email: true,
                            // ... existing code ...
                        },
                    <?php endif; ?>
                    mt_pass: {
                        required: true,
                        minlength: 8,
                        regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
                    },
                },
                messages: {
                    <?php if ($userLang == 'ko'): ?>
                        mt_hp: {
                            required: "<?= $translations['txt_enter_phone_number'] ?>",
                            minlength: "<?= $translations['txt_min_length_error'] ?>",
                        },
                    <?php else: ?>
                        mt_email: {
                            required: "<?= $translations['txt_enter_email'] ?>",
                            email: "<?= $translations['txt_invalid_email_format'] ?>"
                        },
                    <?php endif; ?>
                    mt_pass: {
                        required: "<?= $translations['txt_enter_password'] ?>",
                        minlength: "<?= $translations['txt_min_length_error'] ?>",
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