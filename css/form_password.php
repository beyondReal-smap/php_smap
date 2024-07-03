<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './form_verify';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">사용하실 비밀번호를 입력해주세요.</p>
        <form method="post" name="frm_form" id="frm_form" action="./form_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="form_password" />
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <input type="password" name="mt_pass" id="mt_pass" class="form-control" maxlength="20" placeholder="비밀번호를 입력해주세요.">
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2">비밀번호는 영문+숫자+특수문자 포함 8자리 이상이며, 특수문자는 !@#$%^만 지원됩니다.</div>
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5>비밀번호 확인</h5>
                    </div>
                    <input type="password" name="mt_pass_confirm" id="mt_pass_confirm" class="form-control" maxlength="20" placeholder="비밀번호를 한번 더 입력해주세요.">
                </div>
            </div>
            <div class="bottom_btn_flex_end_wrap" style="height: 320px;">
                <div class="bottom_btn_flex_end_box">
                    <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" disabled>입력했어요!</button>
                </div>
            </div>
        </form>
        <script>
        function f_isValid() {
            if ($('#mt_pass').val() == '' && $('#mt_pass_confirm').val() == '') {
                $('#frm_form button[type="submit"]').prop('disabled', true);
            } else {
                $('#frm_form button[type="submit"]').prop('disabled', false);
            }
        }

        $(document).ready(function() {
            f_isValid();

            $('#mt_pass_confirm').on('change', function() {
                f_isValid();
            });
        });

        $.validator.addMethod("regex", function(value, element, regexpr) {
            return regexpr.test(value);
        }, "비밀번호는 영문+숫자+특수문자 포함 8자리 이상이며, 특수문자는 !@#$%^만 지원됩니다.");

        $("#frm_form").validate({
            submitHandler: function() {
                // $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                mt_pass: {
                    required: true,
                    minlength: 8,
                    regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
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
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>