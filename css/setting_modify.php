<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
// $b_menu = '5';
$h_menu = '6';
$h_url = './setting_list';
$_SUB_HEAD_TITLE = "기본정보 수정";
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

$mt_info = get_member_t_info();
?>
<style>
    select[readonly] {
        background-color: #ddd;
        pointer-events: none;
    }
</style>
<div class="container sub_pg">
    <div class="mt-4">
        <form method="post" name="frm_form" id="frm_form" action="./setting_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="setting_modify" />
            <div class="mt-5">
                <?php
                //프로필 사진 등록/수정
                include $_SERVER['DOCUMENT_ROOT'] . "/profile.inc.php";
                ?>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">닉네임 <b class="text-danger">*</b></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_nickname_cnt">0</span>/12)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_nickname" id="mt_nickname" value="<?= $mt_info['mt_nickname'] ?>" minlength="2" maxlength="12" data-length-id="mt_nickname_cnt" oninput="maxLengthCheck(this)" placeholder="사용하실 닉네임을 입력해주세요.">
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <div class="form_arm_text fs_13 fw_600 fc_gray_600 px-4 line_h1_2">한글/영문/숫자만 입력가능해요!</div>
                        </div>
                    </div>
                </div>
                <div class="ip_wr mt_25 ip_valid">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">이름 <b class="text-danger">*</b></h5>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_name" id="mt_name" value="<?= $mt_info['mt_name'] ?>" maxlength="30" data-length-id="mt_name_cnt" oninput="maxLengthCheck(this)" placeholder="이름을 입력해주세요">
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5 class="">핸드폰번호</h5>
                    </div>
                    <input type="text" class="form-control" name="mt_hp" id="mt_hp" value="<?= format_phone($mt_info['mt_hp']) ?>" readonly minlength="2" maxlength="20" oninput="maxLengthCheck(this)" placeholder="핸드폰번호를 입력해주세요.(숫자만)">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5 class="">생년월일</h5>
                    </div>
                    <input type="text" class="form-control d-flex align-items-center " name="mt_birth" id="mt_birth" value="<?= $mt_info['mt_birth'] ?>" maxlength="10" oninput="maxLengthCheck(this)" placeholder="생년월일 8자리를 입력해주세요.">
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
            </div>
            <div class="bottom_btn_flex_end_wrap mb-5" style="height: 120px;">
                <div class="bottom_btn_flex_end_box">
                    <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block">기본정보 수정하기</button>
                </div>
            </div>
        </form>
        <script>
            $(document).ready(function() {
                $(document).on("keyup", "input.txt-cnt", function() {
                    var cnt_id = $(this).data('length-id');
                    $('#' + cnt_id).text($(this).val().length);
                });
                <?php if ($mt_info['mt_nickname']) { ?>
                    $('#mt_nickname_cnt').text($('#mt_nickname').val().length);
                <?php } ?>
                <?php if ($mt_info['mt_gender']) { ?>
                    $('#mt_gender').val('<?= $mt_info['mt_gender'] ?>');
                <?php } ?>
            });

            /*
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
            */

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
                        // mt_nick_chk: true,
                    },
                },
                messages: {
                    mt_name: {
                        required: "이름을 입력해주세요.",
                    },
                    mt_nickname: {
                        required: "닉네임을 입력해주세요.",
                        // mt_nick_chk: "중복된 닉네임이 존재합니다.",
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