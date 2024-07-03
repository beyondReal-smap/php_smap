<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './form_agree';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">이메일을 입력해주세요.</p>
        <p class="fs_12 fc_gray_600 mt-3 line_h1_2">이메일 확인을 위한 인증메일이 발송될 예정입니다.</p>
        <form method="post" name="frm_form" id="frm_form" action="./form_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="form_email" />
            <div class="mt-5">
                <div class="ip_wr ip_valid">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="email" name="mt_id" id="mt_id" class="form-control lower" placeholder="test@test.com">
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" disabled>입력했어요!</button>
            </div>
        </form>
        <script>
        function f_isValid() {
            if ($('#mt_id').val() == '') {
                $('#frm_form button[type="submit"]').prop('disabled', true);
            } else {
                $('#frm_form button[type="submit"]').prop('disabled', false);
            }
        }

        $(document).ready(function() {
            f_isValid();

            $('#mt_id').on('change', function() {
                f_isValid();
            });
        });

        $.validator.addMethod("mt_id_chk", function(value, element) {
            var rtn = false;

            $.ajax({
                url: './form_update',
                data: {
                    act: 'chk_mt_id',
                    mt_id: value
                },
                type: 'POST',
                async: false,
                success: function(args) {
                    args = $.trim(args);
                    rtn = (args === 'true');
                }
            });

            return rtn;
        });

        $("#frm_form").validate({
            submitHandler: function() {
                var f = document.frm_login;

                // $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                mt_id: {
                    required: true,
                    minlength: 6,
                    email: true,
                    mt_id_chk: true,
                },
            },
            messages: {
                mt_id: {
                    required: "아이디(이메일)를 입력하세요",
                    minlength: "최소 {0}글자이상이어야 합니다",
                    email: "아이디(이메일)규칙에 어긋납니다.",
                    mt_id_chk: "중복된 이메일이 존재합니다.",
                },
            },
            errorPlacement: function(error, element) {
                $(element)
                    .closest("form")
                    .find("span[for='" + element.attr("id") + "']")
                    .append(error);
            },
        });

        $("#mt_id").filter(".lower").on("keyup", function() {
            $(this).val($(this).val().toLowerCase());
        });
        </script>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>