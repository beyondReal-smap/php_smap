<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting_list';
$_SUB_HEAD_TITLE = translate("비밀번호 변경", $userLang); // "비밀번호 변경" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', ''); // "로그인이 필요합니다." 번역
}
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
        <p class="tit_h1 wh_pre line_h1_3"><?= translate("본인 확인을 위해", $userLang); ?>
            <?= translate("현재 비밀번호를 입력해 주세요.", $userLang); ?></p> <!-- "본인 확인을 위해 \n 현재 비밀번호를 입력해 주세요." 번역 -->
        <form method="post" name="frm_form" id="frm_form" action="./setting_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="current_password" />
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5><?= translate("비밀번호", $userLang); ?></h5> <!-- "비밀번호" 번역 -->
                    </div>
                    <input type="password" name="mt_pass" id="mt_pass" class="form-control" maxlength="20" placeholder="<?= translate("비밀번호를 입력해주세요.", $userLang); ?>" onkeyup="f_isValid()"> <!-- "비밀번호를 입력해주세요." 번역 -->
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2"><?= translate("비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.", $userLang); ?></div> <!-- "비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다." 번역 -->
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= translate("확인되었습니다.", $userLang); ?></div> <!-- "확인되었습니다." 번역 -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate("비밀번호를 다시 확인해주세요", $userLang); ?></div> <!-- "비밀번호를 다시 확인해주세요" 번역 -->
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block"><?= translate("비밀번호 변경하기", $userLang); ?></button> <!-- "비밀번호 변경하기" 번역 -->
            </div>
            <div id="layoutViewport"></div>
        </form>
        <script>
            function f_isValid() {
                let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
                let isValidPassword = passwordRegex.test($('#mt_pass').val());
                if (isValidPassword) {
                    $('#frm_form button[type="submit"]').prop('disabled', false);
                } else {
                    $('#frm_form button[type="submit"]').prop('disabled', true);
                }
            }

            $(document).ready(function() {
                f_isValid();

                $('#mt_pass').on('change', function() {
                    f_isValid();
                });
            });

            $.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "<?= translate("비밀번호는 영문+숫자+특수문자 포함 8자리 이상이며, 특수문자는 !@#$%^만 지원됩니다.", $userLang); ?>"); // "비밀번호는 영문+숫자+특수문자 포함 8자리 이상이며, 특수문자는 !@#$%^만 지원됩니다." 번역

            $("#frm_form").validate({
                submitHandler: function() {
                    var f = document.frm_login;

                    // $('#splinner_modal').modal('toggle');

                    return true;
                },
                rules: {
                    mt_pass: {
                        required: true,
                        minlength: 9,
                        regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
                    },
                },
                messages: {
                    mt_pass: {
                        required: "<?= translate("비밀번호를 입력하세요.", $userLang); ?>", // "비밀번호를 입력하세요." 번역
                        minlength: "<?= translate("최소 {0}글자이상이어야 합니다", $userLang); ?>", // "최소 {0}글자이상이어야 합니다" 번역
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
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>