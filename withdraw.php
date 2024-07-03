<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting_list';
$_SUB_HEAD_TITLE = "회원탈퇴";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
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
        <p class="tit_h1 wh_pre line_h1_3">본인 확인을 위해
            현재 비밀번호를 입력해 주세요.
        </p>
        <form method="post" name="frm_form" id="frm_form" action="./setting_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="withdraw" />
            <div class="mt-5">
                <div class="ip_wr" id="mt_pass_text">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <div class="ip_password">
                        <input type="password" name="mt_pass" id="mt_pass" class="form-control" maxlength="20" placeholder="비밀번호를 입력해주세요." oninput="f_isValid(this)">
                        <div class="btn btn_password_eye" id="password_show"><img src="./img/ico_psd_off.png" alt="" style="max-width: 100%;"></div>
                        <div class="btn btn_password_eye d-none" id="password_none"><img src="./img/ico_psd_on.png" alt="" style="max-width: 100%;"></div>
                    </div>
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2">비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.</div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 비밀번호를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block ">비밀번호 확인하기</button>
            </div>
            <div id="layoutViewport"></div>
        </form>
        <script>
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

            function f_isValid() {
                if ($('#mt_pass').val() == '') {
                    $('#frm_form button[type="submit"]').prop('disabled', true);
                } else {
                    $('#frm_form button[type="submit"]').prop('disabled', false);
                }
            }

            $(document).ready(function() {
                f_isValid();

                $('#mt_pass').on('change', function() {
                    f_isValid();
                });

                $(document).on("keyup", "textarea.txt-cnt", function() {
                    var cnt_id = $(this).data('length-id');
                    $('#' + cnt_id).text($(this).val().length);
                });
            });

            $.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.");

            $("#frm_form").validate({
                submitHandler: function() {
                    var f = document.frm_login;

                    // $('#splinner_modal').modal('toggle');

                    var form_data = new FormData();
                    form_data.append("act", "withdraw");
                    form_data.append("mt_pass", $('#mt_pass').val());

                    $.ajax({
                        url: "./setting_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            if (data == 'Y') {
                                // $('#splinner_modal').modal('toggle');
                                $('#withdraw_modal').modal('show');
                            } else {
                                // $('#splinner_modal').modal('toggle');
                                jalert('아이디 및 비밀번호가 올바르지 않습니다.<br/>아이디, 비밀번호는 대문자, 소문자를 구분합니다.<br/><Caps Lock>키가 켜져 있는지 확인하시고 다시 입력하십시오.');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                },
                rules: {
                    mt_pass: {
                        required: true,
                        minlength: 8,
                        regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
                    },
                },
                messages: {
                    mt_pass: {
                        required: "비밀번호를 입력하세요.",
                        minlength: "최소 {0}글자이상이어야 합니다",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });

            function f_withdraw() {
                var c = 0
                $('input:radio[name="mt_retire_chk"]').each(function() {
                    if ($(this).prop("checked") == true) {
                        c++;
                    }
                });

                if (c < 1) {
                    jalert('탈퇴사유를 선택바랍니다.');
                    return false;
                }
                if ($("input:radio[name=mt_retire_chk]:checked").val() == '4' && $('#mt_retire_etc').val() == '') {
                    jalert('탈퇴하시는 기타이유를 입력바랍니다.');
                    return false;
                }

                // $('#splinner_modal').modal('toggle');

                var form_data = new FormData();
                form_data.append("act", "withdraw_on");
                form_data.append("mt_retire_chk", $("input:radio[name=mt_retire_chk]:checked").val());
                form_data.append("mt_retire_etc", $('#mt_retire_etc').val());

                $.ajax({
                    url: "./setting_update",
                    enctype: "multipart/form-data",
                    data: form_data,
                    type: "POST",
                    async: true,
                    contentType: false,
                    processData: false,
                    cache: true,
                    timeout: 5000,
                    success: function(data) {
                        if (data == 'Y') {
                            $('#withdraw_modal').modal('hide');
                            jalert_url('회원탈퇴가 완료되었습니다.', './join_entry');
                        } else {

                            console.log(data);
                        }
                    },
                    error: function(err) {
                        console.log(err);
                    },
                });
            }
        </script>
    </div>
</div>

<!-- H-7 회원탈퇴 안내 -->
<div class="modal fade" id="withdraw_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header px-5 pt-5 pb-4">
                <p class="text_dynamic fs_16 fw_800 line_h1_3">우리 서비스를 떠나시려는 이유를
                    알려주실 수 있나요?
                    귀하의 소중한 의견을 통해
                    더 나은 서비스를 제공하려 노력합니다.
                </p>
            </div>
            <div class="modal-body px-5 pt-0 pb-5">
                <form class="">
                    <div class="ip_wr mb-2">
                        <div class="checks_wr flex-column">
                            <?php
                            foreach ($arr_mt_retire_chk as $key => $val) {
                                if ($val) {
                            ?>
                                    <div class="checks pb-1">
                                        <label class="chk_left">
                                            <input type="radio" name="mt_retire_chk" id="mt_retire_chk" value="<?= $key ?>" />
                                            <span class="ic_box"><i class="xi-check-min"></i></span>
                                            <div class="chk_p text_gray">
                                                <p class="text_dynamic"><?= $val ?></p>
                                            </div>
                                        </label>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                            <!-- 탈퇴이유 -->
                            <div class="ip_wr pb-3">
                                <div class="ip_tit d-flex align-items-center justify-content-between">
                                    <h5 class="fs_15 fw_500 text-text">탈퇴하는 이유를 알려주세요.</h5>
                                </div>
                                <textarea class="form-control txt-cnt" name="mt_retire_etc" id="mt_retire_etc" maxlength="1000" data-length-id="mt_retire_etc_cnt" placeholder="입력해주세요" rows="3"></textarea>
                                <p class="fc_gray_600 fs_12 text-right mt-2">(<span id="mt_retire_etc_cnt">0</span>/1000)</p>
                                <div class="invalid-feedback">1000자까지만 써주세요</div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="bg_gray px_16 pt_16 pb-2 rounded_12">
                    <div class="d-flex align-items-center">
                        <img src="<?= CDN_HTTP ?>/img/ico_warring_chk.png" width="14px" alt="확인해주세요" class="mr_08" />
                        <p class="fs_16 fw_800 line_h1_2">아래 사항을 확인해주세요.</p>
                    </div>
                    <ul class="py_07">
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">모든 데이터와 기록이 삭제돼요.</li>
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">한 번 탈퇴하시면 복구가 불가능해요.</li>
                        <li class="position-relative slash5 text_dynamic pl_22 fs_14 py_06 line_h1_2">재가입 시 이전 정보와 데이터는 복원되지 않아요.</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer w-100 p-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="f_withdraw();">탈퇴하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">나중에</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>