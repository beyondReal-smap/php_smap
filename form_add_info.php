<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '7';
$h_func = "f_back_chk('form_add_info');";
$_SUB_HEAD_TITLE = "";

if($_SESSION['_mt_idx'] == '') {
    gotourl('./logout');
}

include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">부가 정보를 입력해 주세요.</p>
        <form method="post" name="frm_form" id="frm_form" action="./form_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="form_add_info" />
            <div class="mt-5">
                <?php
                    //프로필 사진 등록/수정
                    include $_SERVER['DOCUMENT_ROOT']."/profile.inc.php";
?>
                <div class="ip_wr mt_25 ip_valid">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">이름 <b class="text-danger">*</b></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_name_cnt">0</span>/30)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_name" id="mt_name" maxlength="30" data-length-id="mt_name_cnt" placeholder="이름을 입력해주세요">
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5 class="">핸드폰번호</h5>
                    </div>
                    <input type="number" class="form-control" name="mt_hp" id="mt_hp" numberOnly minlength="2" maxlength="20" placeholder="핸드폰번호를 입력해주세요.(숫자만)">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5 class="">생년월일</h5>
                    </div>
                    <input type="date" class="form-control" name="mt_birth" id="mt_birth" maxlength="10" placeholder="생년월일 8자리를 입력해주세요.">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>성별</h5>
                    </div>
                    <select class="form-control custom-select" name="mt_gender" id="mt_gender">
                        <option value="">선택바랍니다.</option>
                        <option value="1">남자</option>
                        <option value="2">여자</option>
                    </select>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">닉네임 <b class="text-danger">*</b></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_nickname_cnt">0</span>/12)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_nickname" id="mt_nickname" minlength="2" maxlength="12" data-length-id="mt_nickname_cnt" placeholder="사용하실 닉네임을 입력해주세요.">
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <div class="form_arm_text fs_13 fw_600 fc_gray_600 px-4 line_h1_2">한글/영문/숫자만 입력가능해요!</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom_btn_flex_end_wrap" style="height: 120px;">
                <div class="bottom_btn_flex_end_box">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block">입력했어요!</button>
                </div>
            </div>
        </form>
        <script>
        $(document).ready(function() {
            $(document).on("keyup", "input.txt-cnt", function() {
                var cnt_id = $(this).data('length-id');
                $('#' + cnt_id).text($(this).val().length);
            });
        });

        $.validator.addMethod("mt_nick_chk", function(value, element) {
            var rtn = false;

            $.ajax({
                url: './form_update',
                data: {
                    act: 'chk_mt_nick',
                    mt_nickname: value
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
                // $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                mt_name: {
                    required: true,
                },
                mt_nickname: {
                    required: true,
                    mt_nick_chk: true,
                },
            },
            messages: {
                mt_name: {
                    required: "이름을 입력해주세요.",
                },
                mt_nickname: {
                    required: "닉네임을 입력해주세요.",
                    mt_nick_chk: "중복된 닉네임이 존재합니다.",
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