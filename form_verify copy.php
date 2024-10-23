<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './form_email';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">입력하신 휴대폰번호로
            인증번호를 보냈어요.</p>
        <form method="post" name="frm_form" id="frm_form" action="./form_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="form_verify" />
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5>인증번호</h5>
                    </div>
                    <div class="form-row mt_06 ip_valid">
                        <div class="col-12">
                            <input type="number" name="m_confirm_num" id="m_confirm_num" numberOnly maxlength="6" class="form-control input_time_input" placeholder="6자리 숫자">
                            <span class="fc_red fs_15 fw_300 bg_gray_100 input_time" id="m_cofirm_timer"></span>
                        </div>
                    </div>
                </div>
                <div class="ip_wr mt-5 ip_valid d-none-temp" id="re_send_mail_box">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="email" name="mt_id" id="mt_id" class="form-control lower" value="<?= $_SESSION['_mt_id_join'] ?>" readonly placeholder="test@test.com">
                    <button type="button" class="btn fs_12 fc_primary rounded-pill bg_secondary text-center px_12 py_07 text_dynamic w_fit h_fit_im d-flex align-items-center mt-3" onclick="re_send_mail();">인증번호가 안와요! <i class="xi-arrow-right ml-2"></i></button>
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" disabled>입력했어요!</button>
            </div>
        </form>
        <script>
            function f_isValid() {
                if ($('#m_confirm_num').val() == '') {
                    $('#frm_form button[type="submit"]').prop('disabled', true);
                } else {
                    $('#frm_form button[type="submit"]').prop('disabled', false);
                }
            }

            function set_timer() {
                var time = 180;
                var min = "";
                var sec = "";
                $('#frm_form button[type="submit"]').prop("disabled", true);
                $('#resend_mail_box').hide();
                timer = setInterval(function() {
                    min = parseInt(time / 60);
                    sec = time % 60;
                    document.getElementById("m_cofirm_timer").innerHTML = "" + (min.toString().length === 1 ? '0' + min : min) + ":" + (sec.toString().length === 1 ? '0' + sec : sec) + "";
                    time--;
                    if (time < -1) {
                        var ttxt = "인증번호 유효시간이 만료되었습니다.";
                        jalert(ttxt);

                        clearInterval(timer);
                        $("#m_cofirm_timer").hide();
                        $('#re_send_mail_box').show();
                    }
                }, 1000);
                isRunning = true;
            }

            $(document).ready(function() {
                f_isValid();

                $('#m_confirm_num').on('change', function() {
                    f_isValid();
                });

                set_timer();
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

            $.validator.addMethod("m_confirm_chk", function(value, element) {
                var rtn = false;

                $.ajax({
                    url: './form_update',
                    data: {
                        act: 'chk_m_confirm',
                        m_confirm_num: value
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

            function re_send_mail() {
                $('#m_cofirm_num').val('');
                $('#m_cofirm_num').val('');

                var rtn = false;
                var mt_id_t = $('#mt_id').val();
                var f = document.frm_form;

                if (mt_id_t == '') {
                    jalert("이메일을 입력바랍니다.", '', f.mt_id.focus());
                    return false;
                }

                $.ajax({
                    url: './form_update',
                    data: {
                        act: 'chk_mt_id',
                        mt_id: mt_id_t
                    },
                    type: 'POST',
                    async: false,
                    success: function(args) {
                        args = $.trim(args);
                        rtn = (args === 'true');
                    }
                });

                if (rtn == false) {
                    jaler('중복된 이메일이 존재합니다.');
                    return false;
                }

                $('#splinner_modal').modal('toggle');

                $.ajax({
                    url: './form_update',
                    data: {
                        act: 're_send_mail',
                        mt_id: mt_id_t
                    },
                    type: 'POST',
                    async: false,
                    success: function(args) {
                        console.log(args);

                        if (args == 'Y') {
                            $('#splinner_modal').modal('toggle');
                            jalert('이메일을 다시 전송했습니다. 확인바랍니다.');
                        } else {
                            console.log(args);
                        }
                    }
                });

                return false;
            }

            $("#frm_form").validate({
                submitHandler: function() {
                    var f = document.frm_login;

                    $('#splinner_modal').modal('toggle');

                    return true;
                },
                rules: {
                    m_confirm_num: {
                        required: true,
                        minlength: 6,
                        m_confirm_chk: true,
                    },
                },
                messages: {
                    m_confirm_num: {
                        required: "아이디(이메일)를 입력하세요",
                        minlength: "최소 {0}글자이상이어야 합니다",
                        m_confirm_chk: "인증번호가 일치하지 않습니다.",
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
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>